<?php

declare(strict_types=1);

namespace Drupal\ps_search\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\node\NodeInterface;

/**
 * Builds BNPPRE-style price marker icons for the search results map.
 */
final class SearchMapMarkerBuilder {

  private const MARKER_FILL = '#00915A';

  private const MARKER_TEXT = '#FFFFFF';

  private const MARKER_HEIGHT = 28;

  private const MARKER_MIN_WIDTH = 44;

  private const MARKER_CHAR_WIDTH = 7;

  private const MARKER_H_PADDING = 16;

  public function __construct(
    private readonly ConfigFactoryInterface $configFactory,
  ) {}

  /**
   * Builds a short price label for map markers (amount + currency only).
   */
  public function buildPriceLabel(NodeInterface $node): string {
    $onRequest = (string) ($this->configFactory->get('ps_offer.settings')->get('on_request') ?? 'On request');

    if (!$node->hasField('field_budget_value') || $node->get('field_budget_value')->isEmpty()) {
      return $onRequest;
    }

    $raw = $node->get('field_budget_value')->value;
    if ($raw === NULL || $raw === '' || (float) $raw <= 0) {
      return $onRequest;
    }

    $amount = number_format((float) $raw, 0, ',', ' ');
    $currencyCode = strtoupper((string) ($node->get('field_budget_currency')->value ?? 'EUR'));
    $currency = $currencyCode === 'EUR' ? '€' : $currencyCode;

    return $amount . ' ' . $currency;
  }

  /**
   * Builds a Google Maps marker icon as an SVG data URI.
   */
  public function buildPriceMarkerDataUri(string $label): string {
    $safeLabel = htmlspecialchars($label, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    $width = max(self::MARKER_MIN_WIDTH, (int) (mb_strlen($label) * self::MARKER_CHAR_WIDTH + self::MARKER_H_PADDING));
    $height = self::MARKER_HEIGHT;
    $radius = (int) ($height / 2);
    $fill = self::MARKER_FILL;
    $text = self::MARKER_TEXT;

    $svg = <<<SVG
<?xml version="1.0" encoding="UTF-8"?>
<svg xmlns="http://www.w3.org/2000/svg" width="{$width}" height="{$height}" viewBox="0 0 {$width} {$height}" role="img" aria-hidden="true">
  <rect x="0" y="0" width="{$width}" height="{$height}" rx="{$radius}" ry="{$radius}" fill="{$fill}"/>
  <text x="50%" y="50%" dominant-baseline="central" text-anchor="middle" fill="{$text}" font-family="Arial, Helvetica, sans-serif" font-size="12" font-weight="700">{$safeLabel}</text>
</svg>
SVG;

    return 'data:image/svg+xml;charset=UTF-8,' . rawurlencode($svg);
  }

}
