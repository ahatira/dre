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
final class SearchPathResolver implements \Drupal\ps_search\Contract\SearchPathResolverInterface {

  /**
   * Legacy slug kept for 301 redirects from pre-migration bookmarks.
   */
  private const LEGACY_SLUG = 'recherche';

  /**
   * Per-language asset slug alias cache keyed by langcode.
   *
   * @var array<string, array<string, string>>
   */
  private array $assetAliasesByLang = [];

  /**
   * Per-language SEO slug lookup cache keyed by langcode.
   *
   * @var array<string, array{op_to_val: array<string, string>, val_to_op: array<string, string>, asset_to_val: array<string, string>, val_to_asset: array<string, string>}>
   */
  private array $seoMappingsByLang = [];

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
   * Parses SEO path segments into operation/asset codes and locality tail segments.
   *
   * Supports operation+asset, operation-only, and asset-only (Indifférent) patterns.
   *
   * @param string[] $segments
   *   Path segments without language prefix.
   *
   * @return array{operation_type: ?string, asset_type: ?string, locality_segments: list<string>}
   *   Parsed facet codes and remaining locality path segments.
   */
  public function resolveFacetsFromPathSegments(string $langcode, array $segments): array {
    $empty = [
      'operation_type' => NULL,
      'asset_type' => NULL,
      'locality_segments' => [],
    ];
    if ($segments === []) {
      return $empty;
    }

    $m = $this->getSeoSlugMappings($langcode);
    $first = strtolower($segments[0]);

    if (isset($m['op_to_val'][$first])) {
      $result = $empty;
      $result['operation_type'] = $m['op_to_val'][$first];
      $rest = array_slice($segments, 1);
      if ($rest !== [] && isset($m['asset_to_val'][strtolower((string) $rest[0])])) {
        $result['asset_type'] = $m['asset_to_val'][strtolower((string) $rest[0])];
        $result['locality_segments'] = array_values(array_slice($rest, 1));
      }
      else {
        $result['locality_segments'] = $rest;
      }
      return $result;
    }

    if (isset($m['asset_to_val'][$first])) {
      return [
        'operation_type' => NULL,
        'asset_type' => $m['asset_to_val'][$first],
        'locality_segments' => array_values(array_slice($segments, 1)),
      ];
    }

    return $empty;
  }

  /**
   * Builds the SEO path prefix for active operation and/or asset filters.
   *
   * Asset-only paths omit the operation slug (Indifférent / flexible transaction).
   */
  public function buildSeoFilterPathPrefix(string $langcode, ?string $operationType, ?string $assetType): ?string {
    $m = $this->getSeoSlugMappings($langcode);
    $operationType = is_string($operationType) && $operationType !== '' ? strtoupper($operationType) : NULL;
    $assetType = is_string($assetType) && $assetType !== '' ? strtoupper($assetType) : NULL;

    if ($operationType !== NULL) {
      $opSlug = $m['val_to_op'][$operationType] ?? NULL;
      if ($opSlug === NULL) {
        return NULL;
      }
      $path = '/' . $opSlug;
      if ($assetType !== NULL) {
        $assetSlug = $m['val_to_asset'][$assetType] ?? NULL;
        if ($assetSlug !== NULL) {
          $path .= '/' . $assetSlug;
        }
      }
      return $path;
    }

    if ($assetType !== NULL) {
      $assetSlug = $m['val_to_asset'][$assetType] ?? NULL;
      return $assetSlug !== NULL ? '/' . $assetSlug : NULL;
    }

    return NULL;
  }

  /**
   * Whether a path segment is a configured operation slug for a language.
   */
  public function isOperationSlug(string $langcode, string $slug): bool {
    $m = $this->getSeoSlugMappings($langcode);
    return isset($m['op_to_val'][strtolower($slug)]);
  }

  /**
   * Whether a path segment is a configured asset slug (or alias) for a language.
   */
  public function isAssetSlug(string $langcode, string $slug): bool {
    $slug = strtolower($slug);
    $m = $this->getSeoSlugMappings($langcode);
    if (isset($m['asset_to_val'][$slug])) {
      return TRUE;
    }
    $aliases = $this->getAssetSlugAliases($langcode);
    return isset($aliases[$slug]);
  }

  /**
   * Resolves a legacy asset slug alias to its canonical slug.
   */
  public function resolveAssetSlugAlias(string $langcode, string $slug): string {
    $slug = strtolower($slug);
    $aliases = $this->getAssetSlugAliases($langcode);
    return $aliases[$slug] ?? $slug;
  }

  /**
   * Legacy slug for 301 redirects.
   */
  public function getLegacySlug(): string {
    return self::LEGACY_SLUG;
  }

  /**
   * Legacy asset slugs that should 301 to the canonical slug for a language.
   *
   * @return array<string, string>
   *   Map of legacy slug => canonical slug (both lowercase, no slashes).
   */
  public function getAssetSlugAliases(string $langcode): array {
    if (isset($this->assetAliasesByLang[$langcode])) {
      return $this->assetAliasesByLang[$langcode];
    }

    $base = $this->configStorage->read('ps_search.seo_url_mappings') ?: [];
    $override = $this->langConfigOverride->getOverride($langcode, 'ps_search.seo_url_mappings');
    $aliases = array_merge(
      $base['asset_slug_aliases'] ?? [],
      $override->get('asset_slug_aliases') ?? [],
    );

    $normalized = [];
    foreach ($aliases as $legacySlug => $canonicalSlug) {
      $legacy = strtolower(trim((string) $legacySlug));
      $canonical = strtolower(trim((string) $canonicalSlug));
      if ($legacy !== '' && $canonical !== '') {
        $normalized[$legacy] = $canonical;
      }
    }

    $this->assetAliasesByLang[$langcode] = $normalized;
    return $normalized;
  }

  /**
   * SEO slug lookup tables for a specific language.
   *
   * Uses config storage for the default-language base so mappings stay correct
   * when generating URLs for a language other than the current request.
   *
   * @return array{op_to_val: array<string, string>, val_to_op: array<string, string>, asset_to_val: array<string, string>, val_to_asset: array<string, string>}
   */
  public function getSeoSlugMappings(string $langcode): array {
    if (isset($this->seoMappingsByLang[$langcode])) {
      return $this->seoMappingsByLang[$langcode];
    }

    $base = $this->configStorage->read('ps_search.seo_url_mappings') ?: [];
    $langConfig = $this->langConfigOverride->getOverride($langcode, 'ps_search.seo_url_mappings');

    $opTypes = array_merge(
      $base['operation_types'] ?? [],
      $langConfig->get('operation_types') ?? [],
    );
    $assetTypes = array_merge(
      $base['asset_types'] ?? [],
      $langConfig->get('asset_types') ?? [],
    );

    $opToVal = [];
    $valToOp = [];
    foreach ($opTypes as $value => $slug) {
      $slug = strtolower((string) $slug);
      $value = strtoupper((string) $value);
      $opToVal[$slug] = $value;
      $valToOp[$value] = $slug;
    }

    $assetToVal = [];
    $valToAsset = [];
    foreach ($assetTypes as $value => $slug) {
      $slug = strtolower((string) $slug);
      $value = strtoupper((string) $value);
      $assetToVal[$slug] = $value;
      $valToAsset[$value] = $slug;
    }

    foreach ($this->getAssetSlugAliases($langcode) as $legacySlug => $canonicalSlug) {
      if (isset($assetToVal[$canonicalSlug]) && !isset($assetToVal[$legacySlug])) {
        $assetToVal[$legacySlug] = $assetToVal[$canonicalSlug];
      }
    }

    $this->seoMappingsByLang[$langcode] = [
      'op_to_val' => $opToVal,
      'val_to_op' => $valToOp,
      'asset_to_val' => $assetToVal,
      'val_to_asset' => $valToAsset,
    ];

    return $this->seoMappingsByLang[$langcode];
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
   * Machine route slug from the Views page path (never config translations).
   */
  private function getMachineSearchPathSlug(): string {
    if ($this->machineSlug !== NULL) {
      return $this->machineSlug;
    }

    $viewData = $this->configStorage->read('views.view.ps_search_offers') ?: [];
    $path = (string) ($viewData['display']['page_list']['display_options']['path'] ?? '');
    $slug = strtolower(trim($path, '/'));
    $this->machineSlug = $slug !== '' ? $slug : 'find-property';
    return $this->machineSlug;
  }

  /**
   * Maps stored machine search paths to the public slug for a language.
   */
  public function resolveStoredPublicSearchPath(string $path, string $langcode): string {
    $path = trim($path);
    if ($path === '') {
      return $this->getPublicPath($langcode);
    }

    $machine = $this->getInternalPath();
    $legacyMachine = '/find-property';
    $isMachine = $path === $machine
      || $path === $legacyMachine
      || str_starts_with($path, $machine . '?')
      || str_starts_with($path, $legacyMachine . '?');

    if (!$isMachine) {
      return $path;
    }

    $suffix = '';
    if (str_contains($path, '?')) {
      $suffix = substr($path, strpos($path, '?'));
    }

    return $this->getPublicPath($langcode) . $suffix;
  }

}
