<?php

declare(strict_types=1);

namespace Drupal\ps_diagnostic\Service;

/**
 * Interface for diagnostic class calculator service.
 */
interface DiagnosticClassCalculatorInterface {

  /**
   * Calculates the energy class from a numeric value.
   *
   * @param string $typeId
   *   The diagnostic ID (e.g., 'dpe', 'ges').
   * @param float $value
   *   The numeric value to classify.
   *
   * @return string|null
   *   The class code (A-G) or NULL if invalid.
   */
  public function calculateClass(string $typeId, float $value): ?string;

  /**
   * Gets display information for a diagnostic.
   *
   * @param array<string, mixed> $diagnosticData
   *   Diagnostic data array.
   *
   * @return array<string, mixed>
   *   Display info with keys: class, color, unit, display_text, is_special.
   */
  public function getDisplayInfo(array $diagnosticData): array;

}
