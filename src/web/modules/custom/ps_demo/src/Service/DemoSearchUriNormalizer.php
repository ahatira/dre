<?php

declare(strict_types=1);

namespace Drupal\ps_demo\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\layout_builder\SectionListInterface;
use Drupal\node\NodeInterface;
use Drupal\ps_search\Contract\SearchPathResolverInterface;

/**
 * Localizes demo menu and homepage links that still use the EN search slug.
 */
final class DemoSearchUriNormalizer {

  private const MACHINE_SEARCH_PREFIX = '/find-property';

  public function __construct(
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly SearchPathResolverInterface $searchPathResolver,
  ) {}

  /**
   * Rewrites internal:/find-property* URIs and stored /find-property paths.
   */
  public function normalize(): void {
    $this->normalizeMenuLinks();
    $this->normalizeHomepageLayout();
    \Drupal::service('plugin.manager.menu.link')->rebuild();
  }

  private function normalizeMenuLinks(): void {
    $storage = $this->entityTypeManager->getStorage('menu_link_content');
  /** @var \Drupal\menu_link_content\MenuLinkContentInterface[] $links */
    $links = $storage->loadMultiple();
    foreach ($links as $link) {
      foreach ($link->getTranslationLanguages() as $langcode => $_language) {
        $translation = $link->getTranslation($langcode);
        if (!$translation->hasField('link') || $translation->get('link')->isEmpty()) {
          continue;
        }
        $uri = (string) $translation->get('link')->uri;
        $localized = $this->localizeInternalSearchUri($uri, $langcode);
        if ($localized !== $uri) {
          $translation->set('link', ['uri' => $localized]);
          $link->save();
        }
      }
    }
  }

  private function normalizeHomepageLayout(): void {
    $homepage_uuid = (string) (\Drupal::config('ps_demo.settings')->get('homepage_uuid') ?? '');
    if ($homepage_uuid === '') {
      return;
    }

    try {
      $node = \Drupal::service('entity.repository')->loadEntityByUuid('node', $homepage_uuid);
    }
    catch (\Exception) {
      return;
    }

    if (!$node instanceof NodeInterface || !$node->hasField('layout_builder__layout')) {
      return;
    }

    $storage = $this->entityTypeManager->getStorage('node');
    $editable = $storage->load($node->id());
    if (!$editable instanceof NodeInterface) {
      return;
    }

    $changed = FALSE;
    foreach ($editable->getTranslationLanguages() as $langcode => $_language) {
      $translation = $editable->getTranslation($langcode);
      if (!$translation->hasField('layout_builder__layout')) {
        continue;
      }

      $field = $translation->get('layout_builder__layout');
      if (!$field instanceof SectionListInterface) {
        continue;
      }

      $updated = FALSE;
      foreach ($field->getSections() as $section) {
        foreach ($section->getComponents() as $component) {
          $config = $component->get('configuration');
          if (!is_array($config)) {
            continue;
          }
          $patched = $this->localizeBlockConfiguration($config, $langcode);
          if ($patched !== $config) {
            $component->setConfiguration($patched);
            $updated = TRUE;
          }
        }
      }

      if ($updated) {
        $changed = TRUE;
      }
    }

    if ($changed) {
      $editable->save();
    }
  }

  /**
   * @param array<string, mixed> $config
   *
   * @return array<string, mixed>
   */
  private function localizeBlockConfiguration(array $config, string $langcode): array {
    foreach (['promo_cta_url', 'cta_url', 'see_more_url'] as $key) {
      if (!isset($config[$key]) || !is_string($config[$key])) {
        continue;
      }
      $localized = $this->localizePublicSearchPath($config[$key], $langcode);
      if ($localized !== $config[$key]) {
        $config[$key] = $localized;
      }
    }

    if (isset($config['items']) && is_array($config['items'])) {
      foreach ($config['items'] as $index => $item) {
        if (!is_array($item) || !isset($item['url']) || !is_string($item['url'])) {
          continue;
        }
        $localized = $this->localizePublicSearchPath($item['url'], $langcode);
        if ($localized !== $item['url']) {
          $config['items'][$index]['url'] = $localized;
        }
      }
    }

    return $config;
  }

  private function localizeInternalSearchUri(string $uri, string $langcode): string {
    if (!str_starts_with($uri, 'internal:' . self::MACHINE_SEARCH_PREFIX)) {
      return $uri;
    }

    $suffix = substr($uri, strlen('internal:' . self::MACHINE_SEARCH_PREFIX));
    return 'internal:' . $this->searchPathResolver->getPublicPath($langcode) . $suffix;
  }

  private function localizePublicSearchPath(string $path, string $langcode): string {
    return $this->searchPathResolver->resolveStoredPublicSearchPath($path, $langcode);
  }

}
