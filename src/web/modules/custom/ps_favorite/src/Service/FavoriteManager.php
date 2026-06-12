<?php

declare(strict_types=1);

namespace Drupal\ps_favorite\Service;

use Drupal\Core\Cache\CacheTagsInvalidatorInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\ps_favorite\Entity\FavoriteTarget;
use Drupal\ps_favorite\Repository\FavoriteRepositoryInterface;

final class FavoriteManager implements FavoriteManagerInterface {

  /**
   * @var array<string, int[]>
   */
  private array $anonymousEntityIds = [];

  /**
    * @var array<string, array{max_favorites: int, view_mode: string, status: bool}>|null
   */
    private ?array $targetDefinitions = NULL;

  public function __construct(
    private readonly AccountProxyInterface $currentUser,
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly FavoriteRepositoryInterface $repository,
    private readonly FavoriteCookieStorage $cookieStorage,
    private readonly FavoriteCookieState $cookieState,
    private readonly CacheTagsInvalidatorInterface $cacheTagsInvalidator,
    private readonly ConfigFactoryInterface $configFactory,
  ) {}

  public function addFavorite(EntityInterface $entity): bool {
    if (!$this->supportsEntity($entity)) {
      return FALSE;
    }

    if ($this->isFavorite($entity)) {
      return FALSE;
    }

    $limit = $this->getLimitForEntity($entity);
    if ($limit !== NULL && $this->getTargetFavoritesCount($entity) >= $limit) {
      return FALSE;
    }

    $changed = FALSE;
    if ($this->currentUser->isAuthenticated()) {
      $changed = $this->repository->add((int) $this->currentUser->id(), $entity->getEntityTypeId(), (int) $entity->id());
    }
    else {
      $ids = $this->getAnonymousEntityIds($entity->getEntityTypeId(), TRUE);
      $entityId = (int) $entity->id();
      if (!in_array($entityId, $ids, TRUE)) {
        $ids[] = $entityId;
        $this->setAnonymousEntityIds($entity->getEntityTypeId(), $ids);
        $changed = TRUE;
      }
    }

    if ($changed) {
      $this->invalidateEntity($entity);
    }

    return $changed;
  }

  public function removeFavorite(EntityInterface $entity): bool {
    if (!$this->supportsEntity($entity)) {
      return FALSE;
    }

    $changed = FALSE;
    if ($this->currentUser->isAuthenticated()) {
      $changed = $this->repository->remove((int) $this->currentUser->id(), $entity->getEntityTypeId(), (int) $entity->id());
    }
    else {
      $ids = $this->getAnonymousEntityIds($entity->getEntityTypeId(), TRUE);
      $filtered = array_values(array_diff($ids, [(int) $entity->id()]));
      $changed = count($filtered) !== count($ids);
      if ($changed) {
        $this->setAnonymousEntityIds($entity->getEntityTypeId(), $filtered);
      }
    }

    if ($changed) {
      $this->invalidateEntity($entity);
    }

    return $changed;
  }

  public function toggleFavorite(EntityInterface $entity): bool {
    return $this->isFavorite($entity)
      ? !$this->removeFavorite($entity)
      : $this->addFavorite($entity);
  }

  public function isFavorite(EntityInterface $entity): bool {
    if (!$this->supportsEntity($entity)) {
      return FALSE;
    }

    if ($this->currentUser->isAuthenticated()) {
      return $this->repository->has((int) $this->currentUser->id(), $entity->getEntityTypeId(), (int) $entity->id());
    }

    return in_array((int) $entity->id(), $this->getAnonymousEntityIds($entity->getEntityTypeId(), TRUE), TRUE);
  }

  public function getFavorites(?string $entityTypeId = NULL, int $limit = 0, int $offset = 0): array {
    return $this->loadEntries($this->getFavoriteEntries($entityTypeId, $limit, $offset));
  }

  public function getFavoritesCount(?string $entityTypeId = NULL): int {
    return count($this->getFavorites($entityTypeId));
  }

  public function mergeAnonymousFavorites(?string $entityTypeId = NULL): void {
    if (!$this->currentUser->isAuthenticated()) {
      return;
    }

    $entityTypeIds = $entityTypeId !== NULL ? [$entityTypeId] : array_keys($this->cookieStorage->getAllItems());
    foreach ($entityTypeIds as $currentEntityTypeId) {
      $entityIds = $this->getAnonymousEntityIds($currentEntityTypeId, TRUE);
      if ($entityIds !== []) {
        $storage = $this->entityTypeManager->getStorage($currentEntityTypeId);
        $entities = $storage->loadMultiple($entityIds);
        $supportedIds = [];
        foreach ($entityIds as $candidateId) {
          $entity = $entities[$candidateId] ?? NULL;
          if ($entity instanceof EntityInterface && $this->supportsEntity($entity)) {
            $supportedIds[] = $candidateId;
          }
        }

        if ($supportedIds !== []) {
          $this->repository->mergeEntityIds((int) $this->currentUser->id(), $currentEntityTypeId, $supportedIds);
          $this->invalidateGlobal();
        }
      }

      $this->setAnonymousEntityIds($currentEntityTypeId, []);
    }
  }

  public function getLimitForEntity(EntityInterface $entity): ?int {
    $targetKey = $this->getTargetRuleKey($entity);
    if ($targetKey === NULL) {
      return NULL;
    }

    $definitions = $this->getTargetDefinitions();
    return isset($definitions[$targetKey]['max_favorites']) && $definitions[$targetKey]['max_favorites'] > 0 ? $definitions[$targetKey]['max_favorites'] : NULL;
  }

  public function supportsEntity(EntityInterface $entity): bool {
    return $this->getTargetRuleKey($entity) !== NULL;
  }

  public function getPreferredViewMode(EntityInterface $entity): ?string {
    $targetKey = $this->getTargetRuleKey($entity);
    if ($targetKey === NULL) {
      return NULL;
    }

    $definitions = $this->getTargetDefinitions();
    $viewMode = $definitions[$targetKey]['view_mode'] ?? '';
    return $viewMode !== '' ? $viewMode : NULL;
  }

  /**
   * @return array<string, array{max_favorites: int, view_mode: string, status: bool}>
   *   Active target definitions keyed by entity target.
   */
  private function getTargetDefinitions(): array {
    if ($this->targetDefinitions !== NULL) {
      return $this->targetDefinitions;
    }

    $this->targetDefinitions = [];
    $storage = $this->entityTypeManager->getStorage('ps_favorite_target');
    $entities = $storage->loadMultiple();
    foreach ($entities as $entity) {
      if (!$entity instanceof FavoriteTarget || !$entity->isEnabled()) {
        continue;
      }

      $this->targetDefinitions[$entity->getTargetKey()] = [
        'max_favorites' => $entity->getMaxFavorites(),
        'view_mode' => $entity->getViewMode(),
        'status' => $entity->isEnabled(),
      ];
    }

    if ($this->targetDefinitions !== []) {
      return $this->targetDefinitions;
    }

    // Backward-compatible fallback for legacy map-based configuration.
    $limitMap = $this->getLegacyLimitMap();
    $viewModeMap = $this->getLegacyViewModeMap();
    foreach (array_unique(array_merge(array_keys($limitMap), array_keys($viewModeMap))) as $targetKey) {
      $this->targetDefinitions[$targetKey] = [
        'max_favorites' => $limitMap[$targetKey] ?? 0,
        'view_mode' => $viewModeMap[$targetKey] ?? '',
        'status' => TRUE,
      ];
    }

    return $this->targetDefinitions;
  }

  private function getTargetFavoritesCount(EntityInterface $entity): int {
    $targetKey = $this->getTargetRuleKey($entity);
    if ($targetKey === NULL) {
      return 0;
    }

    $count = 0;
    foreach ($this->getFavorites($entity->getEntityTypeId()) as $favoriteEntity) {
      if ($favoriteEntity instanceof EntityInterface && $this->getTargetRuleKey($favoriteEntity) === $targetKey) {
        $count++;
      }
    }

    return $count;
  }

  private function getTargetRuleKey(EntityInterface $entity): ?string {
    $entityTypeId = $entity->getEntityTypeId();
    $bundle = method_exists($entity, 'bundle') ? (string) $entity->bundle() : '';
    $definitions = $this->getTargetDefinitions();

    if ($bundle !== '') {
      $exactKey = $entityTypeId . '.' . $bundle;
      if (array_key_exists($exactKey, $definitions)) {
        return $exactKey;
      }
    }

    $wildcardKey = $entityTypeId . '.*';
    return array_key_exists($wildcardKey, $definitions) ? $wildcardKey : NULL;
  }

  /**
   * @return array<string, int>
   *   Legacy parsed limit map from configuration.
   */
  private function getLegacyLimitMap(): array {
    $limitMap = [];
    $raw = trim((string) $this->configFactory->get('ps_favorite.settings')->get('max_favorites_map'));
    if ($raw === '') {
      return $limitMap;
    }

    $lines = preg_split('/\r\n|\r|\n/', $raw) ?: [];
    foreach ($lines as $line) {
      $line = trim($line);
      if ($line === '') {
        continue;
      }
      if (preg_match('/^([a-z0-9_]+\.(?:[a-z0-9_]+|\*)):(\d+)$/', $line, $matches)) {
        $limitMap[$matches[1]] = (int) $matches[2];
      }
    }

    return $limitMap;
  }

  /**
   * @return array<string, string>
   *   Legacy parsed view mode map from configuration.
   */
  private function getLegacyViewModeMap(): array {
    $viewModeMap = [];
    $raw = trim((string) $this->configFactory->get('ps_favorite.settings')->get('view_mode_map'));
    if ($raw === '') {
      return $viewModeMap;
    }

    $lines = preg_split('/\r\n|\r|\n/', $raw) ?: [];
    foreach ($lines as $line) {
      $line = trim($line);
      if ($line === '') {
        continue;
      }
      if (preg_match('/^([a-z0-9_]+\.(?:[a-z0-9_]+|\*)):([a-z0-9_]+)$/', $line, $matches)) {
        $viewModeMap[$matches[1]] = $matches[2];
      }
    }

    return $viewModeMap;
  }

  /**
   * @return array<int, array{entity_type: string, entity_id: int}>
   *   Favorite entries ordered by recency.
   */
  private function getFavoriteEntries(?string $entityTypeId, int $limit, int $offset): array {
    if ($this->currentUser->isAuthenticated()) {
      if ($entityTypeId !== NULL) {
        return array_map(
          static fn (int $entityId): array => ['entity_type' => $entityTypeId, 'entity_id' => $entityId],
          $this->repository->getEntityIds($entityTypeId, (int) $this->currentUser->id(), $limit, $offset),
        );
      }

      return $this->repository->getEntries((int) $this->currentUser->id(), $limit, $offset);
    }

    $entries = [];
    if ($entityTypeId !== NULL) {
      foreach (array_slice($this->getAnonymousEntityIds($entityTypeId, TRUE), $offset, $limit ?: NULL) as $entityId) {
        $entries[] = ['entity_type' => $entityTypeId, 'entity_id' => $entityId];
      }
      return $entries;
    }

    $entries = [];
    $allItems = $this->cookieState->hasPendingChanges()
      ? array_merge($this->cookieStorage->getAllItems(), $this->cookieState->getOverrides())
      : $this->cookieStorage->getAllItems();

    foreach ($this->cookieState->getClearedEntityTypes() as $clearedEntityTypeId) {
      unset($allItems[$clearedEntityTypeId]);
    }

    foreach ($allItems as $currentEntityTypeId => $entityIds) {
      foreach ($entityIds as $entityId) {
        $entries[] = ['entity_type' => $currentEntityTypeId, 'entity_id' => (int) $entityId];
      }
    }

    if ($offset > 0 || $limit > 0) {
      $entries = array_slice($entries, $offset, $limit ?: NULL);
    }

    return $entries;
  }

  /**
   * @param array<int, array{entity_type: string, entity_id: int}> $entries
   *   Favorite entries.
   *
   * @return \Drupal\Core\Entity\EntityInterface[]
   *   Supported visible entities ordered by recency.
   */
  private function loadEntries(array $entries): array {
    if ($entries === []) {
      return [];
    }

    $entitiesByType = [];
    foreach ($entries as $entry) {
      $entitiesByType[$entry['entity_type']][] = $entry['entity_id'];
    }

    $loaded = [];
    foreach ($entitiesByType as $entityTypeId => $entityIds) {
      $loaded[$entityTypeId] = $this->entityTypeManager->getStorage($entityTypeId)->loadMultiple($entityIds);
    }

    $ordered = [];
    foreach ($entries as $entry) {
      $entity = $loaded[$entry['entity_type']][$entry['entity_id']] ?? NULL;
      if ($entity instanceof EntityInterface && $this->supportsEntity($entity) && $entity->access('view')) {
        $ordered[] = $entity;
      }
    }

    return $ordered;
  }

  /**
   * @return int[]
   *   Anonymous entity IDs.
   */
  private function getAnonymousEntityIds(string $entityTypeId, bool $sanitizeExisting): array {
    if (!array_key_exists($entityTypeId, $this->anonymousEntityIds)) {
      $this->anonymousEntityIds[$entityTypeId] = $this->cookieState->hasOverride($entityTypeId)
        ? $this->cookieState->getEntityIds($entityTypeId)
        : $this->cookieStorage->getEntityIds($entityTypeId);
    }

    if (!$sanitizeExisting || $this->anonymousEntityIds[$entityTypeId] === []) {
      return $this->anonymousEntityIds[$entityTypeId];
    }

    $storage = $this->entityTypeManager->getStorage($entityTypeId);
    $entities = $storage->loadMultiple($this->anonymousEntityIds[$entityTypeId]);
    $filtered = [];
    foreach ($this->anonymousEntityIds[$entityTypeId] as $entityId) {
      if (isset($entities[$entityId])) {
        $filtered[] = $entityId;
      }
    }

    if ($filtered !== $this->anonymousEntityIds[$entityTypeId]) {
      $this->setAnonymousEntityIds($entityTypeId, $filtered);
    }

    return $this->anonymousEntityIds[$entityTypeId];
  }

  /**
   * @param int[] $entityIds
   *   Anonymous entity IDs.
   */
  private function setAnonymousEntityIds(string $entityTypeId, array $entityIds): void {
    $this->anonymousEntityIds[$entityTypeId] = array_values(array_filter(array_unique(array_map('intval', $entityIds))));
    if ($this->anonymousEntityIds[$entityTypeId] === []) {
      $this->cookieState->clearEntityType($entityTypeId);
      return;
    }

    $this->cookieState->setEntityIds($entityTypeId, $this->anonymousEntityIds[$entityTypeId]);
  }

  private function invalidateEntity(EntityInterface $entity): void {
    $this->cacheTagsInvalidator->invalidateTags([
      'ps_favorite:list',
      'ps_favorite:count',
      sprintf('ps_favorite:%s:%d', $entity->getEntityTypeId(), (int) $entity->id()),
      ...$entity->getCacheTags(),
    ]);
  }

  private function invalidateGlobal(): void {
    $this->cacheTagsInvalidator->invalidateTags([
      'ps_favorite:list',
      'ps_favorite:count',
    ]);
  }

}
