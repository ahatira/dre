<?php

declare(strict_types=1);

namespace Drupal\ps_offer;

use Drupal\node\NodeInterface;

/**
 * Resolves offer matrix visibility from ps_context (optional runtime bridge).
 */
interface OfferContextResolverInterface {

  /**
   * Whether a form tab is visible for the offer per active context rules.
   */
  public function isTabVisible(NodeInterface $offer, string $tab): bool;

  /**
   * Whether a form field wrapper is visible for the offer per active context rules.
   */
  public function isFieldVisible(NodeInterface $offer, string $field): bool;

  /**
   * Whether the offer uses capacity instead of surface per the matrix.
   */
  public function isCapacityDriven(NodeInterface $offer): bool;

}
