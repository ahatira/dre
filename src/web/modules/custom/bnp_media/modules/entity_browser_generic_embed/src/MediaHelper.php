<?php

declare(strict_types=1);

namespace Drupal\entity_browser_generic_embed;

/**
 * Utility helpers for media selection workflows.
 */
final class MediaHelper {

  /**
   * Normalizes entity IDs list from mixed payloads.
   *
   * @param mixed $raw
   *   Input payload.
   *
   * @return array<int, string>
   *   Normalized IDs.
   */
  public function normalizeEntityIds(mixed $raw): array {
    if (is_string($raw)) {
      $raw = preg_split('/\s+/', trim($raw)) ?: [];
    }
    if (!is_array($raw)) {
      return [];
    }
    return array_values(array_filter(array_map('strval', $raw), static fn(string $id): bool => $id !== ''));
  }

}
