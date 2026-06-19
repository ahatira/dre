<?php

declare(strict_types=1);

namespace Drupal\ps_homepage\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\Entity\EntityViewMode;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\layout_builder\Entity\LayoutBuilderEntityViewDisplay;
use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;
use Drupal\path_alias\PathAliasInterface;
use Psr\Log\LoggerInterface;

/**
 * Creates the shell homepage node (nid 1) and sets the site front page.
 */
final class HomepageShellInstaller {

  public const HOMEPAGE_NODE_ID = 1;

  public function __construct(
    private readonly ConfigFactoryInterface $configFactory,
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly LanguageManagerInterface $languageManager,
    private readonly EntityFieldManagerInterface $entityFieldManager,
    private readonly HomepageLayoutFieldEnsurer $layoutFieldEnsurer,
    private readonly HomepageDefaultLayoutBuilder $defaultLayoutBuilder,
    private readonly HomepageLayoutPersister $layoutPersister,
    private readonly HomepageSectionLibraryInstaller $sectionLibraryInstaller,
    private readonly HomepageFaqConfigRefresher $faqConfigRefresher,
    private readonly HomepageMarketStudyConfigRefresher $marketStudyConfigRefresher,
    private readonly LoggerInterface $logger,
  ) {}

  /**
   * Installs homepage node/1 with default LB layout and front page config.
   */
  public function install(): void {
    $this->installShell(includeSectionLibrary: TRUE);
  }

  /**
   * Creates homepage node and LB layout after a full config import.
   *
   * Skips Section Library and content refreshers (demo / post-import steps).
   */
  public function installFromConfiguration(): void {
    $this->installShell(includeSectionLibrary: FALSE);
  }

  /**
   * Shared shell: node/1, LB sections, front page, path aliases.
   */
  private function installShell(bool $includeSectionLibrary): void {
    if (!$this->entityTypeManager->getStorage('node_type')->load('page')) {
      throw new \RuntimeException('Homepage shell install requires the page content type.');
    }

    $this->prepareLayoutBuilder();
    $node = $this->upsertHomepageNode();
    $this->assertHomepageNodeId($node);
    $this->applyLayout($node);
    $this->setFrontPage();
    $this->ensurePathAliases($node);
    if ($includeSectionLibrary) {
      $this->sectionLibraryInstaller->install();
      $this->faqConfigRefresher->refreshFrontPage();
      $this->marketStudyConfigRefresher->refreshFrontPage();
    }

    $this->logger->notice('ps_homepage: shell homepage installed at /node/{nid}.', [
      'nid' => $node->id(),
    ]);
  }

  /**
   * Returns the configured homepage node UUID.
   */
  public static function homepageUuid(ConfigFactoryInterface $configFactory): string {
    $uuid = (string) ($configFactory->get('ps_homepage.settings')->get('homepage_uuid') ?? '');
    if ($uuid !== '') {
      return $uuid;
    }

    return (string) ($configFactory->get('ps_demo.settings')->get('homepage_uuid') ?? 'b2000001-0000-4000-8000-000000000001');
  }

  /**
   * Ensures Layout Builder view mode and layout field exist.
   */
  private function prepareLayoutBuilder(): void {
    $this->ensureFullViewMode();
    $this->ensureLayoutBuilderField();
    $this->layoutFieldEnsurer->ensurePageLayoutFieldTranslatable();
  }

  /**
   * Creates or updates the homepage node with translations.
   */
  private function upsertHomepageNode(): NodeInterface {
    $uuid = self::homepageUuid($this->configFactory);
    $homepageConfig = $this->configFactory->get('ps_homepage.homepage');
    $titles = $homepageConfig->get('node.titles') ?? [];
    if (!is_array($titles)) {
      $titles = [];
    }

    $storage = $this->entityTypeManager->getStorage('node');
    $existing = $storage->loadByProperties(['uuid' => $uuid]);
    $node = $existing ? reset($existing) : NULL;

    $defaultLangcode = $this->languageManager->getDefaultLanguage()->getId();

    if (!$node instanceof NodeInterface) {
      $this->assertNoBlockingNodes();
      $node = Node::create([
        'uuid' => $uuid,
        'type' => 'page',
        'langcode' => $defaultLangcode,
        'title' => (string) ($titles[$defaultLangcode] ?? 'Homepage'),
        'status' => NodeInterface::PUBLISHED,
        'promote' => FALSE,
      ]);
    }

    foreach ($titles as $langcode => $title) {
      if (!$this->languageManager->getLanguage($langcode)) {
        continue;
      }
      $translation = $node->hasTranslation($langcode)
        ? $node->getTranslation($langcode)
        : $node->addTranslation($langcode);
      $translation->setTitle((string) $title);
      $translation->setPublished(TRUE);
    }

    $node->save();

    return $node;
  }

  /**
   * Ensures no other nodes exist before creating node/1.
   */
  private function assertNoBlockingNodes(): void {
    $count = (int) $this->entityTypeManager->getStorage('node')->getQuery()
      ->accessCheck(FALSE)
      ->count()
      ->execute();
    if ($count > 0) {
      throw new \RuntimeException(
        'Homepage shell install requires an empty node table so the homepage can be node/1.',
      );
    }
  }

  /**
   * Verifies the homepage was saved as node/1.
   */
  private function assertHomepageNodeId(NodeInterface $node): void {
    if ((int) $node->id() !== self::HOMEPAGE_NODE_ID) {
      throw new \RuntimeException(sprintf(
        'Homepage must be node %d, got node %d.',
        self::HOMEPAGE_NODE_ID,
        (int) $node->id(),
      ));
    }
  }

  /**
   * Applies the default 9-section Layout Builder layout.
   */
  private function applyLayout(NodeInterface $node): void {
    if (!$node->hasField('layout_builder__layout')) {
      throw new \RuntimeException('Homepage node is missing layout_builder__layout field.');
    }

    $this->layoutPersister->saveAllTranslationLayouts(
      $node,
      fn (string $langcode): array => $this->defaultLayoutBuilder->buildSections($langcode),
    );
  }

  /**
   * Sets system.site page.front to /node/1.
   */
  private function setFrontPage(): void {
    $path = '/node/' . self::HOMEPAGE_NODE_ID;
    $this->configFactory->getEditable('system.site')
      ->set('page.front', $path)
      ->save(TRUE);
  }

  /**
   * Creates path aliases for the homepage in enabled languages.
   */
  private function ensurePathAliases(NodeInterface $node): void {
    if (!$this->entityTypeManager->hasDefinition('path_alias')) {
      return;
    }

    $paths = $this->configFactory->get('ps_homepage.homepage')->get('node.path') ?? [];
    if (!is_array($paths)) {
      return;
    }

    $aliasStorage = $this->entityTypeManager->getStorage('path_alias');
    $systemPath = '/node/' . $node->id();

    foreach ($paths as $langcode => $alias) {
      if (!$this->languageManager->getLanguage($langcode)) {
        continue;
      }

      $aliasPath = '/' . ltrim((string) $alias, '/');
      $existing = $aliasStorage->loadByProperties([
        'path' => $systemPath,
        'langcode' => $langcode,
      ]);

      $entity = $existing ? reset($existing) : NULL;
      if (!$entity instanceof PathAliasInterface) {
        $entity = $aliasStorage->create([
          'path' => $systemPath,
          'alias' => $aliasPath,
          'langcode' => $langcode,
        ]);
      }
      else {
        $entity->set('alias', $aliasPath);
      }
      $entity->save();
    }
  }

  /**
   * Ensures node.full view mode exists for Layout Builder.
   */
  private function ensureFullViewMode(): void {
    $viewModeStorage = $this->entityTypeManager->getStorage('entity_view_mode');
    if (!$viewModeStorage->load('node.full')) {
      EntityViewMode::create([
        'id' => 'node.full',
        'targetEntityType' => 'node',
        'label' => 'Full',
        'description' => 'Layout Builder page display (homepage).',
        'status' => TRUE,
      ])->save();
    }

    $legacy = $viewModeStorage->load('node.page.full');
    if ($legacy) {
      $legacy->delete();
    }
  }

  /**
   * Enables Layout Builder on node.page.full when the layout field is missing.
   */
  private function ensureLayoutBuilderField(): void {
    $fieldDefinitions = $this->entityFieldManager->getFieldDefinitions('node', 'page');
    if (isset($fieldDefinitions['layout_builder__layout'])) {
      return;
    }

    $display = LayoutBuilderEntityViewDisplay::load('node.page.full');
    if (!$display instanceof LayoutBuilderEntityViewDisplay) {
      throw new \RuntimeException('Missing node.page.full display for homepage Layout Builder.');
    }

    $display->enableLayoutBuilder()->setOverridable(FALSE)->save();
    $display->enableLayoutBuilder()->setOverridable(TRUE)->save();
    $this->entityFieldManager->clearCachedFieldDefinitions();
  }

}
