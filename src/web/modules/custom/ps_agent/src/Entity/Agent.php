<?php

declare(strict_types=1);

namespace Drupal\ps_agent\Entity;

use Drupal\Core\Entity\Attribute\ContentEntityType;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\ps_core\Trait\EntityProtectionTrait;
use Drupal\user\EntityOwnerTrait;

/**
 * Defines the Agent entity.
 */
#[ContentEntityType(
  id: 'ps_agent',
  label: new \Drupal\Core\StringTranslation\TranslatableMarkup('Agent'),
  label_collection: new \Drupal\Core\StringTranslation\TranslatableMarkup('Agents'),
  label_singular: new \Drupal\Core\StringTranslation\TranslatableMarkup('agent'),
  label_plural: new \Drupal\Core\StringTranslation\TranslatableMarkup('agents'),
  label_count: ['singular' => '@count agent', 'plural' => '@count agents'],
  bundle_label: new \Drupal\Core\StringTranslation\TranslatableMarkup('Agent type'),
  bundle_entity_type: 'ps_agent_type',
  handlers: [
    'list_builder' => 'Drupal\\ps_agent\\AgentListBuilder',
    'access' => 'Drupal\\ps_agent\\AgentAccessControlHandler',
    'form' => [
      'default' => 'Drupal\\ps_agent\\Form\\AgentForm',
      'add' => 'Drupal\\ps_agent\\Form\\AgentForm',
      'edit' => 'Drupal\\ps_agent\\Form\\AgentForm',
      'delete' => 'Drupal\\ps_agent\\Form\\AgentDeleteForm',
    ],
    'route_provider' => [
      'html' => 'Drupal\\ps_agent\\Routing\\AgentHtmlRouteProvider',
    ],
    'views_data' => 'Drupal\\views\\EntityViewsData',
  ],
  base_table: 'ps_agent',
  admin_permission: 'administer ps agent entities',
  entity_keys: [
    'id' => 'id',
    'uuid' => 'uuid',
    'bundle' => 'type',
    'owner' => 'uid',
    'status' => 'status',
  ],
  links: [
    'canonical' => '/admin/ps/content/agent/{ps_agent}',
    'add-page' => '/admin/ps/content/agent/add',
    'add-form' => '/admin/ps/content/agent/add/{ps_agent_type}',
    'edit-form' => '/admin/ps/content/agent/{ps_agent}/edit',
    'delete-form' => '/admin/ps/content/agent/{ps_agent}/delete',
    'collection' => '/admin/ps/content/agent',
  ],
  field_ui_base_route: 'entity.ps_agent_type.edit_form',
)]
final class Agent extends ContentEntityBase implements AgentInterface {

  use EntityChangedTrait;
  use EntityOwnerTrait;
  use EntityProtectionTrait;

  /**
   * {@inheritdoc}
   */
  public function label(): string {
    $first_name = $this->getFirstName();
    $last_name = $this->getLastName();
    return trim($first_name . ' ' . $last_name);
  }

  /**
   * {@inheritdoc}
   */
  public function getFirstName(): string {
    return (string) $this->get('first_name')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function getLastName(): string {
    return (string) $this->get('last_name')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function getEmail(): string {
    return (string) $this->get('email')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function getPhone(): string {
    return (string) $this->get('phone')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage): void {
    parent::preSave($storage);
    $this->set('has_avatar', !$this->get('avatar')->isEmpty());
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type): array {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['uid'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Authored by'))
      ->setDescription(t('The user ID of the author of the Agent entity.'))
      ->setSetting('target_type', 'user')
      ->setDefaultValueCallback(static::class . '::getDefaultEntityOwner')
      ->setTranslatable(FALSE)
      ->setRevisionable(FALSE)
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => 90,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => 60,
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Published'))
      ->setDefaultValue(TRUE)
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => 91,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['first_name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('First name'))
      ->setRequired(TRUE)
      ->setSettings(['max_length' => 128])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 0,
      ])
      ->setDisplayOptions('view', [
        'type' => 'string',
        'label' => 'above',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['civility'] = BaseFieldDefinition::create('ps_dictionary')
      ->setLabel(t('Civility'))
      ->setRequired(FALSE)
      ->setSetting('dictionary_type', 'civility')
      ->setDisplayOptions('form', [
        'type' => 'ps_dictionary_options_select',
        'weight' => 1,
      ])
      ->setDisplayOptions('view', [
        'type' => 'ps_dictionary_formatter',
        'label' => 'above',
        'weight' => 1,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['last_name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Last name'))
      ->setRequired(TRUE)
      ->setSettings(['max_length' => 128])
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

    $fields['internal_external'] = BaseFieldDefinition::create('list_string')
      ->setLabel(t('Internal or external'))
      ->setRequired(FALSE)
      ->setDefaultValue('INTERNAL')
      ->setSettings([
        'allowed_values' => [
          'INTERNAL' => 'Internal',
          'EXTERNAL' => 'External',
        ],
      ])
      ->setDisplayOptions('form', [
        'type' => 'options_select',
        'weight' => 6,
      ])
      ->setDisplayOptions('view', [
        'type' => 'list_default',
        'label' => 'above',
        'weight' => 6,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['has_avatar'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Has avatar'))
      ->setDefaultValue(FALSE)
      ->setDisplayOptions('view', [
        'type' => 'boolean',
        'label' => 'above',
        'weight' => 8,
        'settings' => [
          'format' => 'custom',
          'format_custom_true' => 'Yes',
          'format_custom_false' => 'No',
        ],
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['email'] = BaseFieldDefinition::create('email')
      ->setLabel(t('Email'))
      ->setRequired(TRUE)
      ->setDisplayOptions('form', [
        'type' => 'email_default',
        'weight' => 2,
      ])
      ->setDisplayOptions('view', [
        'type' => 'email_mailto',
        'label' => 'above',
        'weight' => 2,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['phone'] = BaseFieldDefinition::create('telephone')
      ->setLabel(t('Phone'))
      ->setRequired(FALSE)
      ->setPropertyConstraints('value', ['Regex' => ['pattern' => '/^\+?[0-9\s\-\(\)\.]{7,24}$/']])
      ->setDisplayOptions('form', [
        'type' => 'telephone_default',
        'weight' => 3,
      ])
      ->setDisplayOptions('view', [
        'type' => 'string',
        'label' => 'above',
        'weight' => 3,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['job_title'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Job title'))
      ->setRequired(FALSE)
      ->setSettings(['max_length' => 128])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 4,
      ])
      ->setDisplayOptions('view', [
        'type' => 'string',
        'label' => 'above',
        'weight' => 4,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['avatar'] = BaseFieldDefinition::create('image')
      ->setLabel(t('Avatar'))
      ->setRequired(FALSE)
      ->setSettings([
        'file_extensions' => 'png jpg jpeg webp',
        'alt_field' => FALSE,
        'title_field' => FALSE,
      ])
      ->setDisplayOptions('form', [
        'type' => 'image_focal_point',
        'weight' => 7,
      ])
      ->setDisplayOptions('view', [
        'type' => 'image',
        'label' => 'above',
        'weight' => 7,
        'settings' => [
          'image_style' => 'thumbnail',
          'image_link' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setTranslatable(FALSE)
      ->setDisplayOptions('view', [
        'type' => 'timestamp',
        'label' => 'above',
        'weight' => 95,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setTranslatable(FALSE);

    // Computed field for display name (first_name + last_name).
    $fields['display_name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Display name'))
      ->setComputed(TRUE)
      ->setClass('\Drupal\ps_agent\Field\DisplayNameItemList')
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayOptions('view', [
        'type' => 'string',
        'label' => 'hidden',
        'weight' => 0,
      ]);

    // Entity protection fields (for CRM import protection).
    $fields['source_tracking'] = self::sourceTrackingBaseFieldDefinition();
    $fields['checksum'] = self::checksumBaseFieldDefinition();
    
    $fields['internal_lock'] = self::internalLockBaseFieldDefinition()
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => 95,
      ])
      ->setDescription(t('⚠️ <strong>Enable this lock to protect manual edits from automated CRM imports.</strong> When locked, this agent will not be overwritten during CRM synchronization.'));

    return $fields;
  }

}
