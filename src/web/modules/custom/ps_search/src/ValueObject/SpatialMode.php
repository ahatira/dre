<?php

declare(strict_types=1);

namespace Drupal\ps_search\ValueObject;

/**
 * Spatial filtering strategy applied to a search query.
 */
enum SpatialMode: string {

  case None = 'none';
  case BboxAndPostal = 'bbox_and_postal';
  case Geofilt = 'geofilt';
  case Viewport = 'viewport';
  case Isochrone = 'isochrone';

}
