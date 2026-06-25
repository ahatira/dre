<?php

declare(strict_types=1);

namespace Drupal\ps_migrate\Entity;

use Drupal\Core\Entity\ContentEntityInterface;

/**
 * CRM XML import run record.
 */
interface ImportRunInterface extends ContentEntityInterface {

  public const STATUS_PENDING = 'pending';

  public const STATUS_PROCESSING = 'processing';

  public const STATUS_SUCCESS = 'success';

  public const STATUS_FAILED = 'failed';

  public const MODE_FULL = 'full';

  public const MODE_DELTA = 'delta';

  public const ROLLBACK_NONE = 'none';

  public const ROLLBACK_ROLLED_BACK = 'rolled_back';

  public const ROLLBACK_PARTIAL = 'partial';

  public const ROLLBACK_UNAVAILABLE = 'unavailable';

  /**
   * Gets the source XML filename.
   */
  public function getFilename(): string;

  /**
   * Gets the pipeline status.
   */
  public function getPipelineStatus(): string;

  /**
   * Gets import mode (full or delta).
   */
  public function getImportMode(): string;

  /**
   * Gets decoded stats array.
   *
   * @return array<string, mixed>
   */
  public function getStats(): array;

  /**
   * Gets human-readable messages.
   */
  public function getMessages(): string;

  /**
   * Gets decoded snapshot array for rollback.
   *
   * @return array<string, mixed>
   */
  public function getSnapshot(): array;

  /**
   * Gets rollback status.
   */
  public function getRollbackStatus(): string;

  /**
   * Gets stored duration in milliseconds, or 0 when unavailable.
   */
  public function getDurationMs(): int;

}
