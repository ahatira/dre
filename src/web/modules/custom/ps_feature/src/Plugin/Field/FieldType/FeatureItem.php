<?php

namespace Drupal\ps_feature\Plugin\Field\FieldType;

use Drupal\Core\Field\Attribute\FieldType;
use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Plugin implementation of the 'feature' field type.
 */
#[FieldType(
  id: 'feature',
  label: new TranslatableMarkup('Feature'),
  description: new TranslatableMarkup('Stores a feature definition reference and its payload.'),
  default_widget: 'feature_default',
  default_formatter: 'feature_default',
  category: 'ps_feature',
)]
class FeatureItem extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition): array {
    $properties['feature_definition_id'] = DataDefinition::create('string')
      ->setLabel(t('Feature Definition ID'))
      ->setDescription(t('The ID of the feature definition config entity.'))
      ->setRequired(TRUE);

    $properties['payload'] = DataDefinition::create('string')
      ->setLabel(t('Payload'))
      ->setDescription(t('The JSON payload containing feature values.'))
      ->setRequired(TRUE);

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition): array {
    return [
      'columns' => [
        'feature_definition_id' => [
          'type' => 'varchar',
          'length' => 128,
          'not null' => TRUE,
        ],
        'payload' => [
          'type' => 'text',
          'size' => 'normal',
          'not null' => TRUE,
        ],
      ],
      'indexes' => [
        'feature_definition_id' => ['feature_definition_id'],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty(): bool {
    $feature_id = $this->get('feature_definition_id')->getValue();
    $payload = $this->get('payload')->getValue();
    
    return empty($feature_id) || empty($payload);
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultFieldSettings(): array {
    return [
      'allowed_groups' => [],
      'allowed_features' => [],
    ] + parent::defaultFieldSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function fieldSettingsForm(array $form, FormStateInterface $form_state): array {
    $element = parent::fieldSettingsForm($form, $form_state);

    // Get all feature groups.
    $groups = \Drupal::entityTypeManager()
      ->getStorage('fb_feature_group')
      ->loadMultiple();
    
    $group_options = [];
    foreach ($groups as $group) {
      $group_options[$group->id()] = $group->label();
    }

    $element['allowed_groups'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Allowed feature groups'),
      '#description' => $this->t('Restrict which feature groups can be used. Leave empty to allow all.'),
      '#options' => $group_options,
      '#default_value' => $this->getSetting('allowed_groups') ?: [],
    ];

    // Get all feature definitions.
    $features = \Drupal::entityTypeManager()
      ->getStorage('fb_feature_definition')
      ->loadMultiple();
    
    $feature_options = [];
    foreach ($features as $feature) {
      $feature_options[$feature->id()] = $feature->label() . ' (' . $feature->getGroup() . ')';
    }

    $element['allowed_features'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Allowed features'),
      '#description' => $this->t('Restrict which features can be used. Leave empty to allow all.'),
      '#options' => $feature_options,
      '#default_value' => $this->getSetting('allowed_features') ?: [],
    ];

    return $element;
  }

  /**
   * Gets the feature definition entity.
   *
   * @return \Drupal\ps_feature\Entity\FeatureDefinition|null
   *   The feature definition entity or NULL.
   */
  public function getFeatureDefinition() {
    $id = $this->get('feature_definition_id')->getValue();
    if (empty($id)) {
      return NULL;
    }

    return \Drupal::entityTypeManager()
      ->getStorage('fb_feature_definition')
      ->load($id);
  }

  /**
   * Gets the decoded payload array.
   *
   * @return array
   *   The decoded payload.
   */
  public function getPayloadArray(): array {
    $payload_json = $this->get('payload')->getValue();
    if (empty($payload_json)) {
      return [];
    }

    $payload = json_decode($payload_json, TRUE);
    return is_array($payload) ? $payload : [];
  }

  /**
   * Sets the payload from an array.
   *
   * @param array $payload
   *   The payload array.
   */
  public function setPayloadArray(array $payload): void {
    $this->set('payload', json_encode($payload));
  }

}
