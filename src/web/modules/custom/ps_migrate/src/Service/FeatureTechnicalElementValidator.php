<?php

declare(strict_types=1);

namespace Drupal\ps_migrate\Service;

/**
 * Validates normalized technical element records for feature migrations.
 */
final class FeatureTechnicalElementValidator {

  /**
   * Validates a normalized technical element record.
   *
   * @param array $record
   *   Normalized record from the parser/source mapping.
   *
   * @return array{errors: array<int, array{code: string, message: string}>, warnings: array<int, array{code: string, message: string}>}
   *   Structured validation messages.
   */
  public function validate(array $record): array {
    $errors = [];
    $warnings = [];

    $group_code = trim((string) ($record['group_code'] ?? ''));
    $feature_code = trim((string) ($record['feature_code'] ?? ''));
    $definition_id = trim((string) ($record['definition_id'] ?? ''));
    $type_driver = trim((string) ($record['type_driver'] ?? ''));

    if ($group_code === '') {
      $errors[] = $this->issue('missing_group_code', 'Missing feature group code.');
    }
    elseif (preg_match('/^[A-Z0-9_]+$/', $group_code) !== 1) {
      $warnings[] = $this->issue('group_code_non_canonical', 'Feature group code is not canonical uppercase underscore format.');
    }

    if ($feature_code === '') {
      $errors[] = $this->issue('missing_feature_code', 'Missing feature code.');
    }
    elseif (preg_match('/^[A-Z0-9_]+$/', $feature_code) !== 1) {
      $warnings[] = $this->issue('feature_code_non_canonical', 'Feature code is not canonical uppercase underscore format.');
    }

    if ($definition_id !== '' && strlen($definition_id) > 128) {
      $errors[] = $this->issue('definition_id_too_long', 'Generated feature definition ID exceeds 128 characters.');
    }

    if ($type_driver === '') {
      $errors[] = $this->issue('missing_type_driver', 'Missing type driver.');
    }

    $payload = is_array($record['payload'] ?? NULL) ? $record['payload'] : [];
    $value = $payload['value'] ?? NULL;
    $unit = $payload['unit'] ?? NULL;

    if ($type_driver === 'numeric' && $value !== NULL) {
      $numeric_candidate = str_replace(',', '.', trim((string) $value));
      if ($numeric_candidate === '' || !is_numeric($numeric_candidate)) {
        $warnings[] = $this->issue('numeric_value_not_numeric', 'Numeric driver has a non-numeric value payload.');
      }
    }

    if ($type_driver === 'flag' && $value !== NULL && trim((string) $value) !== '') {
      $warnings[] = $this->issue('flag_has_scalar_value', 'Flag driver received a scalar value; presence will be inferred.');
    }

    if ($unit !== NULL && strlen(trim((string) $unit)) > 32) {
      $warnings[] = $this->issue('unit_too_long', 'Feature unit is longer than 32 characters.');
    }

    return [
      'errors' => $errors,
      'warnings' => $warnings,
    ];
  }

  /**
   * Builds a structured issue entry.
   */
  private function issue(string $code, string $message): array {
    return [
      'code' => $code,
      'message' => $message,
    ];
  }

}