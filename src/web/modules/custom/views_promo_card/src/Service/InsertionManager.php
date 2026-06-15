<?php

declare(strict_types=1);

namespace Drupal\views_promo_card\Service;

use Drupal\views\ViewExecutable;
use Drupal\views_promo_card\Entity\PromoCardPlacementInterface;

/**
 * Computes row positions where promo cards should be inserted.
 */
final class InsertionManager {

  /**
   * Constructs an InsertionManager.
   */
  public function __construct(
    private readonly PlacementResolver $placementResolver,
    private readonly CardRenderer $cardRenderer,
    private readonly CardRotationManager $rotationManager,
  ) {}

  /**
   * Builds slot map: 1-based row index => render array.
   *
   * @return array<int, array<string, mixed>>
   *   Insertion slots keyed by position after row N.
   */
  public function buildSlots(ViewExecutable $view, int $pageRowCount): array {
    $placements = $this->placementResolver->resolve($view);
    if ($placements === [] || $pageRowCount <= 0) {
      return [];
    }

    $slots = [];
    foreach ($placements as $placement) {
      $card_id = $this->rotationManager->pickCardId($placement);
      if ($card_id === '') {
        continue;
      }
      $build = $this->cardRenderer->buildById($card_id);
      if ($build === NULL) {
        continue;
      }

      $positions = $this->computePositions($placement, $pageRowCount);
      foreach ($positions as $position) {
        if (!isset($slots[$position])) {
          $slots[$position] = $build;
        }
      }
    }

    ksort($slots);
    return $slots;
  }

  /**
   * Computes 1-based insertion positions for a placement.
   *
   * @return int[]
   *   Positions after which to insert.
   */
  private function computePositions(PromoCardPlacementInterface $placement, int $pageRowCount): array {
    $positions = [];
    $max = $placement->getMaxInsertionsPerPage();

    foreach ($placement->getPlacementRules() as $rule) {
      $type = (string) ($rule['type'] ?? 'fixed');
      if ($type === 'fixed') {
        $position = max(1, (int) ($rule['position'] ?? 1));
        if ($position <= $pageRowCount) {
          $positions[] = $position;
        }
      }
      elseif ($type === 'interval') {
        $every = max(1, (int) ($rule['every'] ?? 1));
        $start = max(1, (int) ($rule['start_at'] ?? $every));
        for ($i = $start; $i <= $pageRowCount; $i += $every) {
          $positions[] = $i;
        }
      }
    }

    $positions = array_values(array_unique($positions));
    sort($positions);
    return array_slice($positions, 0, $max);
  }

}
