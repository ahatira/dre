<?php

namespace Drupal\ps_agent\Plugin\Action;

use Drupal\Core\Action\ActionBase;
use Drupal\Core\Session\AccountInterface;

/**
 * Publishes an agent.
 *
 * @Action(
 *   id = "agent_publish_action",
 *   label = @Translation("Publish agent"),
 *   type = "ps_agent"
 * )
 */
class PublishAgent extends ActionBase {

  /**
   * {@inheritdoc}
   */
  public function execute($entity = NULL) {
    if ($entity) {
      $entity->set('status', TRUE)->save();
    }
  }

  /**
   * {@inheritdoc}
   */
  public function access($object, AccountInterface $account = NULL, $return_as_object = FALSE) {
    /** @var \Drupal\ps_agent\Entity\AgentInterface $object */
    $result = $object->access('update', $account, TRUE)
      ->andIf($object->status->access('edit', $account, TRUE));

    return $return_as_object ? $result : $result->isAllowed();
  }

}
