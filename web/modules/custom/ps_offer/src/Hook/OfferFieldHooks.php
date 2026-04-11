<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Hook;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\field\Entity\FieldConfig;

/**
 * Hooks for ps_offer field creation and management.
 */
final class OfferFieldHooks {

  /**
   * Implements hook_entity_bundle_field_info().
   *
   * Provides field definitions for the offer node type.
   */
  #[Hook('entity_bundle_field_info')]
  public function entityBundleFieldInfo(EntityTypeInterface $entityType, string $bundle): array {
    $fields = [];

    if ($entityType->id() === 'node' && $bundle === 'offer') {
      // Bundle-specific fields will be added via field UI or programmatically.
      // See README_FIELDS.txt for planned fields.
    }

    return $fields;
  }

  /**
   * Creates field storage and field instances for offer node type.
   *
   * Called during module installation.
   */
  public static function createOfferFields(): void {
    // External ID field
    if (!FieldStorageConfig::loadByName('node', 'external_id')) {
      FieldStorageConfig::create([
        'field_name' => 'external_id',
        'entity_type' => 'node',
        'type' => 'string',
        'settings' => ['max_length' => 255],
      ])->save();

      FieldConfig::create([
        'field_name' => 'external_id',
        'entity_type' => 'node',
        'bundle' => 'offer',
        'label' => 'External ID',
        'description' => 'CRM system identifier (readonly)',
        'required' => FALSE,
      ])->save();
    }

    // Reference field
    if (!FieldStorageConfig::loadByName('node', 'reference')) {
      FieldStorageConfig::create([
        'field_name' => 'reference',
        'entity_type' => 'node',
        'type' => 'string',
        'settings' => ['max_length' => 100],
      ])->save();

      FieldConfig::create([
        'field_name' => 'reference',
        'entity_type' => 'node',
        'bundle' => 'offer',
        'label' => 'Reference',
        'description' => 'Internal reference code',
        'required' => FALSE,
      ])->save();
    }

    // Property type code field
    if (!FieldStorageConfig::loadByName('node', 'property_type_code')) {
      FieldStorageConfig::create([
        'field_name' => 'property_type_code',
        'entity_type' => 'node',
        'type' => 'string',
        'settings' => ['max_length' => 50],
      ])->save();

      FieldConfig::create([
        'field_name' => 'property_type_code',
        'entity_type' => 'node',
        'bundle' => 'offer',
        'label' => 'Property Type',
        'description' => 'Property type code from dictionary (ACT, BUR)',
        'required' => TRUE,
      ])->save();
    }

    // Transaction type codes field
    if (!FieldStorageConfig::loadByName('node', 'transaction_type_codes')) {
      FieldStorageConfig::create([
        'field_name' => 'transaction_type_codes',
        'entity_type' => 'node',
        'type' => 'text',
      ])->save();

      FieldConfig::create([
        'field_name' => 'transaction_type_codes',
        'entity_type' => 'node',
        'bundle' => 'offer',
        'label' => 'Transaction Types',
        'description' => 'Transaction type codes from dictionary (LOC, SAL)',
        'required' => TRUE,
      ])->save();
    }

    // Address field
    if (!FieldStorageConfig::loadByName('node', 'address')) {
      FieldStorageConfig::create([
        'field_name' => 'address',
        'entity_type' => 'node',
        'type' => 'string',
        'settings' => ['max_length' => 500],
      ])->save();

      FieldConfig::create([
        'field_name' => 'address',
        'entity_type' => 'node',
        'bundle' => 'offer',
        'label' => 'Address',
        'description' => 'Full property address',
        'required' => TRUE,
      ])->save();
    }

    // Postal code field
    if (!FieldStorageConfig::loadByName('node', 'postal_code')) {
      FieldStorageConfig::create([
        'field_name' => 'postal_code',
        'entity_type' => 'node',
        'type' => 'string',
        'settings' => ['max_length' => 20],
      ])->save();

      FieldConfig::create([
        'field_name' => 'postal_code',
        'entity_type' => 'node',
        'bundle' => 'offer',
        'label' => 'Postal Code',
        'required' => TRUE,
      ])->save();
    }

    // City label field
    if (!FieldStorageConfig::loadByName('node', 'city_label')) {
      FieldStorageConfig::create([
        'field_name' => 'city_label',
        'entity_type' => 'node',
        'type' => 'string',
        'settings' => ['max_length' => 255],
      ])->save();

      FieldConfig::create([
        'field_name' => 'city_label',
        'entity_type' => 'node',
        'bundle' => 'offer',
        'label' => 'City',
        'required' => TRUE,
      ])->save();
    }

    // Description field
    if (!FieldStorageConfig::loadByName('node', 'description')) {
      FieldStorageConfig::create([
        'field_name' => 'description',
        'entity_type' => 'node',
        'type' => 'text_long',
      ])->save();

      FieldConfig::create([
        'field_name' => 'description',
        'entity_type' => 'node',
        'bundle' => 'offer',
        'label' => 'Description',
        'description' => 'Detailed offer description',
        'required' => FALSE,
      ])->save();
    }
  }

}
