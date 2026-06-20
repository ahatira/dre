<?php

declare(strict_types=1);

namespace Drupal\ps_migrate;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Access control for import_run entities.
 */
final class ImportRunAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account): AccessResult {
    if ($account->hasPermission('manage ps_migrate import pipeline')
      || $account->hasPermission('manage ps_migrate')) {
      return AccessResult::allowed()->cachePerPermissions();
    }
    if ($operation === 'view' && $account->hasPermission('view ps_migrate import runs')) {
      return AccessResult::allowed()->cachePerPermissions();
    }
    return AccessResult::forbidden()->cachePerPermissions();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL): AccessResult {
    return AccessResult::allowedIf(
      $account->hasPermission('manage ps_migrate import pipeline')
      || $account->hasPermission('manage ps_migrate'),
    )->cachePerPermissions();
  }

}
