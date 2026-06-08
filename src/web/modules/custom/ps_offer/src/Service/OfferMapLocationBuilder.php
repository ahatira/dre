<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Service;

use Drupal\address\Plugin\Field\FieldType\AddressItem;
use Drupal\node\NodeInterface;

/**
 * Builds public location labels for offer map and location blocks.
 */
final class OfferMapLocationBuilder {

  /**
   * Returns whether the offer exposes its precise street address on the front.
   */
  public function showsExactAddress(NodeInterface $node): bool {
    if (!$node->hasField('field_show_address') || $node->get('field_show_address')->isEmpty()) {
      return TRUE;
    }

    return (bool) $node->get('field_show_address')->value;
  }

  /**
   * Returns a single-line locality label (postal code + city).
   */
  public function buildLocalityLabel(NodeInterface $node): string {
    $item = $this->getAddressItem($node);
    if (!$item instanceof AddressItem) {
      return '';
    }

    return trim(trim((string) ($item->postal_code ?? '')) . ' ' . trim((string) ($item->locality ?? '')));
  }

  /**
   * Returns the public address label, empty when the precise address is hidden.
   */
  public function buildPublicAddress(NodeInterface $node): string {
    if (!$this->showsExactAddress($node)) {
      return '';
    }

    $item = $this->getAddressItem($node);
    if (!$item instanceof AddressItem) {
      return '';
    }

    $parts = array_filter([
      trim((string) ($item->address_line1 ?? '')),
      trim((string) ($item->postal_code ?? '')),
      trim((string) ($item->locality ?? '')),
    ]);

    return implode(', ', $parts);
  }

  /**
   * Returns the location line for the offer detail location block.
   */
  public function buildLocationLine(NodeInterface $node): string {
    $item = $this->getAddressItem($node);
    if (!$item instanceof AddressItem) {
      return '';
    }

    $city = $this->buildLocalityLabel($node);
    if (!$this->showsExactAddress($node)) {
      return $city;
    }

    $line = trim((string) ($item->address_line1 ?? ''));
    return match (TRUE) {
      $line !== '' && $city !== '' => $line . ' - ' . $city,
      $line !== '' => $line,
      default => $city,
    };
  }

  /**
   * Returns the offer address field item when available.
   */
  private function getAddressItem(NodeInterface $node): ?AddressItem {
    if (!$node->hasField('field_address') || $node->get('field_address')->isEmpty()) {
      return NULL;
    }

    $item = $node->get('field_address')->first();
    return $item instanceof AddressItem ? $item : NULL;
  }

}
