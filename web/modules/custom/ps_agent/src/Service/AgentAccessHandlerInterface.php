<?php

declare(strict_types=1);

namespace Drupal\ps_agent\Service;

use Drupal\ps_agent\Entity\AgentInterface;

/**
 * Interface for AgentAccessHandler service.
 */
interface AgentAccessHandlerInterface {

  /**
   * Checks if current user can view an agent.
   *
   * @param \Drupal\ps_agent\Entity\AgentInterface $agent
   *   The agent entity.
   *
   * @return bool
   *   TRUE if allowed, FALSE otherwise.
   */
  public function canViewAgent(AgentInterface $agent): bool;

  /**
   * Checks if current user can edit an agent.
   *
   * @param \Drupal\ps_agent\Entity\AgentInterface $agent
   *   The agent entity.
   *
   * @return bool
   *   TRUE if allowed, FALSE otherwise.
   */
  public function canEditAgent(AgentInterface $agent): bool;

  /**
   * Checks if current user can delete an agent.
   *
   * @param \Drupal\ps_agent\Entity\AgentInterface $agent
   *   The agent entity.
   *
   * @return bool
   *   TRUE if allowed, FALSE otherwise.
   */
  public function canDeleteAgent(AgentInterface $agent): bool;

}
