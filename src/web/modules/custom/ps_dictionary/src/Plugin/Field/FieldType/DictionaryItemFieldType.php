<?php

declare(strict_types=1);

namespace Drupal\ps_dictionary\Plugin\Field\FieldType;

use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\TypedData\OptionsProviderInterface;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Plugin implementation of the 'ps_dictionary' field type.
 *
 * @FieldType(
 *   id = "ps_dictionary",
 *   label = @Translation("Dictionary code"),
 *   description = @Translation("Stores a business dictionary code (ex: BUR, SALE, EUR)."),
 *   default_widget = "ps_dictionary_options_select",
 *   default_formatter = "ps_dictionary_formatter"
 * )
 */
class DictionaryItemFieldType extends FieldItemBase implements OptionsProviderInterface {

  public static function defaultFieldSettings(): array {
    return [
      'dictionary_type' => '',
    ] + parent::defaultFieldSettings();
  }

  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition): array {
    $properties['value'] = DataDefinition::create('string')
      ->setLabel(t('Dictionary code'));
    return $properties;
  }

  public static function schema(FieldStorageDefinitionInterface $field_definition): array {
    return [
      'columns' => [
        'value' => [
          'type' => 'varchar',
          'length' => 16,
        ],
      ],
    ];
  }

  public function isEmpty(): bool {
    $value = $this->get('value')->getValue();
    return $value === NULL || $value === '';
  }

  public function fieldSettingsForm(array $form, FormStateInterface $form_state): array {
    $element = [];
    $types = [];
    $storage = \Drupal::entityTypeManager()->getStorage('ps_dictionary_type');
    foreach ($storage->loadMultiple() as $id => $entity) {
      $types[$id] = $entity->label();
    }

    $element['dictionary_type'] = [
      '#type' => 'select',
      '#title' => t('Dictionary type'),
      '#options' => $types,
      '#empty_option' => t('- Select -'),
      '#default_value' => (string) $this->getSetting('dictionary_type'),
      '#required' => TRUE,
      '#description' => t('Defines which dictionary is used to build options and autocomplete matches.'),
    ];

    return $element;
  }

  public static function fieldSettingsSummary(FieldDefinitionInterface $field_definition): array {
    $summary = [];
    $type = (string) $field_definition->getSetting('dictionary_type');
    if ($type !== '') {
      $summary[] = t('Dictionary type: @type', ['@type' => $type]);
    }
    else {
      $summary[] = t('Dictionary type is not configured yet.');
    }
    return $summary;
  }

  public static function generateSampleValue(FieldDefinitionInterface $field_definition): array {
    return ['value' => ''];
  }

  public static function mainPropertyName(): string {
    return 'value';
  }

  public function preSave(): void {
    if ($this->get('value')->getValue() !== NULL) {
      $this->set('value', mb_strtoupper((string) $this->get('value')->getValue()));
    }
  }

  public function getPossibleValues(?AccountInterface $account = NULL): array {
    return array_keys($this->getPossibleOptions($account));
  }

  public function getPossibleOptions(?AccountInterface $account = NULL): array {
    return $this->buildOptions();
  }

  public function getSettableValues(?AccountInterface $account = NULL): array {
    return array_keys($this->getSettableOptions($account));
  }

  public function getSettableOptions(?AccountInterface $account = NULL): array {
    return $this->buildOptions();
  }

  private function buildOptions(): array {
    $type = (string) $this->getSetting('dictionary_type');
    if ($type === '') {
      return [];
    }

    $resolver = \Drupal::service('ps_dictionary.resolver');
    $options = [];
    foreach ($resolver->all($type) as $entry) {
      $options[$entry['code']] = $entry['label'] . ' (' . $entry['code'] . ')';
    }

    return $options;
  }
}
