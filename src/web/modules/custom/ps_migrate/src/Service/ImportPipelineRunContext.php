<?php

declare(strict_types=1);

namespace Drupal\ps_migrate\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\ps_migrate\Entity\ImportRunInterface;

/**
 * Holds per-run pipeline context for migrate process plugins.
 */
final class ImportPipelineRunContext {

  private ?string $mode = NULL;

  private ?int $importRunId = NULL;

  public function __construct(
    private readonly ConfigFactoryInterface $configFactory,
  ) {}

  /**
   * Starts tracking context for a pipeline run.
   */
  public function begin(string $mode, int $importRunId): void {
    $this->mode = in_array($mode, [ImportRunInterface::MODE_FULL, ImportRunInterface::MODE_DELTA], TRUE)
      ? $mode
      : ImportRunInterface::MODE_FULL;
    $this->importRunId = $importRunId;
  }

  /**
   * Clears the current run context.
   */
  public function clear(): void {
    $this->mode = NULL;
    $this->importRunId = NULL;
  }

  /**
   * Whether a pipeline run is currently active.
   */
  public function isActive(): bool {
    return $this->importRunId !== NULL;
  }

  /**
   * Returns the active import run ID, if any.
   */
  public function getImportRunId(): ?int {
    return $this->importRunId;
  }

  /**
   * Returns the active import mode, if any.
   */
  public function getMode(): ?string {
    return $this->mode;
  }

  /**
   * Whether the current run is a delta import.
   */
  public function isDelta(): bool {
    return $this->mode === ImportRunInterface::MODE_DELTA;
  }

  /**
   * Whether unchanged offers should be skipped for the current run.
   */
  public function shouldSkipUnchangedOffers(): bool {
    if (!$this->isDelta()) {
      return FALSE;
    }

    return (bool) $this->configFactory
      ->get('ps_migrate.import_pipeline_settings')
      ->get('skip_unchanged_offers');
  }

}
