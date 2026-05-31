<?php

declare(strict_types=1);

namespace Drupal\ps_surface\Entity;

use Drupal\Core\Entity\Attribute\ContentEntityType;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\RevisionLogEntityTrait;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ps_core\Trait\EntityProtectionTrait;

/**
 * Defines the surface division entity.
 */
#[ContentEntityType(
  id: 'ps_surface_division',
  label: new TranslatableMarkup('Surface division'),
  label_collection: new TranslatableMarkup('Surface divisions'),
  label_singular: new TranslatableMarkup('surface division'),
  label_plural: new TranslatableMarkup('surface divisions'),
  label_count: ['singular' => '@count surface division', 'plural' => '@count surface divisions'],
  handlers: [
    'storage' => 'Drupal\Core\Entity\Sql\SqlContentEntityStorage',
    'view_builder' => 'Drupal\Core\Entity\EntityViewBuilder',
    'list_builder' => 'Drupal\ps_surface\Controller\SurfaceDivisionListBuilder',
    'views_data' => 'Drupal\views\EntityViewsData',
    'form' => [
      'default' => 'Drupal\Core\Entity\ContentEntityForm',
      'add' => 'Drupal\Core\Entity\ContentEntityForm',
      'edit' => 'Drupal\Core\Entity\ContentEntityForm',
      'delete' => 'Drupal\Core\Entity\ContentEntityDeleteForm',
    ],
    'access' => 'Drupal\Core\Entity\EntityAccessControlHandler',
    'route_provider' => [
      'html' => 'Drupal\Core\Entity\Routing\AdminHtmlRouteProvider',
    ],
  ],
  base_table: 'ps_surface_division',
  data_table: 'ps_surface_division_field_data',
  revision_table: 'ps_surface_division_revision',
  revision_data_table: 'ps_surface_division_field_revision',
  translatable: TRUE,
  admin_permission: 'administer ps surface entities',
  entity_keys: [
    'id' => 'id',
    'revision' => 'revision_id',
    'uuid' => 'uuid',
    'langcode' => 'langcode',
    'label' => 'division_label',
  ],
  revision_metadata_keys: [
    'revision_user' => 'revision_user',
    'revision_created' => 'revision_created',
    'revision_log_message' => 'revision_log_message',
  ],
  links: [
    'canonical' => '/admin/ps/content/surface-division/{ps_surface_division}',
    'add-form' => '/admin/ps/content/surface-division/add',
    'edit-form' => '/admin/ps/content/surface-division/{ps_surface_division}/edit',
    'delete-form' => '/admin/ps/content/surface-division/{ps_surface_division}/delete',
    'collection' => '/admin/ps/content/surface-division',
    'version-history' => '/admin/ps/content/surface-division/{ps_surface_division}/revisions',
    'revision' => '/admin/ps/content/surface-division/{ps_surface_division}/revisions/{ps_surface_division_revision}/view',
    'revision_revert' => '/admin/ps/content/surface-division/{ps_surface_division}/revisions/{ps_surface_division_revision}/revert',
    'revision_delete' => '/admin/ps/content/surface-division/{ps_surface_division}/revisions/{ps_surface_division_revision}/delete',
  ],
)]
final class SurfaceDivision extends ContentEntityBase implements SurfaceDivisionInterface {

  use EntityChangedTrait;
  use RevisionLogEntityTrait;
  use EntityProtectionTrait;


  /**
   * {@inheritdoc}
   */
  public function getDivisionReference(): string {
    return (string) ($this->get('division_reference')->value ?? '');
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type): array {
    $fields = parent::baseFieldDefinitions($entity_type);
    $fields += static::revisionLogBaseFieldDefinitions($entity_type);


    $fields['division_reference'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Division reference'))
      ->setDescription(t('Business identifier for the division.'))
      ->setRequired(TRUE)
      ->setSettings(['max_length' => 128])
      ->setRevisionable(TRUE)
      ->setTranslatable(FALSE)
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 1,
      ])
      ->setDisplayOptions('view', [
        'type' => 'string',
        'label' => 'above',
        'weight' => 1,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['division_label'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Division label'))
      ->setDescription(t('Human readable label for the division.'))
      ->setRequired(FALSE)
      ->setSettings(['max_length' => 255])
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE)
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 2,
      ])
      ->setDisplayOptions('view', [
        'type' => 'string',
        'label' => 'above',
        'weight' => 2,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['surfaces'] = BaseFieldDefinition::create('ps_surface_item')
      ->setLabel(t('Surfaces'))
      ->setDescription(t('Qualified surface values attached to this division.'))
      ->setRequired(FALSE)
      ->setCardinality(BaseFieldDefinition::CARDINALITY_UNLIMITED)
      ->setRevisionable(TRUE)
      ->setTranslatable(FALSE)
      ->setDisplayOptions('form', [
        'type' => 'ps_surface_item_default',
        'weight' => 3,
      ])
      ->setDisplayOptions('view', [
        'type' => 'ps_surface_item_default',
        'label' => 'above',
        'weight' => 3,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['division_status'] = BaseFieldDefinition::create('list_string')
      ->setLabel(t('Division status'))
      ->setDescription(t('Operational status of the division.'))
      ->setSettings([
        'allowed_values' => [
          'AVAILABLE' => 'Available',
          'PARTIAL' => 'Partial',
          'UNAVAILABLE' => 'Unavailable',
          'UNKNOWN' => 'Unknown',
        ],
      ])
      ->setDefaultValue('UNKNOWN')
      ->setRevisionable(TRUE)
      ->setTranslatable(FALSE)
      ->setDisplayOptions('form', [
        'type' => 'options_select',
        'weight' => 4,
      ])
      ->setDisplayOptions('view', [
        'type' => 'list_default',
        'label' => 'above',
        'weight' => 4,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['availability_text'] = self::availabilityTextBaseFieldDefinition()
      ->setTranslatable(TRUE)
      ->setDisplayOptions('form', [
        'type' => 'string_textarea',
        'weight' => 5,
        'settings' => ['rows' => 3],
      ])
      ->setDisplayOptions('view', [
        'type' => 'basic_string',
        'label' => 'above',
        'weight' => 5,
      ]);

    // Source tracking field - hidden from form, managed programmatically.
    $fields['source_tracking'] = self::sourceTrackingBaseFieldDefinition();

    // Checksum field - used for import idempotence, view-only.
    $fields['checksum'] = self::checksumBaseFieldDefinition();

    $fields['internal_lock'] = self::internalLockBaseFieldDefinition()
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => 7,
      ])
      ->setDisplayOptions('view', [
        'type' => 'boolean',
        'label' => 'above',
        'weight' => 7,
      ]);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setRevisionable(TRUE)
      ->setTranslatable(FALSE)
      ->setDisplayOptions('view', [
        'type' => 'timestamp',
        'label' => 'above',
        'weight' => 90,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setRevisionable(TRUE)
      ->setTranslatable(FALSE)
      ->setDisplayOptions('view', [
        'type' => 'timestamp',
        'label' => 'above',
        'weight' => 91,
      ])
      ->setDisplayConfigurable('view', TRUE);

    return $fields;
  }

}
