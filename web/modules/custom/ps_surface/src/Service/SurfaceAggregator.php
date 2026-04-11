<?php

declare(strict_types=1);

namespace Drupal\ps_surface\Service;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\ps_surface\Plugin\Field\FieldType\SurfaceItem;

/**
 * Default surface aggregator.
 */
final class SurfaceAggregator implements SurfaceAggregatorInterface {

  /**
   * Constructs the aggregator.
   */
  public function __construct(private readonly SurfaceValidatorInterface $validator) {
  }

  /**
   * {@inheritdoc}
   */
  public function sum(iterable $items): float {
    $total = 0.0;

    foreach ($items as $item) {
      // @phpstan-ignore instanceof.alwaysTrue
      if (!($item instanceof SurfaceItem)) {
        continue;
      }

      $errors = $this->validator->validateItem($item);
      if (!empty($errors)) {
        continue;
      }

      $value = $item->getValue();
      if ($value !== NULL) {
        $total += $value;
      }
    }

    return $total;
  }

  /**
   * {@inheritdoc}
   */
  public function sumField(FieldItemListInterface $field): float {
    return $this->sum($field);
  }

  /**
   * {@inheritdoc}
   */
  public function sumRows(array $rows): float {
    $total = 0.0;

    foreach ($rows as $row) {
      $errors = $this->validator->validateRow($row);
      if (!empty($errors)) {
        continue;
      }

      $value = $row['value'] ?? NULL;
      if ($value !== NULL && is_numeric($value)) {
        $total += (float) $value;
      }
    }

    return $total;
  }

}
