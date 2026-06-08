<?php

declare(strict_types=1);

namespace Drupal\ps_theme\Utility;

use Drupal\node\NodeInterface;

/**
 * Builds props for the offer-card SDC (teaser grid) from an offer node.
 */
final class OfferCardPropsBuilder {

  use OfferNodeCardPropsTrait;

  /**
   * Builds offer-card component props from a node.
   *
   * @return array<string, mixed>
   *   Props keyed for ps_theme:offer-card.
   */
  public static function build(NodeInterface $node): array {
    return (new self())->buildGridProps($node);
  }

  /**
   * @return array<string, mixed>
   */
  private function buildGridProps(NodeInterface $node): array {
    $operationCode = (string) ($node->get('field_operation_type')->value ?? '');
    $assetCode = (string) ($node->get('field_asset_type')->value ?? '');

    $title = (string) ($node->get('field_commercial_title')->value ?? '');
    if ($title === '') {
      $title = $node->label() ?? '';
    }

    $badges = [];
    if ($assetCode !== '') {
      $badges[] = $this->dictionaryLabel('asset_type', $assetCode);
    }

    $imageUrl = $this->resolvePrimaryImageUrl($node);
    if ($imageUrl === NULL) {
      $imageUrl = $this->placeholderImageUrl();
    }

    $budget = $this->buildBudgetParts($node);
    $price = $budget['qualifiers'] === ''
      ? $budget['amount']
      : $budget['amount'] . ' ' . $budget['qualifiers'];

    return [
      'title' => $title,
      'url' => $node->toUrl()->toString(),
      'image' => $imageUrl,
      'image_alt' => $title,
      'location' => $this->formatGridLocation($node),
      'surface' => $this->formatSurface($node),
      'price' => $price,
      'operation' => $operationCode === 'VEN' ? 'sale' : 'rent',
      'badges' => array_values(array_filter($badges)),
      'favorite' => TRUE,
    ];
  }

  private function formatGridLocation(NodeInterface $node): ?string {
    if (!$node->hasField('field_address') || $node->get('field_address')->isEmpty()) {
      return NULL;
    }

    $address = $node->get('field_address')->first();
    if ($address === NULL) {
      return NULL;
    }

    $locality = trim((string) ($address->locality ?? ''));
    $postal = trim((string) ($address->postal_code ?? ''));
    $parts = array_filter([$locality, $postal]);

    return $parts !== [] ? implode(' ', $parts) : NULL;
  }

}
