<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\Entity\EntityInterface;

/**
 * Builds surface/capacity KPI strings for offer displays (bnppre.fr rules).
 *
 * - COW → capacity (postes / seats), no m².
 * - TER → total land area only (ha / m²), no divisibility suffix.
 * - Divisible → "{TOTAL} {unit} ({template @surface})" when MINIM or ETREF
 *   is strictly below TOTAL (e.g. "2 000 m² (divisible dès 80 m²)").
 * - Non-divisible → "{TOTAL} {unit}" or "{DISPO} {unit}" when TOTAL is absent.
 */
final class OfferSurfaceKpiBuilder {

  /**
   * Asset types driven by capacity instead of surface. */
  private const CAPACITY_DRIVEN_TYPES = ['COW'];

  /**
   * Land asset types: no divisibility suffix. */
  private const LAND_TYPES = ['TER'];

  /**
   * Minimum lot qualifications for divisible offers (priority order).
   */
  private const MIN_LOT_QUALIFICATIONS = ['MINIM', 'ETREF'];

  /**
   * Primary surface for non-divisible offers (TOTAL, then DISPO).
   */
  private const PRIMARY_SURFACE_QUALIFICATIONS = ['TOTAL', 'DISPO'];

  public function __construct(
    private readonly ConfigFactoryInterface $configFactory,
  ) {}

  /**
   * Builds the KPI fragment (surface or capacity) for an offer entity.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The offer entity.
   * @param iterable|null $surface_items
   *   Optional surface field items (e.g. from a field formatter). When omitted,
   *   values are read from the entity field_surfaces.
   */
  public function buildKpiSummary(EntityInterface $entity, ?iterable $surface_items = NULL): string {
    $parts = $this->buildKpiParts($entity, $surface_items);
    if ($parts['primary'] === '') {
      return '';
    }

    if ($parts['suffix'] !== NULL && $parts['suffix'] !== '') {
      return $parts['primary'] . ' ' . $parts['suffix'];
    }

    return $parts['primary'];
  }

  /**
   * Builds structured surface KPI parts for styled markup.
   *
   * @return array{primary: string, suffix: string|null}
   *   Primary surface label and optional divisibility suffix.
   */
  public function buildKpiParts(EntityInterface $entity, ?iterable $surface_items = NULL): array {
    $asset_type = $entity->hasField('field_asset_type')
      ? strtoupper((string) ($entity->get('field_asset_type')->value ?? ''))
      : '';

    if (in_array($asset_type, self::CAPACITY_DRIVEN_TYPES, TRUE)) {
      $capacity = $this->formatCapacity($entity);

      return [
        'primary' => $capacity,
        'suffix' => NULL,
      ];
    }

    $surfaces = $surface_items !== NULL
      ? $this->indexSurfacesFromIterable($surface_items)
      : $this->indexSurfaces($entity);

    return $this->resolveSurfaceParts($entity, $asset_type, $surfaces);
  }

  /**
   * Builds a render array with primary (#333) and suffix (muted) spans.
   *
   * @param list<string> $wrapper_classes
   *   Additional wrapper classes (e.g. ps-surface-compare).
   *
   * @return array<string, mixed>
   *   Render array, or empty when no surface is available.
   */
  public function buildKpiRenderArray(
    EntityInterface $entity,
    ?iterable $surface_items = NULL,
    array $wrapper_classes = ['ps-surface-kpi'],
  ): array {
    $parts = $this->buildKpiParts($entity, $surface_items);
    if ($parts['primary'] === '') {
      return [];
    }

    return $this->buildPartsRenderArray($parts, $wrapper_classes);
  }

  /**
   * Builds a render array from structured surface KPI parts.
   *
   * @param array{primary: string, suffix: string|null} $parts
   * @param list<string> $wrapper_classes
   *
   * @return array<string, mixed>
   */
  public function buildPartsRenderArray(array $parts, array $wrapper_classes = ['ps-surface-kpi']): array {
    $build = [
      '#type' => 'container',
      '#attributes' => ['class' => $wrapper_classes],
      '#attached' => [
        'library' => ['ps_offer/surface_kpi'],
      ],
      'primary' => [
        '#type' => 'html_tag',
        '#tag' => 'span',
        '#value' => $parts['primary'],
        '#attributes' => ['class' => ['ps-surface-kpi__primary']],
      ],
    ];

    if ($parts['suffix'] !== NULL && $parts['suffix'] !== '') {
      $build['suffix'] = [
        '#type' => 'html_tag',
        '#tag' => 'span',
        '#value' => ' ' . $parts['suffix'],
        '#attributes' => ['class' => ['ps-surface-kpi__suffix']],
      ];
    }

    return $build;
  }

  /**
   * Builds the compare-table surface label.
   *
   * Alias of buildKpiSummary() — same formatting rules site-wide.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The offer entity.
   * @param iterable|null $surface_items
   *   Optional surface field items.
   */
  public function buildCompareKpiSummary(EntityInterface $entity, ?iterable $surface_items = NULL): string {
    return $this->buildKpiSummary($entity, $surface_items);
  }

  /**
   * Formats capacity for coworking offers.
   */
  private function formatCapacity(EntityInterface $entity): string {
    if (!$entity->hasField('field_capacity_total') || $entity->get('field_capacity_total')->isEmpty()) {
      return '';
    }

    $total = (int) $entity->get('field_capacity_total')->value;
    if ($total <= 0) {
      return '';
    }

    $unit = (string) ($this->settings()->get('surface_capacity_unit') ?? 'seats');
    return $this->formatInteger($total) . ' ' . $unit;
  }

  /**
   * Resolves structured surface KPI parts.
   *
   * @param array<string, array{value: float, unit_label: string}> $surfaces
   *
   * @return array{primary: string, suffix: string|null}
   */
  private function resolveSurfaceParts(EntityInterface $entity, string $asset_type, array $surfaces): array {
    if (in_array($asset_type, self::LAND_TYPES, TRUE)) {
      $primary = $this->resolvePrimarySurface($surfaces, ['TOTAL']);
      if ($primary === NULL) {
        return ['primary' => '', 'suffix' => NULL];
      }

      return [
        'primary' => $this->formatSurfaceValue($primary),
        'suffix' => NULL,
      ];
    }

    $isDivisible = $entity->hasField('field_divisible') && (bool) $entity->get('field_divisible')->value;

    if ($isDivisible) {
      $total = $surfaces['TOTAL'] ?? NULL;
      if ($total === NULL || $total['value'] <= 0) {
        $primary = $this->resolvePrimarySurface($surfaces);
        if ($primary === NULL) {
          return ['primary' => '', 'suffix' => NULL];
        }

        return [
          'primary' => $this->formatSurfaceValue($primary),
          'suffix' => NULL,
        ];
      }

      $parts = [
        'primary' => $this->formatSurfaceValue($total),
        'suffix' => NULL,
      ];
      $min = $this->resolveMinimumLot($surfaces);
      if ($min !== NULL && $min['value'] > 0 && $min['value'] < $total['value']) {
        $parts['suffix'] = $this->buildDivisibleSuffixPart($min);
      }

      return $parts;
    }

    $primary = $this->resolvePrimarySurface($surfaces);
    if ($primary === NULL) {
      return ['primary' => '', 'suffix' => NULL];
    }

    return [
      'primary' => $this->formatSurfaceValue($primary),
      'suffix' => NULL,
    ];
  }

  /**
   * Builds the divisibility suffix in parentheses.
   *
   * @param array{value: float, unit_label: string} $min
   */
  private function buildDivisibleSuffixPart(array $min): string {
    $suffix = str_replace(
      '@surface',
      $this->formatSurfaceValue($min),
      (string) ($this->settings()->get('surface_divisible_template') ?? 'Divisible from @surface'),
    );
    if ($suffix === '') {
      return '';
    }

    $first = mb_substr($suffix, 0, 1, 'UTF-8');
    $rest = mb_substr($suffix, 1, NULL, 'UTF-8');

    return '(' . mb_strtolower($first, 'UTF-8') . $rest . ')';
  }

  /**
   * Resolves the primary surface (TOTAL, then DISPO by default).
   *
   * @param array<string, array{value: float, unit_label: string}> $surfaces
   * @param list<string>|null $qualifications
   *
   * @return array{value: float, unit_label: string}|null
   */
  private function resolvePrimarySurface(array $surfaces, ?array $qualifications = NULL): ?array {
    $qualifications ??= self::PRIMARY_SURFACE_QUALIFICATIONS;

    foreach ($qualifications as $qualification) {
      if (!isset($surfaces[$qualification])) {
        continue;
      }
      if ($surfaces[$qualification]['value'] > 0) {
        return $surfaces[$qualification];
      }
    }

    return NULL;
  }

  /**
   * Resolves the minimum divisible lot (MINIM, then ETREF).
   *
   * @param array<string, array{value: float, unit_label: string}> $surfaces
   *
   * @return array{value: float, unit_label: string}|null
   */
  private function resolveMinimumLot(array $surfaces): ?array {
    foreach (self::MIN_LOT_QUALIFICATIONS as $qualification) {
      if (!isset($surfaces[$qualification])) {
        continue;
      }
      if ($surfaces[$qualification]['value'] > 0) {
        return $surfaces[$qualification];
      }
    }

    return NULL;
  }

  /**
   * @param array{value: float, unit_label: string} $surface
   */
  private function formatSurfaceValue(array $surface): string {
    return $this->formatNumber($surface['value']) . ' ' . $surface['unit_label'];
  }

  /**
   * Indexes surface field items from the entity.
   *
   * @return array<string, array{value: float, unit_label: string}>
   *   Surface values keyed by qualification code.
   */
  private function indexSurfaces(EntityInterface $entity): array {
    if (!$entity->hasField('field_surfaces') || $entity->get('field_surfaces')->isEmpty()) {
      return [];
    }

    return $this->indexSurfacesFromIterable($entity->get('field_surfaces'));
  }

  /**
   * Indexes surface field items by qualification.
   *
   * @return array<string, array{value: float, unit_label: string}>
   *   Surface values keyed by qualification code.
   */
  private function indexSurfacesFromIterable(iterable $items): array {
    $indexed = [];

    foreach ($items as $item) {
      $qualification = strtoupper((string) ($item->qualification ?? ''));
      if ($qualification === '') {
        continue;
      }

      $raw = $item->value ?? NULL;
      if ($raw === NULL || (float) $raw <= 0) {
        continue;
      }

      $indexed[$qualification] = [
        'value' => (float) $raw,
        'unit_label' => $this->unitLabel((string) ($item->unit_code ?? 'M2')),
      ];
    }

    return $indexed;
  }

  /**
   * Resolves a display unit label from a unit code.
   */
  private function unitLabel(string $unit_code): string {
    return strtolower($unit_code) === 'ha' ? 'ha' : 'm²';
  }

  /**
   * Formats a decimal surface value (French grouping).
   */
  private function formatNumber(float $value): string {
    $decimals = fmod($value, 1.0) === 0.0 ? 0 : 2;
    return number_format($value, $decimals, ',', ' ');
  }

  /**
   * Formats an integer capacity value.
   */
  private function formatInteger(int $value): string {
    return number_format($value, 0, ',', ' ');
  }

  /**
   * Loads language-aware offer display settings.
   */
  private function settings(): ImmutableConfig {
    return $this->configFactory->get('ps_offer.settings');
  }

}
