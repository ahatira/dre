<?php

namespace Drupal\ps_feature\Plugin\FeatureType;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ps_feature\Attribute\FeatureType;
use Drupal\ps_feature\Plugin\FeatureTypeBase;

/**
 * Range feature type plugin.
 */
#[FeatureType(
  id: 'range',
  label: new TranslatableMarkup('Value range'),
  description: new TranslatableMarkup('Min-max range with unit: {min: num, max: num, unit: string}'),
)]
class RangeFeatureType extends FeatureTypeBase {

  /**
   * {@inheritdoc}
   */
  public function validate(array $payload): array {
    $errors = [];

    if (!isset($payload['min'])) {
      $errors[] = "Range payload must contain 'min' key.";
    }
    elseif (!is_numeric($payload['min'])) {
      $errors[] = "Range 'min' must be a number.";
    }
    elseif ($payload['min'] < 0) {
      $errors[] = "Range 'min' must be >= 0.";
    }

    if (!isset($payload['max'])) {
      $errors[] = "Range payload must contain 'max' key.";
    }
    elseif (!is_numeric($payload['max'])) {
      $errors[] = "Range 'max' must be a number.";
    }
    elseif ($payload['max'] < 0) {
      $errors[] = "Range 'max' must be >= 0.";
    }

    if (isset($payload['min'], $payload['max']) && is_numeric($payload['min']) && is_numeric($payload['max'])) {
      if ($payload['min'] > $payload['max']) {
        $errors[] = "Range 'min' must be <= 'max'.";
      }
    }

    if (!isset($payload['unit'])) {
      $errors[] = "Range payload must contain 'unit' key.";
    }
    elseif (!is_string($payload['unit']) || empty(trim($payload['unit']))) {
      $errors[] = "Range 'unit' must be a non-empty string.";
    }

    return $errors;
  }

  /**
   * {@inheritdoc}
   */
  public function normalize(array $payload): array {
    return [
      'min' => (float) ($payload['min'] ?? 0),
      'max' => (float) ($payload['max'] ?? 0),
      'unit' => trim($payload['unit'] ?? ''),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildPayloadForm(array $current_payload = []): array {
    return [
      'min' => [
        '#type' => 'number',
        '#title' => t('Minimum'),
        '#default_value' => $current_payload['min'] ?? '',
        '#step' => 'any',
        '#required' => TRUE,
      ],
      'max' => [
        '#type' => 'number',
        '#title' => t('Maximum'),
        '#default_value' => $current_payload['max'] ?? '',
        '#step' => 'any',
        '#required' => TRUE,
      ],
      'unit' => [
        '#type' => 'textfield',
        '#title' => t('Unit'),
        '#default_value' => $current_payload['unit'] ?? '',
        '#maxlength' => 32,
        '#required' => TRUE,
        '#description' => t('e.g., m², m, kg, €, etc.'),
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function format(array $payload): string {
    $min = $payload['min'] ?? 0;
    $max = $payload['max'] ?? 0;
    $unit = $payload['unit'] ?? '';
    return sprintf('%s - %s %s', number_format($min, 2), number_format($max, 2), $unit);
  }

}
