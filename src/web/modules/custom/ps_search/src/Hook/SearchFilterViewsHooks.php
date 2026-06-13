<?php

declare(strict_types=1);

namespace Drupal\ps_search\Hook;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\ps_offer\Service\OfferMapSettings;
use Drupal\ps_search\Api\ApiRoutePaths;
use Drupal\ps_search\Search\Filter\FilterBarBuilder;
use Drupal\ps_search\Search\Filter\SearchResultsHeaderBuilder;
use Drupal\ps_search\Service\LocationCentroidResolver;
use Drupal\ps_search\Service\MapBoundsResolver;
use Drupal\ps_search\Service\SearchContentLanguageResolver;
use Drupal\ps_search\Service\SearchMapSettingsBuilder;
use Drupal\ps_search\Service\SearchResultCounter;
use Drupal\search_api\Plugin\views\query\SearchApiQuery;
use Drupal\views\ViewExecutable;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Injects filter UI and map drupalSettings on the public search view.
 */
final class SearchFilterViewsHooks {

  /**
   * Search transport keys mapped to offer map travel mode constants.
   */
  private const TRANSPORT_MODE_MAP = [
    'walking' => 'WALKING',
    'transports' => 'TRANSIT',
    'bike' => 'BICYCLING',
    'car' => 'DRIVING',
  ];

  public function __construct(
    private readonly FilterBarBuilder $filterBarBuilder,
    private readonly SearchResultsHeaderBuilder $resultsHeaderBuilder,
    private readonly LocationCentroidResolver $locationCentroidResolver,
    private readonly MapBoundsResolver $mapBoundsResolver,
    private readonly SearchResultCounter $resultCounter,
    private readonly OfferMapSettings $offerMapSettings,
    private readonly ConfigFactoryInterface $configFactory,
    private readonly RequestStack $requestStack,
    private readonly SearchMapSettingsBuilder $mapSettingsBuilder,
    private readonly SearchContentLanguageResolver $contentLanguageResolver,
  ) {}

  /**
   * Implements hook_views_pre_execute().
   *
   * Restricts the offer list to the current content language (same as counts/map).
   */
  #[Hook('views_pre_execute')]
  public function viewsPreExecute(ViewExecutable $view): void {
    if ($view->id() !== 'ps_search_offers') {
      return;
    }

    $query = $view->getQuery();
    if (!$query instanceof SearchApiQuery) {
      return;
    }

    $request = $this->requestStack->getCurrentRequest();
    if ($request === NULL) {
      return;
    }

    $langcodes = $this->contentLanguageResolver->resolveSearchLangcodes($request);
    if ($langcodes === []) {
      return;
    }

    $query->setLanguages($langcodes);
  }

  /**
   * Implements hook_preprocess_HOOK().
   */
  #[Hook('preprocess_views_view')]
  public function preprocessViewsView(array &$variables): void {
    $view = $variables['view'] ?? NULL;
    if (!$view instanceof ViewExecutable || $view->id() !== 'ps_search_offers') {
      return;
    }

    if (($variables['display_id'] ?? '') !== 'page_list') {
      return;
    }

    $variables['search_filter_bar'] = $this->filterBarBuilder->build();
    $variables['search_filter_bar_mobile_actions'] = $this->filterBarBuilder->buildMobileActions();
    $variables['search_results_header'] = $this->resultsHeaderBuilder->buildRenderArray($view);

    foreach ([
      'ps_search/search-page-map-init',
      'ps_search/search-page-map-markers',
      'ps_search/search-page-map-bounds',
      'ps_search/search-page-map-popup',
      'ps_search/search-page-map-zone',
      'ps_search/search-page-location',
      'ps_search/search-alert-offcanvas',
    ] as $library) {
      $variables['#attached']['library'][] = $library;
    }

    $request = $this->requestStack->getCurrentRequest();
    if ($request === NULL) {
      return;
    }

    $threshold = max(1, (int) ($this->configFactory->get('ps_search.map_zone_settings')->get('list_pager_threshold') ?? 100));
    $zoneCount = $this->resultCounter->countInBounds($request);
    $globalCount = $this->resultCounter->countBusinessFilters($request);

    $variables['#attached']['drupalSettings']['psSearch']['apiBase'] = ApiRoutePaths::BASE;
    $variables['#attached']['drupalSettings']['psSearch']['markersUrl'] = ApiRoutePaths::MARKERS;
    $variables['#attached']['drupalSettings']['psSearch']['isochroneUrl'] = ApiRoutePaths::ISOCHRONE;
    $markersMax = max(1, min((int) ($this->configFactory->get('ps_search.map_zone_settings')->get('markers_max') ?? 500), 1000));
    $mapZoneConfig = $this->configFactory->get('ps_search.map_zone_settings');
    $variables['#attached']['drupalSettings']['psSearch']['markersMax'] = $markersMax;
    $variables['#attached']['drupalSettings']['psSearch']['markersClusterEnabled'] = (bool) ($mapZoneConfig->get('markers_cluster_enabled') ?? TRUE);
    $variables['#attached']['drupalSettings']['psSearch']['markerClusterSkipBelow'] = max(
      0,
      (int) ($mapZoneConfig->get('marker_cluster_skip_below') ?? 10),
    );
    $variables['#attached']['drupalSettings']['psSearch']['globalCount'] = $globalCount;
    $variables['#attached']['drupalSettings']['psSearch']['zoneCount'] = $zoneCount;
    $variables['#attached']['drupalSettings']['psSearch']['listPagerThreshold'] = $threshold;
    $variables['#attached']['drupalSettings']['psSearch']['listLoadAll'] = $zoneCount > 0 && $zoneCount <= $threshold;
    $variables['#attached']['drupalSettings']['psSearch']['map'] = $this->mapSettingsBuilder->buildForRequest($request);
    if ($globalCount > 0 && $zoneCount === 0) {
      $variables['ps_search_empty_zone_only'] = TRUE;
    }
    $variables['search_transport_icons'] = $this->resolveSearchTransportIcons();
    $variables['search_map_zone_controls'] = [
      '#theme' => 'ps_search_map_zone_controls',
      'search_transport_icons' => $variables['search_transport_icons'],
    ];

    $autoFitToResults = $this->mapBoundsResolver->autoFitToResults($request);
    $variables['#attached']['drupalSettings']['psSearch']['autoFitToResults'] = $autoFitToResults;

    $locationMap = $this->locationCentroidResolver->resolveFromRequest($request);
    if ($locationMap !== NULL && $locationMap['lat'] !== NULL && $locationMap['lng'] !== NULL) {
      $variables['#attached']['drupalSettings']['psSearch']['locationMap'] = $locationMap;
    }

    $activeBounds = $this->mapBoundsResolver->resolveActiveBounds($request);
    if ($activeBounds !== NULL) {
      $variables['#attached']['drupalSettings']['psSearch']['mapBounds'] = [
        'swLat' => $activeBounds->swLat,
        'swLng' => $activeBounds->swLng,
        'neLat' => $activeBounds->neLat,
        'neLng' => $activeBounds->neLng,
        'explicit' => $this->mapBoundsResolver->hasExplicitBounds($request),
        'autoFit' => $autoFitToResults,
        'queryValue' => $activeBounds->toQueryValue(),
      ];
    }
  }

  /**
   * Resolves configured travel mode icons for the search map float panel.
   *
   * @return array<string, array{pack: string, id: string}>
   *   Icon pack/id keyed by search transport id.
   */
  private function resolveSearchTransportIcons(): array {
    $configured = $this->offerMapSettings->getTravelModeIcons();
    $icons = [];

    foreach (self::TRANSPORT_MODE_MAP as $transport => $mode) {
      $icons[$transport] = $configured[$mode] ?? [
        'pack' => 'bnp_custom',
        'id' => match ($transport) {
          'walking' => 'walking',
          'transports' => 'transport',
          'bike' => 'bike',
          'car' => 'car',
          default => 'walking',
        },
      ];
    }

    return $icons;
  }

}
