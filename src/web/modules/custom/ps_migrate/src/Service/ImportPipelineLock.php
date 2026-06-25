<?php

declare(strict_types=1);

namespace Drupal\ps_migrate\Service;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\State\StateInterface;

/**
 * Site-wide lock preventing concurrent CRM import pipeline runs.
 */
final class ImportPipelineLock {

  private const STATE_KEY = 'ps_migrate.import_pipeline.lock';

  private const TTL_SECONDS = 7200;

  public function __construct(
    private readonly StateInterface $state,
    private readonly TimeInterface $time,
  ) {}

  /**
   * Attempts to acquire the import lock.
   */
  public function acquire(string $filename, int $importRunId = 0): bool {
    $existing = $this->getLock();
    if ($existing !== NULL && !$this->isExpired($existing)) {
      return FALSE;
    }

    $now = $this->time->getRequestTime();
    $this->state->set(self::STATE_KEY, [
      'filename' => $filename,
      'import_run_id' => $importRunId,
      'started' => $now,
      'expires' => $now + self::TTL_SECONDS,
    ]);

    return TRUE;
  }

  /**
   * Updates the lock with the import run ID after entity creation.
   */
  public function attachImportRunId(int $importRunId): void {
    $lock = $this->getLock();
    if ($lock === NULL) {
      return;
    }
    $lock['import_run_id'] = $importRunId;
    $this->state->set(self::STATE_KEY, $lock);
  }

  /**
   * Releases the import lock.
   */
  public function release(): void {
    $this->state->delete(self::STATE_KEY);
  }

  /**
   * Returns TRUE when a non-expired lock is held.
   */
  public function isLocked(): bool {
    $lock = $this->getLock();
    return $lock !== NULL && !$this->isExpired($lock);
  }

  /**
   * Returns TRUE when a lock exists but has expired.
   */
  public function isStale(): bool {
    $lock = $this->getLock();
    return $lock !== NULL && $this->isExpired($lock);
  }

  /**
   * Force-releases an expired or active lock.
   */
  public function forceRelease(): void {
    $this->release();
  }

  /**
   * @return array<string, mixed>|null
   *   Active lock payload.
   */
  public function getLock(): ?array {
    $lock = $this->state->get(self::STATE_KEY);
    return is_array($lock) ? $lock : NULL;
  }

  /**
   * @param array<string, mixed> $lock
   */
  private function isExpired(array $lock): bool {
    return (int) ($lock['expires'] ?? 0) < $this->time->getRequestTime();
  }

}
