<?php

declare(strict_types=1);

namespace Drupal\ps_migrate\Service;

/**
 * Canonicalizes feature payload defaults for stable comparisons and writes.
 */
final class FeaturePayloadDefaultsNormalizer {

  /**
   * Normalizes a payload defaults value recursively.
   *
   * @param mixed $value
   *   The raw payload defaults value.
   *
   * @return array
   *   A canonicalized payload defaults array.
   */
  public function normalize(mixed $value): array {
    $normalized = $this->normalizeValue($value);
    return is_array($normalized) ? $normalized : [];
  }

  /**
   * Normalizes a value recursively.
   *
   * @param mixed $value
   *   The value to normalize.
   *
   * @return mixed
   *   The normalized value.
   */
  private function normalizeValue(mixed $value): mixed {
    if (is_array($value)) {
      $normalized = [];
      foreach ($value as $key => $item) {
        $candidate = $this->normalizeValue($item);
        if ($this->shouldKeep($candidate)) {
          $normalized[$key] = $candidate;
        }
      }

      if ($this->isList($value)) {
        $normalized = array_values($normalized);
      }
      else {
        ksort($normalized);
      }

      return $normalized;
    }

    if (is_string($value)) {
      $value = trim($value);
      return $value === '' ? NULL : $value;
    }

    if ($value === NULL) {
      return NULL;
    }

    return $value;
  }

  /**
   * Determines whether a value should be kept in the normalized array.
   */
  private function shouldKeep(mixed $value): bool {
    return !($value === NULL || $value === []);
  }

  /**
   * Determines whether the array is list-like.
   */
  private function isList(array $value): bool {
    return array_keys($value) === range(0, count($value) - 1);
  }

}