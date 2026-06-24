<?php

declare(strict_types=1);

namespace Drupal\ps_search\PathProcessor;

use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\PathProcessor\InboundPathProcessorInterface;
use Drupal\Core\PathProcessor\OutboundPathProcessorInterface;
use Drupal\Core\Render\BubbleableMetadata;
use Drupal\Core\Site\Settings;
use Drupal\ps_search\Contract\GeoZoneRepositoryInterface;
use Drupal\ps_search\Contract\SearchContextSerializerInterface;
use Drupal\ps_search\Service\SearchEngineSettingsReader;
use Drupal\ps_search\Service\SearchExposedFiltersQueryNormalizer;
use Drupal\ps_search\Service\SearchPathResolver;
use Drupal\ps_search\Service\SearchSeoLocalityPathBuilder;
use Symfony\Component\HttpFoundation\Request;

/**
 * Converts SEO-friendly URLs to internal search paths and vice versa.
 *
 * Inbound (priority 290, after language processor at 300):
 *   /a-louer/bureaux/paris/ -> /find-property + query params
 *   /bureaux/loire-42/parigny-42120/ -> /find-property + asset + locality (no op)
 *
 * Outbound (priority 110, before language processor at 100):
 *   /find-property + query['operation_type'=>'LOC',...] -> /a-louer/bureaux/paris/
 *   /find-property + query['asset_type'=>'BUR'] (no op) -> /office/…
 *
 * Slugs are configurable via ps_search.seo_url_mappings config and
 * translatable per language via the Config Translation module.
 *
 * IMPORTANT: uses LanguageInterface::TYPE_URL (URL prefix language) to resolve
 * slugs, NOT TYPE_INTERFACE (admin user preferred language). This ensures that
 * /fr/a-louer is correctly resolved for admin users whose interface language is EN.
 */
final class SearchSeoPathProcessor implements InboundPathProcessorInterface, OutboundPathProcessorInterface {

  public function __construct(
    private readonly LanguageManagerInterface $languageManager,
    private readonly SearchPathResolver $searchPathResolver,
    private readonly SearchSeoLocalityPathBuilder $seoLocalityPathBuilder,
    private readonly SearchEngineSettingsReader $engineSettings,
    private readonly GeoZoneRepositoryInterface $geoZoneRepository,
    private readonly SearchContextSerializerInterface $contextSerializer,
  ) {}

  /**
   * {@inheritdoc}
   *
   * Converts SEO paths to /find-property and injects facet query params.
   * Sets _disable_route_normalizer to prevent the redirect module from
   * issuing a 301 back to /find-property.
   */
  public function processInbound($path, Request $request): string {
    $langcode = $this->languageManager->getCurrentLanguage(LanguageInterface::TYPE_URL)->getId();

    $stripped = ltrim($path, '/');
    $segments = ($stripped !== '') ? array_values(array_filter(explode('/', $stripped))) : [];
    if ($segments === []) {
      return $path;
    }

    // Offer detail URLs share SEO prefixes but end with .html — leave to path aliases.
    if (str_ends_with(strtolower($path), '.html')) {
      return $path;
    }

    $facets = $this->searchPathResolver->resolveFacetsFromPathSegments($langcode, $segments);
    if ($facets['operation_type'] === NULL && $facets['asset_type'] === NULL) {
      return $path;
    }

    // Operation paths: op + up to 3 tail segments. Asset-only: up to 3 total segments.
    $maxSegments = $facets['operation_type'] !== NULL ? 4 : 3;
    if (count($segments) > $maxSegments) {
      return $path;
    }

    $params = [];
    if ($facets['operation_type'] !== NULL) {
      $params['operation_type'] = $facets['operation_type'];
    }
    if ($facets['asset_type'] !== NULL) {
      $params['asset_type'] = $facets['asset_type'];
    }

    $this->applyLocationParams($params, $facets['locality_segments']);

    $request->query->add($params);
    $this->syncLocationsQueryParam($request);
    SearchExposedFiltersQueryNormalizer::normalizeRequest($request);
    $request->attributes->set('_disable_route_normalizer', TRUE);

    return $this->searchPathResolver->getInternalPath();
  }

  /**
   * {@inheritdoc}
   *
   * Converts /find-property + query params -> SEO slug paths.
   */
  public function processOutbound($path, &$options = [], ?Request $request = NULL, ?BubbleableMetadata $bubbleable_metadata = NULL): string {
    if ($path !== $this->searchPathResolver->getInternalPath()) {
      return $path;
    }

    $query = $options['query'] ?? [];
    $rawOp = $this->firstQueryScalar($query['operation_type'] ?? NULL);
    $rawAsset = $this->firstQueryScalar($query['asset_type'] ?? NULL);

    if ($rawOp === NULL && $rawAsset === NULL) {
      return $path;
    }

    $langcode = $this->resolveOutboundLangcode($options);
    $seoPrefix = $this->searchPathResolver->buildSeoFilterPathPrefix($langcode, $rawOp, $rawAsset);
    if ($seoPrefix === NULL) {
      return $path;
    }

    if ($rawOp !== NULL) {
      unset($query['operation_type']);
    }
    if ($rawAsset !== NULL) {
      unset($query['asset_type']);
    }

    $seoPath = $seoPrefix;

    if ($this->engineSettings->isSearchContextEnabled()) {
      $pathQuery = $query;
      if ($rawOp !== NULL) {
        $pathQuery['operation_type'] = $rawOp;
      }
      if ($rawAsset !== NULL) {
        $pathQuery['asset_type'] = $rawAsset;
      }
      $built = $this->contextSerializer->buildSeoPathFromQuery($langcode, $pathQuery);
      if ($built !== NULL) {
        $seoPath = $built;
        unset($query['zone'], $query['locality'], $query['locations']);
      }
    }
    else {
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
    }

    $options['query'] = $query;

    return $seoPath . '/';
  }

  /**
   * Adds geo zone or legacy locality params from SEO path tail segments.
   *
   * @param array<string, string> $params
   * @param list<string> $localitySegments
   */
  private function applyLocationParams(array &$params, array $localitySegments): void {
    if ($localitySegments === []) {
      return;
    }

    if ($this->engineSettings->isSearchContextEnabled()) {
      $slug = strtolower((string) end($localitySegments));
      $zone = $this->geoZoneRepository->findBySlug($slug, $this->resolveCountryCode());
      if ($zone !== NULL) {
        $params['zone'] = $zone->slug;
        return;
      }
    }

    $this->applyLocalitySegmentsToParams($params, $localitySegments);
  }

  private function resolveCountryCode(): string {
    $code = Settings::get('ps_country_code');
    return is_string($code) && $code !== '' ? strtolower($code) : 'com';
  }

  /**
   * Resolves the language used for outbound slug translation.
   */
  private function resolveOutboundLangcode(array $options): string {
    if (isset($options['language']) && $options['language'] instanceof LanguageInterface) {
      return $options['language']->getId();
    }

    return $this->languageManager->getCurrentLanguage(LanguageInterface::TYPE_URL)->getId();
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
   * Adds locality filter params from SEO path tail segments.
   *
   * @param array<string, string> $params
   *   Facet params to augment in place.
   * @param string[] $localitySegments
   *   Remaining path segments after operation/asset slugs.
   */
  private function applyLocalitySegmentsToParams(array &$params, array $localitySegments): void {
    $token = $this->seoLocalityPathBuilder->parseLocalitySegments($localitySegments);
    if ($token !== NULL && $token !== '') {
      $params['locality'] = $token;
      return;
    }

    $lastSegment = NULL;
    foreach ($localitySegments as $segment) {
      if ($segment !== '') {
        $lastSegment = $segment;
      }
    }

    if ($lastSegment !== NULL) {
      $params['locality'] = $this->slugToCity($lastSegment);
    }
  }

  /**
   * Converts a URL slug back to a city name for the filter value.
   */
  private function slugToCity(string $slug): string {
    return ucwords(str_replace('-', ' ', $slug));
  }

  /**
   * Normalizes a BEF or scalar query parameter to a string value.
   */
  private function firstQueryScalar(mixed $value): ?string {
    if (is_array($value)) {
      $value = array_key_first($value);
    }
    if (!is_string($value) || $value === '') {
      return NULL;
    }
    return $value;
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
