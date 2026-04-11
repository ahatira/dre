<?php

declare(strict_types=1);

namespace Drupal\ps_agent\ViewsData;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Agent entities.
 *
 * Extends EntityViewsData to expose the operations field for agent list views.
 */
class AgentViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData(): array {
    $data = parent::getViewsData();

    // Expose the operations field on the agent_field_data table.
    $data['agent_field_data']['operations'] = [
      'title' => $this->t('Operations'),
      'help' => $this->t('Provides links to perform actions on the agent.'),
      'field' => [
        'id' => 'entity_operations',
        'click sortable' => FALSE,
      ],
    ];

    return $data;
  }

}
