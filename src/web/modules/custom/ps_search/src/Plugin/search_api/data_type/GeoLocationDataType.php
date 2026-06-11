<?php

declare(strict_types=1);

namespace Drupal\ps_search\Plugin\search_api\data_type;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\search_api\Attribute\SearchApiDataType;
use Drupal\search_api\DataType\DataTypePluginBase;

/**
 * Provides a latitude/longitude location data type for Solr geodist sorting.
 */
#[SearchApiDataType(
  id: 'location',
  label: new TranslatableMarkup('Latitude/Longitude'),
  description: new TranslatableMarkup('Geo point for Solr spatial search and distance sort.'),
)]
final class GeoLocationDataType extends DataTypePluginBase {

  /**
   * {@inheritdoc}
   */
  public function getValue($value) {
    if (is_string($value) && preg_match('#^[-+]?[\d.]+,[-+]?[\d.]+$#', $value)) {
      return $value;
    }

    $matches = [];
    if (is_string($value) && preg_match('#point\((?P<lon>[+-]?[\d.]+)\s+(?P<lat>[+-]?[\d.]+)\)#i', $value, $matches)) {
      return $matches['lat'] . ',' . $matches['lon'];
    }

    return $value;
  }

}
