<?php

namespace Drupal\ps_feature\Plugin\FeatureType;

use Drupal\ps_feature\Plugin\FeatureTypeBase;

/**
 * Numeric feature type plugin.
 *
 * @FeatureType(
 *   id = "numeric",
 *   label = @Translation("Numeric value"),
 *   description = @Translation("Numeric value with unit: {value: num, unit: string}")
 * )
 */
class NumericFeatureType extends FeatureTypeBase {

  /**
   * {@inheritdoc}
   */
  public function validate(array $payload): array {
    $errors = [];

    if (!isset($payload['value'])) {
      $errors[] = "Numeric payload must contain 'value' key.";
    }
    elseif (!is_numeric($payload['value'])) {
      $errors[] = "Numeric 'value' must be a number.";
    }
    elseif ($payload['value'] <= 0) {
      $errors[] = "Numeric 'value' must be greater than 0.";
    }

    if (!isset($payload['unit'])) {
      $errors[] = "Numeric payload must contain 'unit' key.";
    }
    elseif (!is_string($payload['unit']) || empty(trim($payload['unit']))) {
      $errors[] = "Numeric 'unit' must be a non-empty string.";
    }

    return $errors;
  }

  /**
   * {@inheritdoc}
   */
  public function normalize(array $payload): array {
    return [
      'value' => (float) ($payload['value'] ?? 0),
      'unit' => trim($payload['unit'] ?? ''),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildPayloadForm(array $current_payload = []): array {
    return [
      'value' => [
        '#type' => 'number',
        '#title' => t('Value'),
        '#default_value' => $current_payload['value'] ?? '',
        '#step' => 'any',
        '#required' => TRUE,
      ],
      'unit' => [
        '#type' => 'textfield',
        '#title' => t('Unit'),
        '#default_value' => $current_payload['unit'] ?? '',
        '#maxlength' => 32,
        '#required' => TRUE,
        '#description' => t('e.g., m², m, kg, etc.'),
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function format(array $payload): string {
    $value = $payload['value'] ?? 0;
    $unit = $payload['unit'] ?? '';
    return sprintf('%s %s', number_format($value, 2), $unit);
  }

}
