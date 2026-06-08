<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\Extension\ExtensionPathResolver;
use Drupal\Core\Theme\Icon\Plugin\IconPackManagerInterface;
use Drupal\node\NodeInterface;
use Drupal\ps_core\Utility\IconIdUtility;

/**
 * Reads offer map settings exposed to the frontend map behavior.
 */
final class OfferMapSettings {

  /**
   * Default UI Icons for travel mode selector buttons.
   */
  private const TRAVEL_MODE_ICON_DEFAULTS = [
    'DRIVING' => ['pack' => 'bnp_custom', 'id' => 'car'],
    'TRANSIT' => ['pack' => 'bnp_custom', 'id' => 'transport'],
    'WALKING' => ['pack' => 'bnp_custom', 'id' => 'walking'],
    'BICYCLING' => ['pack' => 'bnp_custom', 'id' => 'bike'],
  ];

  /**
   * Default UI Icons for POI filter checkboxes.
   */
  private const POI_FILTER_ICON_DEFAULTS = [
    'transport' => ['pack' => 'bnp_custom', 'id' => 'transport'],
    'parkings' => ['pack' => 'bnp_custom', 'id' => 'parking-borders'],
    'restaurants' => ['pack' => 'bnp_custom', 'id' => 'restaurant'],
    'hotels' => ['pack' => 'bnp_custom', 'id' => 'hotel'],
  ];

  /**
   * Default marker colors for POI categories on the map.
   */
  private const POI_MARKER_COLOR_DEFAULTS = [
    'transport' => '#0072CE',
    'parkings' => '#6C757D',
    'restaurants' => '#E87722',
    'hotels' => '#6B2C91',
  ];

  /**
   * Default pin icons for POI markers on the map.
   */
  private const POI_MARKER_ICON_DEFAULTS = [
    'transport' => ['pack' => 'bnp_custom', 'id' => 'poi-transport'],
    'parkings' => ['pack' => 'bnp_custom', 'id' => 'poi-parking'],
    'restaurants' => ['pack' => 'bnp_custom', 'id' => 'poi-restaurant'],
    'hotels' => ['pack' => 'bnp_custom', 'id' => 'poi-hotel'],
  ];

  public function __construct(
    private readonly ConfigFactoryInterface $configFactory,
    private readonly ExtensionPathResolver $extensionPathResolver,
    private readonly IconPackManagerInterface $iconPackManager,
  ) {}

  /**
   * Returns whether the offer detail map is enabled globally.
   */
  public function isEnabled(): bool {
    return (bool) $this->config()->get('enabled');
  }

  /**
   * Returns whether POI filters are enabled on the offer map.
   */
  public function isPoiEnabled(): bool {
    return $this->isEnabled() && (bool) $this->config()->get('poi_enabled');
  }

  /**
   * Returns whether travel time tools are enabled for exact addresses.
   */
  public function isTravelEnabled(): bool {
    return $this->isEnabled() && (bool) $this->config()->get('travel_enabled');
  }

  /**
   * Returns map settings for drupalSettings.psOfferMap.map.
   *
   * @return array<string, mixed>
   *   Client-side map configuration keyed for JavaScript.
   */
  public function getClientMapSettings(NodeInterface $node): array {
    $config = $this->config();
    $locality = '';
    if ($node->hasField('field_address') && !$node->get('field_address')->isEmpty()) {
      $locality = trim((string) ($node->get('field_address')->first()->locality ?? ''));
    }

    return [
      'poiEnabled' => $this->isPoiEnabled(),
      'poiRadius' => (int) ($config->get('poi_search_radius_m') ?? 800),
      'circleRadius' => (int) ($config->get('circle_radius_m') ?? 2500),
      'circleRadiusLargeCity' => (int) ($config->get('circle_radius_large_cities_m') ?? 1000),
      'zoomExact' => (int) ($config->get('default_zoom_exact') ?? 15),
      'zoomApprox' => (int) ($config->get('default_zoom_approx') ?? 13),
      'zoomApproxLargeCity' => (int) ($config->get('default_zoom_approx_large_city') ?? 14),
      'circleColor' => (string) ($config->get('circle_color') ?? '#00915A'),
      'markerUrl' => $this->getThemeAssetUrl('marker.svg'),
      'markerDrivingUrl' => $this->getThemeAssetUrl('marker-driving.png'),
      'markerTransitUrl' => $this->getThemeAssetUrl('marker-transit.png'),
      'markerWalkingUrl' => $this->getThemeAssetUrl('marker-walking.png'),
      'markerBicyclingUrl' => $this->getThemeAssetUrl('marker-bicycling.png'),
      'largeCities' => $this->getLargeCityLocalities(),
      'isLargeCity' => $this->isLargeCityLocality($locality),
      'poiMarkerUrls' => $this->getPoiMarkerIconUrls(),
      'poiMarkerColors' => $this->getPoiMarkerColors(),
    ];
  }

  /**
   * Returns configured POI filter icons for the offer map component.
   *
   * @return array<string, array{pack: string, id: string}>
   *   Icon pack and id keyed by POI filter value.
   */
  public function getPoiFilterIcons(): array {
    $config = $this->config();
    $icons = [];

    foreach (self::POI_FILTER_ICON_DEFAULTS as $filter => $defaults) {
      $configKey = 'poi_icon_' . $filter;
      $parts = IconIdUtility::resolveParts(
        $config->get($configKey),
        $defaults['pack'],
        $defaults['id'],
      );

      $icons[$filter] = [
        'pack' => $parts['pack'],
        'id' => $parts['id'],
      ];
    }

    return $icons;
  }

  /**
   * Returns marker icon URLs keyed by POI filter value for the map layer.
   *
   * @return array<string, string>
   *   Public icon URLs keyed by POI filter value.
   */
  public function getPoiMarkerIconUrls(): array {
    $config = $this->config();
    $urls = [];

    foreach (self::POI_MARKER_ICON_DEFAULTS as $filter => $defaults) {
      $configKey = 'poi_marker_icon_' . $filter;
      $parts = IconIdUtility::resolveParts(
        $config->get($configKey),
        $defaults['pack'],
        $defaults['id'],
      );

      $urls[$filter] = $this->resolveIconUrl($parts['pack'], $parts['id'], 'poi');
    }

    return $urls;
  }

  /**
   * Returns marker colors keyed by POI filter value for the map layer.
   *
   * @return array<string, string>
   *   Hex colors keyed by POI filter value.
   */
  public function getPoiMarkerColors(): array {
    $config = $this->config();
    $colors = [];

    foreach (self::POI_MARKER_COLOR_DEFAULTS as $filter => $default) {
      $configKey = 'poi_marker_color_' . $filter;
      $value = trim((string) ($config->get($configKey) ?? ''));
      $colors[$filter] = $value !== '' ? $value : $default;
    }

    return $colors;
  }

  /**
   * Returns configured travel mode icons for the offer map component.
   *
   * @return array<string, array{pack: string, id: string}>
   *   Icon pack and id keyed by Google travel mode constant.
   */
  public function getTravelModeIcons(): array {
    $config = $this->config();
    $icons = [];

    foreach (self::TRAVEL_MODE_ICON_DEFAULTS as $mode => $defaults) {
      $configKey = 'travel_mode_icon_' . strtolower($mode);
      $parts = IconIdUtility::resolveParts(
        $config->get($configKey),
        $defaults['pack'],
        $defaults['id'],
      );

      $icons[$mode] = [
        'pack' => $parts['pack'],
        'id' => $parts['id'],
      ];
    }

    return $icons;
  }

  /**
   * Returns config cache tags for the offer map settings.
   *
   * @return array<int, string>
   *   Config cache tags.
   */
  public function getCacheTags(): array {
    return $this->config()->getCacheTags();
  }

  /**
   * Returns normalized large-city locality names from config.
   *
   * @return array<int, string>
   *   Lowercase locality fragments.
   */
  private function getLargeCityLocalities(): array {
    $raw = (string) ($this->config()->get('large_city_localities') ?? '');
    $parts = preg_split('/[\r\n,]+/', $raw) ?: [];

    return array_values(array_filter(array_map(
      static fn (string $value): string => strtolower(trim($value)),
      $parts,
    )));
  }

  /**
   * Checks whether a locality should use large-city map settings.
   */
  private function isLargeCityLocality(string $locality): bool {
    $normalized = strtolower(trim($locality));
    if ($normalized === '') {
      return FALSE;
    }

    foreach ($this->getLargeCityLocalities() as $city) {
      if ($city !== '' && str_contains($normalized, $city)) {
        return TRUE;
      }
    }

    return FALSE;
  }

  /**
   * Returns the immutable offer map settings config object.
   */
  private function config(): ImmutableConfig {
    return $this->configFactory->get('ps_offer.map_settings');
  }

  /**
   * Returns a public URL for a map asset shipped with ps_theme.
   */
  private function getThemeAssetUrl(string $filename): string {
    return '/' . $this->extensionPathResolver->getPath('theme', 'ps_theme') . '/assets/images/map/' . $filename;
  }

  /**
   * Resolves a UI Icon pack/id pair to a public asset URL.
   */
  private function resolveIconUrl(string $pack, string $id, string $group = 'ad'): string {
    $icon = $this->iconPackManager->getIcon($pack . ':' . $id);
    $source = $icon?->getSource();
    if (is_string($source) && $source !== '') {
      return $source;
    }

    return '/' . $this->extensionPathResolver->getPath('theme', 'ui_suite_bnp')
      . '/assets/icons/custom/' . $group . '/' . $id . '.svg';
  }

}
