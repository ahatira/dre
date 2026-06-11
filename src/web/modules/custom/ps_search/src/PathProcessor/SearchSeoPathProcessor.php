<?php

declare(strict_types=1);

namespace Drupal\ps_search\PathProcessor;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\language\Config\LanguageConfigFactoryOverrideInterface;
use Drupal\Core\PathProcessor\InboundPathProcessorInterface;
use Drupal\Core\PathProcessor\OutboundPathProcessorInterface;
use Drupal\Core\Render\BubbleableMetadata;
use Drupal\ps_search\Service\SearchPathResolver;
use Drupal\ps_search\Service\SearchSeoLocalityPathBuilder;
use Symfony\Component\HttpFoundation\Request;

/**
 * Converts SEO-friendly URLs to internal search paths and vice versa.
 *
 * Inbound (priority 290, after language processor at 300):
 *   /a-louer/bureaux/paris/ -> /find-property + query params
 *
 * Outbound (priority 110, before language processor at 100):
 *   /find-property + query['operation_type'=>'LOC',...] -> /a-louer/bureaux/paris/
 *
 * Slugs are configurable via ps_search.seo_url_mappings config and
 * translatable per language via the Config Translation module.
 *
 * IMPORTANT: uses LanguageInterface::TYPE_URL (URL prefix language) to resolve
 * slugs, NOT TYPE_INTERFACE (admin user preferred language). This ensures that
 * /fr/a-louer is correctly resolved for admin users whose interface language is EN.
 */
final class SearchSeoPathProcessor implements InboundPathProcessorInterface, OutboundPathProcessorInterface {

  /**
   * Per-language slug lookup tables, keyed by langcode.
   *
   * @var array<string, array{op_to_val: array<string,string>, val_to_op: array<string,string>, asset_to_val: array<string,string>, val_to_asset: array<string,string>}>
   */
  private array $mappingsByLang = [];

  public function __construct(
    private readonly ConfigFactoryInterface $configFactory,
    private readonly LanguageManagerInterface $languageManager,
    private readonly LanguageConfigFactoryOverrideInterface $langConfigOverride,
    private readonly SearchPathResolver $searchPathResolver,
    private readonly SearchSeoLocalityPathBuilder $seoLocalityPathBuilder,
  ) {}

  /**
   * {@inheritdoc}
   *
   * Converts /a-louer[/asset][/city]/ -> /find-property and injects query params.
   * Sets _disable_route_normalizer to prevent the redirect module from
   * issuing a 301 back to /find-property.
   */
  public function processInbound($path, Request $request): string {
    // Use TYPE_URL (from the URL prefix /fr/, /en/) not TYPE_INTERFACE (admin
    // user's preferred language). This ensures /fr/a-louer is resolved correctly
    // even for admin users whose interface language is EN.
    $langcode = $this->languageManager->getCurrentLanguage(LanguageInterface::TYPE_URL)->getId();
    $m = $this->getMappings($langcode);

    // Extract first segment and check against known operation slugs.
    $stripped = ltrim($path, '/');
    $slashPos = strpos($stripped, '/');
    $firstSegment = $slashPos !== FALSE ? substr($stripped, 0, $slashPos) : $stripped;

    if ($firstSegment === '' || !isset($m['op_to_val'][$firstSegment])) {
      return $path;
    }

    $params = ['operation_type' => $m['op_to_val'][$firstSegment]];
    $rest = $slashPos !== FALSE ? substr($stripped, $slashPos + 1) : '';
    $segments = ($rest !== '') ? array_values(array_filter(explode('/', $rest))) : [];

    $assetFound = FALSE;
    $possibleDeptSegment = NULL;
    $possibleCitySegment = NULL;

    foreach ($segments as $i => $segment) {
      $slug = strtolower($segment);
      if (!$assetFound && isset($m['asset_to_val'][$slug])) {
        $params['asset_type'] = $m['asset_to_val'][$slug];
        $assetFound = TRUE;
      }
      elseif ($segment !== '') {
        // Collect last two non-empty segments as potential dept+city.
        $possibleDeptSegment = $possibleCitySegment;
        $possibleCitySegment = $segment;
      }
    }

    // Try BNPPRE two-segment format: dept-code / city-postal.
    if ($possibleDeptSegment !== NULL && $possibleCitySegment !== NULL) {
      $token = $this->seoLocalityPathBuilder->pathSegmentsToToken(
        $possibleDeptSegment,
        $possibleCitySegment,
      );
      if ($token !== NULL) {
        $params['locality'] = $token;
      }
    }
    elseif ($possibleCitySegment !== NULL) {
      $token = $this->seoLocalityPathBuilder->pathSegmentsToToken(NULL, $possibleCitySegment);
      if ($token === NULL) {
        $token = $this->seoLocalityPathBuilder->deptSegmentToToken($possibleCitySegment);
      }
      if ($token !== NULL) {
        $params['locality'] = $token;
      }
      else {
        $params['locality'] = $this->slugToCity($possibleCitySegment);
      }
    }

    $request->query->add($params);
    $this->syncLocationsQueryParam($request);
    // Prevent redirect module (RouteNormalizerRequestSubscriber, priority 30)
    // from issuing a 301 because the public URL does not match the internal path.
    $request->attributes->set('_disable_route_normalizer', TRUE);

    return $this->searchPathResolver->getInternalPath();
  }

  /**
   * {@inheritdoc}
   *
   * Converts /find-property + query params -> /a-louer[/asset][/city]/
   */
  public function processOutbound($path, &$options = [], ?Request $request = NULL, ?BubbleableMetadata $bubbleable_metadata = NULL): string {
    if ($path !== $this->searchPathResolver->getInternalPath()) {
      return $path;
    }

    $query = $options['query'] ?? [];
    $rawOp = $query['operation_type'] ?? NULL;
    if (!$rawOp || !is_string($rawOp)) {
      return $path;
    }

    $langcode = $this->languageManager->getCurrentLanguage(LanguageInterface::TYPE_URL)->getId();
    $m = $this->getMappings($langcode);

    $opSlug = $m['val_to_op'][strtoupper($rawOp)] ?? NULL;
    if ($opSlug === NULL) {
      return $path;
    }

    $seoPath = '/' . $opSlug;
    unset($query['operation_type']);

    $rawAsset = $query['asset_type'] ?? NULL;
    if ($rawAsset !== NULL && is_string($rawAsset)) {
      $assetSlug = $m['val_to_asset'][strtoupper($rawAsset)] ?? NULL;
      if ($assetSlug !== NULL) {
        $seoPath .= '/' . $assetSlug;
        unset($query['asset_type']);
      }
    }

    $locality = $query['locality'] ?? NULL;
    $locations = $query['locations'] ?? NULL;
    $locationValue = is_string($locations) && $locations !== '' ? $locations : $locality;
    if (is_string($locationValue) && $locationValue !== '') {
      $tokens = $this->extractLocationTokens($locationValue);
      if (count($tokens) === 1) {
        $seoPath = $this->seoLocalityPathBuilder->appendSegmentsToPath($seoPath, $tokens[0]);
        unset($query['locality'], $query['locations']);
      }
      elseif (count($tokens) > 1) {
        $query['locations'] = implode(',', $tokens);
        unset($query['locality']);
      }
    }

    $options['query'] = $query;

    return $seoPath . '/';
  }

  /**
   * Maps public ?locations= to Views exposed filter ?locality=.
   */
  private function syncLocationsQueryParam(Request $request): void {
    $locations = $request->query->get('locations');
    if (!is_string($locations) || trim($locations) === '') {
      return;
    }
    if (!$request->query->has('locality')) {
      $request->query->set('locality', $locations);
    }
  }

  /**
   * Builds slug<->value lookup tables for a specific language.
   *
   * Explicitly loads the language config override for TYPE_URL language
   * (from the URL prefix, e.g. /fr/) instead of relying on ConfigFactory
   * which uses TYPE_INTERFACE (admin user preferred language, e.g. EN).
   *
   * Results are cached per langcode within the request.
   *
   * @return array{op_to_val: array<string,string>, val_to_op: array<string,string>, asset_to_val: array<string,string>, val_to_asset: array<string,string>}
   */
  private function getMappings(string $langcode): array {
    if (isset($this->mappingsByLang[$langcode])) {
      return $this->mappingsByLang[$langcode];
    }

    // Base config (default language, e.g. EN).
    $base = $this->configFactory->get('ps_search.seo_url_mappings');
    // Language-specific override from the language.LANGCODE collection.
    $langConfig = $this->langConfigOverride->getOverride($langcode, 'ps_search.seo_url_mappings');

    // Override wins over base for keys present in both.
    $opTypes = array_merge(
      $base->get('operation_types') ?? [],
      $langConfig->get('operation_types') ?? [],
    );
    $assetTypes = array_merge(
      $base->get('asset_types') ?? [],
      $langConfig->get('asset_types') ?? [],
    );

    $opToVal = []; $valToOp = [];
    foreach ($opTypes as $value => $slug) {
      $slug = strtolower((string) $slug);
      $value = strtoupper((string) $value);
      $opToVal[$slug] = $value;
      $valToOp[$value] = $slug;
    }

    $assetToVal = []; $valToAsset = [];
    foreach ($assetTypes as $value => $slug) {
      $slug = strtolower((string) $slug);
      $value = strtoupper((string) $value);
      $assetToVal[$slug] = $value;
      $valToAsset[$value] = $slug;
    }

    foreach ($this->searchPathResolver->getAssetSlugAliases($langcode) as $legacySlug => $canonicalSlug) {
      $legacySlug = strtolower($legacySlug);
      $canonicalSlug = strtolower($canonicalSlug);
      if (isset($assetToVal[$canonicalSlug]) && !isset($assetToVal[$legacySlug])) {
        $assetToVal[$legacySlug] = $assetToVal[$canonicalSlug];
      }
    }

    $this->mappingsByLang[$langcode] = [
      'op_to_val'    => $opToVal,
      'val_to_op'    => $valToOp,
      'asset_to_val' => $assetToVal,
      'val_to_asset' => $valToAsset,
    ];

    return $this->mappingsByLang[$langcode];
  }

  /**
   * Converts a URL slug back to a city name for the filter value.
   *
   * Example: "la-defense" -> "La Defense"
   */
  private function slugToCity(string $slug): string {
    return ucwords(str_replace('-', ' ', $slug));
  }

  /**
   * Parses and sanitizes a comma/semicolon-separated location list.
   *
   * @return string[]
   *   A deduplicated list of up to 10 location tokens.
   */
  private function extractLocationTokens(string $value): array {
    $parts = preg_split('/[,;]+/', $value) ?: [];
    $tokens = [];

    foreach ($parts as $part) {
      $cleaned = trim($part);
      if ($cleaned === '') {
        continue;
      }
      $key = mb_strtolower($cleaned);
      $tokens[$key] = $cleaned;
      if (count($tokens) >= 10) {
        break;
      }
    }

    return array_values($tokens);
  }

}
