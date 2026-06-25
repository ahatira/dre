<?php

declare(strict_types=1);

namespace Drupal\ps_context\Service;

use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Resolves search budget filter labels/units from the offer context matrix.
 *
 * Mirrors ps_context budget rules (PROJECT_MATRIX §4.2, §7.2) and bnppre.fr UX:
 * - Flexible search → generic Budget min/max (€)
 * - LOC + surface assets → rent per m²/year (PER_M2)
 * - LOC + COW → rent per seat/year (PER_POSTE)
 * - VEN → global sale price (GLOBAL)
 */
final class SearchBudgetFilterResolver {

  use StringTranslationTrait;

  private const OP_SALE = 'VEN';

  private const OP_RENT_CODES = ['LOC', 'RENT'];

  private const ASSET_COW = 'COW';

  /**
   * Resolves budget filter presentation for asset × operation context.
   *
   * @return array{field_label: string, toggle_default: string, min_label: string, max_label: string, input_unit: string, value_suffix: string, step: int, budget_unit: string|null}
   *   Budget filter presentation config.
   */
  public function resolve(?string $assetType, ?string $operationType = NULL): array {
    $asset = $assetType !== NULL && $assetType !== '' ? strtoupper($assetType) : NULL;
    $op = $operationType !== NULL && $operationType !== '' ? strtoupper($operationType) : NULL;

    if ($op === self::OP_SALE) {
      return $this->saleConfig();
    }

    if ($op !== NULL && in_array($op, self::OP_RENT_CODES, TRUE)) {
      if ($asset === self::ASSET_COW) {
        return $this->rentPerPosteConfig();
      }

      return $this->rentPerM2Config();
    }

    return $this->flexibleConfig();
  }

  /**
   * Homepage hero budget field — bnppre.fr entry point rules.
   *
   * Static label "Prix max. (HT/HD)" (editorial override in hero block).
   * Placeholder varies by operation; COW + LOC uses per-seat placeholder.
   *
   * @return array{max_label: string, max_placeholder: string, step: int}
   *   Homepage budget field presentation.
   */
  public function resolveHomepageEntry(?string $assetType, ?string $operationType = NULL): array {
    $asset = $assetType !== NULL && $assetType !== '' ? strtoupper($assetType) : NULL;
    $op = $operationType !== NULL && $operationType !== '' ? strtoupper($operationType) : NULL;
    $baseLabel = (string) $this->t('Prix max. (HT/HD)');

    if ($op === self::OP_SALE) {
      return [
        'max_label' => $baseLabel,
        'max_placeholder' => (string) $this->t('Prix max. (HT/HD)'),
        'step' => 1000,
      ];
    }

    if ($op !== NULL && in_array($op, self::OP_RENT_CODES, TRUE)) {
      if ($asset === self::ASSET_COW) {
        return [
          'max_label' => $baseLabel,
          'max_placeholder' => (string) $this->t('Loyer max. (€/poste/an)'),
          'step' => 1,
        ];
      }

      return [
        'max_label' => $baseLabel,
        'max_placeholder' => (string) $this->t('Loyer max. (HT/HC/m²/an)'),
        'step' => 10,
      ];
    }

    return [
      'max_label' => $baseLabel,
      'max_placeholder' => (string) $this->t('Budget max. (HT)'),
      'step' => 10,
    ];
  }

  /**
   * Asset × operation map for homepage hero budget placeholders.
   *
   * @param list<string> $assetCodes
   *   Asset type codes from SEO mappings.
   *
   * @return array<string, array<string, array{max_label: string, max_placeholder: string, step: int}>>
   */
  public function buildHomepageConfigMap(array $assetCodes): array {
    $operations = ['', 'LOC', 'VEN'];
    $map = [
      '' => [],
    ];

    foreach ($operations as $op) {
      $map[''][$op] = $this->resolveHomepageEntry(NULL, $op !== '' ? $op : NULL);
    }

    foreach ($assetCodes as $code) {
      $asset = strtoupper($code);
      $map[$asset] = [];
      foreach ($operations as $op) {
        $map[$asset][$op] = $this->resolveHomepageEntry($asset, $op !== '' ? $op : NULL);
      }
    }

    return $map;
  }

  /**
   * Operation map for homepage hero budget placeholders (bnppre.fr).
   *
   * @return array<string, array{max_label: string, max_placeholder: string, step: int}>
   */
  public function buildHomepageOperationMap(): array {
    return [
      '' => $this->resolveHomepageEntry(NULL, NULL),
      'LOC' => $this->resolveHomepageEntry(NULL, 'LOC'),
      'VEN' => $this->resolveHomepageEntry(NULL, 'VEN'),
    ];
  }

  /**
   * Builds a nested map for client-side budget label updates.
   *
   * @param list<string> $assetCodes
   *   Asset type codes from SEO mappings.
   *
   * @return array<string, array<string, array<string, mixed>>>
   *   Nested map: asset code → operation code → config.
   */
  public function buildConfigMap(array $assetCodes): array {
    $operations = ['', 'LOC', 'VEN'];
    $map = [
      '' => [],
    ];

    foreach ($operations as $op) {
      $map[''][$op] = $this->resolve(NULL, $op !== '' ? $op : NULL);
    }

    foreach ($assetCodes as $code) {
      $asset = strtoupper($code);
      $map[$asset] = [];
      foreach ($operations as $op) {
        $map[$asset][$op] = $this->resolve($asset, $op !== '' ? $op : NULL);
      }
    }

    return $map;
  }

  /**
   * Generic budget filter (no operation selected — bnppre "Budget min/max").
   *
   * @return array<string, mixed>
   *   Budget filter presentation config.
   */
  private function flexibleConfig(): array {
    return [
      'field_label' => (string) $this->t('Budget'),
      'toggle_default' => (string) $this->t('Budget'),
      'min_label' => (string) $this->t('Min budget (€)'),
      'max_label' => (string) $this->t('Max budget (€)'),
      'input_unit' => '€',
      'value_suffix' => ' €',
      'step' => 10,
      'budget_unit' => NULL,
    ];
  }

  /**
   * Sale price filter (VEN → GLOBAL).
   *
   * @return array<string, mixed>
   *   Budget filter presentation config.
   */
  private function saleConfig(): array {
    return [
      'field_label' => (string) $this->t('Price'),
      'toggle_default' => (string) $this->t('Price'),
      'min_label' => (string) $this->t('Min price (€)'),
      'max_label' => (string) $this->t('Max price (€)'),
      'input_unit' => '€',
      'value_suffix' => ' €',
      'step' => 1000,
      'budget_unit' => 'GLOBAL',
    ];
  }

  /**
   * Rental filter per m²/year (LOC + BUR/ENT/ACT/COM/TER).
   *
   * @return array<string, mixed>
   *   Budget filter presentation config.
   */
  private function rentPerM2Config(): array {
    return [
      'field_label' => (string) $this->t('Rent'),
      'toggle_default' => (string) $this->t('Rent'),
      'min_label' => (string) $this->t('Min rent (€/m²/year)'),
      'max_label' => (string) $this->t('Max rent (€/m²/year)'),
      'input_unit' => '€/m²/an',
      'value_suffix' => ' €/m²/an',
      'step' => 10,
      'budget_unit' => 'PER_M2',
    ];
  }

  /**
   * Rental filter per seat/year (LOC + COW → PER_POSTE).
   *
   * @return array<string, mixed>
   *   Budget filter presentation config.
   */
  private function rentPerPosteConfig(): array {
    return [
      'field_label' => (string) $this->t('Rent'),
      'toggle_default' => (string) $this->t('Rent'),
      'min_label' => (string) $this->t('Min rent (€/seat/year)'),
      'max_label' => (string) $this->t('Max rent (€/seat/year)'),
      'input_unit' => '€/poste/an',
      'value_suffix' => ' €/poste/an',
      'step' => 1,
      'budget_unit' => 'PER_POSTE',
    ];
  }

}
