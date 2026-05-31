<?php

declare(strict_types=1);

namespace Drupal\entity_browser_generic_embed\Form;

/**
 * Shared helpers for bulk creation forms.
 */
trait BulkCreationEntityFormTrait {

  /**
   * Normalizes newline-separated user input into rows.
   */
  protected function normalizeRows(string $input): array {
    $rows = preg_split('/\R+/', trim($input)) ?: [];
    return array_values(array_filter(array_map('trim', $rows), static fn(string $row): bool => $row !== ''));
  }

}
