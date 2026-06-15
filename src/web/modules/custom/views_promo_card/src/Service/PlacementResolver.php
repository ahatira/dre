<?php

declare(strict_types=1);

namespace Drupal\views_promo_card\Service;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\views_promo_card\Entity\PromoCardPlacementInterface;
use Drupal\views\ViewExecutable;

/**
 * Resolves active placements for a Views display.
 */
final class PlacementResolver {

  /**
   * Constructs a PlacementResolver.
   */
  public function __construct(
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly ConditionEvaluator $conditionEvaluator,
    private readonly CacheBackendInterface $cache,
  ) {}

  /**
   * Returns matching placements for the given view display.
   *
   * @return \Drupal\views_promo_card\Entity\PromoCardPlacementInterface[]
   *   Active placements sorted by weight.
   */
  public function resolve(ViewExecutable $view): array {
    $view_id = $view->id();
    $display_id = $view->current_display ?? '';
    $cid = "views_promo_card:placements:{$view_id}:{$display_id}";
    if ($cached = $this->cache->get($cid)) {
      $placements = $cached->data;
    }
    else {
      $storage = $this->entityTypeManager->getStorage('promo_card_placement');
      /** @var \Drupal\views_promo_card\Entity\PromoCardPlacementInterface[] $all */
      $all = $storage->loadMultiple();
      $placements = [];
      foreach ($all as $placement) {
        if (!$placement->status()) {
          continue;
        }
        if ($placement->getViewId() !== $view_id || $placement->getDisplayId() !== $display_id) {
          continue;
        }
        $placements[] = $placement;
      }
      usort($placements, static fn(PromoCardPlacementInterface $a, PromoCardPlacementInterface $b): int => $a->get('weight') <=> $b->get('weight'));
      $this->cache->set($cid, $placements, CacheBackendInterface::CACHE_PERMANENT, [
        'promo_card_placement_list',
      ]);
    }

    return array_values(array_filter(
      $placements,
      fn(PromoCardPlacementInterface $placement): bool => $this->conditionEvaluator->matches($placement, $view),
    ));
  }

}
