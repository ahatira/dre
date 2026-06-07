<?php

declare(strict_types=1);

namespace Drupal\ps_demo\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\FileStorage;
use Drupal\Core\DefaultContent\Existing;
use Drupal\Core\DefaultContent\Finder;
use Drupal\Core\DefaultContent\Importer;
use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Extension\ModuleExtensionList;
use Drupal\node\NodeInterface;
use Drupal\path_alias\PathAliasInterface;
use Drupal\ps_theme\Service\HomepageInstaller;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Imports demo content and applies post-import site configuration.
 */
final class DemoInstaller {

  /**
   * Stable path_alias UUIDs synced from ps_demo.settings front_paths.
   *
   * @var array<string, string>
   */
  private const PATH_ALIAS_UUIDS = [
    'en' => 'b3000001-0000-4000-8000-000000000001',
    'fr' => 'b3000001-0000-4000-8000-000000000002',
  ];

  public function __construct(
    private readonly ModuleExtensionList $moduleExtensionList,
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly EntityRepositoryInterface $entityRepository,
    private readonly ConfigFactoryInterface $configFactory,
    private readonly ModuleHandlerInterface $moduleHandler,
    private readonly Importer $defaultContentImporter,
    private readonly LoggerInterface $logger,
  ) {}

  /**
   * Creates a DemoInstaller instance from the service container.
   */
  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get('extension.list.module'),
      $container->get('entity_type.manager'),
      $container->get('entity.repository'),
      $container->get('config.factory'),
      $container->get('module_handler'),
      $container->get(Importer::class),
      $container->get('logger.channel.ps_demo'),
    );
  }

  /**
   * Validates runtime before module installation completes.
   */
  public function prepareInstall(): void {
    $this->assertRuntimeReady();
  }

  /**
   * Imports content and applies demo configuration after dependencies run.
   */
  public function finalizeInstall(): void {
    if (\Drupal::moduleHandler()->moduleExists('ps_homepage')) {
      HomepageInstaller::create(\Drupal::getContainer())->prepareLayoutBuilder();
    }
    $this->importContent();
    (new DemoMenuNormalizer($this->entityTypeManager))->normalize();
    $this->importDemoConfiguration();
    $this->applyPathAliases();
    $this->applyFrontPage();
  }

  /**
   * Ensures required modules and the front theme are available.
   */
  private function assertRuntimeReady(): void {
    foreach (['ps_homepage', 'ps_block', 'social_media_links'] as $module) {
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
   * Imports YAML entities from the module export/content/ directory.
   */
  private function importContent(): void {
    $path = $this->moduleExtensionList->getPath('ps_demo') . '/export/content';
    if (!is_dir($path)) {
      return;
    }

    $finder = new Finder($path);
    if ($finder->data === []) {
      return;
    }

    $this->defaultContentImporter->importContent($finder, Existing::Skip);
    \Drupal::service('plugin.manager.menu.link')->rebuild();
  }

  /**
   * Imports partial CMI from src/config/demo/ (mega-menu, langues, Follow us…).
   *
   * Mirrors `drush config:import --partial` without full-site validation.
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
   * Points homepage URL aliases to the imported node (Config-First).
   */
  private function applyPathAliases(): void {
    if (!$this->entityTypeManager->hasDefinition('path_alias')) {
      return;
    }

    $settings = $this->configFactory->get('ps_demo.settings');
    $front_paths = $settings->get('front_paths');
    if (!is_array($front_paths) || $front_paths === []) {
      return;
    }

    $homepage = $this->loadHomepageNode();
    if (!$homepage) {
      return;
    }

    $system_path = '/node/' . $homepage->id();
    $storage = $this->entityTypeManager->getStorage('path_alias');

    foreach ($front_paths as $langcode => $alias) {
      if (!is_string($langcode) || !is_string($alias)) {
        continue;
      }

      $uuid = self::PATH_ALIAS_UUIDS[$langcode] ?? NULL;
      $entity = NULL;
      if ($uuid) {
        try {
          $entity = $this->entityRepository->loadEntityByUuid('path_alias', $uuid);
        }
        catch (\Exception) {
          $entity = NULL;
        }
      }

      if (!$entity instanceof PathAliasInterface) {
        $entity = $storage->create([
          'uuid' => $uuid,
          'langcode' => $langcode,
        ]);
      }

      $entity->set('path', $system_path);
      $entity->set('alias', '/' . ltrim($alias, '/'));
      $entity->set('status', TRUE);
      $entity->save();
    }
  }

  /**
   * Sets the site front page from the homepage node UUID (Config-First).
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
