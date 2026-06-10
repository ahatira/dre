<?php

declare(strict_types=1);

namespace Drupal\ps_search\Hook;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\geofield_map\Plugin\views\style\GeofieldGoogleMapViewStyle;
use Drupal\ps_search\Service\SearchMapMarkerBuilder;
use Drupal\views\Plugin\views\row\RowPluginBase;

/**
 * Geofield Map settings and marker icons for the property search page.
 *
 * Markers render via Views map_attachment (geofield_map). Custom JS only adapts
 * clustering (OMS co-located split) and list/map sync behaviours.
 */
final class GeofieldMapHooks {

  /**
   * View display that renders the search map attachment.
   */
  private const SEARCH_MAP_DISPLAY = 'map_attachment';

  /**
   * Transparent 1×1 pixel — MarkerClusterer styles[].url; CSS draws the circle.
   */
  private const CLUSTER_ICON_TRANSPARENT = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';

  public function __construct(
    private readonly SearchMapMarkerBuilder $markerBuilder,
  ) {}

  /**
   * Implements hook_geofield_map_googlemap_view_style_alter().
   */
  #[Hook('geofield_map_googlemap_view_style_alter')]
  public function googlemapViewStyleAlter(array &$js_settings, GeofieldGoogleMapViewStyle $view_style): void {
    if ($view_style->view->id() !== 'ps_search_offers'
      || $view_style->view->current_display !== self::SEARCH_MAP_DISPLAY) {
      return;
    }

    $js_settings['map_settings']['map_markercluster']['markercluster_control'] = TRUE;
    $js_settings['map_settings']['map_center']['center_force'] = FALSE;
    $js_settings['map_settings']['map_zoom_and_pan']['zoom']['force'] = FALSE;

    $js_settings['map_settings']['map_markercluster']['markercluster_additional_options'] = (string) json_encode([
      'minimumClusterSize' => 2,
      'maxZoom' => 18,
      'styles' => [
        [
          'url' => self::CLUSTER_ICON_TRANSPARENT,
          'height' => 36,
          'width' => 36,
          'textColor' => '#00915A',
          'textSize' => '13',
          'fontWeight' => 'bold',
        ],
      ],
    ], JSON_UNESCAPED_SLASHES);
  }

  /**
   * Implements hook_geofield_map_views_feature_alter().
   */
  #[Hook('geofield_map_views_feature_alter')]
  public function geofieldMapViewsFeatureAlter(array &$feature, mixed $result, RowPluginBase $row_plugin): void {
    $view = $row_plugin->view ?? NULL;
    if ($view === NULL || $view->id() !== 'ps_search_offers' || $view->current_display !== self::SEARCH_MAP_DISPLAY) {
      return;
    }

    if (!isset($feature['geometry']) || ($feature['geometry']->type ?? '') !== 'Point') {
      return;
    }

    $rowData = $feature['properties']['data'] ?? [];
    $budgetRaw = $this->stripRenderedField($rowData['field_budget_value'] ?? '');
    $currencyRaw = $this->stripRenderedField($rowData['field_budget_currency'] ?? '');
    $label = $this->markerBuilder->buildPriceLabelFromValues($budgetRaw, $currencyRaw);

    $feature['properties']['icon'] = $this->markerBuilder->buildPriceMarkerDataUri($label);
    $feature['properties']['tooltip'] = $label;
    $feature['properties']['ps_search_price'] = $label;
    $feature['properties']['ps_search_nid'] = (string) ($feature['properties']['entity_id'] ?? '');
  }

  /**
   * Normalizes a rendered Views field value to plain text.
   */
  private function stripRenderedField(mixed $value): string {
    if (!is_string($value) || $value === '') {
      return '';
    }

    return trim(html_entity_decode(strip_tags($value), ENT_QUOTES | ENT_HTML5));
  }

}
