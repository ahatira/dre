<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Service;

use Drupal\node\NodeInterface;

/**
 * Resolves a search locality token from an offer address field.
 */
final class OfferLocationTokenResolver {

  /**
   * Returns a postal code or locality string usable as search locality token.
   */
  public function resolveFromOffer(NodeInterface $node): ?string {
    if (!$node->hasField('field_address') || $node->get('field_address')->isEmpty()) {
      return NULL;
    }

    $address = (array) $node->get('field_address')->first()->getValue();
    $postalCode = trim((string) ($address['postal_code'] ?? ''));
    if ($postalCode !== '') {
      return $postalCode;
    }

    $locality = trim((string) ($address['locality'] ?? ''));
    if ($locality !== '') {
      return $locality;
    }

    $dependentLocality = trim((string) ($address['dependent_locality'] ?? ''));
    return $dependentLocality !== '' ? $dependentLocality : NULL;
  }

}
