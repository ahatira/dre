<?php

declare(strict_types=1);

namespace Drupal\ps_surface\Service;

use Drupal\Core\Field\FieldItemListInterface;

/**
 * Aggregates surface values across items or fields.
 */
interface SurfaceAggregatorInterface {

  /**
   * Sums surface values from an iterable of SurfaceItem objects.
   *
   * @param iterable<int, \Drupal\ps_surface\Plugin\Field\FieldType\SurfaceItem> $items
   *   Items to sum.
   *
   * @return float
   *   Aggregate surface value.
   */
  public function sum(iterable $items): float;

  /**
   * Sums surface values from a field item list.
   *
   * @param \Drupal\Core\Field\FieldItemListInterface $field
   *   Field containing surface items.
   *
   * @return float
   *   Aggregate surface value.
   */
  public function sumField(FieldItemListInterface $field): float;

  /**
   * Validates and sums raw rows.
   *
   * @param array<int, array<string, mixed>> $rows
   *   Raw surface rows.
   *
   * @return float
   *   Aggregate surface value.
   */
  public function sumRows(array $rows): float;

}
