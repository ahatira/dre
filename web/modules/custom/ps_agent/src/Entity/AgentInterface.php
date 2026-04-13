<?php

declare(strict_types=1);

namespace Drupal\ps_agent\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Interface for Agent entities.
 */
interface AgentInterface extends ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface
{
  /**
   * Gets the external ID (CRM identifier).
   *
   * @return string|null
   *   The external ID or NULL.
   */
    public function getExternalId(): ?string;

  /**
   * Sets the external ID.
   *
   * @param string $externalId
   *   The external ID.
   *
   * @return $this
   */
    public function setExternalId(string $externalId): static;

  /**
   * Gets the agent's civility (title/salutation).
   *
   * @return string|null
   *   The civility code (e.g., 'MR', 'MS') or NULL.
   */
    public function getCivility(): ?string;

  /**
   * Sets the agent's civility.
   *
   * @param string $civility
   *   The civility code.
   *
   * @return $this
   */
    public function setCivility(string $civility): static;

  /**
   * Gets the agent's first name.
   *
   * @return string|null
   *   The first name or NULL.
   */
    public function getFirstName(): ?string;

  /**
   * Sets the agent's first name.
   *
   * @param string $firstName
   *   The first name.
   *
   * @return $this
   */
    public function setFirstName(string $firstName): static;

  /**
   * Gets the agent's last name.
   *
   * @return string|null
   *   The last name or NULL.
   */
    public function getLastName(): ?string;

  /**
   * Sets the agent's last name.
   *
   * @param string $lastName
   *   The last name.
   *
   * @return $this
   */
    public function setLastName(string $lastName): static;

  /**
   * Gets the agent's email.
   *
   * @return string|null
   *   The email or NULL.
   */
    public function getEmail(): ?string;

  /**
   * Sets the agent's email.
   *
   * @param string $email
   *   The email.
   *
   * @return $this
   */
    public function setEmail(string $email): static;

  /**
   * Gets the agent's phone number.
   *
   * @return string|null
   *   The phone or NULL.
   */
    public function getPhone(): ?string;

  /**
   * Sets the agent's phone number.
   *
   * @param string $phone
   *   The phone.
   *
   * @return $this
   */
    public function setPhone(string $phone): static;

  /**
   * Checks if the agent is active.
   *
   * @return bool
   *   TRUE if active, FALSE otherwise.
   */
    public function isActive(): bool;

  /**
   * Sets the active status.
   *
   * @param bool $active
   *   TRUE to activate, FALSE to deactivate.
   *
   * @return $this
   */
    public function setActive(bool $active): static;
}
