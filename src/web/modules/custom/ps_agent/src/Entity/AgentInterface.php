<?php

declare(strict_types=1);

namespace Drupal\ps_agent\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Defines the interface for the Agent entity.
 */
interface AgentInterface extends ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface {

  public function getFirstName(): string;

  public function getLastName(): string;

  public function getEmail(): string;

  public function getPhone(): string;

}
