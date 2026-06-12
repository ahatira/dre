<?php

declare(strict_types=1);

namespace Drupal\ps_search\Entity;

/**
 * Provides constants for Search alert entities.
 */
interface SearchAlertInterface {

  public const STATUS_ACTIVE = 'active';

  public const STATUS_PAUSED = 'paused';

  public const FREQUENCE_DAILY = 'daily';

  public const FREQUENCE_WEEKLY = 'weekly';

  /**
   * Gets the alert display name.
   */
  public function getAlertName(): string;

  /**
   * Gets the professional email address.
   */
  public function getProfEmail(): string;

  /**
   * Gets notification frequency.
   */
  public function getFrequence(): string;

  /**
   * Gets stored search criteria as array.
   *
   * @return array<string, mixed>
   */
  public function getCriteria(): array;

  /**
   * Gets the criteria hash used for deduplication.
   */
  public function getCriteriaHash(): string;

}
