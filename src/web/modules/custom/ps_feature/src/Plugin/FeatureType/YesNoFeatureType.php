<?php

namespace Drupal\ps_feature\Plugin\FeatureType;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ps_feature\Attribute\FeatureType;
use Drupal\ps_feature\Plugin\FeatureTypeBase;

/**
 * Yes/No feature type plugin.
 */
#[FeatureType(
  id: 'yes_no',
  label: new TranslatableMarkup('Yes/No'),
  description: new TranslatableMarkup('Explicit boolean value: {value: bool}'),
)]
class YesNoFeatureType extends FeatureTypeBase {

  /**
   * {@inheritdoc}
   */
  public function validate(array $payload): array {
    $errors = [];

    if (!isset($payload['value'])) {
      $errors[] = "Yes/No payload must contain 'value' key.";
    }
    elseif (!is_bool($payload['value'])) {
      $errors[] = "Yes/No 'value' must be a boolean.";
    }

    return $errors;
  }

  /**
   * {@inheritdoc}
   */
  public function normalize(array $payload): array {
    return [
      'value' => (bool) ($payload['value'] ?? FALSE),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildPayloadForm(array $current_payload = []): array {
    return [
      'value' => [
        '#type' => 'radios',
        '#title' => t('Value'),
        '#options' => [
          1 => t('Yes'),
          0 => t('No'),
        ],
        '#default_value' => isset($current_payload['value']) ? (int) $current_payload['value'] : 0,
        '#required' => TRUE,
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function format(array $payload): string {
    $value = $payload['value'] ?? FALSE;
    return $value ? t('Yes') : t('No');
  }

}
