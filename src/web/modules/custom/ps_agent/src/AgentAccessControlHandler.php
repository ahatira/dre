<?php

declare(strict_types=1);

namespace Drupal\ps_agent;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\ps_agent\Entity\AgentInterface;

/**
 * Access controller for Agent entities.
 */
final class AgentAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account): AccessResult {
    if ($operation === 'view' && $entity instanceof AgentInterface) {
      if ($entity->hasField('status') && (bool) $entity->get('status')->value) {
        return AccessResult::allowed()
          ->cachePerPermissions()
          ->addCacheableDependency($entity);
      }

      return AccessResult::allowedIfHasPermission($account, 'administer ps agent entities')
        ->cachePerPermissions()
        ->addCacheableDependency($entity);
    }

    return match ($operation) {
      'update', 'delete' => AccessResult::allowedIfHasPermission($account, 'administer ps agent entities'),
      default => AccessResult::neutral(),
    };
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL): AccessResult {
    return AccessResult::allowedIfHasPermission($account, 'administer ps agent entities');
  }

}
