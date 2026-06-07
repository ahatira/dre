<?php

declare(strict_types=1);

namespace Drupal\ps_surface\Entity;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Access handler for surface division entities embedded on public offers.
 */
final class SurfaceDivisionAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account): AccessResult {
    if (in_array($operation, ['view', 'view label'], TRUE)) {
      // Embedded in offer fields only — no public canonical route.
      return AccessResult::allowed()->addCacheableDependency($entity);
    }

    return parent::checkAccess($entity, $operation, $account);
  }

}
