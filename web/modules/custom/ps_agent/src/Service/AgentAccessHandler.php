<?php

declare(strict_types=1);

namespace Drupal\ps_agent\Service;

use Drupal\Core\Session\AccountProxyInterface;
use Drupal\ps_agent\Entity\AgentInterface;

/**
 * Agent Access Handler service.
 *
 * Manages access control for agent entities.
 *
 * @see \Drupal\ps_agent\Service\AgentAccessHandlerInterface
 */
final class AgentAccessHandler implements AgentAccessHandlerInterface {

  /**
   * Constructs AgentAccessHandler.
   *
   * @param \Drupal\Core\Session\AccountProxyInterface $currentUser
   *   The current user.
   */
  public function __construct(
    private readonly AccountProxyInterface $currentUser,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function canViewAgent(AgentInterface $agent): bool {
    return $agent->access('view', $this->currentUser->getAccount());
  }

  /**
   * {@inheritdoc}
   */
  public function canEditAgent(AgentInterface $agent): bool {
    return $agent->access('update', $this->currentUser->getAccount());
  }

  /**
   * {@inheritdoc}
   */
  public function canDeleteAgent(AgentInterface $agent): bool {
    return $agent->access('delete', $this->currentUser->getAccount());
  }

}
