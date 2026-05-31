<?php

declare(strict_types=1);

namespace Drupal\ps_surface\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Plugin implementation of the 'ps_surface_item' field type.
 *
 * @FieldType(
 *   id = "ps_surface_item",
 *   label = @Translation("PS surface item"),
 *   description = @Translation("Stores a qualified physical surface item."),
 *   default_widget = "ps_surface_item_default",
 *   default_formatter = "ps_surface_item_default"
 * )
 */
final class SurfaceItemFieldType extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition): array {
    $properties['value'] = DataDefinition::create('float')
      ->setLabel(t('Value'))
      ->setRequired(TRUE);

    $properties['qualification'] = DataDefinition::create('string')
      ->setLabel(t('Qualification code'))
      ->setRequired(TRUE);

    $properties['unit_code'] = DataDefinition::create('string')
      ->setLabel(t('Unit code'));

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition): array {
    return [
      'columns' => [
        'value' => [
          'type' => 'numeric',
          'precision' => 12,
          'scale' => 2,
          'not null' => FALSE,
        ],
        'qualification' => [
          'type' => 'varchar',
          'length' => 32,
          'not null' => FALSE,
        ],
        'unit_code' => [
          'type' => 'varchar',
          'length' => 16,
          'not null' => FALSE,
        ],
      ],
      'indexes' => [
        'qualification' => ['qualification'],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public static function mainPropertyName(): string {
    return 'value';
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty(): bool {
    $value = $this->get('value')->getValue();
    $qualification = $this->get('qualification')->getValue();

    return $value === NULL || $value === '' || $qualification === NULL || $qualification === '';
  }

}
