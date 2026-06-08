<?php

declare(strict_types=1);

namespace Drupal\ps_search\Hook;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\geofield_map\Plugin\views\style\GeofieldGoogleMapViewStyle;
use Drupal\node\NodeInterface;
use Drupal\ps_search\Service\SearchMapMarkerBuilder;
use Drupal\Core\Entity\Plugin\DataType\EntityAdapter;
use Drupal\views\Plugin\views\row\RowPluginBase;
use Drupal\views\ResultRow;

/**
 * Geofield Map customizations for the property search page.
 */
final class GeofieldMapHooks {

  /**
   * Transparent 1×1 pixel — MarkerClusterer requires a styles[].url; CSS draws the circle.
   */
  private const CLUSTER_ICON_TRANSPARENT = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';

  public function __construct(
    private readonly SearchMapMarkerBuilder $markerBuilder,
  ) {}

  /**
   * Implements hook_geofield_map_views_feature_alter().
   */
  #[Hook('geofield_map_views_feature_alter')]
  public function viewsFeatureAlter(array &$feature, ResultRow $row, ?RowPluginBase $rowPlugin = NULL): void {
    if (!$this->isSearchMapContext($rowPlugin)) {
      return;
    }

    if (($feature['geometry']->type ?? '') !== 'Point') {
      return;
    }

    $entity = $this->resolveOfferEntity($row);
    if (!$entity instanceof NodeInterface) {
      return;
    }

    $label = $this->markerBuilder->buildPriceLabel($entity);
    $feature['properties']['icon'] = $this->markerBuilder->buildPriceMarkerDataUri($label);
    $feature['properties']['ps_search_price'] = $label;
    $feature['properties']['ps_search_nid'] = (string) $entity->id();
  }

  /**
   * Implements hook_geofield_map_googlemap_view_style_alter().
   */
  #[Hook('geofield_map_googlemap_view_style_alter')]
  public function googlemapViewStyleAlter(array &$js_settings, GeofieldGoogleMapViewStyle $view_style): void {
    if ($view_style->view->id() !== 'ps_search_offers' || $view_style->view->current_display !== 'map_attachment') {
      return;
    }

    $js_settings['map_settings']['map_markercluster']['markercluster_control'] = TRUE;
    $js_settings['map_settings']['map_center']['center_force'] = FALSE;
    $js_settings['map_settings']['map_zoom_and_pan']['zoom']['force'] = FALSE;

    $js_settings['map_settings']['map_markercluster']['markercluster_additional_options'] = (string) json_encode([
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
   * Checks whether the feature belongs to the search map attachment.
   */
  private function isSearchMapContext(?RowPluginBase $rowPlugin): bool {
    if ($rowPlugin === NULL || !isset($rowPlugin->view)) {
      return FALSE;
    }

    return $rowPlugin->view->id() === 'ps_search_offers'
      && $rowPlugin->view->current_display === 'map_attachment';
  }

  /**
   * Resolves the offer node from a Search API views result row.
   */
  private function resolveOfferEntity(ResultRow $row): ?NodeInterface {
    if (!isset($row->_object) || !$row->_object instanceof EntityAdapter) {
      return NULL;
    }

    $entity = $row->_object->getValue();
    if (!$entity instanceof NodeInterface || $entity->bundle() !== 'offer') {
      return NULL;
    }

    return $entity;
  }

}
