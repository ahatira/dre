<?php

declare(strict_types=1);

namespace Drupal\ps_context\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Defines the interface for a context rule entity.
 */
interface PsContextRuleInterface extends ConfigEntityInterface {

  /**
   * Returns the condition items.
   *
   * @return array<int, array{field_name: string, operator: string, value: string}>
   */
  public function getConditions(): array;

  /**
   * Returns the conditions logic operator: 'AND' or 'OR'.
   */
  public function getConditionsLogic(): string;

  /**
   * Returns the action items.
   *
   * @return array<int, array{action_type: string, target: string, value: string}>
   */
  public function getActions(): array;

  /**
   * Returns the weight used to order rules during evaluation.
   */
  public function getWeight(): int;

}
