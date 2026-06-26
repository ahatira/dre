<?php

declare(strict_types=1);

namespace Drupal\ps_migrate\Service;

use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate\Row;

/**
 * Projects CRM XML migration rows into entity field snapshots.
 */
final class CrmXmlSnapshotMigrationProjector {

  public function __construct(
    private readonly CrmXmlSnapshotDestinationNormalizer $destinationNormalizer,
  ) {}

  /**
   * Builds field snapshots for a completed CRM XML migration.
   *
   * @return array<string, array<string, mixed>>
   *   Entity field values keyed by snapshot lookup key.
   */
  public function buildFieldSnapshots(MigrationInterface $migration): array {
    $migrationId = $migration->id();
    if (!in_array($migrationId, [
      'ps_offer_from_xml',
      'ps_agent_from_xml',
      'ps_media_from_xml',
      'ps_media_virtual_tour_from_xml',
    ], TRUE)) {
      return [];
    }

    $processor = new CrmXmlSnapshotRowProcessor($migration);
    $source = $migration->getSourcePlugin();
    $source->rewind();

    $snapshots = [];
    while ($source->valid()) {
      $sourceRow = $source->current();
      if (!is_array($sourceRow)) {
        $source->next();
        continue;
      }

      $row = new Row($sourceRow, $source->getIds());
      $destination = $processor->buildDestinationValues($row);
      if ($destination === []) {
        $source->next();
        continue;
      }

      $key = $this->buildSnapshotKey($migrationId, $sourceRow, $destination);
      if ($key !== '') {
        $snapshots[$key] = $this->destinationNormalizer->normalize($destination);
      }

      $source->next();
    }

    return $snapshots;
  }

  /**
   * Builds the lookup key used by post-import snapshot subscribers.
   *
   * @param array<string, mixed> $sourceRow
   * @param array<string, mixed> $destination
   */
  private function buildSnapshotKey(string $migrationId, array $sourceRow, array $destination): string {
    return match ($migrationId) {
      'ps_offer_from_xml' => trim((string) ($sourceRow['business_id'] ?? $destination['field_business_id'] ?? '')),
      'ps_agent_from_xml' => trim((string) ($sourceRow['uid'] ?? $destination['field_business_id'] ?? '')),
      'ps_media_from_xml', 'ps_media_virtual_tour_from_xml' => $this->buildMediaSnapshotKey($sourceRow),
      default => '',
    };
  }

  /**
   * @param array<string, mixed> $sourceRow
   */
  private function buildMediaSnapshotKey(array $sourceRow): string {
    $businessId = trim((string) ($sourceRow['business_id_parent'] ?? ''));
    $order = (int) ($sourceRow['order'] ?? 0);
    if ($businessId === '' || $order <= 0) {
      return '';
    }

    return $businessId . ':' . $order;
  }

}
