<?php

declare(strict_types=1);

namespace Drupal\ps_search\Service;

use Drupal\node\NodeInterface;

/**
 * Builds BNPPRE-style price marker icons for the search results map.
 *
 * Used by `/api/ps/markers` (SVG data URIs in JS via drupalSettings).
 * Reference preview: ps_theme/assets/images/map/marker-price.svg.
 */
final class SearchMapMarkerBuilder {

  private const MARKER_GREEN = '#00915A';

  private const MARKER_WHITE = '#FFFFFF';

  /**
   * Map marker label when budget is missing or zero (Non Communiqué). */
  private const MAP_NO_PRICE_LABEL = 'NC';

  /**
   * Height of the rectangular body (excluding pointer). */
  private const BODY_HEIGHT = 26;

  private const BOX_MIN_WIDTH = 44;

  private const CHAR_WIDTH = 7;

  private const H_PADDING = 16;

  private const BORDER = 2;

  private const POINTER_HEIGHT = 7;

  private const POINTER_HALF_WIDTH = 5;

  private const DOT_SIZE = 8;

  private const DOT_GAP = 4;

  /**
   * Builds a short price label for map markers (amount + currency only).
   */
  public function buildPriceLabel(NodeInterface $node): string {
    if (!$node->hasField('field_budget_value') || $node->get('field_budget_value')->isEmpty()) {
      return self::MAP_NO_PRICE_LABEL;
    }

    $currencyCode = 'EUR';
    if ($node->hasField('field_budget_currency') && !$node->get('field_budget_currency')->isEmpty()) {
      $currencyCode = (string) $node->get('field_budget_currency')->value;
    }

    return $this->buildPriceLabelFromValues(
      $node->get('field_budget_value')->value,
      $currencyCode,
    );
  }

  /**
   * Builds a price label from indexed Search API values (no entity load).
   */
  public function buildPriceLabelFromValues(mixed $budgetRaw, mixed $currencyRaw = NULL): string {
    if ($budgetRaw === NULL || $budgetRaw === '' || (float) $budgetRaw <= 0) {
      return self::MAP_NO_PRICE_LABEL;
    }

    $amount = number_format((float) $budgetRaw, 0, ',', ' ');
    $currencyCode = strtoupper((string) ($currencyRaw ?? 'EUR'));
    $currency = $currencyCode === 'EUR' ? '€' : $currencyCode;

    return $amount . ' ' . $currency;
  }

  /**
   * Builds a Google Maps marker icon as an SVG data URI.
   */
  public function buildPriceMarkerDataUri(string $label, bool $active = FALSE): string {
    return 'data:image/svg+xml;charset=UTF-8,' . rawurlencode($this->buildPriceMarkerSvg($label, $active));
  }

  /**
   * Builds the price bubble + anchor dot marker SVG.
   */
  public function buildPriceMarkerSvg(string $label, bool $active = FALSE): string {
    $safeLabel = htmlspecialchars($label, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    $boxWidth = max(self::BOX_MIN_WIDTH, (int) (mb_strlen($label) * self::CHAR_WIDTH + self::H_PADDING));
    $totalWidth = max($boxWidth, self::DOT_SIZE + 4);
    $boxX = (int) (($totalWidth - $boxWidth) / 2);
    $centerX = (int) ($totalWidth / 2);
    $pointerTip = self::BODY_HEIGHT + self::POINTER_HEIGHT;
    $dotCy = $pointerTip + self::DOT_GAP + (int) (self::DOT_SIZE / 2);
    $totalHeight = $pointerTip + self::DOT_GAP + self::DOT_SIZE;
    $dotRadius = (int) (self::DOT_SIZE / 2);
    $border = (string) self::BORDER;

    if ($active) {
      $fill = self::MARKER_GREEN;
      $textFill = self::MARKER_WHITE;
      $stroke = self::MARKER_GREEN;
    }
    else {
      $fill = self::MARKER_WHITE;
      $textFill = self::MARKER_GREEN;
      $stroke = self::MARKER_GREEN;
    }

    $bubblePath = $this->buildBubblePath(
      $boxX,
      $boxX + $boxWidth,
      $centerX,
      self::BODY_HEIGHT,
      self::POINTER_HALF_WIDTH,
      $pointerTip,
    );

    return <<<SVG
<?xml version="1.0" encoding="UTF-8"?>
<svg xmlns="http://www.w3.org/2000/svg" width="{$totalWidth}" height="{$totalHeight}" viewBox="0 0 {$totalWidth} {$totalHeight}" role="img" aria-hidden="true">
  <path d="{$bubblePath}" fill="{$fill}" stroke="{$stroke}" stroke-width="{$border}" stroke-linejoin="round"/>
  <text x="{$centerX}" y="13" dominant-baseline="central" text-anchor="middle" fill="{$textFill}" font-family="Arial, Helvetica, sans-serif" font-size="12" font-weight="700">{$safeLabel}</text>
  <circle cx="{$centerX}" cy="{$dotCy}" r="{$dotRadius}" fill="{$stroke}"/>
</svg>
SVG;
  }

  /**
   * Builds a single outline path for the price bubble (body + pointer).
   */
  private function buildBubblePath(
    int $left,
    int $right,
    int $centerX,
    int $bodyBottom,
    int $pointerHalfWidth,
    int $pointerTip,
  ): string {
    return sprintf(
      'M %d,0 H %d V %d L %d,%d L %d,%d L %d,%d H %d Z',
      $left,
      $right,
      $bodyBottom,
      $centerX + $pointerHalfWidth,
      $bodyBottom,
      $centerX,
      $pointerTip,
      $centerX - $pointerHalfWidth,
      $bodyBottom,
      $left,
    );
  }

}
