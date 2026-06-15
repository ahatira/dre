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
   * Builds offer-teaser-card component props from a node.
   *
   * @return array<string, mixed>
   *   Props keyed for ps_theme:offer-teaser-card.
   */
  public static function buildTeaser(NodeInterface $node): array {
    return (new self())->buildTeaserProps($node);
  }

  /**
   * @return array<string, mixed>
   */
  private function buildTeaserProps(NodeInterface $node): array {
    $operationCode = (string) ($node->get('field_operation_type')->value ?? '');
    $surfaceParts = $this->formatSurfaceParts($node);

    $imageUrl = $this->resolvePrimaryImageUrl($node);
    if ($imageUrl === NULL) {
      $imageUrl = $this->placeholderImageUrl();
    }

    $budget = $this->buildBudgetParts($node);

    return [
      'title' => $this->formatTeaserTitle($node),
      'url' => $node->toUrl()->toString(),
      'image' => $imageUrl,
      'image_alt' => $this->formatTeaserTitle($node),
      'location' => $this->formatGridLocation($node),
      'surface' => $this->formatSurface($node),
      'surface_primary' => $surfaceParts['primary'] !== '' ? $surfaceParts['primary'] : NULL,
      'surface_suffix' => $surfaceParts['suffix'],
      'surface_price_line' => $surfaceParts['primary'] !== '' ? $surfaceParts['primary'] : NULL,
      'price_amount' => $budget['amount'],
      'price_qualifiers' => $budget['qualifiers'],
      'operation' => $operationCode === 'VEN' ? 'sale' : 'rent',
      'show_favorite' => TRUE,
      'show_compare' => TRUE,
      'node_id' => (int) $node->id(),
    ];
  }

  /**
   * Product title for homepage teaser cards (Figma line 2).
   */
  private function formatTeaserTitle(NodeInterface $node): string {
    $commercial = trim((string) ($node->get('field_commercial_title')->value ?? ''));
    if ($commercial !== '') {
      return $commercial;
    }

    $assetCode = (string) ($node->get('field_asset_type')->value ?? '');
    if ($assetCode !== '') {
      return $this->dictionaryLabel('asset_type', $assetCode);
    }

    return $node->label() ?? '';
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
    $surfaceParts = $this->formatSurfaceParts($node);

    return [
      'title' => $title,
      'url' => $node->toUrl()->toString(),
      'image' => $imageUrl,
      'image_alt' => $title,
      'location' => $this->formatGridLocation($node),
      'surface' => $this->formatSurface($node),
      'surface_primary' => $surfaceParts['primary'] !== '' ? $surfaceParts['primary'] : NULL,
      'surface_suffix' => $surfaceParts['suffix'],
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
