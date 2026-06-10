<?php

declare(strict_types=1);

namespace Drupal\ps_search\EventSubscriber;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Database\Connection;
use Drupal\language\Config\LanguageConfigFactoryOverrideInterface;
use Drupal\ps_dictionary\Service\DictionaryResolver;
use Drupal\ps_search\Service\SearchPathResolver;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Redirects /find-property?operation_type=LOC[&asset_type=BUR][&locality=Paris]
 * to the canonical SEO URL /a-louer[/bureaux][/paris]/.
 *
 * Also 301-redirects the legacy slug /recherche when it is not the current
 * language search path (e.g. EN bookmarks after migration to /find-property).
 *
 * Priority: 31 — runs after RouterListener (32) and before
 * RouteNormalizerRequestSubscriber (30). AJAX requests are skipped.
 */
final class SearchCanonicalRedirectSubscriber implements EventSubscriberInterface {

  private const DEPARTMENT_DICTIONARY_TYPE = 'department';

  /** @var array<string, array{op: array<string,string>, asset: array<string,string>}> keyed by langcode */
  private array $mappingsByLang = [];

  public function __construct(
    private readonly ConfigFactoryInterface $configFactory,
    private readonly LanguageConfigFactoryOverrideInterface $langConfigOverride,
    private readonly Connection $database,
    private readonly SearchPathResolver $searchPathResolver,
    private readonly DictionaryResolver $dictionaryResolver,
  ) {}

  public static function getSubscribedEvents(): array {
    return [
      KernelEvents::REQUEST => [['onRequest', 31]],
    ];
  }

  public function onRequest(RequestEvent $event): void {
    if (!$event->isMainRequest()) {
      return;
    }

    $request = $event->getRequest();

    // Skip AJAX / XHR requests (BEF autosubmit, Views AJAX pagination, etc.).
    if ($request->isXmlHttpRequest()) {
      return;
    }

    $pathInfo = $request->getPathInfo();

    // --- Case 1: path is already a SEO URL → set _disable_route_normalizer ---
    // Drupal's RouteProvider caches the route collection. On cache hits it skips
    // processInbound(), so _disable_route_normalizer is never set by the path
    // processor. We set it here (priority 31, every request, no caching) instead.
    //
    // Match /[lang]/operation-slug[/...] patterns.
    if (preg_match('#^((?:/[a-z]{2,8}(?:-[a-z]{2,4})?)?)/([-a-z]+)((?:/[-a-z]*)*?)/?$#', $pathInfo, $seoCheck)) {
      $langPrefix = $seoCheck[1];
      $firstSegment = strtolower($seoCheck[2]);
      $restSegments = trim($seoCheck[3] ?? '', '/');
      $langcode = $langPrefix ? ltrim($langPrefix, '/') : $this->getDefaultLangcode();
      $m = $this->getMappings($langcode);
      if (in_array($firstSegment, $m['op'], TRUE)) {
        $restParts = $restSegments !== '' ? explode('/', $restSegments) : [];
        if ($restParts !== []) {
          $aliases = $this->searchPathResolver->getAssetSlugAliases($langcode);
          $assetSegment = strtolower($restParts[0]);
          if (isset($aliases[$assetSegment])) {
            $restParts[0] = $aliases[$assetSegment];
            $target = $langPrefix . '/' . $firstSegment . '/' . implode('/', $restParts) . '/';
            $queryString = $request->getQueryString();
            if ($queryString !== NULL && $queryString !== '') {
              $target .= '?' . $queryString;
            }
            $event->setResponse(new RedirectResponse($target, 301));
            return;
          }
        }

        // Check if asset_type query param needs to be incorporated into the path.
        $rawAssetParam = $request->query->all()['asset_type'] ?? NULL;
        if ($rawAssetParam !== NULL) {
          $assetType = is_array($rawAssetParam) ? array_key_first($rawAssetParam) : $rawAssetParam;
          if (is_string($assetType) && !empty($assetType)) {
            $assetSlug = $m['asset'][strtoupper($assetType)] ?? NULL;
            // Only redirect if the asset slug is not already in the path.
            if ($assetSlug !== NULL && strpos($restSegments, $assetSlug) === FALSE) {
              $seoPath = $langPrefix . '/' . $firstSegment . '/' . $assetSlug . '/';
              $remainingQuery = $request->query->all();
              unset($remainingQuery['asset_type'], $remainingQuery['operation_type']);
              if (!empty($remainingQuery)) {
                $seoPath .= '?' . http_build_query($remainingQuery);
              }
              $event->setResponse(new RedirectResponse($seoPath, 301));
              return;
            }
          }
        }
        $request->attributes->set('_disable_route_normalizer', TRUE);
        return;
      }
    }

    // Match /[lang]/search-slug (any configured or legacy search path segment).
    if (!preg_match('#^((?:/[a-z]{2,8}(?:-[a-z]{2,4})?)?)/([^/]+)$#', $pathInfo, $matches)) {
      return;
    }

    $langPrefix = $matches[1] ?? '';
    $segment = strtolower($matches[2]);
    $langcode = $langPrefix ? ltrim($langPrefix, '/') : $this->getDefaultLangcode();

    if (!$this->searchPathResolver->isSearchPathSegment($segment)) {
      return;
    }

    $currentSlug = $this->searchPathResolver->getSlugForLang($langcode);
    if ($segment === $this->searchPathResolver->getLegacySlug() && $segment !== $currentSlug) {
      $target = $langPrefix . '/' . $currentSlug;
      $queryString = $request->getQueryString();
      if ($queryString !== NULL && $queryString !== '') {
        $target .= '?' . $queryString;
      }
      $event->setResponse(new RedirectResponse($target, 301));
      return;
    }

    // operation_type must be present to build an SEO URL.
    // BEF links with facets uses operation_type[LOC]=LOC (array format).
    // Direct links use operation_type=LOC (scalar format).
    $rawOpParam = $request->query->all()['operation_type'] ?? NULL;
    if (is_array($rawOpParam)) {
      // BEF array format: keys are the values.
      $operationType = array_key_first($rawOpParam);
    }
    else {
      $operationType = $rawOpParam;
    }
    if (empty($operationType) || !is_string($operationType)) {
      return;
    }

    // Detect URL language from path prefix (not interface/admin preference).
    $langcode = $langPrefix ? ltrim($langPrefix, '/') : $this->getDefaultLangcode();
    $m = $this->getMappings($langcode);

    $opSlug = $m['op'][strtoupper($operationType)] ?? NULL;
    if ($opSlug === NULL) {
      return;
    }

    $seoPath = $langPrefix . '/' . $opSlug;

    // asset_type also supports BEF array format.
    $rawAssetParam = $request->query->all()['asset_type'] ?? NULL;
    if (is_array($rawAssetParam)) {
      $assetType = array_key_first($rawAssetParam);
    }
    else {
      $assetType = $rawAssetParam;
    }
    if (!empty($assetType) && is_string($assetType)) {
      $assetSlug = $m['asset'][strtoupper($assetType)] ?? NULL;
      if ($assetSlug !== NULL) {
        $seoPath .= '/' . $assetSlug;
      }
    }

    $localityRaw = $request->query->all()['locality'] ?? NULL;
    $tokens = is_array($localityRaw)
      ? array_values(array_filter(array_map('strval', $localityRaw)))
      : (is_string($localityRaw) && $localityRaw !== '' ? $this->extractLocationTokens($localityRaw) : []);

    if ($tokens !== []) {
      $localityData = $this->fetchLocalityData($tokens[0]);
      if ($localityData !== NULL) {
        // BNPPRE format: dept-code / city-postal.
        $postalCode = $localityData['postal_code'] ?? '';
        $deptCode = substr($postalCode, 0, 2);
        $deptName = $this->getDepartmentName($deptCode);
        $deptSlug = $deptName ? $this->cityToSlug($deptName) : '';
        $citySlug = $this->cityToSlug($localityData['locality']);

        if ($deptSlug && $deptCode) {
          $seoPath .= '/' . $deptSlug . '-' . $deptCode;
        }
        if ($citySlug) {
          $seoPath .= '/' . $citySlug;
          if ($postalCode) {
            $seoPath .= '-' . $postalCode;
          }
        }
      }
      else {
        // Fallback: simple slug if data not found.
        $seoPath .= '/' . $this->cityToSlug($tokens[0]);
      }
    }

    $seoPath .= '/';

    // Preserve remaining query params (budget, surface, keywords, etc.).
    $remainingQuery = $request->query->all();
    unset($remainingQuery['operation_type'], $remainingQuery['asset_type']);
    if (count($tokens) <= 1) {
      unset($remainingQuery['locality']);
    }
    if (!empty($remainingQuery)) {
      $seoPath .= '?' . http_build_query($remainingQuery);
    }

    $event->setResponse(new RedirectResponse($seoPath, 301));
  }

  private function getMappings(string $langcode): array {
    if (isset($this->mappingsByLang[$langcode])) {
      return $this->mappingsByLang[$langcode];
    }

    $base       = $this->configFactory->get('ps_search.seo_url_mappings');
    $langConfig = $this->langConfigOverride->getOverride($langcode, 'ps_search.seo_url_mappings');

    $opTypes    = array_merge($base->get('operation_types') ?? [], $langConfig->get('operation_types') ?? []);
    $assetTypes = array_merge($base->get('asset_types') ?? [], $langConfig->get('asset_types') ?? []);

    $op = []; $asset = [];
    foreach ($opTypes as $value => $slug) {
      $op[strtoupper((string) $value)] = strtolower((string) $slug);
    }
    foreach ($assetTypes as $value => $slug) {
      $asset[strtoupper((string) $value)] = strtolower((string) $slug);
    }

    $this->mappingsByLang[$langcode] = ['op' => $op, 'asset' => $asset];
    return $this->mappingsByLang[$langcode];
  }

  private function getDefaultLangcode(): string {
    return \Drupal::languageManager()->getDefaultLanguage()->getId();
  }

  private function cityToSlug(string $city): string {
    $ascii = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $city);
    if ($ascii === FALSE) {
      $ascii = $city;
    }
    $ascii = strtolower($ascii);
    $slug = preg_replace('/[^a-z0-9]+/', '-', $ascii);
    return trim((string) $slug, '-');
  }

  /**
   * Fetches locality structured data (admin_area, postal_code) from database.
   *
   * @param string $locality
   *   City name.
   *
   * @return array{locality: string, admin_area: string, postal_code: string}|null
   *   Structured data or NULL if not found.
   */
  private function fetchLocalityData(string $locality): ?array {
    $select = $this->database->select('node__field_address', 'a');
    $select->fields('a', ['field_address_locality', 'field_address_administrative_area', 'field_address_postal_code']);
    $select->condition('a.field_address_locality', $locality, '=');
    $select->range(0, 1);

    $row = $select->execute()->fetchAssoc();
    if ($row === FALSE) {
      return NULL;
    }

    return [
      'locality' => (string) ($row['field_address_locality'] ?? ''),
      'admin_area' => (string) ($row['field_address_administrative_area'] ?? ''),
      'postal_code' => (string) ($row['field_address_postal_code'] ?? ''),
    ];
  }

  /**
   * Returns department name from 2-digit INSEE code.
   */
  private function getDepartmentName(string $code): ?string {
    return $this->dictionaryResolver->resolveLabel(self::DEPARTMENT_DICTIONARY_TYPE, $code);
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
