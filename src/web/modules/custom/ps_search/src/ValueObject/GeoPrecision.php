<?php

declare(strict_types=1);

namespace Drupal\ps_search\ValueObject;

/**
 * Precision level of a resolved geo context.
 */
enum GeoPrecision: string {

  case Exact = 'exact';
  case Approximate = 'approximate';
  case Admin = 'admin';

}
