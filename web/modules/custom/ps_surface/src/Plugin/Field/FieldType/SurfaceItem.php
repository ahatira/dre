<?php

declare(strict_types=1);

namespace Drupal\ps_surface\Plugin\Field\FieldType;

use Drupal\ps_surface\Plugin\Validation\Constraint\SurfaceCompletenessConstraint;
use Drupal\Core\Field\Attribute\FieldType;
use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\ps_surface\Plugin\Validation\Constraint\SurfaceDictionaryConstraint;

/**
 * Field type storing a quantified surface with dictionary-backed qualifiers.
 *
 * Validates codes against ps_dictionary (surface_unit, surface_type,
 * surface_nature, surface_qualification).
 *
 * XML mapping from GLOBAL_SURFACES/SURFACE:
 * - VALUE → value
 * - UNIT_CODE → unit (surface_unit dictionary)
 * - TYPE_CODE → type (surface_type dictionary)
 * - NATURE_CODE → nature (surface_nature dictionary)
 * - QUALIFICATION → qualification (surface_qualification dictionary)
 *
 * @see specs/mockup/xml/OBL_ES_20251108111254.xml
 */
#[FieldType(
  id: 'ps_surface',
  label: new TranslatableMarkup('Surface'),
  description: new TranslatableMarkup('Stores surface value, unit, type, nature, and qualification.'),
  category: 'propertysearch',
  default_widget: 'ps_surface_default',
  default_formatter: 'ps_surface_default',
)]
final class SurfaceItem extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultStorageSettings(): array {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultFieldSettings(): array {
    return [
      'default_unit' => 'M2',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty(): bool {
    $value = $this->get('value')->getValue();
    // Consider the item empty whenever the numeric value is empty,
    // regardless of other sub-properties. This allows saving field
    // settings/defaults without forcing a value when the field is not
    // required, while keeping validation active when a value is provided.
    return $value === NULL || $value === '';
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition): array {
    unset($field_definition);
    $properties = [];

    $properties['value'] = DataDefinition::create('float')
      ->setLabel(new TranslatableMarkup('Value'))
      ->setDescription(new TranslatableMarkup('Surface value in the given unit.'))
      ->setRequired(TRUE);

    $properties['unit'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Unit'))
      ->setDescription(new TranslatableMarkup('Unit code from ps_dictionary surface_unit.'))
      ->setRequired(TRUE);

    $properties['type'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Type'))
      ->setDescription(new TranslatableMarkup('Type code from ps_dictionary surface_type.'))
      ->setRequired(FALSE);

    $properties['nature'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Nature'))
      ->setDescription(new TranslatableMarkup('Nature code from ps_dictionary surface_nature.'))
      ->setRequired(FALSE);

    $properties['qualification'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Qualification'))
      ->setDescription(new TranslatableMarkup('Qualification code from ps_dictionary surface_qualification.'))
      ->setRequired(FALSE);

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition): array {
    unset($field_definition);
    return [
      'columns' => [
        'value' => [
          'type' => 'numeric',
          'precision' => 12,
          'scale' => 2,
          'not null' => FALSE,
        ],
        'unit' => [
          'type' => 'varchar',
          'length' => 12,
          'not null' => FALSE,
        ],
        'type' => [
          'type' => 'varchar',
          'length' => 64,
          'not null' => FALSE,
        ],
        'nature' => [
          'type' => 'varchar',
          'length' => 64,
          'not null' => FALSE,
        ],
        'qualification' => [
          'type' => 'varchar',
          'length' => 64,
          'not null' => FALSE,
        ],
      ],
      'indexes' => [
        'value' => ['value'],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getConstraints(): array {
    $constraints = parent::getConstraints();

    $constraints[] = new SurfaceDictionaryConstraint();
    // Ensure that when a numeric value is provided, a unit is also set.
    $constraints[] = new SurfaceCompletenessConstraint();

    return $constraints;
  }

  /**
   * Gets the numeric surface value.
   */
  public function getValue(): ?float {
    $value = $this->get('value')->getValue();
    return is_numeric($value) ? (float) $value : NULL;
  }

  /**
   * Gets the unit code.
   */
  public function getUnit(): ?string {
    $value = $this->get('unit')->getValue();
    return $value === NULL || $value === '' ? NULL : (string) $value;
  }

  /**
   * Gets the type code.
   */
  public function getType(): ?string {
    $value = $this->get('type')->getValue();
    return $value === NULL || $value === '' ? NULL : (string) $value;
  }

  /**
   * Gets the nature code.
   */
  public function getNature(): ?string {
    $value = $this->get('nature')->getValue();
    return $value === NULL || $value === '' ? NULL : (string) $value;
  }

  /**
   * Gets the qualification code.
   */
  public function getQualification(): ?string {
    $value = $this->get('qualification')->getValue();
    return $value === NULL || $value === '' ? NULL : (string) $value;
  }

}
