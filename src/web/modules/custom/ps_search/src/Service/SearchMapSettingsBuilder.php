<?php

declare(strict_types=1);

namespace Drupal\ps_search\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Builds drupalSettings for the PS-native search map shell (PR1+).
 *
 * Reads the Google Maps API key from `geofield_map.settings` (shared BO key;
 * module supplied transitively via ps_offer) and map shell options from
 * ps_search.map_zone_settings.
 */
final class SearchMapSettingsBuilder {

  public const MAP_ELEMENT_ID = 'ps-search-map';

  /**
   * Default MarkerClusterer options (BNPPRE cluster styling).
   */
  private const DEFAULT_CLUSTER_OPTIONS = [
    'minimumClusterSize' => 2,
    'maxZoom' => 14,
    'gridSize' => 60,
    'styles' => [
      [
        'url' => "data:image/svg+xml;charset=UTF-8,%3Csvg xmlns='http://www.w3.org/2000/svg' width='40' height='40' viewBox='0 0 40 40'%3E%3Ccircle cx='20' cy='20' r='18' fill='%23FFFFFF' stroke='%2300915A' stroke-width='2'/%3E%3C/svg%3E",
        'width' => 40,
        'height' => 40,
        'textColor' => '#00915A',
        'textSize' => 13,
        'textLineHeight' => 40,
        'fontWeight' => 'bold',
        'anchorText' => [0, 0],
      ],
    ],
  ];

  public function __construct(
    private readonly ConfigFactoryInterface $configFactory,
    private readonly LocationCentroidResolver $locationCentroidResolver,
    private readonly LanguageManagerInterface $languageManager,
  ) {}

  /**
   * Builds map shell settings for the current search request.
   *
   * @return array<string, mixed>
   *   JSON-serializable map settings for drupalSettings.psSearch.map.
   */
  public function buildForRequest(Request $request): array {
    $zoneConfig = $this->configFactory->get('ps_search.map_zone_settings');
    $geofieldConfig = $this->configFactory->get('geofield_map.settings');

    $center = $this->resolveCenter($request, $zoneConfig);

    $clusterOptions = $this->parseClusterOptions(
      (string) ($zoneConfig->get('cluster_options') ?? ''),
    );

    $mapId = trim((string) ($zoneConfig->get('google_map_id') ?? ''));

    return [
      'enabled' => TRUE,
      'elementId' => self::MAP_ELEMENT_ID,
      'apiKey' => trim((string) ($geofieldConfig->get('gmap_api_key') ?? '')),
      'language' => $this->languageManager->getCurrentLanguage()->getId(),
      'center' => $center,
      'zoom' => (int) ($zoneConfig->get('default_zoom') ?? 6),
      'zoomMin' => (int) ($zoneConfig->get('zoom_min') ?? 1),
      'zoomMax' => (int) ($zoneConfig->get('zoom_max') ?? 22),
      'gestureHandling' => (string) ($zoneConfig->get('gesture_handling') ?? 'auto'),
      'clusterOptions' => $clusterOptions,
      'mapId' => $mapId !== '' ? $mapId : NULL,
      'lazyLoad' => (bool) ($zoneConfig->get('lazy_load') ?? FALSE),
    ];
  }

  /**
   * Resolves the initial map center from locality or default CMI.
   *
   * @return array{lat: float, lng: float}
   *   Map center coordinates.
   */
  private function resolveCenter(Request $request, $zoneConfig): array {
    $location = $this->locationCentroidResolver->resolveFromRequest($request);
    if ($location !== NULL && isset($location['lat'], $location['lng'])) {
      return [
        'lat' => (float) $location['lat'],
        'lng' => (float) $location['lng'],
      ];
    }

    return [
      'lat' => (float) ($zoneConfig->get('default_center_lat') ?? 46.603354),
      'lng' => (float) ($zoneConfig->get('default_center_lng') ?? 1.888334),
    ];
  }

  /**
   * Parses cluster JSON from map zone settings.
   *
   * @return array<string, mixed>
   *   MarkerClusterer options array.
   */
  private function parseClusterOptions(string $raw): array {
    if ($raw === '') {
      return self::DEFAULT_CLUSTER_OPTIONS;
    }

    try {
      $decoded = json_decode($raw, TRUE, 512, JSON_THROW_ON_ERROR);
    }
    catch (\JsonException) {
      return self::DEFAULT_CLUSTER_OPTIONS;
    }

    return is_array($decoded) ? $decoded : self::DEFAULT_CLUSTER_OPTIONS;
  }

}
