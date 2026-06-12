<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Trait;

use Drupal\Core\Config\ImmutableConfig;

/**
 * Shared budget line formatting for offer field formatters.
 */
trait OfferBudgetFormattingTrait {

  /**
   * @return array{amount: string, qualifiers: string, show_info: bool, from_prefix: string}|null
   */
  protected function buildBudgetParts(object $entity): ?array {
    $config = $this->budgetConfig();

    if (!$entity->hasField('field_budget_value') || $entity->get('field_budget_value')->isEmpty()) {
      return [
        'amount' => (string) ($config->get('on_request') ?? ''),
        'qualifiers' => '',
        'show_info' => FALSE,
        'from_prefix' => '',
      ];
    }

    $raw = $entity->get('field_budget_value')->value;
    if ($raw === NULL || $raw === '' || (float) $raw <= 0) {
      return [
        'amount' => (string) ($config->get('on_request') ?? ''),
        'qualifiers' => '',
        'show_info' => FALSE,
        'from_prefix' => '',
      ];
    }

    $amount = number_format((float) $raw, 0, ',', ' ');
    $currency_code = (string) ($entity->get('field_budget_currency')->value ?? 'EUR');
    $currency = match (strtoupper($currency_code)) {
      'EUR' => '€',
      default => $this->dictionaryLabel('currency', $currency_code) ?: $currency_code,
    };

    $qualifiers = $this->formatBudgetQualifiers($entity, $config);
    $show_info = $entity->hasField('field_budget_ht')
      && (bool) $entity->get('field_budget_ht')->value;

    return [
      'amount' => $amount . ' ' . $currency,
      'qualifiers' => $qualifiers,
      'show_info' => $show_info,
      'from_prefix' => $this->formatBudgetFromPrefix($entity, $config),
    ];
  }

  /**
   * Builds a single-line budget label.
   */
  protected function formatBudgetLine(object $entity): string {
    $parts = $this->buildBudgetParts($entity);
    if ($parts === NULL) {
      return '';
    }

    if ($parts['qualifiers'] === '') {
      return $parts['amount'];
    }

    return $parts['amount'] . ' ' . $parts['qualifiers'];
  }

  protected function formatBudgetQualifiers(object $entity, ImmutableConfig $config): string {
    $tax_label = $this->formatBudgetTaxLabel($entity, $config);
    $unit_suffix = $this->formatBudgetUnitSuffix($entity);

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

  protected function formatBudgetTaxLabel(object $entity, ImmutableConfig $config): string {
    $is_ht = $entity->hasField('field_budget_ht') && (bool) $entity->get('field_budget_ht')->value;

    if (!$is_ht) {
      return (string) ($config->get('label_ttc') ?? '');
    }

    if (!$this->isRentalBudgetOperation($entity)) {
      return 'HT/HD';
    }

    if (!$entity->hasField('field_budget_cc') || $entity->get('field_budget_cc')->isEmpty()) {
      return 'HT';
    }

    $charges = (bool) $entity->get('field_budget_cc')->value ? 'CC' : 'HC';
    return 'HT/' . $charges;
  }

  protected function formatBudgetUnitSuffix(object $entity): string {
    if (!$this->isRentalBudgetOperation($entity)) {
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

  protected function isRentalBudgetOperation(object $entity): bool {
    if (!$entity->hasField('field_operation_type') || $entity->get('field_operation_type')->isEmpty()) {
      return FALSE;
    }

    $code = strtoupper((string) $entity->get('field_operation_type')->value);
    return in_array($code, ['LOC', 'RENT'], TRUE);
  }

  /**
   * Whether the budget should be prefixed with "From" / "À partir de".
   */
  protected function shouldShowBudgetFromPrefix(object $entity): bool {
    if ($entity->hasField('field_divisible') && (bool) $entity->get('field_divisible')->value) {
      return TRUE;
    }

    if (!$this->isRentalBudgetOperation($entity)) {
      return FALSE;
    }

    $unit_code = $entity->hasField('field_budget_unit') && !$entity->get('field_budget_unit')->isEmpty()
      ? strtoupper((string) $entity->get('field_budget_unit')->value)
      : '';

    return $unit_code === 'PER_M2';
  }

  /**
   * Resolves the localized "from" prefix when applicable.
   */
  protected function formatBudgetFromPrefix(object $entity, ImmutableConfig $config): string {
    if (!$this->shouldShowBudgetFromPrefix($entity)) {
      return '';
    }

    return trim((string) ($config->get('budget_from_prefix') ?? ''));
  }

  /**
   * Resolves a dictionary label for display.
   */
  protected function dictionaryLabel(string $type, string $code): string {
    if ($code === '' || !\Drupal::hasService('ps_dictionary.resolver')) {
      return $code;
    }
    $label = \Drupal::service('ps_dictionary.resolver')->resolveLabel($type, $code);
    return $label ?: $code;
  }

  /**
   * Loads language-aware budget display config.
   */
  abstract protected function budgetConfig(): ImmutableConfig;

}
