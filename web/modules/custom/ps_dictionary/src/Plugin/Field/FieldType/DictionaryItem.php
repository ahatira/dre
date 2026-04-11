<?php

declare(strict_types=1);

namespace Drupal\ps_dictionary\Plugin\Field\FieldType;

use Drupal\Core\Field\Attribute\FieldType;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\options\Plugin\Field\FieldType\ListStringItem;

/**
 * Dictionary field type - like List (text) but with dictionary-based values.
 *
 * This field is identical to Drupal's core List (text) field, except that
 * instead of manually entering allowed values, they are automatically loaded
 * from a configured ps_dictionary_type.
 *
 * Uses the standard Drupal List field widgets and formatters.
 */
#[FieldType(
  id: 'ps_dictionary',
  label: new TranslatableMarkup('Dictionary'),
  description: new TranslatableMarkup('List field with values from a business dictionary.'),
  category: 'propertysearch',
  default_widget: 'options_select',
  default_formatter: 'list_default',
)]
final class DictionaryItem extends ListStringItem {

  /**
   * {@inheritdoc}
   *
   * Stores the dictionary_type setting and registers the allowed values callback.
   * Drupal's options module will call ps_dictionary_allowed_values() to load
   * values from the selected dictionary.
   */
  public static function defaultStorageSettings(): array {
    return [
      'dictionary_type' => '',
      'allowed_values_function' => 'ps_dictionary_allowed_values',
    ] + parent::defaultStorageSettings();
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition): array {
    $schema = parent::schema($field_definition);
    $schema['columns']['value']['length'] = 255;
    return $schema;
  }

  /**
   * {@inheritdoc}
   *
   * Replaces the manual "Allowed values" textarea with a dictionary
   * type selector.
   */
  public function storageSettingsForm(array &$form, FormStateInterface $form_state, $has_data): array {
    // Load dictionary types.
    $storage = \Drupal::entityTypeManager()->getStorage('ps_dictionary_type');
    $types = $storage->loadMultiple();

    $options = ['' => $this->t('- Select a dictionary -')];
    foreach ($types as $type) {
      $options[$type->id()] = $type->label();
    }

    $element = [];
    $element['dictionary_type'] = [
      '#type' => 'select',
      '#title' => $this->t('Dictionary type'),
      '#description' => $this->t('Select the dictionary that provides the allowed values for this field. <strong>This setting cannot be changed after data has been created.</strong>'),
      '#options' => $options,
      '#default_value' => $this->getSetting('dictionary_type'),
      '#required' => TRUE,
      '#disabled' => $has_data,
    ];

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public static function storageSettingsSummary(FieldStorageDefinitionInterface $storage_definition): array {
    $summary = [];
    $dictionary_type = $storage_definition->getSetting('dictionary_type');

    if (empty($dictionary_type)) {
      $summary[] = new TranslatableMarkup('Dictionary type: <em>Not configured</em>');
    }
    else {
      $summary[] = new TranslatableMarkup('Dictionary type: @type', ['@type' => $dictionary_type]);
    }

    return $summary;
  }

}
