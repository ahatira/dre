<?php

declare(strict_types=1);

namespace Drupal\ps_agent\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\Attribute\ContentEntityType;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\user\EntityOwnerTrait;
use Drupal\ps_agent\AgentListBuilder;
use Drupal\ps_agent\Form\AgentDeleteForm;
use Drupal\ps_agent\Form\AgentForm;

/**
 * Defines the Agent entity.
 *
 * A content entity for managing real estate agents with CRM synchronization
 * and BO-protected fields. Provides complete agent data management with
 * external ID tracking for CRM imports.
 *
 * @ingroup ps_agent
 *
 * @see \Drupal\ps_agent\Entity\AgentInterface
 */
#[ContentEntityType(
    id: 'agent',
    label: new TranslatableMarkup('Agent'),
    label_collection: new TranslatableMarkup('Agents'),
    label_singular: new TranslatableMarkup('agent'),
    label_plural: new TranslatableMarkup('agents'),
    label_count: [
    'singular' => '@count agent',
    'plural' => '@count agents',
    ],
    bundle_entity_type: 'agent_type',
    handlers: [
    'list_builder' => AgentListBuilder::class,
    'views_data' => 'Drupal\ps_agent\ViewsData\AgentViewsData',
    'form' => [
      'default' => AgentForm::class,
      'add' => AgentForm::class,
      'edit' => AgentForm::class,
      'delete' => AgentDeleteForm::class,
    ],
    'access' => 'Drupal\Core\Entity\EntityAccessControlHandler',
    ],
    base_table: 'agent',
    data_table: 'agent_field_data',
    translatable: true,
    admin_permission: 'administer agent entities',
    entity_keys: [
    'id' => 'id',
    'uuid' => 'uuid',
    'label' => 'label',
    'langcode' => 'langcode',
    'bundle' => 'type',
    'owner' => 'uid',
    ],
    links: [
    'add-form' => '/admin/ps/content/agents/add/{agent_type}',
    'add-page' => '/admin/ps/content/agents/add',
    'edit-form' => '/admin/ps/content/agents/{agent}/edit',
    'delete-form' => '/admin/ps/content/agents/{agent}/delete',
    'canonical' => '/agent/{agent}',
    'collection' => '/admin/ps/content/agents',
    ],
    field_ui_base_route: 'entity.agent_type.edit_form',
)]
final class Agent extends ContentEntityBase implements AgentInterface
{
    use EntityChangedTrait;
    use EntityOwnerTrait;

  /**
   * {@inheritdoc}
   */
    public function preSave(EntityStorageInterface $storage): void
    {
        $firstName = trim((string) ($this->get('first_name')->value ?? ''));
        $lastName = trim((string) ($this->get('last_name')->value ?? ''));
        $this->set('label', trim($firstName . ' ' . $lastName));

        parent::preSave($storage);
    }

  /**
   * {@inheritdoc}
   */
    public function getExternalId(): ?string
    {
        return $this->get('external_id')->value;
    }

  /**
   * {@inheritdoc}
   */
    public function setExternalId(string $externalId): static
    {
        $this->set('external_id', $externalId);
        return $this;
    }

  /**
   * {@inheritdoc}
   */
    public function getCivility(): ?string
    {
        return $this->get('civility')->value;
    }

  /**
   * {@inheritdoc}
   */
    public function setCivility(string $civility): static
    {
        $this->set('civility', $civility);
        return $this;
    }

  /**
   * {@inheritdoc}
   */
    public function getFirstName(): ?string
    {
        return $this->get('first_name')->value;
    }

  /**
   * {@inheritdoc}
   */
    public function setFirstName(string $firstName): static
    {
        $this->set('first_name', $firstName);
        return $this;
    }

  /**
   * {@inheritdoc}
   */
    public function getLastName(): ?string
    {
        return $this->get('last_name')->value;
    }

  /**
   * {@inheritdoc}
   */
    public function setLastName(string $lastName): static
    {
        $this->set('last_name', $lastName);
        return $this;
    }

  /**
   * {@inheritdoc}
   */
    public function getEmail(): ?string
    {
        return $this->get('email')->value;
    }

  /**
   * {@inheritdoc}
   */
    public function setEmail(string $email): static
    {
        $this->set('email', $email);
        return $this;
    }

  /**
   * {@inheritdoc}
   */
    public function getPhone(): ?string
    {
        return $this->get('phone')->value;
    }

  /**
   * {@inheritdoc}
   */
    public function setPhone(string $phone): static
    {
        $this->set('phone', $phone);
        return $this;
    }

  /**
   * {@inheritdoc}
   */
    public function isActive(): bool
    {
        return (bool) $this->get('status')->value;
    }

  /**
   * {@inheritdoc}
   */
    public function setActive(bool $active): static
    {
        $this->set('status', $active ? 1 : 0);
        return $this;
    }

  /**
   * {@inheritdoc}
   */
    public static function baseFieldDefinitions(EntityTypeInterface $entityType): array
    {
        $fields = parent::baseFieldDefinitions($entityType);

        $fields['type'] = BaseFieldDefinition::create('entity_reference')
        ->setLabel(new TranslatableMarkup('Type'))
        ->setDescription(new TranslatableMarkup('Agent type'))
        ->setSetting('target_type', 'agent_type')
        ->setReadOnly(false)
        ->setRequired(true)
        ->setDisplayOptions('view', [
        'label' => 'inline',
        'type' => 'entity_reference_label',
        'weight' => -10,
        ])
        ->setDisplayConfigurable('view', true)
        ->setDisplayOptions('form', [
        'type' => 'options_select',
        'weight' => -10,
        ])
        ->setDisplayConfigurable('form', true);

        $fields['external_id'] = BaseFieldDefinition::create('string')
        ->setLabel(new TranslatableMarkup('External ID'))
        ->setDescription(new TranslatableMarkup('CRM system identifier'))
        ->setSetting('max_length', 255)
        ->setReadOnly(true)
        ->setDisplayOptions('view', [
        'label' => 'inline',
        'weight' => -5,
        ])
        ->setDisplayConfigurable('view', true);

        $fields['label'] = BaseFieldDefinition::create('string')
        ->setLabel(new TranslatableMarkup('Label'))
        ->setDescription(new TranslatableMarkup('Auto-computed from first name and last name'))
        ->setSetting('max_length', 510)
        ->setReadOnly(true)
        ->setDisplayOptions('view', [
        'label' => 'inline',
        'type' => 'string',
        'weight' => -4,
        ])
        ->setDisplayConfigurable('view', true);

        $fields['civility'] = BaseFieldDefinition::create('ps_dictionary')
        ->setLabel(new TranslatableMarkup('Civility'))
        ->setDescription(new TranslatableMarkup('Agent title (Mr., Ms., etc.)'))
        ->setSetting('dictionary_type', 'civility')
        ->setTranslatable(false)
        ->setDisplayOptions('view', [
        'label' => 'inline',
        'type' => 'list_default',
        'weight' => -3,
        ])
        ->setDisplayConfigurable('view', true)
        ->setDisplayOptions('form', [
        'type' => 'options_select',
        'weight' => -3,
        ])
        ->setDisplayConfigurable('form', true);

        $fields['first_name'] = BaseFieldDefinition::create('string')
        ->setLabel(new TranslatableMarkup('First Name'))
        ->setSetting('max_length', 255)
        ->setTranslatable(false)
        ->setRequired(true)
        ->setDisplayOptions('view', [
        'label' => 'inline',
        'type' => 'string',
        'weight' => 0,
        ])
        ->setDisplayConfigurable('view', true)
        ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 0,
        ])
        ->setDisplayConfigurable('form', true);

        $fields['last_name'] = BaseFieldDefinition::create('string')
        ->setLabel(new TranslatableMarkup('Last Name'))
        ->setSetting('max_length', 255)
        ->setTranslatable(false)
        ->setRequired(true)
        ->setDisplayOptions('view', [
        'label' => 'inline',
        'type' => 'string',
        'weight' => 1,
        ])
        ->setDisplayConfigurable('view', true)
        ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 1,
        ])
        ->setDisplayConfigurable('form', true);

        $fields['email'] = BaseFieldDefinition::create('email')
        ->setLabel(new TranslatableMarkup('Email'))
        ->setDescription(new TranslatableMarkup('BO-protected field'))
        ->setTranslatable(false)
        ->setDisplayOptions('view', [
        'label' => 'inline',
        'weight' => 2,
        ])
        ->setDisplayConfigurable('view', true)
        ->setDisplayOptions('form', [
        'weight' => 2,
        ])
        ->setDisplayConfigurable('form', true);

        $fields['phone'] = BaseFieldDefinition::create('telephone')
        ->setLabel(new TranslatableMarkup('Phone'))
        ->setDescription(new TranslatableMarkup('BO-protected field'))
        ->setTranslatable(false)
        ->setDisplayOptions('view', [
        'label' => 'inline',
        'type' => 'telephone_link',
        'weight' => 3,
        ])
        ->setDisplayConfigurable('view', true)
        ->setDisplayOptions('form', [
        'type' => 'telephone_default',
        'weight' => 3,
        ])
        ->setDisplayConfigurable('form', true);

        $fields['avatar'] = BaseFieldDefinition::create('image')
        ->setLabel(new TranslatableMarkup('Avatar'))
        ->setDescription(new TranslatableMarkup('Agent avatar image'))
        ->setTranslatable(false)
        ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'image',
        'weight' => 4,
        'settings' => [
          'image_style' => 'thumbnail',
        ],
        ])
        ->setDisplayOptions('form', [
        'type' => 'image_image',
        'weight' => 4,
        'settings' => [
          'preview_image_style' => 'thumbnail',
          'progress_indicator' => 'throbber',
        ],
        ])
        ->setDisplayConfigurable('view', true)
        ->setDisplayConfigurable('form', true);

        $fields['status'] = BaseFieldDefinition::create('boolean')
        ->setLabel(new TranslatableMarkup('Active'))
        ->setDefaultValue(true)
        ->setDisplayOptions('view', [
        'label' => 'inline',
        'weight' => 10,
        ])
        ->setDisplayConfigurable('view', true)
        ->setDisplayOptions('form', [
        'weight' => 10,
        ])
        ->setDisplayConfigurable('form', true);

        $fields['path'] = BaseFieldDefinition::create('path')
        ->setLabel(new TranslatableMarkup('URL alias'))
        ->setTranslatable(true)
        ->setDisplayOptions('form', [
        'type' => 'path',
        'weight' => 30,
        ])
        ->setDisplayConfigurable('form', true)
        ->setDisplayConfigurable('view', true)
        ->setComputed(true);

        $fields['created'] = BaseFieldDefinition::create('created')
        ->setLabel(new TranslatableMarkup('Created'))
        ->setDisplayOptions('view', [
        'label' => 'inline',
        'weight' => 20,
        ])
        ->setDisplayConfigurable('view', true)
        ->setDisplayOptions('form', [
        'type' => 'datetime_timestamp',
        'weight' => 20,
        ])
        ->setDisplayConfigurable('form', true);

        $fields['changed'] = BaseFieldDefinition::create('changed')
        ->setLabel(new TranslatableMarkup('Last Updated'))
        ->setDisplayOptions('view', [
        'label' => 'inline',
        'weight' => 21,
        ])
        ->setDisplayConfigurable('view', true);

        $fields += static::ownerBaseFieldDefinitions($entityType);
        $fields['uid']
        ->setLabel(new TranslatableMarkup('Authored by'))
        ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => 19,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => 60,
          'placeholder' => '',
        ],
        ])
        ->setDisplayConfigurable('form', true)
        ->setDisplayConfigurable('view', true);

        return $fields;
    }
}
