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
 * - Other types → "{TOTAL} m²" and, when divisible with a minimum lot
 *   strictly below total, "{template @surface}" (e.g. "Divisible dès 68 m²").
 */
final class OfferSurfaceKpiBuilder {

  /**
   * Asset types driven by capacity instead of surface. */
  private const CAPACITY_DRIVEN_TYPES = ['COW'];

  /**
   * Land asset types: no divisibility suffix. */
  private const LAND_TYPES = ['TER'];

  /**
   * Surface qualifications used as minimum divisible lot (priority order). */
  private const MIN_LOT_QUALIFICATIONS = ['MINIM', 'DISPO'];

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
    $asset_type = $entity->hasField('field_asset_type')
      ? strtoupper((string) ($entity->get('field_asset_type')->value ?? ''))
      : '';

    if (in_array($asset_type, self::CAPACITY_DRIVEN_TYPES, TRUE)) {
      return $this->formatCapacity($entity);
    }

    $surfaces = $surface_items !== NULL
      ? $this->indexSurfacesFromIterable($surface_items)
      : $this->indexSurfaces($entity);

    return $this->formatSurfaceSummary($entity, $asset_type, $surfaces);
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
   * Formats surface total with optional divisibility suffix.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The offer entity.
   * @param string $asset_type
   *   Uppercase asset type code.
   * @param array<string, array{value: float, unit_label: string}> $surfaces
   *   Surface values keyed by qualification code.
   */
  private function formatSurfaceSummary(EntityInterface $entity, string $asset_type, array $surfaces): string {
    $total = $surfaces['TOTAL'] ?? NULL;
    if ($total === NULL || $total['value'] <= 0) {
      return '';
    }

    $unit_label = $total['unit_label'];
    $text = $this->formatNumber($total['value']) . ' ' . $unit_label;

    if ($this->shouldShowDivisibleSuffix($entity, $asset_type, $total['value'], $surfaces)) {
      $min = $this->resolveMinimumLot($surfaces);
      if ($min !== NULL) {
        $suffix = str_replace(
          '@surface',
          $this->formatNumber($min['value']) . ' ' . $min['unit_label'],
          (string) ($this->settings()->get('surface_divisible_template') ?? 'Divisible from @surface'),
        );
        $separator = (string) ($this->settings()->get('surface_kpi_separator') ?? ' · ');
        $text .= $separator . $suffix;
      }
    }

    return $text;
  }

  /**
   * Whether the divisibility suffix should be appended.
   */
  private function shouldShowDivisibleSuffix(
    EntityInterface $entity,
    string $asset_type,
    float $total,
    array $surfaces,
  ): bool {
    if (in_array($asset_type, self::LAND_TYPES, TRUE)) {
      return FALSE;
    }

    if (!$entity->hasField('field_divisible') || !(bool) $entity->get('field_divisible')->value) {
      return FALSE;
    }

    $min = $this->resolveMinimumLot($surfaces);
    if ($min === NULL) {
      return FALSE;
    }

    return $min['value'] > 0 && $min['value'] < $total;
  }

  /**
   * Resolves the minimum divisible lot from MINIM or DISPO qualifications.
   *
   * @param array<string, array{value: float, unit_label: string}> $surfaces
   *   Indexed surface values.
   *
   * @return array{value: float, unit_label: string}|null
   *   Minimum lot, or NULL when unavailable.
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
