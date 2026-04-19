<?php

declare(strict_types=1);

namespace Drupal\ps_price\Plugin\Field\FieldFormatter;

use Drupal\Component\Utility\Html;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Field\Attribute\FieldFormatter;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ps_dictionary\Service\DictionaryManagerInterface;
use Drupal\ps_price\Plugin\Field\FieldType\PriceItem;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Offer-oriented price formatter.
 */
#[FieldFormatter(
  id: 'ps_price_offer',
  label: new TranslatableMarkup('Offer'),
  field_types: ['ps_price'],
  weight: 2,
)]
class PriceOfferFormatter extends FormatterBase implements ContainerFactoryPluginInterface {

  /**
   * Constructs a PriceOfferFormatter object.
   */
  public function __construct(
    string $plugin_id,
    mixed $plugin_definition,
    mixed $field_definition,
    array $settings,
    string $label,
    string $view_mode,
    array $third_party_settings,
    protected readonly DictionaryManagerInterface $dictionaryManager,
    protected readonly ConfigFactoryInterface $configFactory,
    protected readonly LanguageManagerInterface $languageManager,
  ) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(
    ContainerInterface $container,
    array $configuration,
    $plugin_id,
    $plugin_definition,
  ): static {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['label'],
      $configuration['view_mode'],
      $configuration['third_party_settings'],
      $container->get('ps_dictionary.manager'),
      $container->get('config.factory'),
      $container->get('language_manager'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings(): array {
    return [
      'show_tooltip' => TRUE,
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state): array {
    $elements = parent::settingsForm($form, $form_state);

    $elements['show_tooltip'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show info tooltip'),
      '#default_value' => (bool) $this->getSetting('show_tooltip'),
    ];

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary(): array {
    return [
      $this->getSetting('show_tooltip')
        ? (string) $this->t('Tooltip enabled')
        : (string) $this->t('Tooltip disabled'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode): array {
    unset($langcode);
    $elements = [];

    foreach ($items as $delta => $item) {
      assert($item instanceof PriceItem);
      $data = $this->buildDisplayData($item);

      $elements[$delta] = [
        '#theme' => 'ps_price_offer',
        '#value_prefix' => $data['value_prefix'],
        '#value' => $data['value'],
        '#unit' => $data['unit'],
        '#tooltip_text' => $data['tooltip_text'],
        '#tooltip_html' => nl2br(Html::escape($data['tooltip_text'])),
        '#tooltip_aria' => (string) $this->t('Price information'),
        '#attached' => [
          'library' => ['ps_price/offer_formatter'],
        ],
        '#cache' => [
          'tags' => ['config:ps_price.settings'],
        ],
      ];
    }

    return $elements;
  }

  /**
   * Builds display payload for one price item.
   *
   * @return array{value_prefix: string, value: string, unit: string, tooltip_text: string}
   *   Render payload.
   */
  protected function buildDisplayData(PriceItem $item): array {
    $settings = $this->configFactory->get('ps_price.settings');

    if ($item->isOnRequest()) {
      return [
        'value_prefix' => '',
        'value' => (string) $this->t('On request'),
        'unit' => '',
        'tooltip_text' => '',
      ];
    }

    $currencySymbol = '';
    if (!empty($item->currency_code)) {
      $currencySymbol = (string) ($this->dictionaryManager->getMetadataValue('currency', (string) $item->currency_code, 'symbol', $item->currency_code) ?? $item->currency_code);
    }

    $value = trim($this->formatAmount((float) ($item->amount ?? 0)) . ' ' . $currencySymbol);
    $valuePrefix = !empty($item->is_from) ? (string) $this->t('From') : '';

    $labelHt = trim((string) ($settings->get('price_flag_ht_label') ?? 'HT'));
    $labelHc = trim((string) ($settings->get('price_flag_hc_label') ?? 'HC'));
    $tooltipHt = trim((string) ($settings->get('price_tooltip_ht_text') ?? 'HT: VAT excluded'));
    $tooltipHc = trim((string) ($settings->get('price_tooltip_hc_text') ?? 'HC: Service charges excluded'));
    $tooltipExtra = trim((string) ($settings->get('price_tooltip_extra_text') ?? ''));

    $flags = [];
    $tooltipLines = [];

    if (!empty($item->is_vat_excluded)) {
      $flags[] = $labelHt !== '' ? $labelHt : 'HT';
      if ($tooltipHt !== '') {
        $tooltipLines[] = $tooltipHt;
      }
    }
    if (empty($item->is_charges_included)) {
      $flags[] = $labelHc !== '' ? $labelHc : 'HC';
      if ($tooltipHc !== '') {
        $tooltipLines[] = $tooltipHc;
      }
    }

    if ($tooltipExtra !== '' && $tooltipLines !== []) {
      foreach (preg_split('/\r\n|\r|\n/', $tooltipExtra) ?: [] as $line) {
        $line = trim((string) $line);
        if ($line !== '') {
          $tooltipLines[] = $line;
        }
      }
    }

    $unitParts = [];
    if ($flags !== []) {
      $unitParts[] = implode('/', $flags);
    }

    if (!empty($item->unit_code)) {
      $unitSymbol = (string) ($this->dictionaryManager->getMetadataValue('price_unit', (string) $item->unit_code, 'symbol', '') ?? '');
      if ($unitSymbol === '') {
        $unitSymbol = (string) ($this->dictionaryManager->getLabel('price_unit', (string) $item->unit_code) ?? (string) $item->unit_code);
      }
      $unitParts[] = ltrim($unitSymbol, '/');
    }

    if (!empty($item->period_code)) {
      $period = $this->mapPeriodCode((string) $item->period_code);
      $unitParts[] = ltrim($period, '/');
    }

    $showTooltip = (bool) $this->getSetting('show_tooltip');

    return [
      'value_prefix' => $valuePrefix,
      'value' => $value,
      'unit' => $unitParts !== [] ? implode('/', $unitParts) : '',
      'tooltip_text' => $showTooltip && $tooltipLines !== [] ? implode("\n", $tooltipLines) : '',
    ];
  }

  /**
   * Formats amount using current locale without trailing .00 decimals.
   */
  protected function formatAmount(float $amount): string {
    $langcode = $this->languageManager->getCurrentLanguage()->getId();
    $locale = \Locale::canonicalize(str_replace('-', '_', $langcode));

    $formatter = \NumberFormatter::create($locale, \NumberFormatter::DECIMAL);
    if (!$formatter instanceof \NumberFormatter) {
      return (string) $amount;
    }

    if (fmod($amount, 1.0) === 0.0) {
      $formatter->setAttribute(\NumberFormatter::MIN_FRACTION_DIGITS, 0);
      $formatter->setAttribute(\NumberFormatter::MAX_FRACTION_DIGITS, 0);
    }
    else {
      $formatter->setAttribute(\NumberFormatter::MIN_FRACTION_DIGITS, 2);
      $formatter->setAttribute(\NumberFormatter::MAX_FRACTION_DIGITS, 2);
    }

    $formatted = $formatter->format($amount);
    return $formatted !== FALSE ? $formatted : (string) $amount;
  }

  /**
   * Maps period codes for compact display.
   */
  protected function mapPeriodCode(string $periodCode): string {
    return match (strtoupper($periodCode)) {
      'ANN' => 'an',
      'MEN' => 'mois',
      'TRI' => 'trimestre',
      'SEM' => 'semaine',
      default => $periodCode,
    };
  }

}
