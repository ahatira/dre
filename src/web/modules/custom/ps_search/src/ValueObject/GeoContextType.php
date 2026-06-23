<?php

declare(strict_types=1);

namespace Drupal\ps_search\ValueObject;

/**
 * Semantic geo context types for search v2.
 */
enum GeoContextType: string {

  case Address = 'address';
  case City = 'city';
  case Postal = 'postal';
  case Department = 'department';
  case Region = 'region';
  case CountryDefault = 'country_default';
  case Coordinates = 'coordinates';

}
