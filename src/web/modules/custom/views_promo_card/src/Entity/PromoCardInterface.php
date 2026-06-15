<?php

declare(strict_types=1);

namespace Drupal\views_promo_card\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Interface for Promo Card config entities.
 */
interface PromoCardInterface extends ConfigEntityInterface {

  /**
   * Returns the SDC pattern ID.
   */
  public function getPatternId(): string;

  /**
   * Returns the preset ID used to create this card, if any.
   */
  public function getPresetId(): string;

  /**
   * Returns the UI Patterns component configuration.
   *
   * @return array<string, mixed>
   *   UI Patterns configuration.
   */
  public function getUiPatterns(): array;

}
