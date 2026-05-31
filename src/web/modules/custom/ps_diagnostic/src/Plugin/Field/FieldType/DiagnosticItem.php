<?php

declare(strict_types=1);

namespace Drupal\ps_diagnostic\Plugin\Field\FieldType;

use Drupal\Core\Field\Attribute\FieldType;
use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Field type for one diagnostic XML item.
 */
#[FieldType(
  id: 'diagnostic_item',
  label: new TranslatableMarkup('Diagnostic item'),
  description: new TranslatableMarkup('Stores one diagnostic item based on XML structure.'),
  default_widget: 'diagnostic_item_default',
  default_formatter: 'diagnostic_item_default',
  category: 'ps_diagnostic',
)]
final class DiagnosticItem extends FieldItemBase {

  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition): array {
    $properties = [];

    foreach ([
      'diagnostic_type' => 'Diagnostic type',
      'class' => 'Class',
      'value' => 'Value',
      'diagnostic_date' => 'Diagnostic date',
      'validity_end_date' => 'Validity end date',
    ] as $name => $label) {
      $properties[$name] = DataDefinition::create('string')->setLabel(t($label));
    }

    $properties['no_classification'] = DataDefinition::create('boolean')->setLabel(t('No classification'));
    $properties['non_applicable'] = DataDefinition::create('boolean')->setLabel(t('Non applicable'));

    return $properties;
  }

  public static function schema(FieldStorageDefinitionInterface $field_definition): array {
    return [
      'columns' => [
        'diagnostic_type' => ['type' => 'varchar', 'length' => 32, 'not null' => FALSE],
        'class' => ['type' => 'varchar', 'length' => 32, 'not null' => FALSE],
        'value' => ['type' => 'varchar', 'length' => 64, 'not null' => FALSE],
        'diagnostic_date' => ['type' => 'varchar', 'length' => 20, 'not null' => FALSE],
        'validity_end_date' => ['type' => 'varchar', 'length' => 20, 'not null' => FALSE],
        'no_classification' => ['type' => 'int', 'size' => 'tiny', 'not null' => FALSE],
        'non_applicable' => ['type' => 'int', 'size' => 'tiny', 'not null' => FALSE],
      ],
      'indexes' => [
        'diagnostic_type' => ['diagnostic_type'],
      ],
    ];
  }

  public function isEmpty(): bool {
    return ($this->get('diagnostic_type')->getValue() === NULL || $this->get('diagnostic_type')->getValue() === '')
      && ($this->get('class')->getValue() === NULL || $this->get('class')->getValue() === '')
      && ($this->get('value')->getValue() === NULL || $this->get('value')->getValue() === '')
      && !$this->get('no_classification')->getValue()
      && !$this->get('non_applicable')->getValue();
  }

  public static function defaultFieldSettings(): array {
    return [
      'allowed_types' => ['dpe', 'ges'],
    ] + parent::defaultFieldSettings();
  }

  public function fieldSettingsForm(array $form, FormStateInterface $form_state): array {
    $element = parent::fieldSettingsForm($form, $form_state);

    $element['allowed_types'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Allowed diagnostic type IDs'),
      '#description' => $this->t('One type ID per line. Leave empty to allow all types.'),
      '#default_value' => implode("\n", (array) $this->getSetting('allowed_types')),
    ];

    return $element;
  }

}
