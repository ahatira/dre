<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;

/**
 * Formats the offer budget as a single composed label.
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
final class OfferBudgetDisplayFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode): array {
    $entity = $items->getEntity();
    if ($entity === NULL) {
      return [];
    }

    $text = $this->formatBudget($entity);
    if ($text === '') {
      return [];
    }

    return [
      0 => [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $text,
        '#attributes' => ['class' => ['ps-offer-budget']],
      ],
    ];
  }

  /**
   * Builds the composed budget string from offer fields.
   */
  private function formatBudget(object $entity): string {
    if (!$entity->hasField('field_budget_value') || $entity->get('field_budget_value')->isEmpty()) {
      return (string) $this->t('On request');
    }

    $raw = $entity->get('field_budget_value')->value;
    if ($raw === NULL || $raw === '' || (float) $raw <= 0) {
      return (string) $this->t('On request');
    }

    $amount = number_format((float) $raw, 0, ',', ' ');
    $currencyCode = (string) ($entity->get('field_budget_currency')->value ?? 'EUR');
    $currency = match (strtoupper($currencyCode)) {
      'EUR' => '€',
      default => $this->dictionaryLabel('currency', $currencyCode) ?: $currencyCode,
    };

    $parts = [$amount, $currency];

    if ($entity->hasField('field_budget_ht') && !(bool) $entity->get('field_budget_ht')->value) {
      $parts[] = (string) $this->t('TTC');
    }
    else {
      $parts[] = 'HT';
    }

    if ($entity->hasField('field_budget_cc')) {
      $parts[] = (bool) $entity->get('field_budget_cc')->value ? 'CC' : 'HC';
    }

    if ($entity->hasField('field_budget_unit') && !$entity->get('field_budget_unit')->isEmpty()) {
      $unit = $this->dictionaryLabel('budget_unit', (string) $entity->get('field_budget_unit')->value);
      if ($unit !== '') {
        $parts[] = '/' . $unit;
      }
    }

    $operationCode = $entity->hasField('field_operation_type')
      ? (string) ($entity->get('field_operation_type')->value ?? '')
      : '';
    if ($operationCode === 'LOC' && $entity->hasField('field_budget_period')) {
      $period = (string) ($entity->get('field_budget_period')->value ?? '');
      $suffix = match ($period) {
        'MONTH' => '/' . $this->t('month'),
        'YEAR' => '/' . $this->t('year'),
        default => '',
      };
      if ($suffix !== '') {
        $parts[] = ltrim($suffix, '/');
      }
    }

    return implode(' ', array_filter($parts, static fn (string $part): bool => $part !== ''));
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

}
