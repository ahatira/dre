<?php

declare(strict_types=1);

namespace Drupal\ps_migrate\Service;

use Drupal\migrate\MigrateExecutable;
use Drupal\migrate\MigrateSkipRowException;
use Drupal\migrate\Row;

/**
 * Runs migrate process pipelines without persisting destination entities.
 */
final class CrmXmlSnapshotRowProcessor extends MigrateExecutable {

  /**
   * Processes one source row through the migration pipeline.
   *
   * @return array<string, mixed>
   *   Destination property values keyed by destination field name.
   */
  public function buildDestinationValues(Row $row): array {
    $pipeline = $this->migration->getProcessPlugins();

    try {
      foreach ($pipeline as $destinationProperty => $plugins) {
        if (!is_string($destinationProperty) || str_starts_with($destinationProperty, '_')) {
          continue;
        }
        $this->processPipeline($row, $destinationProperty, $plugins, NULL);
      }
    }
    catch (MigrateSkipRowException) {
      return [];
    }

    return $row->getDestination();
  }

}
