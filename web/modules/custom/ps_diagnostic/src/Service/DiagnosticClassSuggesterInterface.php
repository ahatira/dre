<?php

declare(strict_types=1);

namespace Drupal\ps_diagnostic\Service;

/**
 * Interface for diagnostic class suggester service.
 *
 * Provides intelligent suggestions for next diagnostic class values based on
 * existing patterns (alphabetical sequence, color progression, range increments).
 */
interface DiagnosticClassSuggesterInterface {

  /**
   * Suggests the next class based on existing classes.
   *
   * Analyzes existing classes to predict the next label, color, and range_max.
   * Handles special cases:
   * - First class: suggests 'A' with standard green
   * - Alphabetical progression: A → B → C
   * - Plus suffix progression: G+ → G++ → G+++
   * - Minus suffix progression: A- → A-- → A---
   * - Standard DPE colors for A-G
   * - Color interpolation beyond G.
   *
   * @param array $existingClasses
   *   Existing classes array keyed by code (lowercase label).
   *   Each class should have 'label', 'color', 'range_max' keys.
   *
   * @return array
   *   Suggested class with keys:
   *   - 'label' (string): Next class label (e.g., 'B', 'G++', 'A--')
   *   - 'color' (string): Hex color code (e.g., '#8DC63F')
   *   - 'range_max' (int|null): NULL for new last class
   *
   * @example
   * @code
   * $suggestion = $suggester->suggestNextClass([
   *   'a' => ['label' => 'A', 'color' => '#00A651', 'range_max' => 70],
   *   'b' => ['label' => 'B', 'color' => '#8DC63F', 'range_max' => NULL],
   * ]);
   * // Returns: ['label' => 'C', 'color' => '#FFF200', 'range_max' => NULL]
   * @endcode
   */
  public function suggestNextClass(array $existingClasses): array;

}
