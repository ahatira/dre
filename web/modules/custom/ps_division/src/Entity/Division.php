<?php

declare(strict_types=1);

namespace Drupal\ps_division\Entity;

use Drupal\Core\Entity\Attribute\ContentEntityType;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityPublishedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\EntityOwnerTrait;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Defines the Division content entity.
 *
 * Represents a spatial subdivision (lot, floor, apartment) within a parent
 * real estate property. Holds structural classification and surfaces with
 * dictionary-validated codes.
 *
 * Performance: O(1) field access; total surface aggregation O(n) where n
 * is the number of surface items (typically small: 1-10).
 *
 * @see \Drupal\ps_division\Entity\DivisionInterface
 */
#[ContentEntityType(
  id: 'ps_division',
  label: new TranslatableMarkup('Division'),
  label_collection: new TranslatableMarkup('Divisions'),
  label_singular: new TranslatableMarkup('division'),
  label_plural: new TranslatableMarkup('divisions'),
  label_count: [
    'singular' => '@count division',
    'plural' => '@count divisions',
  ],
  handlers: [
    'list_builder' => 'Drupal\\ps_division\\DivisionListBuilder',
    'form' => [
      'default' => 'Drupal\\ps_division\\Form\\DivisionForm',
      'add' => 'Drupal\\ps_division\\Form\\DivisionForm',
      'edit' => 'Drupal\\ps_division\\Form\\DivisionForm',
      'delete' => 'Drupal\\ps_division\\Form\\DivisionDeleteForm',
    ],
    'access' => 'Drupal\\ps_division\\DivisionAccessControlHandler',
  ],
  base_table: 'ps_division',
  data_table: 'ps_division_field_data',
  translatable: TRUE,
  admin_permission: 'administer ps_division entities',
  entity_keys: [
    'id' => 'id',
    'uuid' => 'uuid',
    'label' => 'building_name',
    'langcode' => 'langcode',
    'bundle' => 'type',
    'published' => 'status',
    'owner' => 'uid',
  ],
  bundle_entity_type: 'division_type',
  field_ui_base_route: 'entity.division_type.edit_form',
  links: [
    'canonical' => '/division/{ps_division}',
    'add-form' => '/admin/ps/content/divisions/add/{division_type}',
    'add-page' => '/admin/ps/content/divisions/add',
    'edit-form' => '/admin/ps/content/divisions/{ps_division}/edit',
    'delete-form' => '/admin/ps/content/divisions/{ps_division}/delete',
    'collection' => '/admin/ps/content/divisions',
  ],
)]
final class Division extends ContentEntityBase implements DivisionInterface {

  use EntityChangedTrait;
  use EntityOwnerTrait;
  use EntityPublishedTrait;

  /**
   * {@inheritdoc}
   */
  public function getBuildingName(): string {
    $value = $this->get('building_name')->value;
    return is_string($value) ? $value : '';
  }

  /**
   * {@inheritdoc}
   */
  public function setBuildingName(string $name): static {
    $this->set('building_name', $name);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getLot(): ?string {
    $value = $this->get('lot')->value;
    if ($value === NULL || $value === '') {
      return NULL;
    }
    return is_string($value) ? $value : NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function setLot(?string $lot): static {
    $this->set('lot', $lot);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getAvailability(): ?string {
    $value = $this->get('availability')->value;
    if ($value === NULL || $value === '') {
      return NULL;
    }
    return is_string($value) ? $value : NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function setAvailability(?string $availability): static {
    $this->set('availability', $availability);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getTotalSurface(): float {
    $total = 0.0;
    if (!$this->hasField('surfaces')) {
      return $total;
    }
    foreach ($this->get('surfaces') as $item) {
      /** @var \Drupal\ps_surface\Plugin\Field\FieldType\SurfaceItem $item */
      $value = $item->get('value')->getValue();
      if (is_numeric($value) && (float) $value > 0.0) {
        $total += (float) $value;
      }
    }
    return $total;
  }

  /**
   * {@inheritdoc}
   *
   * @return array<string, \Drupal\Core\Field\FieldDefinitionInterface>
   *   Base field definitions.
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type): array {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['langcode'] = BaseFieldDefinition::create('language')
      ->setLabel(new TranslatableMarkup('Language'))
      ->setDescription(new TranslatableMarkup('The language code for the Division entity.'))
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'language',
        'weight' => 2,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['floor'] = BaseFieldDefinition::create('ps_dictionary')
      ->setLabel(new TranslatableMarkup('Floor'))
      ->setDescription(new TranslatableMarkup('Floor code from floor dictionary (PB, P1, S1, etc.).'))
      ->setSetting('dictionary_type', 'floor')
      ->setDisplayOptions('view', [
        'label' => 'inline',
        'type' => 'list_default',
        'weight' => 3,
      ])
      ->setDisplayOptions('form', [
        'type' => 'options_select',
        'weight' => 3,
      ])
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayConfigurable('form', TRUE);

    $fields['building_name'] = BaseFieldDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Building Name'))
      ->setDescription(new TranslatableMarkup('Name of the building containing this division (entity label).'))
      ->setSetting('max_length', 255)
      ->setTranslatable(TRUE)
      ->setRequired(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'inline',
        'type' => 'string',
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayConfigurable('form', TRUE);

    // Get configurable dictionary types from settings.
    $config = \Drupal::config('ps_division.settings');
    $type_dictionary = $config->get('dictionaries.division_type') ?? 'surface_type';
    $nature_dictionary = $config->get('dictionaries.division_nature') ?? 'surface_nature';

    $fields['division_type'] = BaseFieldDefinition::create('ps_dictionary')
      ->setLabel(new TranslatableMarkup('Type'))
      ->setDescription(new TranslatableMarkup('Division type from configured dictionary.'))
      ->setSetting('dictionary_type', $type_dictionary)
      ->setDisplayOptions('view', [
        'label' => 'inline',
        'type' => 'list_default',
        'weight' => 4,
      ])
      ->setDisplayOptions('form', [
        'type' => 'options_select',
        'weight' => 4,
      ])
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayConfigurable('form', TRUE);

    $fields['nature'] = BaseFieldDefinition::create('ps_dictionary')
      ->setLabel(new TranslatableMarkup('Nature'))
      ->setDescription(new TranslatableMarkup('Division nature from configured dictionary.'))
      ->setSetting('dictionary_type', $nature_dictionary)
      ->setDisplayOptions('view', [
        'label' => 'inline',
        'type' => 'list_default',
        'weight' => 5,
      ])
      ->setDisplayOptions('form', [
        'type' => 'options_select',
        'weight' => 5,
      ])
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayConfigurable('form', TRUE);

    $fields['lot'] = BaseFieldDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Lot'))
      ->setDescription(new TranslatableMarkup('Lot identifier (alphanumeric).'))
      ->setSetting('max_length', 255)
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'inline',
        'type' => 'string',
        'weight' => 1,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 1,
      ])
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayConfigurable('form', TRUE);

    $fields['availability'] = BaseFieldDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Availability'))
      ->setDescription(new TranslatableMarkup('Availability status.'))
      ->setSetting('max_length', 255)
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'inline',
        'type' => 'string',
        'weight' => 6,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 6,
      ])
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayConfigurable('form', TRUE);

    $fields['surfaces'] = BaseFieldDefinition::create('ps_surface')
      ->setLabel(new TranslatableMarkup('Surfaces'))
      ->setDescription(new TranslatableMarkup('Surface measurements with unit, type, nature, and qualification.'))
      ->setCardinality(BaseFieldDefinition::CARDINALITY_UNLIMITED)
      ->setRequired(FALSE)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'ps_surface_default',
        'weight' => 7,
      ])
      ->setDisplayOptions('form', [
        'type' => 'ps_surface_default',
        'weight' => 7,
      ])
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayConfigurable('form', TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(new TranslatableMarkup('Created'))
      ->setDescription(new TranslatableMarkup('The time that the division was created.'))
      ->setTranslatable(FALSE);

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(new TranslatableMarkup('Changed'))
      ->setDescription(new TranslatableMarkup('The time that the division was last edited.'))
      ->setTranslatable(FALSE);

    // Add the owner field.
    $fields += static::ownerBaseFieldDefinitions($entity_type);

    // Add the published field.
    $fields += static::publishedBaseFieldDefinitions($entity_type);

    return $fields;
  }

}
