<?php

declare(strict_types=1);

namespace Drupal\ps_features\Service;

use Drupal\ps_dictionary\Service\DictionaryManagerInterface;

/**
 * Implements value normalization for feature input arrays.
 *
 * @see docs/specs/04-ps-features.md#value-normalizer
 */
final class ValueNormalizer implements ValueNormalizerInterface {

  /**
   * Constructor.
   */
  public function __construct(
    private readonly ?DictionaryManagerInterface $dictionaryManager = NULL,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function normalize(array $raw, array $definitions, bool $allowUnknown = FALSE): array {
    $normalized = [];
    $errors = [];

    foreach ($raw as $code => $value) {
      if (!isset($definitions[$code])) {
        if ($allowUnknown) {
          continue;
        }
        $errors[$code] = 'Unknown feature code';
        continue;
      }

      $def = $definitions[$code];
      $type = $def->getValueType();

      // Normalize per type.
      switch ($type) {
        case 'flag':
          // Flag feature: value does not store anything, just boolean truthy.
          // Added if present.
          $normalized[$code] = TRUE;
          break;

        case 'yesno':
          $normalized[$code] = filter_var($value, FILTER_VALIDATE_BOOLEAN) ? 'yes' : 'no';
          break;

        case 'numeric':
          if ($value === '' || $value === NULL) {
            continue 2;
          }
          $float = filter_var($value, FILTER_VALIDATE_FLOAT);
          if ($float === FALSE) {
            $errors[$code] = 'Invalid numeric value';
            continue 2;
          }
          $normalized[$code] = $float;
          break;

        case 'range':
          if (!is_array($value) || !isset($value['min'], $value['max'])) {
            $errors[$code] = 'Range value must have min and max';
            continue 2;
          }
          $min = filter_var($value['min'], FILTER_VALIDATE_FLOAT);
          $max = filter_var($value['max'], FILTER_VALIDATE_FLOAT);
          if ($min === FALSE || $max === FALSE) {
            $errors[$code] = 'Range boundaries must be numeric';
            continue 2;
          }
          if ($min > $max) {
            $errors[$code] = 'Range min greater than max';
            continue 2;
          }
          $normalized[$code] = ['min' => $min, 'max' => $max];
          break;

        case 'dictionary':
          $dictType = $def->getDictionaryType();
          if (!$dictType || !is_string($value) || $value === '') {
            $errors[$code] = 'Dictionary value missing';
            continue 2;
          }
          if ($this->dictionaryManager && !$this->dictionaryManager->isValid($dictType, $value)) {
            $errors[$code] = 'Invalid dictionary code';
            continue 2;
          }
          $normalized[$code] = $value;
          break;

        case 'string':
          if ($value === '' || $value === NULL) {
            continue 2;
          }
          $normalized[$code] = (string) $value;
          break;

        default:
          $errors[$code] = 'Unsupported feature value type';
      }
    }

    return [
      'values' => $normalized,
      'errors' => $errors,
    ];
  }

}
