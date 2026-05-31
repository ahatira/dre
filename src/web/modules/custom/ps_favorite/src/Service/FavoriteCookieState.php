<?php

declare(strict_types=1);

namespace Drupal\ps_favorite\Service;

final class FavoriteCookieState {

  /**
   * @var array<string, int[]>
   */
  private array $overrides = [];

  /**
   * @var array<string, bool>
   */
  private array $clearedEntityTypes = [];

  public function setEntityIds(string $entityTypeId, array $entityIds): void {
    $this->overrides[$entityTypeId] = array_values(array_filter(array_unique(array_map('intval', $entityIds))));
    unset($this->clearedEntityTypes[$entityTypeId]);
  }

  public function clearEntityType(string $entityTypeId): void {
    unset($this->overrides[$entityTypeId]);
    $this->clearedEntityTypes[$entityTypeId] = TRUE;
  }

  public function hasOverride(string $entityTypeId): bool {
    return array_key_exists($entityTypeId, $this->overrides);
  }

  /**
   * @return int[]
   *   Overridden entity IDs for the entity type.
   */
  public function getEntityIds(string $entityTypeId): array {
    return $this->overrides[$entityTypeId] ?? [];
  }

  /**
   * @return array<string, int[]>
   *   All cookie overrides.
   */
  public function getOverrides(): array {
    return $this->overrides;
  }

  /**
   * @return string[]
   *   Entity types scheduled for removal from the cookie.
   */
  public function getClearedEntityTypes(): array {
    return array_keys($this->clearedEntityTypes);
  }

  public function hasPendingChanges(): bool {
    return $this->overrides !== [] || $this->clearedEntityTypes !== [];
  }

}
