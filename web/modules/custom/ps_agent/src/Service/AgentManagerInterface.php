<?php

declare(strict_types=1);

namespace Drupal\ps_agent\Service;

use Drupal\ps_agent\Entity\AgentInterface;

/**
 * Interface for AgentManager service.
 */
interface AgentManagerInterface {

  /**
   * Gets all active agents.
   *
   * @return array<int, \Drupal\ps_agent\Entity\AgentInterface>
   *   Array of agent entities.
   */
  public function getActiveAgents(): array;

  /**
   * Loads an agent by external ID.
   *
   * @param string $externalId
   *   The external CRM ID.
   *
   * @return \Drupal\ps_agent\Entity\AgentInterface|null
   *   The agent or NULL if not found.
   */
  public function getAgentByExternalId(string $externalId): ?AgentInterface;

  /**
   * Checks if an agent exists by a given field.
   *
   * @param string $field
   *   The field name.
   * @param string $value
   *   The value to search for.
   *
   * @return bool
   *   TRUE if exists, FALSE otherwise.
   */
  public function agentExists(string $field, string $value): bool;

  /**
   * Creates a new agent.
   *
   * @param string $firstName
   *   The first name.
   * @param string $lastName
   *   The last name.
   * @param array<string, mixed> $values
   *   Additional field values.
   *
   * @return \Drupal\ps_agent\Entity\AgentInterface
   *   The created agent.
   */
  public function createAgent(string $firstName, string $lastName, array $values = []): AgentInterface;

  /**
   * Saves an agent.
   *
   * @param \Drupal\ps_agent\Entity\AgentInterface $agent
   *   The agent to save.
   *
   * @return int
   *   The status code.
   */
  public function saveAgent(AgentInterface $agent): int;

  /**
   * Deletes an agent.
   *
   * @param \Drupal\ps_agent\Entity\AgentInterface $agent
   *   The agent to delete.
   */
  public function deleteAgent(AgentInterface $agent): void;

  /**
   * Gets agents by a field value.
   *
   * @param string $field
   *   The field name.
   * @param mixed $value
   *   The value to match.
   *
   * @return array<int, \Drupal\ps_agent\Entity\AgentInterface>
   *   Array of matching agents.
   */
  public function getAgentsByField(string $field, mixed $value): array;

}
