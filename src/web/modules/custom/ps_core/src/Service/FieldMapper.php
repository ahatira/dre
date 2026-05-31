<?php

declare(strict_types=1);

namespace Drupal\ps_core\Service;

final class FieldMapper {

  public function mapValue(string $fieldType, mixed $value): mixed {
    return match ($fieldType) {
      'string' => $this->mapString($value),
      'decimal' => $this->mapDecimal($value),
      'boolean' => $this->mapBoolean($value),
      default => $value,
    };
  }

  private function mapString(mixed $value): ?string {
    if ($value === NULL) {
      return NULL;
    }
    $normalized = trim((string) $value);
    return $normalized === '' ? NULL : $normalized;
  }

  private function mapDecimal(mixed $value): ?float {
    if ($value === NULL || $value === '') {
      return NULL;
    }
    if (!is_numeric($value)) {
      return NULL;
    }
    return (float) $value;
  }

  private function mapBoolean(mixed $value): bool {
    if (is_bool($value)) {
      return $value;
    }

    return in_array(mb_strtolower((string) $value), ['1', 'true', 'yes', 'oui'], TRUE);
  }

}
