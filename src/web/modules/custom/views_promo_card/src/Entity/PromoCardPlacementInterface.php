<?php

declare(strict_types=1);

namespace Drupal\views_promo_card\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Interface for Promo Card Placement config entities.
 */
interface PromoCardPlacementInterface extends ConfigEntityInterface {

  /**
   * Returns the target Views ID.
   */
  public function getViewId(): string;

  /**
   * Returns the target display ID.
   */
  public function getDisplayId(): string;

  /**
   * Returns card references with weights.
   *
   * @return array<int, array<string, mixed>>
   *   Card reference items.
   */
  public function getCards(): array;

  /**
   * Returns the rotation strategy ID.
   */
  public function getRotation(): string;

  /**
   * Returns placement rules.
   *
   * @return array<int, array<string, mixed>>
   *   Placement rules.
   */
  public function getPlacementRules(): array;

  /**
   * Returns visibility condition configurations.
   *
   * @return array<int, array<string, mixed>>
   *   Condition configs.
   */
  public function getConditions(): array;

  /**
   * Returns conditions logic (and/or).
   */
  public function getConditionsLogic(): string;

  /**
   * Returns max insertions per page.
   */
  public function getMaxInsertionsPerPage(): int;

}
