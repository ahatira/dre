<?php

declare(strict_types=1);

namespace Drupal\ps_theme\Service;

use Drupal\Component\Serialization\Yaml;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ExtensionPathResolver;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Entity\Entity\EntityViewMode;
use Drupal\layout_builder\Entity\LayoutBuilderEntityViewDisplay;
use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;
use Drupal\path_alias\PathAliasInterface;

/**
 * Creates the Stellar homepage node with a default Layout Builder layout.
 *
 * @deprecated in ps_theme:1.0.0 and is removed from ps_theme:2.0.0. The
 *   homepage node is imported from ps_demo/content/node/*.yml via default_content.
 */
final class HomepageInstaller {

  private const NODE_UUID = 'b2000001-0000-4000-8000-000000000001';

  public function __construct(
    private readonly ExtensionPathResolver $extensionPathResolver,
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly LanguageManagerInterface $languageManager,
    private readonly ConfigFactoryInterface $configFactory,
  ) {}

  public static function create(\Symfony\Component\DependencyInjection\ContainerInterface $container): self {
    return new self(
      $container->get('extension.path.resolver'),
      $container->get('entity_type.manager'),
      $container->get('language_manager'),
      $container->get('config.factory'),
    );
  }

  public function install(): void {
    $this->ensureHomepageModule();
    if (!$this->entityTypeManager->getStorage('node_type')->load('page')) {
      return;
    }

    /** @var array<string, mixed> $data */
    $data = $this->homepageData();
    if ($data === []) {
      return;
    }

    $node = $this->upsertHomepageNode($data);
    $this->prepareLayoutBuilder();
    $this->applyLayout($node);
    $this->setFrontPage($node);
    $this->ensurePathAliases($node, $data);
  }

  /**
   * Enables Layout Builder on page.full (required before ps_demo node import).
   */
  public function prepareLayoutBuilder(): void {
    $this->ensureFullViewMode();
    $this->ensureLayoutBuilderField();
  }

  private function ensureHomepageModule(): void {
    if (\Drupal::moduleHandler()->moduleExists('ps_homepage')) {
      return;
    }
    \Drupal::service('module_installer')->install(['ps_homepage'], TRUE);
  }

  /**
   * @return array<string, mixed>
   */
  private function homepageData(): array {
    if (\Drupal::moduleHandler()->moduleExists('ps_demo')) {
      $path = \Drupal::service('extension.list.module')->getPath('ps_demo') . '/config/install/ps_demo.homepage.yml';
      if (is_readable($path)) {
        /** @var array<string, mixed> $data */
        $data = Yaml::decode((string) file_get_contents($path));
        return $data;
      }
    }

    return [];
  }

  /**
   * @param array<string, mixed> $data
   */
  private function upsertHomepageNode(array $data): NodeInterface {
    $storage = $this->entityTypeManager->getStorage('node');
    $existing = $storage->loadByProperties(['uuid' => self::NODE_UUID]);
    $node = $existing ? reset($existing) : NULL;

    $defaultLangcode = $this->languageManager->getDefaultLanguage()->getId();
    $titles = $data['node']['titles'] ?? [];

    if (!$node instanceof NodeInterface) {
      $node = Node::create([
        'uuid' => self::NODE_UUID,
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

    // Remove legacy misnamed view mode shipped in an earlier optional config.
    $legacy = $viewModeStorage->load('node.page.full');
    if ($legacy) {
      $legacy->delete();
    }
  }

  private function ensureLayoutBuilderField(): void {
    $fieldDefinitions = \Drupal::service('entity_field.manager')->getFieldDefinitions('node', 'page');
    if (isset($fieldDefinitions['layout_builder__layout'])) {
      return;
    }

    $display = LayoutBuilderEntityViewDisplay::load('node.page.full');
    if (!$display instanceof LayoutBuilderEntityViewDisplay) {
      return;
    }

    $display->enableLayoutBuilder()->setOverridable(FALSE)->save();
    $display->enableLayoutBuilder()->setOverridable(TRUE)->save();
    \Drupal::service('entity_field.manager')->clearCachedFieldDefinitions();
  }

  private function applyLayout(NodeInterface $node): void {
    if (!$node->hasField('layout_builder__layout')) {
      return;
    }

    /** @var \Drupal\ps_homepage\Service\HomepageDefaultLayoutBuilder $layoutBuilder */
    $layoutBuilder = \Drupal::service('ps_homepage.default_layout_builder');
    $node->get('layout_builder__layout')->setValue($layoutBuilder->buildSections());
    $node->save();
  }

  private function setFrontPage(NodeInterface $node): void {
    $this->configFactory->getEditable('system.site')
      ->set('page.front', '/node/' . $node->id())
      ->save(TRUE);
  }

  /**
   * @param array<string, mixed> $data
   */
  private function ensurePathAliases(NodeInterface $node, array $data): void {
    if (!$this->entityTypeManager->hasDefinition('path_alias')) {
      return;
    }

    $paths = $data['node']['path'] ?? [];
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

}
