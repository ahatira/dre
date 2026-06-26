<?php

declare(strict_types=1);

namespace Drupal\ps_core\Service;

/**
 * Provides the import conflict detection window for entity protection.
 */
interface ConflictWindowProviderInterface {

  /**
   * Returns the conflict detection window in seconds.
   *
   * 0 means any checksum mismatch is reported as a conflict.
   */
  public function getConflictWindowSeconds(): int;

}
