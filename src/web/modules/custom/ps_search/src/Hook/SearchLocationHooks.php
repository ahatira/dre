<?php

declare(strict_types=1);

namespace Drupal\ps_search\Hook;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\ps_offer\Service\OfferMapSettings;
use Drupal\ps_search\Service\LocationCentroidResolver;
use Drupal\ps_search\Service\MapBoundsResolver;
use Drupal\ps_search\Service\SearchResultCounter;
use Drupal\views\ViewExecutable;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Map settings and drupalSettings for the property search view.
 */
final class SearchLocationHooks {

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
    private readonly LocationCentroidResolver $locationCentroidResolver,
    private readonly MapBoundsResolver $mapBoundsResolver,
    private readonly SearchResultCounter $resultCounter,
    private readonly OfferMapSettings $offerMapSettings,
    private readonly ConfigFactoryInterface $configFactory,
    private readonly RequestStack $requestStack,
  ) {}

  /**
   * Implements hook_preprocess_HOOK() for views_view.
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

    $request = $this->requestStack->getCurrentRequest();
    if ($request === NULL) {
      return;
    }

    $threshold = max(1, (int) ($this->configFactory->get('ps_search.map_zone_settings')->get('list_pager_threshold') ?? 100));
    $zoneCount = $this->resultCounter->countInBounds($request);
    $globalCount = $this->resultCounter->countBusinessFilters($request);

    $variables['#attached']['drupalSettings']['psSearch']['markersUrl'] = '/ps-search/markers';
    $variables['#attached']['drupalSettings']['psSearch']['isochroneUrl'] = '/ps-search/isochrone';
    $variables['#attached']['drupalSettings']['psSearch']['globalCount'] = $globalCount;
    $variables['#attached']['drupalSettings']['psSearch']['zoneCount'] = $zoneCount;
    $variables['#attached']['drupalSettings']['psSearch']['listPagerThreshold'] = $threshold;
    $variables['#attached']['drupalSettings']['psSearch']['listLoadAll'] = $zoneCount > 0 && $zoneCount <= $threshold;
    if ($globalCount > 0 && $zoneCount === 0) {
      $variables['ps_search_empty_zone_only'] = TRUE;
    }
    $variables['search_transport_icons'] = $this->resolveSearchTransportIcons();

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
