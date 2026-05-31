<?php

declare(strict_types=1);

namespace Drupal\entity_browser_generic_embed;

/**
 * Contract for matching incoming input payloads.
 */
interface InputMatchInterface {

  /**
   * Returns true when the input matches this strategy.
   */
  public function matches(string $input): bool;

}
