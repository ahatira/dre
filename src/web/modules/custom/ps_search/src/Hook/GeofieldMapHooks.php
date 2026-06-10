<?php

declare(strict_types=1);

namespace Drupal\ps_search\Hook;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\geofield_map\Plugin\views\style\GeofieldGoogleMapViewStyle;

/**
 * Geofield Map shell settings for the property search page.
 *
 * Markers are loaded client-side via /ps-search/markers (Phase 4.1).
 * This hook only configures cluster/center behaviour on the empty map shell.
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

}
