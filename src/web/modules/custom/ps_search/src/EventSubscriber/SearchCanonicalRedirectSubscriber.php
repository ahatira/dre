<?php

declare(strict_types=1);

namespace Drupal\ps_search\EventSubscriber;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Database\Connection;
use Drupal\language\Config\LanguageConfigFactoryOverrideInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Redirects /recherche?operation_type=LOC[&asset_type=BUR][&locality=Paris]
 * to the canonical SEO URL /a-louer[/bureau][/paris]/.
 *
 * This ensures users and bots always land on the SEO URL, not on the raw
 * /recherche?... form submission URL.
 *
 * Priority: 31 — runs after RouterListener (32) and before
 * RouteNormalizerRequestSubscriber (30). AJAX requests are skipped.
 */
final class SearchCanonicalRedirectSubscriber implements EventSubscriberInterface {

  /** @var array<string, array{op: array<string,string>, asset: array<string,string>}> keyed by langcode */
  private array $mappingsByLang = [];

  public function __construct(
    private readonly ConfigFactoryInterface $configFactory,
    private readonly LanguageConfigFactoryOverrideInterface $langConfigOverride,
    private readonly Connection $database,
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

    // Only handle paths that end with /recherche (with optional language prefix
    // such as /fr). Match /recherche or /XX/recherche.
    if (!preg_match('#^(/[a-z]{2,8}(?:-[a-z]{2,4})?)?/recherche$#', $pathInfo, $matches)) {
      return;
    }

    $langPrefix = $matches[1] ?? '';

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

    $locality = $request->query->get('locality');
    if (!empty($locality) && is_string($locality)) {
      $tokens = $this->extractLocationTokens($locality);
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
        } else {
          // Fallback: simple slug if data not found.
          $seoPath .= '/' . $this->cityToSlug($tokens[0]);
        }
      }
    }

    $seoPath .= '/';

    // Preserve remaining query params (budget, surface, keywords, etc.).
    $remainingQuery = $request->query->all();
    unset($remainingQuery['operation_type'], $remainingQuery['asset_type']);
    if (empty($locality) || !is_string($locality) || count($this->extractLocationTokens($locality)) <= 1) {
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
   * Returns department name from 2-digit code.
   *
   * @param string $code
   *   Department code (e.g., "75", "92").
   *
   * @return string|null
   *   Department name or NULL if not found.
   */
  private function getDepartmentName(string $code): ?string {
    $departments = [
      '01' => 'Ain', '02' => 'Aisne', '03' => 'Allier', '04' => 'Alpes-de-Haute-Provence',
      '05' => 'Hautes-Alpes', '06' => 'Alpes-Maritimes', '07' => 'Ardeche', '08' => 'Ardennes',
      '09' => 'Ariege', '10' => 'Aube', '11' => 'Aude', '12' => 'Aveyron',
      '13' => 'Bouches-du-Rhone', '14' => 'Calvados', '15' => 'Cantal', '16' => 'Charente',
      '17' => 'Charente-Maritime', '18' => 'Cher', '19' => 'Correze', '21' => 'Cote-d\'Or',
      '22' => 'Cotes-d\'Armor', '23' => 'Creuse', '24' => 'Dordogne', '25' => 'Doubs',
      '26' => 'Drome', '27' => 'Eure', '28' => 'Eure-et-Loir', '29' => 'Finistere',
      '2A' => 'Corse-du-Sud', '2B' => 'Haute-Corse', '30' => 'Gard', '31' => 'Haute-Garonne',
      '32' => 'Gers', '33' => 'Gironde', '34' => 'Herault', '35' => 'Ille-et-Vilaine',
      '36' => 'Indre', '37' => 'Indre-et-Loire', '38' => 'Isere', '39' => 'Jura',
      '40' => 'Landes', '41' => 'Loir-et-Cher', '42' => 'Loire', '43' => 'Haute-Loire',
      '44' => 'Loire-Atlantique', '45' => 'Loiret', '46' => 'Lot', '47' => 'Lot-et-Garonne',
      '48' => 'Lozere', '49' => 'Maine-et-Loire', '50' => 'Manche', '51' => 'Marne',
      '52' => 'Haute-Marne', '53' => 'Mayenne', '54' => 'Meurthe-et-Moselle', '55' => 'Meuse',
      '56' => 'Morbihan', '57' => 'Moselle', '58' => 'Nievre', '59' => 'Nord',
      '60' => 'Oise', '61' => 'Orne', '62' => 'Pas-de-Calais', '63' => 'Puy-de-Dome',
      '64' => 'Pyrenees-Atlantiques', '65' => 'Hautes-Pyrenees', '66' => 'Pyrenees-Orientales',
      '67' => 'Bas-Rhin', '68' => 'Haut-Rhin', '69' => 'Rhone', '70' => 'Haute-Saone',
      '71' => 'Saone-et-Loire', '72' => 'Sarthe', '73' => 'Savoie', '74' => 'Haute-Savoie',
      '75' => 'Paris', '76' => 'Seine-Maritime', '77' => 'Seine-et-Marne', '78' => 'Yvelines',
      '79' => 'Deux-Sevres', '80' => 'Somme', '81' => 'Tarn', '82' => 'Tarn-et-Garonne',
      '83' => 'Var', '84' => 'Vaucluse', '85' => 'Vendee', '86' => 'Vienne',
      '87' => 'Haute-Vienne', '88' => 'Vosges', '89' => 'Yonne', '90' => 'Territoire-de-Belfort',
      '91' => 'Essonne', '92' => 'Hauts-de-Seine', '93' => 'Seine-Saint-Denis', '94' => 'Val-de-Marne',
      '95' => 'Val-d\'Oise',
    ];
    return $departments[$code] ?? NULL;
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
