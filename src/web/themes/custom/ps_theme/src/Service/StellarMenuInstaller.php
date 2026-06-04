<?php

declare(strict_types=1);

namespace Drupal\ps_theme\Service;

use Drupal\Component\Serialization\Yaml;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ExtensionPathResolver;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Menu\MenuLinkManagerInterface;
use Drupal\menu_link_content\Entity\MenuLinkContent;
use Drupal\menu_link_content\MenuLinkContentInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Installs Stellar default menus from YAML (FR + EN, idempotent).
 *
 * @deprecated in ps_theme:1.0.0 and is removed from ps_theme:2.0.0. Menu links
 *   are imported from ps_demo/content/menu_link_content/*.yml.
 */
final class StellarMenuInstaller {

  public function __construct(
    private readonly ExtensionPathResolver $extensionPathResolver,
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly LanguageManagerInterface $languageManager,
    private readonly MenuLinkManagerInterface $menuLinkManager,
  ) {}

  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get('extension.path.resolver'),
      $container->get('entity_type.manager'),
      $container->get('language_manager'),
      $container->get('plugin.manager.menu.link'),
    );
  }

  /**
   * Imports or updates menu links declared in config/menu/stellar_menus.yml.
   */
  public function install(): void {
    $path = $this->extensionPathResolver->getPath('theme', 'ps_theme') . '/config/menu/stellar_menus.yml';
    if (!is_readable($path)) {
      return;
    }

    /** @var array{menus?: array<string, list<array<string, mixed>>>} $data */
    $data = Yaml::decode((string) file_get_contents($path));
    foreach ($data['menus'] ?? [] as $menuName => $items) {
      $this->importMenuItems($menuName, $items);
    }

    $this->menuLinkManager->rebuild();
  }

  /**
   * @param list<array<string, mixed>> $items
   * @param string|null $parentPluginId
   */
  private function importMenuItems(string $menuName, array $items, ?string $parentPluginId = NULL): void {
    foreach ($items as $item) {
      $pluginId = $this->upsertLink($menuName, $item, $parentPluginId);
      if (!empty($item['children']) && is_array($item['children'])) {
        $this->importMenuItems($menuName, $item['children'], $pluginId);
      }
    }
  }

  /**
   * @param array<string, mixed> $item
   */
  private function upsertLink(string $menuName, array $item, ?string $parentPluginId): string {
    $uuid = (string) ($item['uuid'] ?? '');
    if ($uuid === '') {
      throw new \InvalidArgumentException('Menu item is missing a UUID.');
    }

    $storage = $this->entityTypeManager->getStorage('menu_link_content');
    $entities = $storage->loadByProperties(['uuid' => $uuid]);
    $entity = $entities ? reset($entities) : NULL;

    $defaultLangcode = $this->languageManager->getDefaultLanguage()->getId();
    $translations = $item['translations'] ?? [];
    if (!isset($translations[$defaultLangcode])) {
      $first = reset($translations);
      $translations[$defaultLangcode] = is_array($first) ? $first : ['title' => 'Link'];
    }

    $linkField = $this->buildLinkField($item);

    if (!$entity instanceof MenuLinkContentInterface) {
      $default = $translations[$defaultLangcode];
      $entity = MenuLinkContent::create([
        'uuid' => $uuid,
        'bundle' => 'menu_link_content',
        'langcode' => $defaultLangcode,
        'menu_name' => $menuName,
        'title' => (string) ($default['title'] ?? ''),
        'link' => $linkField,
        'weight' => (int) ($item['weight'] ?? 0),
        'enabled' => TRUE,
        'expanded' => !empty($item['children']),
        'parent' => $parentPluginId ?? '',
      ]);
    }
    else {
      $entity->set('link', $linkField);
      $entity->set('menu_name', $menuName);
      $entity->set('weight', (int) ($item['weight'] ?? 0));
      $entity->set('enabled', TRUE);
      $entity->set('expanded', !empty($item['children']));
      $entity->set('parent', $parentPluginId ?? '');
    }

    foreach ($translations as $langcode => $translation) {
      if (!$this->languageManager->getLanguage($langcode) || !is_array($translation)) {
        continue;
      }

      $target = $entity->hasTranslation($langcode)
        ? $entity->getTranslation($langcode)
        : $entity->addTranslation($langcode);

      $target->set('title', (string) ($translation['title'] ?? ''));
      $target->set('menu_name', $menuName);
      $target->set('weight', (int) ($item['weight'] ?? 0));
      $target->set('enabled', TRUE);
      $target->set('expanded', !empty($item['children']));
      $target->set('parent', $parentPluginId ?? '');
    }

    $entity->save();
    return 'menu_link_content:' . $entity->uuid();
  }

  /**
   * @param array<string, mixed> $item
   *
   * @return array{uri: string, options: array<string, mixed>}
   */
  private function buildLinkField(array $item): array {
    $link = $item['link'] ?? ['uri' => 'route:<nolink>'];
    if (!is_array($link)) {
      $link = ['uri' => (string) $link];
    }

    $options = $link['options'] ?? [];
    if (!is_array($options)) {
      $options = [];
    }

    if (!empty($item['icon']) && is_string($item['icon'])) {
      $options['attributes'] ??= [];
      $options['attributes']['class'] ??= [];
      if (!is_array($options['attributes']['class'])) {
        $options['attributes']['class'] = [$options['attributes']['class']];
      }
      $options['attributes']['class'][] = 'ps-footer-social__link--icon-' . $item['icon'];
    }

    $linkOptions = $link['options'] ?? [];
    if (is_array($linkOptions)) {
      if (!empty($linkOptions['icon']) && is_array($linkOptions['icon'])) {
        $options['icon'] = $linkOptions['icon'];
      }
      if (!empty($linkOptions['icon_display'])) {
        $options['icon_display'] = $linkOptions['icon_display'];
      }
    }

    return [
      'uri' => (string) ($link['uri'] ?? 'route:<nolink>'),
      'options' => $options,
    ];
  }

}
