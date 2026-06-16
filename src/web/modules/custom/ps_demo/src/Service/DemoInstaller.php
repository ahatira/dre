<?php

declare(strict_types=1);

namespace Drupal\ps_demo\Service;

use Drupal\Component\Serialization\Yaml;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\FileStorage;
use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\default_content\ImporterInterface;
use Drupal\node\NodeInterface;
use Drupal\path_alias\PathAliasInterface;
use Drupal\ps_theme\Service\HomepageInstaller;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Post-import demo setup after default_content has loaded YAML entities.
 */
final class DemoInstaller {

  public function __construct(
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly EntityRepositoryInterface $entityRepository,
    private readonly ConfigFactoryInterface $configFactory,
    private readonly ModuleHandlerInterface $moduleHandler,
    private readonly ImporterInterface $defaultContentImporter,
    private readonly DemoTranslationSync $translationSync,
    private readonly LoggerInterface $logger,
    private readonly DemoHeroMediaImporter $heroMediaImporter,
    private readonly DemoPartialContentImporter $partialContentImporter,
  ) {}

  /**
   * Creates a DemoInstaller instance from the service container.
   */
  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get('entity_type.manager'),
      $container->get('entity.repository'),
      $container->get('config.factory'),
      $container->get('module_handler'),
      $container->get('default_content.importer'),
      new DemoTranslationSync(
        $container->get('entity_type.manager'),
        $container->get('entity.repository'),
        $container->get('config.factory'),
        $container->get('language_manager'),
        new DemoTranslationOverlayLoader(
          $container->get('extension.path.resolver'),
        ),
      ),
      $container->get('logger.channel.ps_demo'),
      DemoHeroMediaImporter::create($container),
      new DemoPartialContentImporter(
        $container->get('extension.list.module'),
        $container->get('entity.repository'),
        $container->get('entity_type.manager'),
        $container->get('default_content.content_entity_normalizer'),
      ),
    );
  }

  /**
   * Validates runtime before module installation completes.
   */
  public function prepareInstall(): void {
    $this->assertRuntimeReady();
  }

  /**
   * Applies demo layout and configuration after default content import.
   */
  public function finalizeInstall(): void {
    if (\Drupal::moduleHandler()->moduleExists('ps_homepage')) {
      HomepageInstaller::create(\Drupal::getContainer())->prepareLayoutBuilder();
      \Drupal::service('ps_homepage.layout_field_ensurer')->ensurePageLayoutFieldTranslatable();
    }
    $this->importContentIfMissing();
    $this->ensureEditorialContentModels();
    $this->heroMediaImporter->importIfMissing();
    $imported = $this->partialContentImporter->importEditorialContentIfMissing();
    $this->ensureEditorialTeaserImages();
    $this->ensureHomepageConfig();
    $this->translationSync->sync();
    $this->applyHomepageLayout();
    (new DemoMenuNormalizer($this->entityTypeManager))->normalize();
    if (\Drupal::hasService('ps_demo.search_uri_normalizer')) {
      \Drupal::service('ps_demo.search_uri_normalizer')->normalize();
    }
    $this->importDemoConfiguration();
    $this->applyPathAliases();
    $this->applyFrontPage();
  }

  /**
   * Ensures required modules and the front theme are available.
   */
  private function assertRuntimeReady(): void {
    foreach (['default_content', 'ps_homepage', 'ps_block', 'social_media_links'] as $module) {
      if (!$this->moduleHandler->moduleExists($module)) {
        throw new \RuntimeException("ps_demo requires the {$module} module.");
      }
    }

    $default_theme = $this->configFactory->get('system.theme')->get('default');
    if ($default_theme !== 'ps_theme') {
      $this->logger->warning(
        'ps_demo: active theme is @theme; ps_theme is recommended for the demo layout.',
        ['@theme' => $default_theme ?? 'none'],
      );
    }
  }

  /**
   * Imports YAML from content/ when the homepage node is missing.
   *
   * On first install, default_content hook_modules_installed() already imports.
   * This fallback supports make demo recovery without duplicating existing UUIDs.
   */
  private function importContentIfMissing(): void {
    if ($this->loadHomepageNode()) {
      return;
    }

    $this->defaultContentImporter->importContent('ps_demo');
    \Drupal::service('plugin.manager.menu.link')->rebuild();
    $this->logger->notice('ps_demo: imported default content via default_content module.');
  }

  /**
   * Sets teaser images on demo articles/studies when missing (existing installs).
   */
  private function ensureEditorialTeaserImages(): void {
    /** @var array<string, string> $map */
    $map = [
      'b2000005-0000-4000-8000-000000000001' => 'c1000004-0000-4000-8000-000000000001',
      'b2000005-0000-4000-8000-000000000002' => 'c1000006-0000-4000-8000-000000000001',
      'b2000005-0000-4000-8000-000000000003' => 'c1000008-0000-4000-8000-000000000001',
      'b2000006-0000-4000-8000-000000000001' => 'c1000010-0000-4000-8000-000000000001',
      'b2000006-0000-4000-8000-000000000002' => 'c1000012-0000-4000-8000-000000000001',
      'b2000006-0000-4000-8000-000000000003' => 'c1000014-0000-4000-8000-000000000001',
    ];

    foreach ($map as $nodeUuid => $mediaUuid) {
      try {
        $node = $this->entityRepository->loadEntityByUuid('node', $nodeUuid);
        $media = $this->entityRepository->loadEntityByUuid('media', $mediaUuid);
      }
      catch (\Exception) {
        continue;
      }

      if (!$node instanceof NodeInterface || !$media) {
        continue;
      }
      if (!$node->hasField('field_teaser_image')) {
        continue;
      }

      $changed = FALSE;
      foreach ($node->getTranslationLanguages() as $langcode => $_language) {
        $translation = $node->getTranslation($langcode);
        if (!$translation->get('field_teaser_image')->isEmpty()) {
          continue;
        }
        $translation->set('field_teaser_image', $media);
        $changed = TRUE;
      }

      if ($changed) {
        $node->setSyncing(TRUE);
        $node->save();
      }
    }
  }

  /**
   * Merges install homepage titles/paths into active config (new lang keys).
   */
  private function ensureHomepageConfig(): void {
    $installPath = $this->moduleHandler->getModule('ps_demo')->getPath() . '/config/install/ps_demo.homepage.yml';
    if (!is_readable($installPath)) {
      return;
    }

    $parsed = Yaml::decode((string) file_get_contents($installPath));
    if (!is_array($parsed) || !isset($parsed['node']) || !is_array($parsed['node'])) {
      return;
    }

    $editable = $this->configFactory->getEditable('ps_demo.homepage');
    foreach (['titles', 'path'] as $key) {
      $installValues = $parsed['node'][$key] ?? NULL;
      if (!is_array($installValues)) {
        continue;
      }
      $current = $editable->get('node.' . $key);
      if (!is_array($current)) {
        $current = [];
      }
      $editable->set('node.' . $key, array_replace($current, $installValues));
    }
    $editable->save();
  }

  /**
   * Imports partial CMI from src/config/demo/ (mega-menu, multilingual).
   */
  private function importDemoConfiguration(): void {
    $source = dirname(\Drupal::root()) . '/config/demo';
    if (!is_dir($source)) {
      $this->logger->warning('ps_demo: demo config directory not found at @path.', ['@path' => $source]);
      return;
    }

    $sync_storage = new FileStorage($source);
    $imported = 0;
    foreach ($sync_storage->listAll() as $name) {
      if ($name === 'README') {
        continue;
      }
      $data = $sync_storage->read($name);
      if (!is_array($data)) {
        continue;
      }
      $this->configFactory->getEditable($name)->setData($data)->save(TRUE);
      $imported++;
    }

    if ($imported > 0) {
      $this->logger->notice('ps_demo: imported @count demo configuration objects from @path.', [
        '@count' => $imported,
        '@path' => $source,
      ]);
    }
  }

  /**
   * Creates homepage path aliases for enabled languages (from ps_demo.homepage).
   */
  private function applyPathAliases(): void {
    if (!$this->entityTypeManager->hasDefinition('path_alias')) {
      return;
    }

    $homepage = $this->loadHomepageNode();
    if (!$homepage) {
      return;
    }

    $front_paths = $this->translationSync->homepageAliasesForEnabledLanguages($homepage);
    if ($front_paths === []) {
      return;
    }

    $system_path = '/node/' . $homepage->id();
    $storage = $this->entityTypeManager->getStorage('path_alias');

    foreach ($front_paths as $langcode => $alias) {
      $entity = NULL;
      $existing = $storage->loadByProperties([
        'path' => $system_path,
        'langcode' => $langcode,
      ]);
      if ($existing !== []) {
        $entity = reset($existing);
      }

      if (!$entity instanceof PathAliasInterface) {
        $entity = $storage->create(['langcode' => $langcode]);
      }

      $entity->set('path', $system_path);
      $entity->set('alias', '/' . ltrim($alias, '/'));
      $entity->set('status', TRUE);
      $entity->save();
    }
  }

  /**
   * Sets the site front page from the homepage node UUID.
   */
  private function applyFrontPage(): void {
    $homepage = $this->loadHomepageNode();
    if (!$homepage) {
      return;
    }

    $system_path = '/node/' . $homepage->id();
    $this->configFactory->getEditable('system.site')
      ->set('page.front', $system_path)
      ->save(TRUE);

    $this->logger->notice('ps_demo: front page set to @path.', ['@path' => $system_path]);
  }

  /**
   * Applies the 9-section homepage LB layout with default block configuration.
   */
  private function applyHomepageLayout(): void {
    if (!$this->moduleHandler->moduleExists('ps_homepage')) {
      return;
    }

    $homepage = $this->loadHomepageNode();
    if (!$homepage || !$homepage->hasField('layout_builder__layout')) {
      return;
    }

    \Drupal::service('ps_homepage.layout_field_ensurer')->ensurePageLayoutFieldTranslatable();

    $layoutBuilder = \Drupal::service('ps_homepage.default_layout_builder');
    $layoutPersister = \Drupal::service('ps_homepage.layout_persister');
    $layoutPersister->saveAllTranslationLayouts(
      $homepage,
      static fn (string $langcode): array => $layoutBuilder->buildSections($langcode),
    );

    $this->logger->notice('ps_demo: applied default 9-section homepage layout.');

    \Drupal::service('ps_homepage.section_library_installer')->install();
    \Drupal::service('ps_homepage.faq_config_refresher')->refreshFrontPage();
    if ($this->moduleHandler->moduleExists('ps_homepage')) {
      \Drupal::service('ps_homepage.market_study_config_refresher')->refreshFrontPage();
    }
  }

  /**
   * Installs optional news / market study configuration when missing.
   */
  private function ensureEditorialContentModels(): void {
    $installer = \Drupal::service('config.installer');

    if ($this->moduleHandler->moduleExists('ps_news')) {
      $path = \Drupal::service('extension.path.resolver')->getPath('module', 'ps_news');
      $installer->installOptionalConfig(new FileStorage($path . '/config/optional'));
    }

    if ($this->moduleHandler->moduleExists('ps_market_study')) {
      $path = \Drupal::service('extension.path.resolver')->getPath('module', 'ps_market_study');
      $installer->installOptionalConfig(new FileStorage($path . '/config/optional'));
    }
  }

  /**
   * Loads the homepage node declared in ps_demo.settings.
   */
  private function loadHomepageNode(): ?NodeInterface {
    $homepage_uuid = (string) ($this->configFactory->get('ps_demo.settings')->get('homepage_uuid') ?? '');
    if ($homepage_uuid === '') {
      return NULL;
    }

    try {
      $node = $this->entityRepository->loadEntityByUuid('node', $homepage_uuid);
    }
    catch (\Exception) {
      $node = NULL;
    }

    return $node instanceof NodeInterface ? $node : NULL;
  }

}
