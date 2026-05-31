<?php

declare(strict_types=1);

namespace Drupal\ps_agent\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;
use Drupal\Core\Entity\Attribute\ConfigEntityType;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Defines the Agent type config entity.
 */
#[ConfigEntityType(
  id: 'ps_agent_type',
  label: new TranslatableMarkup('Agent type'),
  label_collection: new TranslatableMarkup('Agent types'),
  handlers: [
    'list_builder' => 'Drupal\ps_agent\AgentTypeListBuilder',
    'form' => [
      'add' => 'Drupal\ps_agent\Form\AgentTypeForm',
      'edit' => 'Drupal\ps_agent\Form\AgentTypeForm',
      'delete' => 'Drupal\ps_agent\Form\AgentTypeDeleteForm',
    ],
    'route_provider' => [
      'html' => 'Drupal\ps_agent\Routing\AgentTypeHtmlRouteProvider',
    ],
  ],
  config_prefix: 'ps_agent_type',
  bundle_of: 'ps_agent',
  entity_keys: [
    'id' => 'id',
    'label' => 'label',
    'uuid' => 'uuid',
  ],
  links: [
    'add-form' => '/admin/ps/structure/agent/add',
    'edit-form' => '/admin/ps/structure/agent/manage/{ps_agent_type}',
    'delete-form' => '/admin/ps/structure/agent/manage/{ps_agent_type}/delete',
    'collection' => '/admin/ps/structure/agent',
  ],
  admin_permission: 'administer ps agent types',
  config_export: ['id', 'label'],
)]
final class AgentType extends ConfigEntityBundleBase {}
