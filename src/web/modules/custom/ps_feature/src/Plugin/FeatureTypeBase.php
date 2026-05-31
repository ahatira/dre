<?php

namespace Drupal\ps_feature\Plugin;

use Drupal\Core\Plugin\PluginBase;

/**
 * Base class for Feature Type plugins.
 */
abstract class FeatureTypeBase extends PluginBase implements FeatureTypeInterface {

  /**
   * {@inheritdoc}
   */
  public function getPluginId(): string {
    return $this->pluginDefinition['id'];
  }

  /**
   * {@inheritdoc}
   */
  public function getLabel(): string {
    return (string) $this->pluginDefinition['label'];
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription(): string {
    return (string) $this->pluginDefinition['description'];
  }

  /**
   * {@inheritdoc}
   */
  abstract public function validate(array $payload): array;

  /**
   * {@inheritdoc}
   */
  abstract public function normalize(array $payload): array;

  /**
   * {@inheritdoc}
   */
  public function buildPayloadForm(array $current_payload = []): array {
    // Default implementation: simple textarea for JSON.
    return [
      'raw_payload' => [
        '#type' => 'textarea',
        '#title' => t('Payload (JSON)'),
        '#default_value' => !empty($current_payload) ? json_encode($current_payload, JSON_PRETTY_PRINT) : '',
        '#description' => t('Enter the payload as JSON. Structure depends on feature type.'),
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function format(array $payload): string {
    // Default implementation: just show JSON.
    return json_encode($payload);
  }

}
