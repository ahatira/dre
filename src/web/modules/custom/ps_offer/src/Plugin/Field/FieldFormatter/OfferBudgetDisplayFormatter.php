<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Plugin\Field\FieldFormatter;

use Drupal\Component\Utility\Html;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Formats the offer budget as a composed label (Figma Frame 174).
 *
 * @FieldFormatter(
 *   id = "ps_offer_budget_display",
 *   label = @Translation("Offer budget (composed)"),
 *   field_types = {
 *     "decimal",
 *     "float",
 *     "integer"
 *   }
 * )
 */
final class OfferBudgetDisplayFormatter extends FormatterBase implements ContainerFactoryPluginInterface {

  public function __construct(
    string $plugin_id,
    mixed $plugin_definition,
    FieldDefinitionInterface $field_definition,
    array $settings,
    string $label,
    string $view_mode,
    array $third_party_settings,
    private readonly ConfigFactoryInterface $configFactory,
  ) {
    parent::__construct(
      $plugin_id,
      $plugin_definition,
      $field_definition,
      $settings,
      $label,
      $view_mode,
      $third_party_settings,
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): static {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['label'],
      $configuration['view_mode'],
      $configuration['third_party_settings'],
      $container->get('config.factory'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode): array {
    $entity = $items->getEntity();
    if ($entity === NULL) {
      return [];
    }

    $parts = $this->buildBudgetParts($entity);
    if ($parts === NULL) {
      return [];
    }

    $line = [
      '#type' => 'container',
      '#attributes' => ['class' => ['ps-offer-budget__line']],
      'amount' => [
        '#type' => 'html_tag',
        '#tag' => 'span',
        '#value' => $parts['amount'],
        '#attributes' => ['class' => ['ps-offer-budget__amount']],
      ],
    ];

    if ($parts['qualifiers'] !== '') {
      $line['qualifiers'] = [
        '#type' => 'html_tag',
        '#tag' => 'span',
        '#value' => $parts['qualifiers'],
        '#attributes' => ['class' => ['ps-offer-budget__qualifiers']],
      ];
    }

    $build = [
      '#type' => 'container',
      '#attributes' => ['class' => ['ps-offer-budget']],
      'line' => $line,
    ];

    if ($parts['show_info']) {
      $build['tooltip'] = $this->buildInfoTooltip($entity);
    }

    return [0 => $build];
  }

  /**
   * Builds the price info popover (bnppre.fr price-tooltip pattern).
   *
   * @return array<string, mixed>
   *   Render array for the tooltip trigger and panel.
   */
  private function buildInfoTooltip(object $entity): array {
    $config = $this->budgetConfig();
    $tooltip_id = Html::getUniqueId('ps-offer-budget-tooltip');
    $items = $this->buildInfoTooltipItems($entity, $config);

    return [
      '#type' => 'container',
      '#attributes' => ['class' => ['ps-offer-budget__tooltip']],
      'trigger' => [
        '#type' => 'html_tag',
        '#tag' => 'button',
        '#value' => '',
        '#attributes' => [
          'type' => 'button',
          'class' => ['ps-offer-budget__info'],
          'aria-label' => $config->get('price_information') ?? '',
          'aria-expanded' => 'false',
          'aria-controls' => $tooltip_id,
          'data-ps-budget-info' => 'true',
        ],
      ],
      'panel' => [
        '#type' => 'container',
        '#attributes' => [
          'class' => ['ps-offer-budget__tooltip-panel', 'is-hidden'],
          'id' => $tooltip_id,
          'role' => 'tooltip',
        ],
        'title' => [
          '#type' => 'html_tag',
          '#tag' => 'p',
          '#value' => $config->get('tooltip_title') ?? '',
          '#attributes' => ['class' => ['ps-offer-budget__tooltip-title']],
        ],
        'list' => [
          '#theme' => 'item_list',
          '#items' => $items,
          '#attributes' => ['class' => ['ps-offer-budget__tooltip-list']],
        ],
      ],
    ];
  }

  /**
   * Builds tooltip list entries according to offer operation and budget flags.
   *
   * @return list<string>
   *   Localized tooltip lines.
   */
  private function buildInfoTooltipItems(object $entity, ImmutableConfig $config): array {
    $items = [
      (string) ($config->get('label_ht') ?? ''),
    ];

    if ($this->isRentalOperation($entity)) {
      $charges_included = $entity->hasField('field_budget_cc')
        && !$entity->get('field_budget_cc')->isEmpty()
        && (bool) $entity->get('field_budget_cc')->value;

      $items[] = $charges_included
        ? (string) ($config->get('label_cc') ?? '')
        : (string) ($config->get('label_hc') ?? '');
    }
    else {
      $items[] = (string) ($config->get('label_hd') ?? '');
    }

    $items[] = $this->formatFeesLine($entity, $config);

    return array_values(array_filter($items, static fn (string $line): bool => $line !== ''));
  }

  /**
   * Builds the fees line for the popover.
   */
  private function formatFeesLine(object $entity, ImmutableConfig $config): string {
    $prefix = trim((string) ($config->get('fees_prefix') ?? ''));
    $fees = '';

    if ($entity->hasField('field_budget_fees') && !$entity->get('field_budget_fees')->isEmpty()) {
      $fees = trim((string) $entity->get('field_budget_fees')->value);
    }

    if ($fees === '') {
      $fees = $this->isRentalOperation($entity)
        ? trim((string) ($config->get('default_fees_rental') ?? ''))
        : trim((string) ($config->get('default_fees_sale') ?? ''));
    }

    if ($fees === '') {
      return $prefix;
    }

    return $prefix !== '' ? $prefix . ' ' . $fees : $fees;
  }

  /**
   * Builds structured budget display parts.
   *
   * @return array{amount: string, qualifiers: string, show_info: bool}|null
   *   Display parts, or NULL when empty.
   */
  private function buildBudgetParts(object $entity): ?array {
    $config = $this->budgetConfig();

    if (!$entity->hasField('field_budget_value') || $entity->get('field_budget_value')->isEmpty()) {
      return [
        'amount' => (string) ($config->get('on_request') ?? ''),
        'qualifiers' => '',
        'show_info' => FALSE,
      ];
    }

    $raw = $entity->get('field_budget_value')->value;
    if ($raw === NULL || $raw === '' || (float) $raw <= 0) {
      return [
        'amount' => (string) ($config->get('on_request') ?? ''),
        'qualifiers' => '',
        'show_info' => FALSE,
      ];
    }

    $amount = number_format((float) $raw, 0, ',', ' ');
    $currency_code = (string) ($entity->get('field_budget_currency')->value ?? 'EUR');
    $currency = match (strtoupper($currency_code)) {
      'EUR' => '€',
      default => $this->dictionaryLabel('currency', $currency_code) ?: $currency_code,
    };

    $qualifiers = $this->formatQualifiers($entity, $config);
    $show_info = $entity->hasField('field_budget_ht')
      && (bool) $entity->get('field_budget_ht')->value;

    return [
      'amount' => $amount . ' ' . $currency,
      'qualifiers' => $qualifiers,
      'show_info' => $show_info,
    ];
  }

  /**
   * Builds the HT/HC/m²/an qualifier suffix (smaller type in Figma mockup).
   */
  private function formatQualifiers(object $entity, ImmutableConfig $config): string {
    $tax_label = $this->formatTaxLabel($entity, $config);
    $unit_suffix = $this->formatUnitSuffix($entity);

    if ($tax_label === '' && $unit_suffix === '') {
      return '';
    }

    if ($tax_label === '') {
      return $unit_suffix;
    }

    if ($unit_suffix === '') {
      return $tax_label;
    }

    return $tax_label . $unit_suffix;
  }

  /**
   * Builds the tax/charges label (e.g. HT, HT/HC, TTC).
   */
  private function formatTaxLabel(object $entity, ImmutableConfig $config): string {
    $is_ht = $entity->hasField('field_budget_ht') && (bool) $entity->get('field_budget_ht')->value;

    if (!$is_ht) {
      return (string) ($config->get('label_ttc') ?? '');
    }

    if (!$this->isRentalOperation($entity)) {
      return 'HT/HD';
    }

    if (!$entity->hasField('field_budget_cc') || $entity->get('field_budget_cc')->isEmpty()) {
      return 'HT';
    }

    $charges = (bool) $entity->get('field_budget_cc')->value ? 'CC' : 'HC';
    return 'HT/' . $charges;
  }

  /**
   * Builds the unit suffix for rental offers (e.g. /m²/an).
   */
  private function formatUnitSuffix(object $entity): string {
    if (!$this->isRentalOperation($entity)) {
      return '';
    }

    $unit_code = $entity->hasField('field_budget_unit') && !$entity->get('field_budget_unit')->isEmpty()
      ? strtoupper((string) $entity->get('field_budget_unit')->value)
      : '';

    if (in_array($unit_code, ['GLOBAL', 'GLO'], TRUE)) {
      return '';
    }

    $period = $entity->hasField('field_budget_period') && !$entity->get('field_budget_period')->isEmpty()
      ? strtoupper((string) $entity->get('field_budget_period')->value)
      : 'YEAR';

    $period_label = match ($period) {
      'MONTH' => (string) $this->t('mo', [], ['context' => 'Offer budget period short']),
      'YEAR' => (string) $this->t('yr', [], ['context' => 'Offer budget period short']),
      default => '',
    };

    $unit_label = match ($unit_code) {
      'PER_M2' => 'm²',
      'PER_POSTE' => (string) $this->t('seat', [], ['context' => 'Offer budget unit']),
      default => '',
    };

    if ($unit_label === '') {
      return $period_label !== '' ? '/' . $period_label : '';
    }

    return $period_label !== '' ? '/' . $unit_label . '/' . $period_label : '/' . $unit_label;
  }

  /**
   * Whether the offer operation is a rental (LOC/RENT).
   */
  private function isRentalOperation(object $entity): bool {
    if (!$entity->hasField('field_operation_type') || $entity->get('field_operation_type')->isEmpty()) {
      return FALSE;
    }

    $code = strtoupper((string) $entity->get('field_operation_type')->value);
    return in_array($code, ['LOC', 'RENT'], TRUE);
  }

  /**
   * Resolves a dictionary label for display.
   */
  private function dictionaryLabel(string $type, string $code): string {
    if ($code === '' || !\Drupal::hasService('ps_dictionary.resolver')) {
      return $code;
    }
    $label = \Drupal::service('ps_dictionary.resolver')->resolveLabel($type, $code);
    return $label ?: $code;
  }

  /**
   * Loads language-aware budget display config.
   */
  private function budgetConfig(): ImmutableConfig {
    return $this->configFactory->get('ps_offer.settings');
  }

}
