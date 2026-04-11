<?php

declare(strict_types=1);

namespace Drupal\ps_diagnostic\Service;

/**
 * Service to suggest next diagnostic class values intelligently.
 *
 * Analyzes existing classes to predict the next label, color, and range_max
 * based on patterns (alphabetical sequence, color progression, range increments).
 */
class DiagnosticClassSuggester implements DiagnosticClassSuggesterInterface {

  /**
   * Standard DPE color progression (A to G).
   */
  protected const STANDARD_COLORS = [
    'a' => '#00A651',
    'b' => '#8DC63F',
    'c' => '#FFF200',
    'd' => '#F7941D',
    'e' => '#ED1C24',
    'f' => '#C1272D',
    'g' => '#A10D0D',
  ];

  /**
   * Suggests the next class based on existing classes.
   *
   * @param array $existingClasses
   *   Existing classes array keyed by code.
   *
   * @return array
   *   Suggested class with 'label', 'color', 'range_max' keys.
   */
  public function suggestNextClass(array $existingClasses): array {
    if (empty($existingClasses)) {
      // First class: suggest 'A' with standard green.
      return [
        'label' => 'A',
        'color' => self::STANDARD_COLORS['a'],
        'range_max' => NULL,
      ];
    }

    // Get the last class.
    $lastClass = end($existingClasses);
    $lastLabel = $lastClass['label'] ?? '';
    $lastCode = strtolower($lastLabel);

    // Suggest next alphabetical letter.
    $nextLabel = $this->getNextAlphabeticLabel($lastLabel);
    $nextCode = strtolower($nextLabel);

    // Suggest color based on standard progression or interpolation.
    $nextColor = $this->suggestNextColor($nextCode, $existingClasses);

    // Suggest range_max: NULL for new last class.
    return [
      'label' => $nextLabel,
      'color' => $nextColor,
      'range_max' => NULL,
    ];
  }

  /**
   * Gets the next alphabetical label.
   *
   * Handles special cases:
   * - If label ends with '+' or '-', adds another of the same (G+ -> G++).
   * - Otherwise, increments alphabetically (A -> B, Z -> AA).
   *
   * @param string $currentLabel
   *   Current label (e.g., 'A', 'B', 'G+', 'G++').
   *
   * @return string
   *   Next label (e.g., 'B', 'C', 'G++', 'G+++').
   */
  protected function getNextAlphabeticLabel(string $currentLabel): string {
    if ($currentLabel === '') {
      return 'A';
    }

    $currentLabel = strtoupper($currentLabel);
    $lastChar = substr($currentLabel, -1);

    // If ends with + or -, add another of the same character.
    if ($lastChar === '+' || $lastChar === '-') {
      return $currentLabel . $lastChar;
    }

    // Handle single letter (A-Z).
    if (strlen($currentLabel) === 1) {
      $charCode = ord($currentLabel);
      if ($charCode >= ord('A') && $charCode < ord('Z')) {
        return chr($charCode + 1);
      }
      // After Z, suggest AA.
      return 'AA';
    }

    // For multi-letter labels without +/-, increment the last character.
    // Extract suffix (+/- characters) if any.
    $suffix = '';
    $base = $currentLabel;
    while (strlen($base) > 0 && (substr($base, -1) === '+' || substr($base, -1) === '-')) {
      $suffix = substr($base, -1) . $suffix;
      $base = substr($base, 0, -1);
    }

    if ($base === '') {
      // Edge case: only +/- characters.
      return $currentLabel . $lastChar;
    }

    $lastLetter = substr($base, -1);
    $prefix = substr($base, 0, -1);

    if ($lastLetter === 'Z') {
      // Overflow: AA -> AB, AZ -> BA.
      return $prefix . 'A' . 'A' . $suffix;
    }

    return $prefix . chr(ord($lastLetter) + 1) . $suffix;
  }

  /**
   * Suggests the next color based on existing pattern.
   *
   * @param string $nextCode
   *   Next class code (e.g., 'h', 'i').
   * @param array $existingClasses
   *   Existing classes.
   *
   * @return string
   *   Suggested color (hex format).
   */
  protected function suggestNextColor(string $nextCode, array $existingClasses): string {
    // If next code is in standard colors, use it.
    if (isset(self::STANDARD_COLORS[$nextCode])) {
      return self::STANDARD_COLORS[$nextCode];
    }

    // Check if existing classes follow standard DPE pattern.
    $followsStandardPattern = $this->followsStandardPattern($existingClasses);

    if ($followsStandardPattern) {
      // Continue standard pattern with darker red shades.
      return $this->interpolateDarkerRed(count($existingClasses));
    }

    // Otherwise, analyze color progression and interpolate.
    return $this->interpolateColor($existingClasses);
  }

  /**
   * Checks if existing classes follow the standard DPE color pattern.
   *
   * @param array $existingClasses
   *   Existing classes.
   *
   * @return bool
   *   TRUE if standard pattern is followed.
   */
  protected function followsStandardPattern(array $existingClasses): bool {
    foreach ($existingClasses as $code => $class) {
      if (isset(self::STANDARD_COLORS[$code])) {
        $expectedColor = self::STANDARD_COLORS[$code];
        $actualColor = strtoupper($class['color'] ?? '');
        if (strtoupper($expectedColor) !== $actualColor) {
          return FALSE;
        }
      }
    }
    return TRUE;
  }

  /**
   * Interpolates a darker red shade for classes beyond G.
   *
   * @param int $classCount
   *   Number of existing classes.
   *
   * @return string
   *   Darker red hex color.
   */
  protected function interpolateDarkerRed(int $classCount): string {
    // Start from G's color (#A10D0D) and go darker.
    $baseRed = 0xA1;
    $step = 0x10;
    $newRed = max(0x50, $baseRed - ($classCount - 6) * $step);

    return sprintf('#%02X0D0D', $newRed);
  }

  /**
   * Interpolates color based on existing progression.
   *
   * @param array $existingClasses
   *   Existing classes.
   *
   * @return string
   *   Interpolated color.
   */
  protected function interpolateColor(array $existingClasses): string {
    // Get last two colors to determine direction.
    $colors = array_column($existingClasses, 'color');
    $lastColor = end($colors);

    if (!$lastColor) {
      return '#CCCCCC';
    }

    // Simple fallback: slightly darker version of last color.
    return $this->darkenColor($lastColor, 0.1);
  }

  /**
   * Darkens a hex color by a percentage.
   *
   * @param string $hexColor
   *   Hex color (e.g., '#FF0000').
   * @param float $percent
   *   Percentage to darken (0.0 - 1.0).
   *
   * @return string
   *   Darkened hex color.
   */
  protected function darkenColor(string $hexColor, float $percent): string {
    $hexColor = ltrim($hexColor, '#');
    $r = hexdec(substr($hexColor, 0, 2));
    $g = hexdec(substr($hexColor, 2, 2));
    $b = hexdec(substr($hexColor, 4, 2));

    $r = (int) max(0, $r * (1 - $percent));
    $g = (int) max(0, $g * (1 - $percent));
    $b = (int) max(0, $b * (1 - $percent));

    return sprintf('#%02X%02X%02X', $r, $g, $b);
  }

}
