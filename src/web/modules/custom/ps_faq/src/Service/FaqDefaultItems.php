<?php

declare(strict_types=1);

namespace Drupal\ps_faq\Service;

use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\node\NodeInterface;

/**
 * Resolves default FAQ item references for homepage and demo installs.
 */
final class FaqDefaultItems {

  /**
   * Stable demo FAQ node UUIDs (ps_demo export).
   *
   * @var list<string>
   */
  private const DEMO_UUIDS = [
    'b2000004-0000-4000-8000-000000000001',
    'b2000004-0000-4000-8000-000000000002',
    'b2000004-0000-4000-8000-000000000003',
    'b2000004-0000-4000-8000-000000000004',
  ];

  public function __construct(
    private readonly EntityRepositoryInterface $entityRepository,
    private readonly EntityTypeManagerInterface $entityTypeManager,
  ) {}

  /**
   * Returns FAQ block item rows (nid + weight).
   *
   * @return list<array{weight: int, nid: int}>
   */
  public function resolve(): array {
    $fromUuids = $this->resolveFromUuids(self::DEMO_UUIDS);
    if (count($fromUuids) === count(self::DEMO_UUIDS)) {
      return $fromUuids;
    }

    return $this->mergeItems($fromUuids, $this->resolveFromQuery());
  }

  /**
   * @param list<array{weight: int, nid: int}> $primary
   * @param list<array{weight: int, nid: int}> $secondary
   *
   * @return list<array{weight: int, nid: int}>
   */
  private function mergeItems(array $primary, array $secondary): array {
    $seen = [];
    $items = [];

    foreach ([$primary, $secondary] as $source) {
      foreach ($source as $item) {
        $nid = (int) ($item['nid'] ?? 0);
        if ($nid <= 0 || isset($seen[$nid])) {
          continue;
        }
        $seen[$nid] = TRUE;
        $items[] = [
          'weight' => count($items),
          'nid' => $nid,
        ];
        if (count($items) >= 15) {
          break 2;
        }
      }
    }

    return $items;
  }

  /**
   * @param list<string> $uuids
   *
   * @return list<array{weight: int, nid: int}>
   */
  private function resolveFromUuids(array $uuids): array {
    $items = [];

    foreach ($uuids as $weight => $uuid) {
      try {
        $entity = $this->entityRepository->loadEntityByUuid('node', $uuid);
      }
      catch (\Exception) {
        continue;
      }

      if (!$entity instanceof NodeInterface || !$entity->isPublished()) {
        continue;
      }

      $items[] = [
        'weight' => $weight,
        'nid' => (int) $entity->id(),
      ];
    }

    return $items;
  }

  /**
   * Fallback for environments with manually created FAQ nodes.
   *
   * @return list<array{weight: int, nid: int}>
   */
  private function resolveFromQuery(): array {
    $storage = $this->entityTypeManager->getStorage('node');
    $nids = array_values($storage->getQuery()
      ->accessCheck(FALSE)
      ->condition('type', 'faq_item')
      ->condition('status', NodeInterface::PUBLISHED)
      ->sort('field_weight', 'ASC')
      ->sort('title', 'ASC')
      ->range(0, 15)
      ->execute());

    if ($nids === []) {
      return [];
    }

    $items = [];
    foreach ($nids as $weight => $nid) {
      $items[] = [
        'weight' => $weight,
        'nid' => (int) $nid,
      ];
    }

    return $items;
  }

}
