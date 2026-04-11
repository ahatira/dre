<?php

declare(strict_types=1);

namespace Drupal\ps_agent\Service;

use Drupal\ps_agent\Entity\AgentInterface;

/**
 * Interface for AgentFieldProtector service.
 */
interface AgentFieldProtectorInterface {

  /**
   * Checks if a field is BO-editable (protected during CRM imports).
   *
   * @param string $fieldName
   *   The field name.
   *
   * @return bool
   *   TRUE if BO-editable, FALSE otherwise.
   */
  public function isBoEditableField(string $fieldName): bool;

  /**
   * Gets list of BO-editable fields.
   *
   * @return array<int, string>
   *   List of field names.
   */
  public function getBoEditableFields(): array;

  /**
   * Gets BO-editable field values from an agent.
   *
   * @param \Drupal\ps_agent\Entity\AgentInterface $agent
   *   The agent entity.
   *
   * @return array<string, mixed>
   *   Array of field values.
   */
  public function getBoEditableValues(AgentInterface $agent): array;

  /**
   * Restores BO-editable values on an agent.
   *
   * @param \Drupal\ps_agent\Entity\AgentInterface $agent
   *   The agent to update.
   * @param array<string, mixed> $values
   *   The values to restore.
   */
  public function restoreBoEditableValues(AgentInterface $agent, array $values): void;

}
