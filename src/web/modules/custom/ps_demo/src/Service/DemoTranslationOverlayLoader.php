<?php

declare(strict_types=1);

namespace Drupal\ps_demo\Service;

use Drupal\Component\Serialization\Yaml;
use Drupal\Core\Extension\ExtensionPathResolver;

/**
 * Loads per-language demo content overlays (FAQ, menu links, etc.).
 */
final class DemoTranslationOverlayLoader {

  /**
   * Cached overlay maps keyed by YAML basename.
   *
   * @var array<string, array<string, array<string, array<string, mixed>>>|null
   */
  private ?array $cache = NULL;

  public function __construct(
    private readonly ExtensionPathResolver $extensionPathResolver,
  ) {}

  /**
   * Returns FAQ field overlays for a node UUID and language.
   *
   * @return array{title?: string, field_question?: string, field_answer?: array{value: string, format: string}}
   *   Overlay field values when defined.
   */
  public function faqItemOverlay(string $uuid, string $langcode): array {
    $entry = $this->overlayEntry('faq_item_translations', $uuid, $langcode);
    return is_array($entry) ? $entry : [];
  }

  /**
   * Returns menu link title overlay for a UUID and language.
   */
  public function menuLinkTitle(string $uuid, string $langcode): ?string {
    $entry = $this->overlayEntry('menu_link_translations', $uuid, $langcode);
    if (!is_array($entry) || !isset($entry['title']) || !is_string($entry['title'])) {
      return NULL;
    }
    return $entry['title'];
  }

  /**
   * Returns article field overlays for a node UUID and language.
   *
   * @return array{title?: string, body?: array{value: string, format: string}}
   */
  public function articleOverlay(string $uuid, string $langcode): array {
    $entry = $this->overlayEntry('article_translations', $uuid, $langcode);
    return is_array($entry) ? $entry : [];
  }

  /**
   * Returns market study field overlays for a node UUID and language.
   *
   * @return array{title?: string, body?: array{value: string, format: string}}
   */
  public function marketStudyOverlay(string $uuid, string $langcode): array {
    $entry = $this->overlayEntry('market_study_translations', $uuid, $langcode);
    return is_array($entry) ? $entry : [];
  }

  /**
   * Returns taxonomy term name overlay for a UUID and language.
   */
  public function taxonomyTermName(string $uuid, string $langcode): ?string {
    $entry = $this->overlayEntry('taxonomy_term_translations', $uuid, $langcode);
    if (!is_array($entry) || !isset($entry['name']) || !is_string($entry['name'])) {
      return NULL;
    }
    return $entry['name'];
  }

  /**
   * Returns overlay data for a basename, UUID and language.
   *
   * @return array<string, mixed>|null
   *   Overlay values when defined.
   */
  private function overlayEntry(string $basename, string $uuid, string $langcode): ?array {
    $all = $this->loadAll();
    $entry = $all[$basename][$uuid][$langcode] ?? NULL;
    return is_array($entry) ? $entry : NULL;
  }

  /**
   * Loads all overlay YAML files into memory.
   *
   * @return array<string, array<string, array<string, array<string, mixed>>>>
   *   Basename-keyed overlay maps.
   */
  private function loadAll(): array {
    if ($this->cache !== NULL) {
      return $this->cache;
    }

    $this->cache = [];
    $modulePath = $this->extensionPathResolver->getPath('module', 'ps_demo');
    foreach (['faq_item_translations', 'menu_link_translations', 'article_translations', 'market_study_translations', 'taxonomy_term_translations'] as $basename) {
      $file = $modulePath . '/data/' . $basename . '.yml';
      if (!is_readable($file)) {
        $this->cache[$basename] = [];
        continue;
      }
      $parsed = Yaml::decode((string) file_get_contents($file));
      $this->cache[$basename] = is_array($parsed) ? $parsed : [];
    }

    return $this->cache;
  }

}
