<?php

declare(strict_types=1);

namespace Drupal\ps_compare\Service;

use Drupal\Core\Entity\EntityInterface;

/**
 *
 */
interface CompareManagerInterface {

  /**
   *
   */
  public function addCompare(EntityInterface $entity): bool;

  /**
   *
   */
  public function removeCompare(EntityInterface $entity): bool;

  /**
   *
   */
  public function toggleCompare(EntityInterface $entity): bool;

  /**
   *
   */
  public function isCompared(EntityInterface $entity): bool;

  /**
   * @return \Drupal\Core\Entity\EntityInterface[]
   *   Compared entities in FIFO order.
   */
  public function getCompareList(?string $entityTypeId = NULL): array;

  /**
   * @return int[]
   *   Compared entity IDs in FIFO order.
   */
  public function getCompareIds(?string $entityTypeId = NULL): array;

  /**
   *
   */
  public function getCompareCount(?string $entityTypeId = NULL): int;

  /**
   *
   */
  public function getMaxItems(): int;

  /**
   *
   */
  public function getMinItems(): int;

  /**
   *
   */
  public function canOpenComparisonPage(): bool;

  /**
   * Removes invalid entries and returns how many were purged.
   */
  public function purgeInvalid(?string $entityTypeId = NULL): int;

  /**
   *
   */
  public function mergeAnonymousCompare(?string $entityTypeId = NULL): void;

  /**
   *
   */
  public function supportsEntity(EntityInterface $entity): bool;

}
