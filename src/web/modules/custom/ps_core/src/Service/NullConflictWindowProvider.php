<?php

declare(strict_types=1);

namespace Drupal\ps_core\Service;

/**
 * Default provider: checksum mismatch always reports a conflict.
 */
final class NullConflictWindowProvider implements ConflictWindowProviderInterface {

  /**
   * {@inheritdoc}
   */
  public function getConflictWindowSeconds(): int {
    return 0;
  }

}
