<?php

declare(strict_types=1);

namespace Drupal\ps_division;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Access\AccessResultInterface;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Access controller for the Division entity.
 *
 * @see \Drupal\ps_division\Entity\Division
 */
final class DivisionAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account): AccessResultInterface {
    /** @var \Drupal\ps_division\Entity\DivisionInterface $entity */
    return match ($operation) {
      'view' => AccessResult::allowedIfHasPermission($account, 'view division entities'),
      'update', 'delete' => AccessResult::allowedIfHasPermission($account, 'administer ps_division entities'),
      default => AccessResult::neutral(),
    };
  }

  /**
   * {@inheritdoc}
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user account.
   * @param array<string, mixed> $context
   *   Context array.
   * @param string|null $entity_bundle
   *   The entity bundle.
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL): AccessResultInterface {
    return AccessResult::allowedIfHasPermission($account, 'administer ps_division entities');
  }

}
