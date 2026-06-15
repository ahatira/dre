<?php

declare(strict_types=1);

namespace Drupal\views_promo_card\Service;

use Drupal\Core\State\StateInterface;
use Drupal\views_promo_card\Entity\PromoCardPlacement;
use Drupal\views_promo_card\Entity\PromoCardPlacementInterface;

/**
 * Selects which promo card to display for a placement.
 */
final class CardRotationManager {

  /**
   * Constructs a CardRotationManager.
   */
  public function __construct(
    private readonly StateInterface $state,
  ) {}

  /**
   * Picks a promo card ID from a placement configuration.
   */
  public function pickCardId(PromoCardPlacementInterface $placement): ?string {
    $cards = $placement->getCards();
    if ($cards === []) {
      return NULL;
    }

    usort($cards, static fn(array $a, array $b): int => ($a['weight'] ?? 0) <=> ($b['weight'] ?? 0));

    $rotation = $placement->getRotation();
    if ($rotation === PromoCardPlacement::ROTATION_RANDOM) {
      $index = array_rand($cards);
      return (string) ($cards[$index]['promo_card'] ?? '');
    }

    if ($rotation === PromoCardPlacement::ROTATION_ROUND_ROBIN) {
      $key = 'views_promo_card.rotation.' . $placement->id();
      $current = (int) $this->state->get($key, 0);
      $index = $current % count($cards);
      $this->state->set($key, $current + 1);
      return (string) ($cards[$index]['promo_card'] ?? '');
    }

    return (string) ($cards[0]['promo_card'] ?? '');
  }

}
