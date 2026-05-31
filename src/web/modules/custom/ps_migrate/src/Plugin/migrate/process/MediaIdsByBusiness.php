<?php

declare(strict_types=1);

namespace Drupal\ps_migrate\Plugin\migrate\process;

use Drupal\Core\Database\Database;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Resolve media target IDs from ps_media_from_xml map using business ID.
 *
 * @MigrateProcessPlugin(
 *   id = "media_ids_by_business"
 * )
 */
final class MediaIdsByBusiness extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property): array {
    $business_id = trim((string) $value);
    if ($business_id === '') {
      return [];
    }

    $connection = Database::getConnection();
    $schema = $connection->schema();
    $tables = [
      'migrate_map_ps_media_from_xml',
      'migrate_map_ps_media_virtual_tour_from_xml',
    ];

    $rows = [];
    foreach ($tables as $table) {
      if (!$schema->tableExists($table)) {
        continue;
      }

      $query = $connection->select($table, 'm');
      $query->fields('m', ['sourceid2', 'destid1']);
      $query->condition('m.sourceid1', $business_id);
      $rows = array_merge($rows, $query->execute()->fetchAll());
    }

    usort($rows, static function (object $a, object $b): int {
      return ((int) $a->sourceid2) <=> ((int) $b->sourceid2);
    });

    $result = [];
    foreach ($rows as $row_item) {
      $result[] = ['target_id' => (int) $row_item->destid1];
    }

    return $result;
  }

}
