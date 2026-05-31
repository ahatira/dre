<?php

namespace Drupal\ps_feature\Plugin\FeatureType;

use Drupal\ps_feature\Plugin\FeatureTypeBase;

/**
 * Text feature type plugin.
 *
 * @FeatureType(
 *   id = "text",
 *   label = @Translation("Free text"),
 *   description = @Translation("Short text: {value: string}")
 * )
 */
class TextFeatureType extends FeatureTypeBase {

  /**
   * {@inheritdoc}
   */
  public function validate(array $payload): array {
    $errors = [];

    if (!isset($payload['value'])) {
      $errors[] = "Text payload must contain 'value' key.";
    }
    elseif (!is_string($payload['value'])) {
      $errors[] = "Text 'value' must be a string.";
    }
    elseif (mb_strlen($payload['value']) > 255) {
      $errors[] = "Text 'value' must not exceed 255 characters.";
    }

    return $errors;
  }

  /**
   * {@inheritdoc}
   */
  public function normalize(array $payload): array {
    return [
      'value' => mb_substr(trim($payload['value'] ?? ''), 0, 255),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildPayloadForm(array $current_payload = []): array {
    return [
      'value' => [
        '#type' => 'textfield',
        '#title' => t('Value'),
        '#default_value' => $current_payload['value'] ?? '',
        '#maxlength' => 255,
        '#required' => TRUE,
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function format(array $payload): string {
    return $payload['value'] ?? '';
  }

}
