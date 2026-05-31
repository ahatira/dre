<?php

declare(strict_types=1);

namespace Drupal\ps_migrate\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Normalize MEDIA_LIST/MEDIA/ORDER to a sub_process-compatible structure.
 *
 * @MigrateProcessPlugin(
 *   id = "normalize_media_orders"
 * )
 */
final class NormalizeMediaOrders extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property): array {
    if ($value === NULL || $value === '' || $value === []) {
      return [];
    }

    $orders = [];
    if ($value instanceof \SimpleXMLElement) {
      if ($value->count() > 0) {
        foreach ($value as $item) {
          $orders[] = $item;
        }
      }
      else {
        $orders[] = (string) $value;
      }
    }
    else {
      $orders = is_array($value) ? $value : [$value];
    }

    $result = [];
    $business_id = trim((string) ($row->getSourceProperty('business_id') ?? ''));

    foreach ($orders as $order) {
      if (is_array($order)) {
        // Some parsers may expose a nested structure for repeated elements.
        $order = $order['#text'] ?? reset($order);
      }
      if ($order === NULL) {
        continue;
      }

      $order = trim((string) $order);
      if ($order === '') {
        continue;
      }

      $normalized_order = is_numeric($order) ? (int) $order : $order;
      $result[] = [
        'business_id' => $business_id,
        'order' => $normalized_order,
      ];
    }

    return $result;
  }

}
