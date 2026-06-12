<?php

declare(strict_types=1);

namespace Drupal\ps_compare\Service;

use Drupal\Core\Cache\CacheTagsInvalidatorInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\ps_compare\Repository\CompareRepositoryInterface;

/**
 *
 */
final class CompareManager implements CompareManagerInterface {

  /**
   * @var array<string, int[]>
   */
  private array $anonymousEntityIds = [];

  /**
   * @var string[]|null
   */
  private ?array $enabledTargets = NULL;

  public function __construct(
    private readonly AccountProxyInterface $currentUser,
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly CompareRepositoryInterface $repository,
    private readonly CompareCookieStorage $cookieStorage,
    private readonly CompareCookieState $cookieState,
    private readonly CacheTagsInvalidatorInterface $cacheTagsInvalidator,
    private readonly ConfigFactoryInterface $configFactory,
  ) {}

  /**
   *
   */
  public function addCompare(EntityInterface $entity): bool {
    if (!$this->supportsEntity($entity) || !$entity->access('view')) {
      return FALSE;
    }

    if ($this->isCompared($entity)) {
      return FALSE;
    }

    if ($this->getCompareCount($entity->getEntityTypeId()) >= $this->getMaxItems()) {
      return FALSE;
    }

    $changed = FALSE;
    if ($this->currentUser->isAuthenticated()) {
      $changed = $this->repository->add((int) $this->currentUser->id(), $entity->getEntityTypeId(), (int) $entity->id());
    }
    else {
      $entityTypeId = $entity->getEntityTypeId();
      $ids = $this->getAnonymousEntityIds($entityTypeId, FALSE);
      $entityId = (int) $entity->id();
      if (!in_array($entityId, $ids, TRUE)) {
        $ids[] = $entityId;
        $this->setAnonymousEntityIds($entityTypeId, $ids);
        $changed = TRUE;
      }
    }

    if ($changed) {
      $this->invalidateEntity($entity);
    }

    return $changed;
  }

  /**
   *
   */
  public function removeCompare(EntityInterface $entity): bool {
    if (!$this->supportsEntity($entity)) {
      return FALSE;
    }

    $changed = FALSE;
    if ($this->currentUser->isAuthenticated()) {
      $changed = $this->repository->remove((int) $this->currentUser->id(), $entity->getEntityTypeId(), (int) $entity->id());
    }
    else {
      $entityTypeId = $entity->getEntityTypeId();
      $ids = $this->getAnonymousEntityIds($entityTypeId, FALSE);
      $filtered = array_values(array_diff($ids, [(int) $entity->id()]));
      $changed = count($filtered) !== count($ids);
      if ($changed) {
        $this->setAnonymousEntityIds($entityTypeId, $filtered);
      }
    }

    if ($changed) {
      $this->invalidateEntity($entity);
    }

    return $changed;
  }

  /**
   *
   */
  public function toggleCompare(EntityInterface $entity): bool {
    return $this->isCompared($entity)
      ? !$this->removeCompare($entity)
      : $this->addCompare($entity);
  }

  /**
   *
   */
  public function isCompared(EntityInterface $entity): bool {
    if (!$this->supportsEntity($entity)) {
      return FALSE;
    }

    if ($this->currentUser->isAuthenticated()) {
      return $this->repository->has((int) $this->currentUser->id(), $entity->getEntityTypeId(), (int) $entity->id());
    }

    return in_array((int) $entity->id(), $this->getAnonymousEntityIds($entity->getEntityTypeId(), FALSE), TRUE);
  }

  /**
   *
   */
  public function getCompareList(?string $entityTypeId = NULL): array {
    $this->purgeInvalid($entityTypeId);
    return $this->loadEntries($this->getCompareEntries($entityTypeId));
  }

  /**
   *
   */
  public function getCompareIds(?string $entityTypeId = NULL): array {
    $this->purgeInvalid($entityTypeId);
    $entries = $this->getCompareEntries($entityTypeId);
    return array_map(static fn (array $entry): int => $entry['entity_id'], $entries);
  }

  /**
   *
   */
  public function getCompareCount(?string $entityTypeId = NULL): int {
    return count($this->getCompareIds($entityTypeId));
  }

  /**
   *
   */
  public function getMaxItems(): int {
    return max(1, (int) ($this->configFactory->get('ps_compare.settings')->get('max_items') ?? 4));
  }

  /**
   *
   */
  public function getMinItems(): int {
    return max(1, (int) ($this->configFactory->get('ps_compare.settings')->get('min_items') ?? 2));
  }

  /**
   *
   */
  public function canOpenComparisonPage(): bool {
    return $this->getCompareCount() >= $this->getMinItems();
  }

  /**
   *
   */
  public function purgeInvalid(?string $entityTypeId = NULL): int {
    $purged = 0;
    foreach ($this->getCompareEntries($entityTypeId, FALSE) as $entry) {
      $entity = $this->entityTypeManager->getStorage($entry['entity_type'])->load($entry['entity_id']);
      if (!$entity instanceof EntityInterface || !$entity->access('view') || !$this->supportsEntity($entity)) {
        if ($this->currentUser->isAuthenticated()) {
          if ($this->repository->remove((int) $this->currentUser->id(), $entry['entity_type'], $entry['entity_id'])) {
            $purged++;
          }
        }
        else {
          $ids = $this->getAnonymousEntityIds($entry['entity_type'], FALSE);
          if (in_array($entry['entity_id'], $ids, TRUE)) {
            $this->setAnonymousEntityIds($entry['entity_type'], array_values(array_diff($ids, [$entry['entity_id']])));
            $purged++;
          }
        }
      }
    }

    if ($purged > 0) {
      $this->invalidateGlobal();
    }

    return $purged;
  }

  /**
   *
   */
  public function mergeAnonymousCompare(?string $entityTypeId = NULL): void {
    if (!$this->currentUser->isAuthenticated()) {
      return;
    }

    $entityTypeIds = $entityTypeId !== NULL ? [$entityTypeId] : array_keys($this->cookieStorage->getAllItems());
    foreach ($entityTypeIds as $currentEntityTypeId) {
      $entityIds = $this->getAnonymousEntityIds($currentEntityTypeId, FALSE);
      if ($entityIds === []) {
        continue;
      }

      $storage = $this->entityTypeManager->getStorage($currentEntityTypeId);
      $entities = $storage->loadMultiple($entityIds);
      $supportedIds = [];
      foreach ($entityIds as $candidateId) {
        $entity = $entities[$candidateId] ?? NULL;
        if ($entity instanceof EntityInterface && $this->supportsEntity($entity) && $entity->access('view')) {
          $supportedIds[] = $candidateId;
        }
      }

      if ($supportedIds !== []) {
        $this->repository->mergeEntityIds((int) $this->currentUser->id(), $currentEntityTypeId, $supportedIds);
        $this->invalidateGlobal();
      }

      $this->setAnonymousEntityIds($currentEntityTypeId, []);
    }
  }

  /**
   *
   */
  public function supportsEntity(EntityInterface $entity): bool {
    $entityTypeId = $entity->getEntityTypeId();
    $bundle = method_exists($entity, 'bundle') ? (string) $entity->bundle() : '';
    $targetKey = $bundle !== '' ? $entityTypeId . '.' . $bundle : $entityTypeId;

    return in_array($targetKey, $this->getEnabledTargets(), TRUE);
  }

  /**
   * @return string[]
   *   Enabled target keys (entity_type.bundle).
   */
  private function getEnabledTargets(): array {
    if ($this->enabledTargets !== NULL) {
      return $this->enabledTargets;
    }

    $targets = $this->configFactory->get('ps_compare.settings')->get('enabled_targets') ?? [];
    $this->enabledTargets = array_values(array_filter(array_map('strval', $targets)));

    return $this->enabledTargets;
  }

  /**
   * @return array<int, array{entity_type: string, entity_id: int}>
   *   Compare entries in FIFO order.
   */
  private function getCompareEntries(?string $entityTypeId, bool $sanitizeExisting = TRUE): array {
    if ($this->currentUser->isAuthenticated()) {
      $entries = $this->repository->getEntries((int) $this->currentUser->id());
      if ($entityTypeId !== NULL) {
        $entries = array_values(array_filter(
          $entries,
          static fn (array $entry): bool => $entry['entity_type'] === $entityTypeId,
        ));
      }

      return array_map(
        static fn (array $entry): array => [
          'entity_type' => $entry['entity_type'],
          'entity_id' => $entry['entity_id'],
        ],
        $entries,
      );
    }

    if ($entityTypeId !== NULL) {
      $ids = $this->getAnonymousEntityIds($entityTypeId, $sanitizeExisting);
      $entries = [];
      foreach ($ids as $entityId) {
        $entries[] = ['entity_type' => $entityTypeId, 'entity_id' => $entityId];
      }
      return $entries;
    }

    $entries = [];
    $allItems = $this->cookieState->hasPendingChanges()
      ? array_merge($this->cookieStorage->getAllItems(), $this->cookieState->getOverrides())
      : $this->cookieStorage->getAllItems();

    foreach ($allItems as $currentEntityTypeId => $entityIds) {
      foreach ($entityIds as $entityId) {
        $entries[] = ['entity_type' => $currentEntityTypeId, 'entity_id' => (int) $entityId];
      }
    }

    return $entries;
  }

  /**
   * @param array<int, array{entity_type: string, entity_id: int}> $entries
   *   Compare entries.
   *
   * @return \Drupal\Core\Entity\EntityInterface[]
   *   Supported visible entities in FIFO order.
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
    foreach ($entitiesByType as $typeId => $entityIds) {
      $loaded[$typeId] = $this->entityTypeManager->getStorage($typeId)->loadMultiple($entityIds);
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
   *   Anonymous entity IDs in FIFO order.
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
   *   Anonymous entity IDs in FIFO order.
   */
  private function setAnonymousEntityIds(string $entityTypeId, array $entityIds): void {
    $this->anonymousEntityIds[$entityTypeId] = array_values(array_filter(array_unique(array_map('intval', $entityIds))));
    if ($this->anonymousEntityIds[$entityTypeId] === []) {
      $this->cookieState->clearEntityType($entityTypeId);
      return;
    }

    $this->cookieState->setEntityIds($entityTypeId, $this->anonymousEntityIds[$entityTypeId]);
  }

  /**
   *
   */
  private function invalidateEntity(EntityInterface $entity): void {
    $this->cacheTagsInvalidator->invalidateTags([
      'ps_compare:list',
      'ps_compare:count',
      sprintf('ps_compare:%s:%d', $entity->getEntityTypeId(), (int) $entity->id()),
      ...$entity->getCacheTags(),
    ]);
  }

  /**
   *
   */
  private function invalidateGlobal(): void {
    $this->cacheTagsInvalidator->invalidateTags([
      'ps_compare:list',
      'ps_compare:count',
    ]);
  }

}
