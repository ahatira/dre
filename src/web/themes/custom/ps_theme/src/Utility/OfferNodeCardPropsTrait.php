<?php

declare(strict_types=1);

namespace Drupal\ps_theme\Utility;

use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\image\Entity\ImageStyle;
use Drupal\media\MediaInterface;
use Drupal\node\NodeInterface;

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
    if (\Drupal::hasService('ps_offer.surface_kpi_builder')) {
      $text = \Drupal::service('ps_offer.surface_kpi_builder')->buildKpiSummary($node);
      return $text !== '' ? $text : NULL;
    }

    return NULL;
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
    if (!$node->hasField('field_media_gallery') || $node->get('field_media_gallery')->isEmpty()) {
      return [];
    }

    $style = ImageStyle::load(self::IMAGE_STYLE);
    if ($style === NULL) {
      return [];
    }

    $urls = [];
    foreach ($node->get('field_media_gallery')->referencedEntities() as $media) {
      if (!$media instanceof MediaInterface) {
        continue;
      }
      $uri = $this->resolveMediaUri($media);
      if ($uri !== NULL) {
        $urls[] = $style->buildUrl($uri);
      }
    }

    return $urls;
  }

  private function resolvePrimaryImageUrl(NodeInterface $node): ?string {
    $urls = $this->resolveGalleryImageUrls($node);
    return $urls[0] ?? NULL;
  }

  private function placeholderImageUrl(): string {
    $theme = \Drupal::theme()->getActiveTheme()->getPath();
    return '/' . $theme . '/assets/images/offer-placeholder.svg';
  }

  private function resolveMediaUri(MediaInterface $media): ?string {
    $bundle = $media->bundle();
    $candidates = match ($bundle) {
      'image', 'visite_guided' => ['field_media_image'],
      'gallery' => ['field_media_gallery_image'],
      default => ['thumbnail', 'field_media_image'],
    };

    foreach ($candidates as $fieldName) {
      if (!$media->hasField($fieldName) || $media->get($fieldName)->isEmpty()) {
        continue;
      }
      $file = $media->get($fieldName)->entity;
      if ($file !== NULL) {
        return $file->getFileUri();
      }
    }

    return NULL;
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
