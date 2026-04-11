<?php

declare(strict_types=1);

namespace Drupal\ps_features\Plugin\Field\FieldType;

use Drupal\Core\Field\Attribute\FieldType;
use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Defines the 'ps_feature_value' field type.
 *
 * Stores technical feature values with support for 6 value types:
 * - flag: Simple flag (displayed only if present, no value stored)
 * - yesno: Yes/No values (always displayed with answer)
 * - boolean: TRUE/FALSE values (deprecated, use flag or yesno)
 * - dictionary: References to dictionary entries
 * - string: Text values
 * - numeric: Single numeric values with optional unit
 * - range: Min/max numeric ranges with optional unit
 * Complementary free text can be stored via the 'complement' property.
 *
 * Each field item references a feature definition (ps_feature config entity)
 * and stores the appropriate value based on the feature's value_type.
 *
 * Performance: O(1) storage, O(n) for feature lookups (cached).
 *
 * @see \Drupal\ps_features\Entity\Feature
 * @see docs/specs/04-ps-features.md#field-type
 */
#[FieldType(
  id: 'ps_feature_value',
  label: new TranslatableMarkup('Feature Value'),
  description: new TranslatableMarkup('Stores technical feature values with multiple value types.'),
  category: 'propertysearch',
  default_widget: 'ps_feature_value_default',
  default_formatter: 'ps_feature_value_default',
)]
class FeatureValueItem extends FieldItemBase {

  /**
   * Gets raw property value safely.
   *
   * @param string $name
   *   Property name.
   *
   * @return mixed
   *   Property value or NULL.
   */
  private function raw(string $name): mixed {
    return $this->get($name)->getValue();
  }

  /**
   * Gets string property value.
   *
   * @param string $name
   *   Property name.
   *
   * @return string|null
   *   String value or NULL.
   */
  private function str(string $name): ?string {
    $value = $this->raw($name);
    if ($value === NULL || $value === '') {
      return NULL;
    }
    // Ensure string type before return.
    return is_scalar($value) ? (string) $value : NULL;
  }

  /**
   * Gets float property value.
   *
   * @param string $name
   *   Property name.
   *
   * @return float|null
   *   Float value or NULL.
   */
  private function flt(string $name): ?float {
    $value = $this->raw($name);
    return is_numeric($value) ? (float) $value : NULL;
  }

  /**
   * Gets boolean property value.
   *
   * @param string $name
   *   Property name.
   *
   * @return bool
   *   Boolean value.
   */
  private function boolVal(string $name): bool {
    return (bool) $this->raw($name);
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition): array {
    $properties = [];

    $properties['feature_id'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Feature ID'))
      ->setRequired(TRUE);

    $properties['value_type'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Value Type'))
      ->setRequired(TRUE);

    $properties['dictionary_type'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Dictionary Type'));

    $properties['value_boolean'] = DataDefinition::create('boolean')
      ->setLabel(new TranslatableMarkup('Boolean Value'));

    $properties['value_string'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('String Value'));

    $properties['value_numeric'] = DataDefinition::create('float')
      ->setLabel(new TranslatableMarkup('Numeric Value'));

    $properties['value_range_min'] = DataDefinition::create('float')
      ->setLabel(new TranslatableMarkup('Range Min'));

    $properties['value_range_max'] = DataDefinition::create('float')
      ->setLabel(new TranslatableMarkup('Range Max'));

    $properties['unit'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Unit'));

    $properties['complement'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Complement'))
      ->setDescription(new TranslatableMarkup('Additional free text complement for the feature value.'));

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition): array {
    return [
      'columns' => [
        'feature_id' => [
          'type' => 'varchar',
          'length' => 255,
          'not null' => TRUE,
        ],
        'value_type' => [
          'type' => 'varchar',
          'length' => 32,
          'not null' => TRUE,
        ],
        'dictionary_type' => [
          'type' => 'varchar',
          'length' => 255,
        ],
        'value_boolean' => [
          'type' => 'int',
          'size' => 'tiny',
        ],
        'value_string' => [
          'type' => 'varchar',
          'length' => 255,
        ],
        'value_numeric' => [
          'type' => 'numeric',
          'precision' => 15,
          'scale' => 4,
        ],
        'value_range_min' => [
          'type' => 'numeric',
          'precision' => 15,
          'scale' => 4,
        ],
        'value_range_max' => [
          'type' => 'numeric',
          'precision' => 15,
          'scale' => 4,
        ],
        'unit' => [
          'type' => 'varchar',
          'length' => 50,
        ],
        'complement' => [
          'type' => 'text',
          'size' => 'big',
        ],
      ],
      'indexes' => [
        'feature_id' => ['feature_id'],
        'value_type' => ['value_type'],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty(): bool {
    $feature_id = $this->str('feature_id');
    return $feature_id === NULL || $feature_id === '';
  }

  /**
   * Gets the formatted value for display.
   *
   * @return string
   *   The formatted value.
   */
  public function getFormattedValue(): string {
    $type = $this->str('value_type');
    if ($type === NULL) {
      return '';
    }

    return match ($type) {
      'boolean' => $this->formatBoolean(),
      'string' => $this->formatString(),
      'numeric' => $this->formatNumeric(),
      'range' => $this->formatRange(),
      'dictionary' => $this->formatDictionary(),
      'flag' => '',
      default => '',
    };
  }

  /**
   * Formats boolean value.
   *
   * @return string
   *   Formatted boolean value.
   */
  private function formatBoolean(): string {
    return $this->boolVal('value_boolean') ? (string) $this->t('Yes') : (string) $this->t('No');
  }

  /**
   * Formats string value.
   *
   * @return string
   *   Formatted string value.
   */
  private function formatString(): string {
    return $this->str('value_string') ?? '';
  }

  /**
   * Formats numeric value.
   *
   * @return string
   *   Formatted numeric value.
   */
  private function formatNumeric(): string {
    $value = $this->flt('value_numeric');
    if ($value === NULL) {
      return '';
    }
    $unit = $this->str('unit');
    $formatted = (string) $value;
    return $unit ? $formatted . ' ' . $unit : $formatted;
  }

  /**
   * Formats range value.
   *
   * @return string
   *   Formatted range value.
   */
  private function formatRange(): string {
    $min = $this->flt('value_range_min');
    $max = $this->flt('value_range_max');
    $unit = $this->str('unit');

    if ($min === NULL && $max === NULL) {
      return '';
    }

    if ($min === NULL) {
      $max_str = number_format($max, 2, '.', ' ');
      return $unit ? '≤ ' . $max_str . ' ' . $unit : '≤ ' . $max_str;
    }

    if ($max === NULL) {
      $min_str = number_format($min, 2, '.', ' ');
      return $unit ? '≥ ' . $min_str . ' ' . $unit : '≥ ' . $min_str;
    }

    $min_str = number_format($min, 2, '.', ' ');
    $max_str = number_format($max, 2, '.', ' ');
    $range = $min_str . ' - ' . $max_str;
    return $unit ? $range . ' ' . $unit : $range;
  }

  /**
   * Formats dictionary value.
   *
   * @return string
   *   Formatted dictionary value.
   */
  private function formatDictionary(): string {
    $dict_type = $this->str('dictionary_type');
    $code = $this->str('value_string');
    if ($dict_type === NULL || $dict_type === '' || $code === NULL || $code === '') {
      return '';
    }
    // @todo Inject DictionaryManagerInterface when Drupal supports field item DI.
    // Using service locator as temporary solution for field items.
    /** @var \Drupal\ps_dictionary\Service\DictionaryManagerInterface $dictionary_manager */
    $dictionary_manager = \Drupal::service('ps_dictionary.manager');
    $label = $dictionary_manager->getLabel($dict_type, $code);
    return $label !== NULL ? (string) $label : $code;
  }

}
