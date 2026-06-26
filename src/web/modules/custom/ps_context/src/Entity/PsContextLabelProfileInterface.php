<?php

declare(strict_types=1);

namespace Drupal\ps_context\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Interface for ps_context.label_profile config entities.
 */
interface PsContextLabelProfileInterface extends ConfigEntityInterface {

  /**
   * Gets the asset type code or '*' for any.
   */
  public function getAssetType(): string;

  /**
   * Gets the operation type code or '*' for any.
   */
  public function getOperationType(): string;

  /**
   * Gets the profile weight (lower = merged earlier).
   */
  public function getWeight(): int;

  /**
   * @return array<string, string>
   *   Label key => value map.
   */
  public function getLabels(): array;

}
