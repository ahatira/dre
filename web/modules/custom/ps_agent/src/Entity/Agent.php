<?php

declare(strict_types=1);

namespace Drupal\ps_agent\Entity;

use Drupal\Core\Entity\Attribute\ContentEntityType;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityPublishedTrait;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityViewsData;
use Drupal\Core\Entity\Routing\AdminHtmlRouteProvider;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\link\LinkItemInterface;
use Drupal\ps_agent\AgentListBuilder;
use Drupal\ps_agent\Form\AgentDeleteForm;
use Drupal\ps_agent\Form\AgentForm;

/**
 * Defines the Agent entity.
 */
#[ContentEntityType(
    id: 'agent',
    label: new TranslatableMarkup('Agent'),
    label_collection: new TranslatableMarkup('Agents'),
    label_singular: new TranslatableMarkup('agent'),
    label_plural: new TranslatableMarkup('agents'),
    handlers: [
    'list_builder' => AgentListBuilder::class,
    'access' => EntityAccessControlHandler::class,
    'views_data' => EntityViewsData::class,
    'form' => [
      'add' => AgentForm::class,
      'default' => AgentForm::class,
      'edit' => AgentForm::class,
      'delete' => AgentDeleteForm::class,
    ],
    'route_provider' => [
      'html' => AdminHtmlRouteProvider::class,
    ],
    ],
    base_table: 'agent',
    admin_permission: 'administer agent entities',
    entity_keys: [
    'id' => 'id',
    'uuid' => 'uuid',
    'label' => 'name',
    'status' => 'status',
    ],
    links: [
    'canonical' => '/admin/content/agents/{agent}',
    'add-form' => '/admin/content/agents/add',
    'edit-form' => '/admin/content/agents/{agent}/edit',
    'delete-form' => '/admin/content/agents/{agent}/delete',
    'collection' => '/admin/content/agents',
    'settings' => '/admin/structure/agent',
    ],
    field_ui_base_route: 'entity.agent.settings',
    collection_permission: 'administer agent entities',
)]
final class Agent extends ContentEntityBase implements AgentInterface
{
    use EntityChangedTrait;
    use EntityPublishedTrait;

  /**
   * {@inheritdoc}
   */
    public static function preCreate(EntityStorageInterface $storage_controller, array &$values): void
    {
        parent::preCreate($storage_controller, $values);
        $values += [
        'status' => true,
        'webform_id' => 'contact',
        ];
    }

  /**
   * {@inheritdoc}
   */
    public function getName(): ?string
    {
        $value = $this->get('name')->value;
        return $value !== null ? (string) $value : null;
    }

  /**
   * {@inheritdoc}
   */
    public function setName(string $name): self
    {
        $this->set('name', $name);
        return $this;
    }

  /**
   * {@inheritdoc}
   */
    public function getPhone(): ?string
    {
        $value = $this->get('phone')->value;
        return $value !== null ? (string) $value : null;
    }

  /**
   * {@inheritdoc}
   */
    public function setPhone(?string $phone): self
    {
        $this->set('phone', $phone);
        return $this;
    }

  /**
   * {@inheritdoc}
   */
    public static function baseFieldDefinitions(EntityTypeInterface $entity_type): array
    {
        $fields = parent::baseFieldDefinitions($entity_type);

        $fields['id'] = BaseFieldDefinition::create('integer')
        ->setLabel(new TranslatableMarkup('ID'))
        ->setReadOnly(true)
        ->setSetting('unsigned', true);

        $fields['uuid'] = BaseFieldDefinition::create('uuid')
        ->setLabel(new TranslatableMarkup('UUID'))
        ->setReadOnly(true);

        $fields['name'] = BaseFieldDefinition::create('string')
        ->setLabel(new TranslatableMarkup('Name'))
        ->setRequired(true)
        ->setSettings([
        'max_length' => 255,
        'text_processing' => 0,
        ])
        ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'string',
        'weight' => -10,
        ])
        ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -10,
        'settings' => ['size' => 60],
        ]);

        $fields['job_title'] = BaseFieldDefinition::create('string')
        ->setLabel(new TranslatableMarkup('Job title'))
        ->setSettings([
        'max_length' => 128,
        'text_processing' => 0,
        ])
        ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'string',
        'weight' => -9,
        ])
        ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -9,
        ])
        ->setDisplayConfigurable('form', true)
        ->setDisplayConfigurable('view', true);

        $fields['photo'] = BaseFieldDefinition::create('image')
        ->setLabel(new TranslatableMarkup('Photo'))
        ->setSetting('alt_field', true)
        ->setSetting('alt_field_required', true)
        ->setSetting('file_extensions', 'png jpg jpeg webp')
        ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'image',
        'weight' => -8,
        'settings' => [
          'image_style' => '',
          'image_link' => '',
        ],
        ])
        ->setDisplayOptions('form', [
        'type' => 'image_image',
        'weight' => -8,
        ])
        ->setDisplayConfigurable('form', true)
        ->setDisplayConfigurable('view', true);

        $fields['phone'] = BaseFieldDefinition::create('string')
        ->setLabel(new TranslatableMarkup('Phone'))
        ->setSettings([
        'max_length' => 40,
        'text_processing' => 0,
        ])
        ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'string',
        'weight' => -7,
        ])
        ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -7,
        ])
        ->setDisplayConfigurable('form', true)
        ->setDisplayConfigurable('view', true);

        $fields['email'] = BaseFieldDefinition::create('string')
        ->setLabel(new TranslatableMarkup('Email'))
        ->setSettings([
        'max_length' => 254,
        'text_processing' => 0,
        ])
        ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'string',
        'weight' => -6,
        ])
        ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -6,
        ])
        ->setDisplayConfigurable('form', true)
        ->setDisplayConfigurable('view', true);

        $fields['webform_id'] = BaseFieldDefinition::create('string')
        ->setLabel(new TranslatableMarkup('Webform ID'))
        ->setDescription(new TranslatableMarkup('Machine name of the webform to open in modal (example: contact).'))
        ->setSettings([
        'max_length' => 128,
        'text_processing' => 0,
        ])
        ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -5,
        ])
        ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -5,
        ])
        ->setDisplayConfigurable('form', true)
        ->setDisplayConfigurable('view', true);

        $fields['contact_cta'] = BaseFieldDefinition::create('link')
        ->setLabel(new TranslatableMarkup('Contact CTA URL'))
        ->setDescription(new TranslatableMarkup('Optional override URL for AJAX modal open. Use internal:/webform/contact for internal paths.'))
        ->setSettings([
        'link_type' => LinkItemInterface::LINK_GENERIC,
        'title' => 2,
        ])
        ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'link',
        'weight' => -4,
        ])
        ->setDisplayOptions('form', [
        'type' => 'link_default',
        'weight' => -4,
        ])
        ->setDisplayConfigurable('form', true)
        ->setDisplayConfigurable('view', true);

        $fields['status'] = BaseFieldDefinition::create('boolean')
        ->setLabel(new TranslatableMarkup('Published'))
        ->setDefaultValue(true)
        ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => 90,
        ]);

        $fields['created'] = BaseFieldDefinition::create('created')
        ->setLabel(new TranslatableMarkup('Created'));

        $fields['changed'] = BaseFieldDefinition::create('changed')
        ->setLabel(new TranslatableMarkup('Changed'));

        return $fields;
    }
}
