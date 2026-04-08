<?php

declare(strict_types=1);

namespace Drupal\ps_agent\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;

/**
 * Provides an interface for Agent entities.
 */
interface AgentInterface extends ContentEntityInterface, EntityChangedInterface, EntityPublishedInterface {

  /**
   * Gets the agent display name.
   */
  public function getName(): ?string;

  /**
   * Sets the agent display name.
   */
  public function setName(string $name): self;

  /**
   * Gets the agent phone number.
   */
  public function getPhone(): ?string;

  /**
   * Sets the agent phone number.
   */
  public function setPhone(?string $phone): self;

}
