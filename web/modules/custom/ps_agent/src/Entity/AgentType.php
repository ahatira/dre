<?php

declare(strict_types=1);

namespace Drupal\ps_agent\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;
use Drupal\Core\Entity\Attribute\ConfigEntityType;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Defines the Agent Type entity.
 *
 * Bundle entity for managing agent type configurations.
 *
 * @see \Drupal\ps_agent\Entity\AgentTypeInterface
 */
#[ConfigEntityType(
  id: 'agent_type',
  label: new TranslatableMarkup('Agent Type'),
  label_collection: new TranslatableMarkup('Agent Types'),
  label_singular: new TranslatableMarkup('agent type'),
  label_plural: new TranslatableMarkup('agent types'),
  label_count: [
    'singular' => '@count agent type',
    'plural' => '@count agent types',
  ],
  handlers: [
    'list_builder' => 'Drupal\\ps_agent\\AgentTypeListBuilder',
    'form' => [
      'add' => 'Drupal\\ps_agent\\Form\\AgentTypeForm',
      'edit' => 'Drupal\\ps_agent\\Form\\AgentTypeForm',
      'delete' => 'Drupal\\Core\\Entity\\EntityDeleteForm',
    ],
    'route_provider' => [
      'html' => 'Drupal\\Core\\Entity\\Routing\\AdminHtmlRouteProvider',
    ],
  ],
  admin_permission: 'administer agent entities',
  config_prefix: 'agent_type',
  bundle_of: 'agent',
  entity_keys: [
    'id' => 'id',
    'label' => 'label',
  ],
  config_export: [
    'id',
    'label',
    'description',
  ],
  links: [
    'add-form' => '/admin/ps/structure/agent-types/add',
    'edit-form' => '/admin/ps/structure/agent-types/manage/{agent_type}',
    'delete-form' => '/admin/ps/structure/agent-types/manage/{agent_type}/delete',
    'collection' => '/admin/ps/structure/agent-types',
  ],
)]
final class AgentType extends ConfigEntityBundleBase implements AgentTypeInterface {

  /**
   * The agent type ID.
   */
  protected string $id;

  /**
   * The agent type label.
   */
  protected string $label;

  /**
   * The agent type description.
   */
  protected string $description = '';

  /**
   * {@inheritdoc}
   */
  public function getDescription(): string {
    return $this->description;
  }

  /**
   * {@inheritdoc}
   */
  public function setDescription(string $description): static {
    $this->description = $description;
    return $this;
  }

}
