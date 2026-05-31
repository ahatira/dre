<?php

declare(strict_types=1);

namespace Drupal\entity_browser_generic_embed;

/**
 * Utility helper for non-destructive override merges.
 */
final class OverrideHelper {

  /**
   * Merges override values onto defaults.
   */
  public function merge(array $defaults, array $overrides): array {
    return array_replace_recursive($defaults, $overrides);
  }

}
