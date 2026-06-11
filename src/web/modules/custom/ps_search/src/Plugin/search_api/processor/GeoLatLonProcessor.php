<?php

declare(strict_types=1);

namespace Drupal\ps_search\Plugin\search_api\processor;

use Drupal\node\NodeInterface;
use Drupal\search_api\Datasource\DatasourceInterface;
use Drupal\search_api\IndexInterface;
use Drupal\search_api\Item\ItemInterface;
use Drupal\search_api\Processor\ProcessorPluginBase;
use Drupal\search_api\Processor\ProcessorProperty;

/**
 * Adds decimal latitude/longitude fields from offer geofield data.
 *
 * @SearchApiProcessor(
 *   id = "ps_geo_lat_lon_processor",
 *   label = @Translation("Offer geo latitude/longitude"),
 *   description = @Translation("Exposes field_geo_lat and field_geo_lng for map zone filtering."),
 *   stages = {
 *     "add_properties" = 0,
 *   }
 * )
 */
final class GeoLatLonProcessor extends ProcessorPluginBase {

  /**
   * {@inheritdoc}
   */
  public static function supportsIndex(IndexInterface $index): bool {
    foreach ($index->getDatasources() as $datasource) {
      if ($datasource->getEntityTypeId() === 'node') {
        return TRUE;
      }
    }
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function getPropertyDefinitions(?DatasourceInterface $datasource = NULL): array {
    if ($datasource !== NULL) {
      return [];
    }

    return [
      'field_geo_lat' => new ProcessorProperty([
        'label' => $this->t('Geo latitude'),
        'description' => $this->t('Latitude from field_geo.'),
        'type' => 'decimal',
        'processor_id' => $this->getPluginId(),
        'is_list' => FALSE,
      ]),
      'field_geo_lng' => new ProcessorProperty([
        'label' => $this->t('Geo longitude'),
        'description' => $this->t('Longitude from field_geo.'),
        'type' => 'decimal',
        'processor_id' => $this->getPluginId(),
        'is_list' => FALSE,
      ]),
      'field_geo_point' => new ProcessorProperty([
        'label' => $this->t('Geo point'),
        'description' => $this->t('Lat/lon point for Solr distance sort.'),
        'type' => 'location',
        'processor_id' => $this->getPluginId(),
        'is_list' => FALSE,
      ]),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function addFieldValues(ItemInterface $item): void {
    $entity = $item->getOriginalObject()->getValue();
    if (!$entity instanceof NodeInterface || $entity->bundle() !== 'offer') {
      return;
    }
    if (!$entity->hasField('field_geo') || $entity->get('field_geo')->isEmpty()) {
      return;
    }

    $value = $entity->get('field_geo')->first()?->getValue();
    $lat = $value['lat'] ?? NULL;
    $lng = $value['lon'] ?? ($value['lng'] ?? NULL);
    if (!is_numeric($lat) || !is_numeric($lng)) {
      return;
    }

    $fields = $item->getFields(FALSE);

    foreach ($this->getFieldsHelper()->filterForPropertyPath($fields, NULL, 'field_geo_lat') as $field) {
      $field->addValue((float) $lat);
    }
    foreach ($this->getFieldsHelper()->filterForPropertyPath($fields, NULL, 'field_geo_lng') as $field) {
      $field->addValue((float) $lng);
    }
    foreach ($this->getFieldsHelper()->filterForPropertyPath($fields, NULL, 'field_geo_point') as $field) {
      $field->addValue((float) $lat . ',' . (float) $lng);
    }
  }

}
