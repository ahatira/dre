<?php

namespace Drupal\ps_agent\Plugin\Action;

use Drupal\Core\Action\ActionBase;
use Drupal\Core\Session\AccountInterface;

/**
 * Deletes an agent.
 *
 * @Action(
 *   id = "agent_delete_action",
 *   label = @Translation("Delete agent"),
 *   type = "ps_agent"
 * )
 */
class DeleteAgent extends ActionBase {

  /**
   * {@inheritdoc}
   */
  public function execute($entity = NULL) {
    if ($entity) {
      $entity->delete();
    }
  }

  /**
   * {@inheritdoc}
   */
  public function access($object, AccountInterface $account = NULL, $return_as_object = FALSE) {
    /** @var \Drupal\ps_agent\Entity\AgentInterface $object */
    return $object->access('delete', $account, $return_as_object);
  }

}
