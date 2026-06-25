<?php

declare(strict_types=1);

namespace Drupal\ps_migrate\Service;

/**
 * In-memory rollback snapshot for the active CRM import run.
 */
final class ImportRunSnapshotCollector {

  private const int VERSION = 1;

  private ?int $importRunId = NULL;

  private ?string $importMode = NULL;

  /**
   * @var array<int, array{nid: int, business_id: string}>
   */
  private array $offersCreated = [];

  /**
   * @var array<int, array{nid: int, business_id: string, revision_id: int}>
   */
  private array $offersUpdated = [];

  /**
   * @var list<string>
   */
  private array $featureGroupsDeactivated = [];

  /**
   * @var list<string>
   */
  private array $featureGroupsReactivated = [];

  /**
   * @var list<string>
   */
  private array $featureDefinitionsDeactivated = [];

  /**
   * @var list<string>
   */
  private array $featureDefinitionsReactivated = [];

  /**
   * Starts collecting snapshot data for a pipeline run.
   */
  public function begin(int $importRunId, string $importMode): void {
    $this->clear();
    $this->importRunId = $importRunId;
    $this->importMode = $importMode;
  }

  /**
   * Clears the in-memory snapshot.
   */
  public function clear(): void {
    $this->importRunId = NULL;
    $this->importMode = NULL;
    $this->offersCreated = [];
    $this->offersUpdated = [];
    $this->featureGroupsDeactivated = [];
    $this->featureGroupsReactivated = [];
    $this->featureDefinitionsDeactivated = [];
    $this->featureDefinitionsReactivated = [];
  }

  /**
   * Whether snapshot collection is active.
   */
  public function isActive(): bool {
    return $this->importRunId !== NULL;
  }

  /**
   * Records the pre-import revision for an offer update.
   */
  public function stageOfferUpdate(int $nid, int $revisionId, string $businessId): void {
    if (!$this->isActive() || $nid <= 0 || $revisionId <= 0) {
      return;
    }

    if (isset($this->offersCreated[$nid])) {
      unset($this->offersCreated[$nid]);
    }

    if (!isset($this->offersUpdated[$nid])) {
      $this->offersUpdated[$nid] = [
        'nid' => $nid,
        'business_id' => $businessId,
        'revision_id' => $revisionId,
      ];
    }
  }

  /**
   * Records an offer created during the import run.
   */
  public function recordOfferCreated(int $nid, string $businessId): void {
    if (!$this->isActive() || $nid <= 0 || isset($this->offersUpdated[$nid])) {
      return;
    }

    $this->offersCreated[$nid] = [
      'nid' => $nid,
      'business_id' => $businessId,
    ];
  }

  /**
   * Whether an offer update was staged for the given node ID.
   */
  public function hasStagedOfferUpdate(int $nid): bool {
    return isset($this->offersUpdated[$nid]);
  }

  /**
   * Records a feature group status change for rollback.
   */
  public function recordFeatureGroupStatusChange(string $groupId, bool $wasActive, bool $isActive): void {
    if (!$this->isActive() || $groupId === '' || $wasActive === $isActive) {
      return;
    }

    if ($wasActive && !$isActive) {
      $this->featureGroupsDeactivated[] = $groupId;
      return;
    }

    $this->featureGroupsReactivated[] = $groupId;
  }

  /**
   * Records a feature definition status change for rollback.
   */
  public function recordFeatureDefinitionStatusChange(string $definitionId, bool $wasActive, bool $isActive): void {
    if (!$this->isActive() || $definitionId === '' || $wasActive === $isActive) {
      return;
    }

    if ($wasActive && !$isActive) {
      $this->featureDefinitionsDeactivated[] = $definitionId;
      return;
    }

    $this->featureDefinitionsReactivated[] = $definitionId;
  }

  /**
   * Builds the JSON-serializable snapshot payload.
   *
   * @return array<string, mixed>
   */
  public function buildSnapshot(): array {
    return [
      'version' => self::VERSION,
      'import_run_id' => $this->importRunId,
      'import_mode' => $this->importMode,
      'captured_at' => time(),
      'offers' => [
        'created' => array_values($this->offersCreated),
        'updated' => array_values($this->offersUpdated),
      ],
      'features' => [
        'groups_deactivated' => array_values(array_unique($this->featureGroupsDeactivated)),
        'groups_reactivated' => array_values(array_unique($this->featureGroupsReactivated)),
        'definitions_deactivated' => array_values(array_unique($this->featureDefinitionsDeactivated)),
        'definitions_reactivated' => array_values(array_unique($this->featureDefinitionsReactivated)),
      ],
    ];
  }

}
