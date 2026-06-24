<?php

declare(strict_types=1);

namespace Drupal\ps_theme\Utility;

use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\node\NodeInterface;
use Drupal\ps_offer\Service\OfferGalleryImageResolver;

/**
 * Shared offer node data helpers for card prop builders.
 */
trait OfferNodeCardPropsTrait {

  use StringTranslationTrait;

  private const IMAGE_STYLE = 'bnp_media_admin_card';

  private const EXCLUSIVE_MANDATE_CODES = [
    'EXCL',
    'EXCLUSIVITE',
    'EXCLUSIVE',
    'EXCLUSIVITY',
  ];

  private function formatSurface(NodeInterface $node): ?string {
    $parts = $this->formatSurfaceParts($node);
    if ($parts['primary'] === '') {
      return NULL;
    }

    if ($parts['suffix'] !== NULL && $parts['suffix'] !== '') {
      return $parts['primary'] . ' ' . $parts['suffix'];
    }

    return $parts['primary'];
  }

  /**
   * @return array{primary: string, suffix: string|null}
   */
  private function formatSurfaceParts(NodeInterface $node): array {
    if (\Drupal::hasService('ps_offer.surface_kpi_builder')) {
      return \Drupal::service('ps_offer.surface_kpi_builder')->buildKpiParts($node);
    }

    return ['primary' => '', 'suffix' => NULL];
  }

  /**
   * @return array{amount: string, qualifiers: string}
   */
  private function buildBudgetParts(NodeInterface $node): array {
    $config = $this->budgetConfig();

    if (!$node->hasField('field_budget_value') || $node->get('field_budget_value')->isEmpty()) {
      return [
        'amount' => (string) ($config->get('on_request') ?? ''),
        'qualifiers' => '',
      ];
    }

    $raw = $node->get('field_budget_value')->value;
    if ($raw === NULL || $raw === '' || (float) $raw <= 0) {
      return [
        'amount' => (string) ($config->get('on_request') ?? ''),
        'qualifiers' => '',
      ];
    }

    $amount = number_format((float) $raw, 0, ',', ' ');
    $currencyCode = (string) ($node->get('field_budget_currency')->value ?? 'EUR');
    $currency = match (strtoupper($currencyCode)) {
      'EUR' => '€',
      default => $this->dictionaryLabel('currency', $currencyCode) ?: $currencyCode,
    };

    return [
      'amount' => $amount . ' ' . $currency,
      'qualifiers' => $this->formatQualifiers($node, $config),
    ];
  }

  private function formatQualifiers(NodeInterface $node, ImmutableConfig $config): string {
    $taxLabel = $this->formatTaxLabel($node, $config);
    $unitSuffix = $this->formatUnitSuffix($node);

    if ($taxLabel === '' && $unitSuffix === '') {
      return '';
    }

    if ($taxLabel === '') {
      return $unitSuffix;
    }

    if ($unitSuffix === '') {
      return $taxLabel;
    }

    return $taxLabel . $unitSuffix;
  }

  private function formatQualifiersMarkup(string $qualifiers): string {
    return str_replace('m²', 'm<sup>2</sup>', $qualifiers);
  }

  private function formatTaxLabel(NodeInterface $node, ImmutableConfig $config): string {
    $isHt = $node->hasField('field_budget_ht') && (bool) $node->get('field_budget_ht')->value;

    if (!$isHt) {
      return (string) ($config->get('label_ttc') ?? '');
    }

    if (!$this->isRentalOperation($node)) {
      return 'HT/HD';
    }

    if (!$node->hasField('field_budget_cc') || $node->get('field_budget_cc')->isEmpty()) {
      return 'HT';
    }

    $charges = (bool) $node->get('field_budget_cc')->value ? 'CC' : 'HC';
    return 'HT/' . $charges;
  }

  private function formatUnitSuffix(NodeInterface $node): string {
    if (!$this->isRentalOperation($node)) {
      return '';
    }

    $unitCode = $node->hasField('field_budget_unit') && !$node->get('field_budget_unit')->isEmpty()
      ? strtoupper((string) $node->get('field_budget_unit')->value)
      : '';

    if (in_array($unitCode, ['GLOBAL', 'GLO'], TRUE)) {
      return '';
    }

    $period = $node->hasField('field_budget_period') && !$node->get('field_budget_period')->isEmpty()
      ? strtoupper((string) $node->get('field_budget_period')->value)
      : 'YEAR';

    $periodLabel = match ($period) {
      'MONTH' => (string) $this->t('mo', [], ['context' => 'Offer budget period short']),
      'YEAR' => (string) $this->t('yr', [], ['context' => 'Offer budget period short']),
      default => '',
    };

    $unitLabel = match ($unitCode) {
      'PER_M2' => 'm²',
      'PER_POSTE' => (string) $this->t('seat', [], ['context' => 'Offer budget unit']),
      default => '',
    };

    if ($unitLabel === '') {
      return $periodLabel !== '' ? '/' . $periodLabel : '';
    }

    return $periodLabel !== '' ? '/' . $unitLabel . '/' . $periodLabel : '/' . $unitLabel;
  }

  private function isRentalOperation(NodeInterface $node): bool {
    if (!$node->hasField('field_operation_type') || $node->get('field_operation_type')->isEmpty()) {
      return FALSE;
    }

    $code = strtoupper((string) $node->get('field_operation_type')->value);
    return in_array($code, ['LOC', 'RENT'], TRUE);
  }

  private function isExclusive(NodeInterface $node): bool {
    if (!$node->hasField('field_mandate_type') || $node->get('field_mandate_type')->isEmpty()) {
      return FALSE;
    }

    $code = strtoupper((string) $node->get('field_mandate_type')->value);
    if (in_array($code, self::EXCLUSIVE_MANDATE_CODES, TRUE)) {
      return TRUE;
    }

    return str_contains($code, 'EXCL');
  }

  /**
   * @return list<string>
   */
  private function resolveGalleryImageUrls(NodeInterface $node): array {
    return $this->galleryImageResolver()->resolveGalleryImageUrls($node, self::IMAGE_STYLE);
  }

  private function resolvePrimaryImageUrl(NodeInterface $node): ?string {
    return $this->galleryImageResolver()->resolvePrimaryImageUrl($node, self::IMAGE_STYLE);
  }

  private function resolvePrimaryImageUrlWithFallback(NodeInterface $node): string {
    return $this->galleryImageResolver()->resolvePrimaryImageUrlWithFallback($node, self::IMAGE_STYLE);
  }

  private function resolveGalleryImageUrlsWithFallback(NodeInterface $node): array {
    return $this->galleryImageResolver()->resolveGalleryImageUrlsWithFallback($node, self::IMAGE_STYLE);
  }

  /**
   * Uses configured default alt text when the card falls back to the site image.
   */
  private function resolveImageAlt(NodeInterface $node, string $fallback): string {
    if ($this->resolvePrimaryImageUrl($node) !== NULL) {
      return $fallback;
    }

    return $this->galleryImageResolver()->getDefaultImageAlt();
  }

  private function galleryImageResolver(): OfferGalleryImageResolver {
    return \Drupal::service('ps_offer.gallery_image_resolver');
  }

  private function dictionaryLabel(string $type, string $code): string {
    if ($code === '') {
      return '';
    }
    $label = \Drupal::service('ps_dictionary.resolver')->resolveLabel($type, $code);
    return $label ?: $code;
  }

  private function budgetConfig(): ImmutableConfig {
    return \Drupal::configFactory()->get('ps_offer.settings');
  }

}
