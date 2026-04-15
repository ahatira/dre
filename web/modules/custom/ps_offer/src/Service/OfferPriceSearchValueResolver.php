<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Service;

use Drupal\node\NodeInterface;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\ps_price\Plugin\Field\FieldType\PriceItem;
use Drupal\ps_price\Service\PriceFormatterInterface;
use Drupal\ps_price\Service\PriceNormalizer;

/**
 * Resolves stable offer price values for search indexing.
 */
final class OfferPriceSearchValueResolver {

  /**
   * Constructs a resolver service.
   */
  public function __construct(
    private readonly PriceFormatterInterface $priceFormatter,
    private readonly PriceNormalizer $priceNormalizer,
  ) {}

  /**
   * Resolves derived values used by Search API.
   *
   * @return array{display:?string,amount:?float,normalized:?float,on_request:bool}
   *   Derived values for indexing.
   */
  public function resolve(NodeInterface $node): array {
    $mainPrice = $this->resolveMainPrice($node);
    if (!$mainPrice instanceof PriceItem) {
      return [
        'display' => NULL,
        'amount' => NULL,
        'normalized' => NULL,
        'on_request' => FALSE,
      ];
    }

    $isOnRequest = $mainPrice->isOnRequest();
    $amount = $isOnRequest ? NULL : $mainPrice->getAmount();
    $surfaceM2 = $this->resolveSurfaceM2($node);
    /** @var \Drupal\Core\Field\FieldItemInterface $mainField */
    $mainField = $mainPrice;

    return [
      'display' => $this->priceFormatter->format($mainField, [
        'show_currency' => TRUE,
        'show_unit' => TRUE,
        'show_period' => TRUE,
        'show_flags' => TRUE,
      ]),
      'amount' => $amount,
      'normalized' => $this->priceNormalizer->normalize($mainField, $surfaceM2),
      'on_request' => $isOnRequest,
    ];
  }

  /**
   * Picks the main price item using current business fallback rule.
   */
  private function resolveMainPrice(NodeInterface $node): ?PriceItem {
    if (!$node->hasField('field_prices')) {
      return NULL;
    }

    foreach ($node->get('field_prices') as $item) {
      if (!$item instanceof PriceItem) {
        continue;
      }

      if ($item->isOnRequest()) {
        return $item;
      }

      if ($item->getAmount() !== NULL) {
        return $item;
      }
    }

    return NULL;
  }

  /**
   * Resolves an exploitable surface in m2.
   */
  private function resolveSurfaceM2(NodeInterface $node): float {
    if (!$node->hasField('field_surfaces')) {
      return 0.0;
    }

    foreach ($node->get('field_surfaces') as $surface) {
      $value = $surface->get('value')->getValue();
      $unit = strtoupper((string) ($surface->get('unit')->getValue() ?? ''));
      if (!is_numeric($value) || (float) $value <= 0.0) {
        continue;
      }

      // Restrict conversion to explicit m2 to avoid hidden unit drift.
      if ($unit === '' || $unit === 'M2') {
        return (float) $value;
      }
    }

    return 0.0;
  }

}
