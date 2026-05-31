<?php

declare(strict_types=1);

namespace Drupal\ps_core\Trait;

use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Provides reusable base field definitions for entity protection system.
 *
 * Use this trait in entity classes that need internal_lock,
 * source_tracking, or checksum fields for import protection.
 */
trait EntityProtectionTrait {

  /**
   * Defines the internal_lock base field.
   *
   * This boolean field protects manually curated entity data
   * from automated overwrites during imports.
   *
   * @return \Drupal\Core\Field\BaseFieldDefinition
   *   The internal_lock field definition.
   */
  public static function internalLockBaseFieldDefinition(): BaseFieldDefinition {
    return BaseFieldDefinition::create('boolean')
      ->setLabel(new TranslatableMarkup('Internal lock'))
      ->setDescription(new TranslatableMarkup('Protects manually curated data from automated overwrites.'))
      ->setDefaultValue(FALSE)
      ->setRevisionable(TRUE)
      ->setTranslatable(FALSE)
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => 97,
        'settings' => [
          'display_label' => TRUE,
        ],
      ])
      ->setDisplayOptions('view', [
        'type' => 'boolean',
        'label' => 'above',
        'weight' => 97,
        'settings' => [
          'format' => 'custom',
          'format_custom_true' => 'Protected',
          'format_custom_false' => 'Not protected',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);
  }

  /**
   * Defines the source_tracking base field.
   *
   * This long text field stores structured JSON payload
   * for source traceability (source system, ID, timestamp).
   * Hidden from form - managed programmatically.
   *
   * @return \Drupal\Core\Field\BaseFieldDefinition
   *   The source_tracking field definition.
   */
  public static function sourceTrackingBaseFieldDefinition(): BaseFieldDefinition {
    return BaseFieldDefinition::create('string_long')
      ->setLabel(new TranslatableMarkup('Source tracking'))
      ->setDescription(new TranslatableMarkup('Structured JSON payload used for source traceability.'))
      ->setRevisionable(TRUE)
      ->setTranslatable(FALSE)
      ->setDisplayOptions('view', [
        'type' => 'basic_string',
        'label' => 'above',
        'weight' => 98,
      ])
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayConfigurable('view', TRUE);
  }

  /**
   * Defines the checksum base field.
   *
   * This string field stores a SHA256 hash of entity data
   * for idempotence checking during imports.
   *
   * @return \Drupal\Core\Field\BaseFieldDefinition
   *   The checksum field definition.
   */
  public static function checksumBaseFieldDefinition(): BaseFieldDefinition {
    return BaseFieldDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Checksum'))
      ->setDescription(new TranslatableMarkup('SHA256 hash of entity data for import idempotence.'))
      ->setRevisionable(TRUE)
      ->setTranslatable(FALSE)
      ->setSetting('max_length', 64)
      ->setDisplayOptions('view', [
        'type' => 'string',
        'label' => 'above',
        'weight' => 99,
      ])
      ->setDisplayConfigurable('view', TRUE);
  }

  /**
   * Defines the availability_text base field.
   *
   * This text field stores raw availability text imported
   * from source systems for reference.
   *
   * @return \Drupal\Core\Field\BaseFieldDefinition
   *   The availability_text field definition.
   */
  public static function availabilityTextBaseFieldDefinition(): BaseFieldDefinition {
    return BaseFieldDefinition::create('string_long')
      ->setLabel(new TranslatableMarkup('Availability text'))
      ->setDescription(new TranslatableMarkup('Raw availability text imported from source systems.'))
      ->setRevisionable(TRUE)
      ->setTranslatable(FALSE)
      ->setDisplayOptions('form', [
        'type' => 'string_textarea',
        'weight' => 96,
        'settings' => [
          'rows' => 2,
        ],
      ])
      ->setDisplayOptions('view', [
        'type' => 'basic_string',
        'label' => 'above',
        'weight' => 96,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);
  }

}
