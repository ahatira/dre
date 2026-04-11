<?php

declare(strict_types=1);

namespace Drupal\ps_price\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\ps_dictionary\Service\DictionaryManagerInterface;
use Drupal\ps_price\Plugin\Field\FieldType\PriceItem;

/**
 * Price formatter service.
 *
 * Provides locale-aware formatting of price field values with support
 * for business flags (on_request, from, VAT, charges) and price ranges.
 */
final class PriceFormatter implements PriceFormatterInterface {

  use StringTranslationTrait;

  /**
   * Constructs a PriceFormatter object.
   *
   * @param \Drupal\Core\Language\LanguageManagerInterface $languageManager
   *   The language manager service.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   The config factory.
   * @param \Drupal\ps_dictionary\Service\DictionaryManagerInterface $dictionaryManager
   *   The dictionary manager.
   */
  public function __construct(
    protected readonly LanguageManagerInterface $languageManager,
    protected readonly ConfigFactoryInterface $configFactory,
    protected readonly DictionaryManagerInterface $dictionaryManager,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function format(FieldItemInterface $item, array $options = []): string {
    assert($item instanceof PriceItem);

    // Default options.
    $options += [
      'show_currency' => TRUE,
      'show_unit' => TRUE,
      'show_period' => TRUE,
      'show_flags' => TRUE,
    ];

    // Build price string.
    $parts = [];

    // Add "from" prefix if needed.
    if ($options['show_flags'] && $item->is_from) {
      $parts[] = $this->t('From')->render();
    }

    // Format amount or range.
    if ($item->amount !== NULL) {
      $parts[] = $this->formatAmount((float) $item->amount);
    }

    // Add currency.
    if ($options['show_currency'] && $item->currency_code) {
      $parts[] = $item->currency_code;
    }

    // Add unit.
    if ($options['show_unit'] && $item->unit_code) {
      $parts[] = $this->getDisplayValue('price_unit', $item->unit_code);
    }

    // Add period.
    if ($options['show_period'] && $item->period_code) {
      $parts[] = '/' . $this->getDisplayValue('price_period', $item->period_code);
    }

    // Add flags.
    if ($options['show_flags']) {
      $flags = [];
      if ($item->is_vat_excluded) {
        $flags[] = $this->t('excl. VAT')->render();
      }
      if ($item->is_charges_included) {
        $flags[] = $this->t('charges incl.')->render();
      }
      if (!empty($flags)) {
        $parts[] = '(' . implode(', ', $flags) . ')';
      }
    }

    return implode(' ', $parts);
  }

  /**
   * {@inheritdoc}
   */
  public function formatShort(FieldItemInterface $item, array $options = []): string {
    assert($item instanceof PriceItem);

    // Default options.
    $options += [
      'show_currency' => TRUE,
    ];

    $parts = [];

    // Format amount.
    if ($item->amount !== NULL) {
      $parts[] = $this->formatAmount((float) $item->amount);
    }

    // Add currency.
    if ($options['show_currency'] && $item->currency_code) {
      $parts[] = $item->currency_code;
    }

    return implode(' ', $parts);
  }

  /**
   * {@inheritdoc}
   */
  public function getNumericForSearch(FieldItemInterface $item): ?float {
    assert($item instanceof PriceItem);

    if ($item->amount === NULL) {
      return NULL;
    }

    return (float) $item->amount;
  }

  /**
   * Formats a numeric amount according to locale.
   *
   * @param float $amount
   *   The amount to format.
   *
   * @return string
   *   The formatted amount.
   */
  private function formatAmount(float $amount): string {
    $langcode = $this->languageManager->getCurrentLanguage()->getId();
    $locale = \Locale::canonicalize(str_replace('-', '_', $langcode));

    $formatter = numfmt_create($locale, \NumberFormatter::DECIMAL);
    if (!$formatter instanceof \NumberFormatter) {
      return (string) $amount;
    }

    if (function_exists('numfmt_setAttribute')) {
      numfmt_setAttribute($formatter, \NumberFormatter::MIN_FRACTION_DIGITS, 2);
      numfmt_setAttribute($formatter, \NumberFormatter::MAX_FRACTION_DIGITS, 2);
    }

    $formatted = numfmt_format($formatter, $amount);
    return $formatted !== FALSE ? $formatted : (string) $amount;
  }

  /**
   * Gets display value (code or label based on settings).
   *
   * @param string $type
   *   Dictionary type.
   * @param string $code
   *   The code.
   *
   * @return string
   *   Code or label.
   */
  private function getDisplayValue(string $type, string $code): string {
    $useLabels = $this->configFactory->get('ps_price.settings')->get('display_codes_as_labels') ?? FALSE;
    return $useLabels ? ($this->dictionaryManager->getLabel($type, $code) ?? $code) : $code;
  }

}
