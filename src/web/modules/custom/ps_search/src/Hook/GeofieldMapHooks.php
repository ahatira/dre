<?php

declare(strict_types=1);

namespace Drupal\ps_search\Hook;

use Drupal\Core\Extension\ThemeExtensionList;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Url;
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

  public function __construct(
    private readonly SearchMapMarkerBuilder $markerBuilder,
    private readonly ThemeExtensionList $themeExtensionList,
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

    // Fit map to all markers instead of a fixed France-wide zoom.
    $js_settings['map_settings']['map_center']['center_force'] = FALSE;
    $js_settings['map_settings']['map_zoom_and_pan']['zoom']['force'] = FALSE;

    $clusterIcon = $this->clusterIconUrl();
    if ($clusterIcon === '') {
      return;
    }

    $js_settings['map_settings']['map_markercluster']['markercluster_additional_options'] = (string) json_encode([
      'styles' => [
        [
          'url' => $clusterIcon,
          'height' => 36,
          'width' => 36,
          'textColor' => '#ffffff',
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

  /**
   * Returns the absolute URL of the BNPPRE cluster marker icon.
   */
  private function clusterIconUrl(): string {
    $path = $this->themeExtensionList->getPath('ps_theme') . '/assets/images/map/marker-cluster.svg';
    if (!is_readable($path)) {
      return '';
    }

    return Url::fromUri('base:' . $path, ['absolute' => TRUE])->toString();
  }

}
