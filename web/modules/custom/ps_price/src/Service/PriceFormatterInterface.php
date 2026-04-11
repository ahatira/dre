<?php

declare(strict_types=1);

namespace Drupal\ps_price\Service;

use Drupal\Core\Field\FieldItemInterface;

/**
 * Interface for price formatting service.
 *
 * Provides locale-aware formatting of price field values with support
 * for business flags and price ranges.
 *
 * @see \Drupal\ps_price\Service\PriceFormatter
 */
interface PriceFormatterInterface {

  /**
   * Formats a price item with full details.
   *
   * @param \Drupal\Core\Field\FieldItemInterface $item
   *   The price field item.
   * @param array<string, mixed> $options
   *   Formatting options:
   *   - show_currency (bool, default TRUE): Show currency code.
   *   - show_unit (bool, default TRUE): Show price unit.
   *   - show_period (bool, default TRUE): Show period.
   *   - show_flags (bool, default TRUE): Show business flags.
   *
   * @return string
   *   The formatted price string.
   */
  public function format(FieldItemInterface $item, array $options = []): string;

  /**
   * Formats a price item in short format (amount + currency only).
   *
   * @param \Drupal\Core\Field\FieldItemInterface $item
   *   The price field item.
   * @param array<string, mixed> $options
   *   Formatting options:
   *   - show_currency (bool, default TRUE): Show currency code.
   *
   * @return string
   *   The formatted price string.
   */
  public function formatShort(FieldItemInterface $item, array $options = []): string;

  /**
   * Gets normalized numeric value for search indexing.
   *
   * Converts price to a numeric value suitable for search comparison.
   *
   * @param \Drupal\Core\Field\FieldItemInterface $item
   *   The price field item.
   *
   * @return float|null
   *   The normalized price value or NULL if not applicable.
   */
  public function getNumericForSearch(FieldItemInterface $item): ?float;

}
