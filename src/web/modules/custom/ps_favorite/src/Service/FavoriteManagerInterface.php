<?php

declare(strict_types=1);

namespace Drupal\ps_favorite\Service;

use Drupal\Core\Entity\EntityInterface;

interface FavoriteManagerInterface {

  public function addFavorite(EntityInterface $entity): bool;

  public function removeFavorite(EntityInterface $entity): bool;

  public function toggleFavorite(EntityInterface $entity): bool;

  public function isFavorite(EntityInterface $entity): bool;

  /**
   * @return \Drupal\Core\Entity\EntityInterface[]
   *   Favorited entities ordered by recency.
   */
  public function getFavorites(?string $entityTypeId = NULL, int $limit = 0, int $offset = 0): array;

  public function getFavoritesCount(?string $entityTypeId = NULL): int;

  public function mergeAnonymousFavorites(?string $entityTypeId = NULL): void;

  public function getLimitForEntity(EntityInterface $entity): ?int;

  public function supportsEntity(EntityInterface $entity): bool;

  public function getPreferredViewMode(EntityInterface $entity): ?string;
}
