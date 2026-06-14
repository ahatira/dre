<?php

declare(strict_types=1);

namespace Drupal\ps_market_study\Service;

use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\node\NodeInterface;

/**
 * Resolves default market study references for homepage demo content.
 */
final class MarketStudyDefaultItems {

  /**
   * @return list<array{weight: int, nid: int}>
   */
  public function resolve(int $limit = 4): array {
    $storage = $this->entityTypeManager->getStorage('node');
    $nids = array_values($storage->getQuery()
      ->accessCheck(FALSE)
      ->condition('type', 'market_study')
      ->condition('status', NodeInterface::PUBLISHED)
      ->sort('field_display_date', 'DESC')
      ->sort('title', 'ASC')
      ->range(0, $limit)
      ->execute());

    $items = [];
    foreach ($nids as $weight => $nid) {
      $items[] = [
        'weight' => $weight,
        'nid' => (int) $nid,
      ];
    }
    return $items;
  }

  public function __construct(
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly EntityRepositoryInterface $entityRepository,
  ) {}

}
