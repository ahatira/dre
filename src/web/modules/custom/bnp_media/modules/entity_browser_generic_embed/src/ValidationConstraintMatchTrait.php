<?php

declare(strict_types=1);

namespace Drupal\entity_browser_generic_embed;

/**
 * Helpers for lightweight validation checks.
 */
trait ValidationConstraintMatchTrait {

  /**
   * Returns true when all callbacks return true.
   */
  protected function allConstraintsPass(string $input, array $callbacks): bool {
    foreach ($callbacks as $callback) {
      if (is_callable($callback) && !$callback($input)) {
        return FALSE;
      }
    }
    return TRUE;
  }

}
