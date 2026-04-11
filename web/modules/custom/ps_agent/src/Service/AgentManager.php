<?php

declare(strict_types=1);

namespace Drupal\ps_agent\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\ps_agent\Entity\AgentInterface;
use Drupal\ps_dictionary\Service\DictionaryManagerInterface;

/**
 * Agent Manager service.
 *
 * Provides centralized management, lookup, and business logic for agent entities.
 * Handles CRUD operations, CRM synchronization, and agent data lookups.
 *
 * @see \Drupal\ps_agent\Service\AgentManagerInterface
 */
final class AgentManager implements AgentManagerInterface {

  /**
   * Constructs AgentManager.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   * @param \Drupal\ps_dictionary\Service\DictionaryManagerInterface $dictionaryManager
   *   The dictionary manager.
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $loggerFactory
   *   The logger factory.
   */
  public function __construct(
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly DictionaryManagerInterface $dictionaryManager,
    private readonly LoggerChannelFactoryInterface $loggerFactory,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function getActiveAgents(): array {
    $storage = $this->entityTypeManager->getStorage('agent');

    $query = $storage->getQuery()
      ->condition('status', TRUE)
      ->accessCheck(TRUE)
      ->sort('last_name');

    $ids = $query->execute();

    if (empty($ids)) {
      return [];
    }

    /** @var array<int, \Drupal\ps_agent\Entity\AgentInterface> */
    return $storage->loadMultiple($ids);
  }

  /**
   * {@inheritdoc}
   */
  public function getAgentByExternalId(string $externalId): ?AgentInterface {
    $storage = $this->entityTypeManager->getStorage('agent');

    $query = $storage->getQuery()
      ->condition('external_id', $externalId)
      ->accessCheck(TRUE)
      ->range(0, 1);

    $ids = $query->execute();

    if (empty($ids)) {
      return NULL;
    }

    $id = reset($ids);
    /** @var \Drupal\ps_agent\Entity\AgentInterface|null */
    return $storage->load($id);
  }

  /**
   * {@inheritdoc}
   */
  public function agentExists(string $field, string $value): bool {
    $storage = $this->entityTypeManager->getStorage('agent');

    $query = $storage->getQuery()
      ->condition($field, $value)
      ->accessCheck(TRUE)
      ->range(0, 1);

    return (bool) $query->execute();
  }

  /**
   * {@inheritdoc}
   */
  public function createAgent(string $firstName, string $lastName, array $values = []): AgentInterface {
    /** @var \Drupal\ps_agent\Entity\Agent $agent */
    $agent = $this->entityTypeManager->getStorage('agent')->create([
      'first_name' => $firstName,
      'last_name' => $lastName,
      'status' => TRUE,
      ...$values,
    ]);

    return $agent;
  }

  /**
   * {@inheritdoc}
   */
  public function saveAgent(AgentInterface $agent): int {
    return $agent->save();
  }

  /**
   * {@inheritdoc}
   */
  public function deleteAgent(AgentInterface $agent): void {
    $agent->delete();
  }

  /**
   * {@inheritdoc}
   */
  public function getAgentsByField(string $field, mixed $value): array {
    $storage = $this->entityTypeManager->getStorage('agent');

    $query = $storage->getQuery()
      ->condition($field, $value)
      ->accessCheck(TRUE)
      ->sort('last_name');

    $ids = $query->execute();

    if (empty($ids)) {
      return [];
    }

    /** @var array<int, \Drupal\ps_agent\Entity\AgentInterface> */
    return $storage->loadMultiple($ids);
  }

}
