<?php

declare(strict_types=1);

namespace Drupal\ps_agent\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface for defining Agent Type entities.
 *
 * @ingroup ps_agent
 */
interface AgentTypeInterface extends ConfigEntityInterface {

  /**
   * Gets the agent type description.
   *
   * @return string
   *   The agent type description.
   */
  public function getDescription(): string;

  /**
   * Sets the agent type description.
   *
   * @param string $description
   *   The agent type description.
   *
   * @return $this
   */
  public function setDescription(string $description): static;

}
