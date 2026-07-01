<?php

namespace Drupal\ps_feature\Plugin\FeatureType;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ps_feature\Attribute\FeatureType;
use Drupal\ps_feature\Plugin\FeatureTypeBase;

/**
 * Flag feature type plugin.
 */
#[FeatureType(
  id: 'flag',
  label: new TranslatableMarkup('Flag indicator'),
  description: new TranslatableMarkup('Implicit presence: {presence: true}'),
)]
class FlagFeatureType extends FeatureTypeBase {

  /**
   * Resolves flag presence from legacy/current payload keys.
   */
  protected function resolvePresence(array $payload): bool {
    if (array_key_exists('present', $payload)) {
      return (bool) $payload['present'];
    }
    if (array_key_exists('presence', $payload)) {
      return (bool) $payload['presence'];
    }
    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function validate(array $payload): array {
    $errors = [];

    if (!array_key_exists('present', $payload) && !array_key_exists('presence', $payload)) {
      $errors[] = "Flag payload must contain 'present' (or legacy 'presence') key.";
    }
    elseif (array_key_exists('present', $payload) && !is_bool($payload['present'])) {
      $errors[] = "Flag 'present' must be a boolean.";
    }
    elseif (!array_key_exists('present', $payload) && array_key_exists('presence', $payload) && !is_bool($payload['presence'])) {
      $errors[] = "Flag 'presence' must be a boolean.";
    }

    return $errors;
  }

  /**
   * {@inheritdoc}
   */
  public function normalize(array $payload): array {
    return [
      'present' => $this->resolvePresence($payload),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildPayloadForm(array $current_payload = []): array {
    return [
      'present' => [
        '#type' => 'checkbox',
        '#title' => t('Present'),
        '#default_value' => $this->resolvePresence($current_payload),
        '#description' => t('Check if this feature is present.'),
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function format(array $payload): string {
    return $this->resolvePresence($payload) ? t('Present') : t('Absent');
  }

}
