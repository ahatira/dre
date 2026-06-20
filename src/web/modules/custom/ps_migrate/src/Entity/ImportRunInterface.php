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

}
