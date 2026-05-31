<?php

declare(strict_types=1);

namespace Drupal\entity_browser_generic_embed;

/**
 * Helpers for extension-based file input matching.
 */
trait FileInputExtensionMatchTrait {

  /**
   * Checks whether input filename has one of allowed extensions.
   */
  protected function hasAllowedExtension(string $input, array $extensions): bool {
    $extension = strtolower((string) pathinfo($input, PATHINFO_EXTENSION));
    return $extension !== '' && in_array($extension, array_map('strtolower', $extensions), TRUE);
  }

}
