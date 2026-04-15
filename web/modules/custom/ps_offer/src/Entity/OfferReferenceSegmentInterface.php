<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Interface for Offer Reference Segment config entities.
 */
interface OfferReferenceSegmentInterface extends ConfigEntityInterface {

  /**
   * Returns whether segment is enabled.
   */
  public function isEnabled(): bool;

  /**
   * Returns segment type.
   */
  public function getSegmentType(): string;

  /**
   * Returns source field machine name.
   */
  public function getSourceField(): string;

  /**
   * Returns segment length.
   */
  public function getLength(): int;

  /**
   * Returns segment weight.
   */
  public function getWeight(): int;

  /**
   * Returns static value.
   */
  public function getStaticValue(): string;

  /**
   * Returns custom map textarea value.
   */
  public function getCustomMapText(): string;

  /**
   * Returns 1-based start index.
   */
  public function getStartIndex(): int;

  /**
   * Returns date source field.
   */
  public function getDateSourceField(): string;

  /**
   * Returns date format key.
   */
  public function getDateFormat(): string;

  /**
   * Returns auto-start value.
   */
  public function getAutoStart(): int;

}
