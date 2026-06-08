<?php

declare(strict_types=1);

namespace Drupal\ps_search\Service;

use Drupal\Core\Config\StorageInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\language\Config\LanguageConfigFactoryOverrideInterface;

/**
 * Resolves internal and public (translated) search page path slugs.
 *
 * The Views route uses the default slug (find-property). Public URLs per
 * language are configured in ps_search.seo_url_mappings (config translation).
 */
final class SearchPathResolver {

  /**
   * Legacy slug kept for 301 redirects from pre-migration bookmarks.
   */
  private const LEGACY_SLUG = 'recherche';

  /**
   * Per-language slug cache keyed by langcode.
   *
   * @var array<string, string>
   */
  private array $slugByLang = [];

  /**
   * All known slugs (all languages + legacy).
   *
   * @var string[]|null
   */
  private ?array $allSlugs = NULL;

  /**
   * Cached machine route slug from base config storage (never translated).
   */
  private ?string $machineSlug = NULL;

  public function __construct(
    private readonly LanguageManagerInterface $languageManager,
    private readonly LanguageConfigFactoryOverrideInterface $langConfigOverride,
    private readonly StorageInterface $configStorage,
  ) {}

  /**
   * Internal Drupal route path (leading slash, no language prefix).
   *
   * Always the base config slug (machine route), not a translation.
   */
  public function getInternalPath(): string {
    return '/' . $this->getMachineSearchPathSlug();
  }

  /**
   * Public slug for a language (no leading slash).
   */
  public function getSlugForLang(string $langcode): string {
    if (isset($this->slugByLang[$langcode])) {
      return $this->slugByLang[$langcode];
    }

    $langConfig = $this->langConfigOverride->getOverride($langcode, 'ps_search.seo_url_mappings');
    $slug = (string) ($langConfig->get('search_path') ?? $this->getMachineSearchPathSlug());
    $slug = strtolower(trim($slug, '/'));
    $this->slugByLang[$langcode] = $slug !== '' ? $slug : 'find-property';
    return $this->slugByLang[$langcode];
  }

  /**
   * Public path for the current URL language (leading slash).
   */
  public function getPublicPath(?string $langcode = NULL): string {
    $langcode ??= $this->languageManager->getCurrentLanguage(LanguageInterface::TYPE_URL)->getId();
    return '/' . $this->getSlugForLang($langcode);
  }

  /**
   * Whether a path segment equals a known search page slug (any language).
   */
  public function isSearchPathSegment(string $segment): bool {
    $segment = strtolower(trim($segment, '/'));
    if ($segment === '') {
      return FALSE;
    }
    return in_array($segment, $this->getAllSlugs(), TRUE);
  }

  /**
   * Whether the full path (no language prefix) is exactly a search page path.
   */
  public function isSearchPath(string $path): bool {
    $stripped = trim($path, '/');
    if ($stripped === '') {
      return FALSE;
    }
    $segments = explode('/', $stripped);
    return count($segments) === 1 && $this->isSearchPathSegment($segments[0]);
  }

  /**
   * Legacy slug for 301 redirects.
   */
  public function getLegacySlug(): string {
    return self::LEGACY_SLUG;
  }

  /**
   * All configured slugs across languages plus legacy.
   *
   * @return string[]
   */
  public function getAllSlugs(): array {
    if ($this->allSlugs !== NULL) {
      return $this->allSlugs;
    }

    $slugs = [self::LEGACY_SLUG];
    foreach ($this->languageManager->getLanguages() as $langcode => $_lang) {
      $slugs[] = $this->getSlugForLang($langcode);
    }

    $this->allSlugs = array_values(array_unique(array_filter($slugs)));
    return $this->allSlugs;
  }

  /**
   * Machine route slug from base config (Views path), ignoring translations.
   */
  private function getMachineSearchPathSlug(): string {
    if ($this->machineSlug !== NULL) {
      return $this->machineSlug;
    }

    $data = $this->configStorage->read('ps_search.seo_url_mappings') ?: [];
    $slug = strtolower(trim((string) ($data['search_path'] ?? 'find-property'), '/'));
    $this->machineSlug = $slug !== '' ? $slug : 'find-property';
    return $this->machineSlug;
  }

}
