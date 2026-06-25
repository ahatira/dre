<?php

declare(strict_types=1);

namespace Drupal\ps_compare\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\node\NodeInterface;
use Drupal\ps_dictionary\Service\DictionaryResolver;
use Drupal\ps_offer\Service\OfferGalleryImageResolver;
use Drupal\ps_offer\Service\OfferMapLocationBuilder;
use Drupal\ps_offer\Service\OfferSurfaceKpiBuilder;

/**
 * Builds compact offer summaries for the compare panel.
 */
final class CompareOfferSummaryBuilder {

  use StringTranslationTrait;

  private const IMAGE_STYLE = 'ps_compare_column';

  public function __construct(
    private readonly OfferSurfaceKpiBuilder $surfaceKpiBuilder,
    private readonly DictionaryResolver $dictionaryResolver,
    private readonly ConfigFactoryInterface $configFactory,
    private readonly EntityRepositoryInterface $entityRepository,
    private readonly OfferMapLocationBuilder $mapLocationBuilder,
    private readonly OfferGalleryImageResolver $galleryImageResolver,
  ) {}

  /**
   * @return array<string, mixed>|null
   *   Summary props or NULL when the node cannot be summarized.
   */
  public function build(NodeInterface $node): ?array {
    if ($node->bundle() !== 'offer') {
      return NULL;
    }

    $node = $this->entityRepository->getTranslationFromContext($node);
    $budget = $this->buildBudgetParts($node);
    $surfaceParts = $this->surfaceKpiBuilder->buildKpiParts($node);

    return [
      'entity_type_id' => $node->getEntityTypeId(),
      'entity_id' => (int) $node->id(),
      'title' => $this->formatListTitle($node),
      'location' => $this->formatLocation($node),
      'address' => $this->formatFullAddress($node),
      'surface' => $this->formatSurfaceFromParts($surfaceParts),
      'surface_primary' => $surfaceParts['primary'] !== '' ? $surfaceParts['primary'] : NULL,
      'surface_suffix' => $surfaceParts['suffix'],
      'price_amount' => $budget['amount'],
      'price_qualifiers' => $budget['qualifiers'],
      'thumbnail' => $this->galleryImageResolver->resolvePrimaryImageUrlWithFallback($node, self::IMAGE_STYLE),
      'gallery_urls' => $this->galleryImageResolver->resolveGalleryImageUrlsWithFallback($node, self::IMAGE_STYLE),
      'url' => $node->toUrl()->toString(),
    ];
  }

  /**
   * @return list<string>
   */
  public function resolveGalleryImageUrls(NodeInterface $node): array {
    return $this->galleryImageResolver->resolveGalleryImageUrls($node, self::IMAGE_STYLE);
  }

  /**
   * @return list<string>
   */
  public function resolveGalleryFileUris(NodeInterface $node): array {
    return $this->galleryImageResolver->resolveImageUris($node);
  }

  /**
   * Returns the first gallery file URI for email embedding.
   */
  public function resolvePrimaryImageFileUri(NodeInterface $node): ?string {
    return $this->galleryImageResolver->resolvePrimaryImageUri($node);
  }

  private function formatLocation(NodeInterface $node): ?string {
    if (!$node->hasField('field_address') || $node->get('field_address')->isEmpty()) {
      return NULL;
    }

    $address = $node->get('field_address')->first();
    $postal = trim((string) ($address->postal_code ?? ''));
    $locality = trim((string) ($address->locality ?? ''));
    $locality = $locality !== '' ? mb_strtoupper($locality) : '';
    $location = trim($postal . ' ' . $locality);

    return $location !== '' ? $location : NULL;
  }

  /**
   * Full public address for compare column headers (street, postal code, city).
   */
  private function formatFullAddress(NodeInterface $node): ?string {
    $address = trim($this->mapLocationBuilder->buildPublicAddress($node));
    return $address !== '' ? $address : NULL;
  }

  private function formatListTitle(NodeInterface $node): string {
    $commercial = trim((string) ($node->get('field_commercial_title')->value ?? ''));
    if ($commercial !== '') {
      return $commercial;
    }

    $parts = [];
    $operationCode = (string) ($node->get('field_operation_type')->value ?? '');
    if ($operationCode !== '') {
      $parts[] = $this->dictionaryLabel('operation_type', $operationCode);
    }

    $assetCode = (string) ($node->get('field_asset_type')->value ?? '');
    if ($assetCode !== '') {
      $parts[] = $this->dictionaryLabel('asset_type', $assetCode);
    }

    if ($node->hasField('field_address') && !$node->get('field_address')->isEmpty()) {
      $address = $node->get('field_address')->first();
      $locality = trim((string) ($address->locality ?? ''));
      if ($locality !== '') {
        $parts[] = mb_strtoupper($locality);
      }
    }

    if ($parts !== []) {
      return implode(' ', $parts);
    }

    return $node->label() ?? '';
  }

  /**
   * @param array{primary: string, suffix: string|null} $parts
   */
  private function formatSurfaceFromParts(array $parts): ?string {
    if ($parts['primary'] === '') {
      return NULL;
    }

    if ($parts['suffix'] !== NULL && $parts['suffix'] !== '') {
      return $parts['primary'] . ' ' . $parts['suffix'];
    }

    return $parts['primary'];
  }

  private function formatSurface(NodeInterface $node): ?string {
    return $this->formatSurfaceFromParts($this->surfaceKpiBuilder->buildKpiParts($node));
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

  private function dictionaryLabel(string $type, string $code): string {
    if ($code === '') {
      return '';
    }
    $label = $this->dictionaryResolver->resolveLabel($type, $code);
    return $label ?: $code;
  }

  private function budgetConfig(): ImmutableConfig {
    return $this->configFactory->get('ps_offer.settings');
  }

}
