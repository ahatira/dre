<?php

declare(strict_types=1);

namespace Drupal\ps_diagnostic\Plugin\Field\FieldType;

use Drupal\Core\Field\Attribute\FieldType;
use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Defines the 'ps_diagnostic' field type.
 *
 * Stores regulatory diagnostic data (DPE, GES) with simplified structure:
 * - type_id: reference to PsDiagnostic config entity
 * - value: numeric value for automatic class calculation
 * - class: energy class label (A-G, can be overridden)
 * - valid_from/valid_to: validity period (ISO 8601 dates)
 * - no_classification: boolean flag (displays "?" if true)
 * - non_applicable: boolean flag (displays "N/A" if true).
 */
#[FieldType(
  id: 'ps_diagnostic',
  label: new TranslatableMarkup('Diagnostic'),
  description: new TranslatableMarkup('Stores regulatory diagnostic data (DPE, GES, technical indicators).'),
  category: 'propertysearch',
  default_widget: 'ps_diagnostic_default',
  default_formatter: 'ps_diagnostic_default',
)]
class DiagnosticItem extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition): array {
    $properties = [];

    $properties['type_id'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Diagnostic ID'))
      ->setDescription(new TranslatableMarkup('Reference to diagnostic config entity (dpe, ges, etc).'))
      ->setRequired(FALSE);

    $properties['value'] = DataDefinition::create('float')
      ->setLabel(new TranslatableMarkup('Numeric value'))
      ->setDescription(new TranslatableMarkup('Numeric diagnostic value (used for automatic class calculation).'))
      ->setRequired(FALSE);

    $properties['class'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Label code'))
      ->setDescription(new TranslatableMarkup('Energy class label (A-G). Can be manually set or auto-calculated.'))
      ->setRequired(FALSE);

    $properties['valid_from'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Valid from'))
      ->setDescription(new TranslatableMarkup('Diagnostic date (ISO 8601: YYYY-MM-DD).'))
      ->setRequired(FALSE);

    $properties['valid_to'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Valid to'))
      ->setDescription(new TranslatableMarkup('Validity end date (ISO 8601: YYYY-MM-DD).'))
      ->setRequired(FALSE);

    $properties['no_classification'] = DataDefinition::create('boolean')
      ->setLabel(new TranslatableMarkup('No classification'))
      ->setDescription(new TranslatableMarkup('Displays "?" if no class can be determined.'))
      ->setRequired(FALSE);

    $properties['non_applicable'] = DataDefinition::create('boolean')
      ->setLabel(new TranslatableMarkup('Non applicable'))
      ->setDescription(new TranslatableMarkup('Displays "N/A" if diagnostic is not applicable.'))
      ->setRequired(FALSE);

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition): array {
    return [
      'columns' => [
        'type_id' => [
          'type' => 'varchar',
          'length' => 50,
          'not null' => FALSE,
        ],
        'value' => [
          'type' => 'float',
          'size' => 'normal',
          'not null' => FALSE,
        ],
        'class' => [
          'type' => 'varchar',
          'length' => 10,
          'not null' => FALSE,
        ],
        'valid_from' => [
          'type' => 'varchar',
          'length' => 20,
          'not null' => FALSE,
        ],
        'valid_to' => [
          'type' => 'varchar',
          'length' => 20,
          'not null' => FALSE,
        ],
        'no_classification' => [
          'type' => 'int',
          'size' => 'tiny',
          'not null' => FALSE,
          'default' => 0,
        ],
        'non_applicable' => [
          'type' => 'int',
          'size' => 'tiny',
          'not null' => FALSE,
          'default' => 0,
        ],
      ],
      'indexes' => [
        'type_id' => ['type_id'],
        'value' => ['value'],
        'class' => ['class'],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty(): bool {
    $typeId = $this->get('type_id')->getValue();
    $labelCode = $this->get('class')->getValue();
    $valueNumeric = $this->get('value')->getValue();
    $validFrom = $this->get('valid_from')->getValue();
    $validTo = $this->get('valid_to')->getValue();
    $noClassification = (bool) $this->get('no_classification')->getValue();
    $nonApplicable = (bool) $this->get('non_applicable')->getValue();

    // Treat empty string as NULL for numeric value.
    if ($valueNumeric === '') {
      $valueNumeric = NULL;
    }

    // Item considered empty when no core identifying data provided and no flags.
    return ($typeId === NULL || $typeId === '')
      && ($labelCode === NULL || $labelCode === '')
      && $valueNumeric === NULL
      && ($validFrom === NULL || $validFrom === '')
      && ($validTo === NULL || $validTo === '')
      && !$noClassification
      && !$nonApplicable;
  }

  /**
   * {@inheritdoc}
   *
   * @param array<string, mixed>|null $values
   *   The values to set.
   * @param bool $notify
   *   Whether to notify about the change.
   */
  public function setValue($values, $notify = TRUE): void {
    // Normalise array values before parent processing.
    if (is_array($values)) {
      // Empty numeric value should become NULL (avoid primitive type error).
      if (array_key_exists('value', $values) && isset($values['value']) && $values['value'] === '') {
        $values['value'] = NULL;
      }
      // Ensure numeric casting when provided.
      if (isset($values['value'])) {
        // Accept both string and numeric; cast safely.
        if ($values['value'] !== '' && is_numeric($values['value'])) {
          $values['value'] = (float) $values['value'];
        }
      }
      // Normalise class empty string to NULL for easier emptiness check.
      if (array_key_exists('class', $values) && $values['class'] === '') {
        $values['class'] = NULL;
      }
      // Normalise dates empty string to NULL.
      foreach (['valid_from', 'valid_to'] as $dateKey) {
        if (array_key_exists($dateKey, $values) && $values[$dateKey] === '') {
          $values[$dateKey] = NULL;
        }
      }
      // Booleans: cast truthy/falsy values explicitly.
      foreach (['no_classification', 'non_applicable'] as $boolKey) {
        if (array_key_exists($boolKey, $values)) {
          $values[$boolKey] = (bool) $values[$boolKey];
        }
      }
    }
    parent::setValue($values, $notify);
  }

  /**
   * {@inheritdoc}
   */
  public function getConstraints(): array {
    $constraints = parent::getConstraints();

    // Add custom validation constraint for diagnostic data coherence.
    $constraints[] = $this->getTypedDataManager()
      ->getValidationConstraintManager()
      ->create('DiagnosticValid', []);

    // Add custom validation for type_id and class.
    $constraints[] = $this->getTypedDataManager()
      ->getValidationConstraintManager()
      ->create('ComplexData', [
        'type_id' => [
          'Callback' => [
            'callback' => [$this, 'validateTypeId'],
          ],
        ],
        'class' => [
          'Callback' => [
            'callback' => [$this, 'validateLabelCode'],
          ],
        ],
      ]);

    return $constraints;
  }

  /**
   * Validates type_id against existing PsDiagnostic entities.
   *
   * @param string|null $typeId
   *   The type ID to validate.
   * @param \Symfony\Component\Validator\Context\ExecutionContextInterface $context
   *   The validation context.
   */
  public function validateTypeId(?string $typeId, $context): void {
    if (empty($typeId)) {
      return;
    }

    $storage = \Drupal::entityTypeManager()->getStorage('diagnostic');
    $entity = $storage->load($typeId);

    if ($entity === NULL) {
      $context->addViolation('Invalid diagnostic ID: @type_id', ['@type_id' => $typeId]);
    }
  }

  /**
   * Validates class label (A-Z with optional + or - suffixes).
   *
   * @param string|null $labelCode
   *   The label code to validate.
   * @param \Symfony\Component\Validator\Context\ExecutionContextInterface $context
   *   The validation context.
   */
  public function validateLabelCode(?string $labelCode, $context): void {
    if (empty($labelCode)) {
      return;
    }

    // Allow A-Z letters with optional + or - suffixes (e.g., G+, G++, A-).
    if (!preg_match('/^[A-Z+-]+$/i', $labelCode)) {
      $context->addViolation('Invalid label code: @class (must contain only A-Z, +, or -)', ['@class' => $labelCode]);
    }
  }

}
