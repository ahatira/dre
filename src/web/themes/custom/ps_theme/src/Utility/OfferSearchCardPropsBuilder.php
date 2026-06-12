<?php

declare(strict_types=1);

namespace Drupal\ps_theme\Utility;

use Drupal\Core\Render\Markup;
use Drupal\node\NodeInterface;

/**
 * Builds props for the offer-search-card SDC from an offer node.
 */
final class OfferSearchCardPropsBuilder {

  use OfferNodeCardPropsTrait;

  /**
   * Builds offer-search-card component props from a node.
   *
   * @return array<string, mixed>
   *   Props keyed for ps_theme:offer-search-card.
   */
  public static function build(NodeInterface $node): array {
    return (new self())->buildProps($node);
  }

  /**
   * @return array<string, mixed>
   */
  private function buildProps(NodeInterface $node): array {
    $operationCode = (string) ($node->get('field_operation_type')->value ?? '');
    $images = $this->resolveGalleryImageUrls($node);
    $primaryImage = $images[0] ?? $this->placeholderImageUrl();
    $budget = $this->buildBudgetParts($node);
    $qualifiers = $budget['qualifiers'];

    return [
      'title' => $this->formatListTitle($node, $operationCode),
      'url' => $node->toUrl()->toString(),
      'image' => $primaryImage,
      'images' => $images !== [] ? $images : [$primaryImage],
      'image_alt' => $this->formatListTitle($node, $operationCode),
      'location' => $this->formatListLocation($node),
      'surface' => $this->formatSurface($node),
      'price_amount' => $budget['amount'],
      'price_qualifiers' => $qualifiers !== '' ? Markup::create($this->formatQualifiersMarkup($qualifiers)) : '',
      'operation' => $operationCode === 'VEN' ? 'sale' : 'rent',
      'exclusive' => $this->isExclusive($node),
      'show_compare' => TRUE,
      'node_id' => (int) $node->id(),
    ];
  }

  private function formatListTitle(NodeInterface $node, string $operationCode): string {
    $commercial = trim((string) ($node->get('field_commercial_title')->value ?? ''));
    if ($commercial !== '') {
      return $commercial;
    }

    $parts = [];

    if ($operationCode !== '') {
      $parts[] = $this->dictionaryLabel('operation_type', $operationCode);
    }

    $assetCode = (string) ($node->get('field_asset_type')->value ?? '');
    if ($assetCode !== '') {
      $parts[] = $this->dictionaryLabel('asset_type', $assetCode);
    }

    if ($node->hasField('field_address') && !$node->get('field_address')->isEmpty()) {
      $address = $node->get('field_address')->first();
      $locality = trim((string) ($address->locality ?? ''));
      if ($locality !== '') {
        $parts[] = mb_strtoupper($locality);
      }
    }

    if ($parts !== []) {
      return implode(' ', $parts);
    }

    return $node->label() ?? '';
  }

  private function formatListLocation(NodeInterface $node): ?string {
    if (!$node->hasField('field_address') || $node->get('field_address')->isEmpty()) {
      return NULL;
    }

    $address = $node->get('field_address')->first();
    if ($address === NULL) {
      return NULL;
    }

    $postal = trim((string) ($address->postal_code ?? ''));
    $locality = trim((string) ($address->locality ?? ''));
    $locality = $locality !== '' ? mb_strtoupper($locality) : '';

    $parts = array_filter([$postal, $locality]);
    return $parts !== [] ? implode(' ', $parts) : NULL;
  }

}
