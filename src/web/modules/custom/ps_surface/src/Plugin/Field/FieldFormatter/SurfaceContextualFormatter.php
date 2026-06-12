<?php

declare(strict_types=1);

namespace Drupal\ps_surface\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\node\NodeInterface;

/**
 * Contextual surface formatter driven by the offer context matrix (DEC-0022).
 *
 * Delegates to ps_offer.surface_kpi_builder when available (bnppre.fr rules):
 *
 *  - COW                  → empty on the surface field (capacity shown elsewhere)
 *  - TER                  → "{TOTAL} {unit}" only
 *  - Others + divisible   → "{TOTAL} {unit} ({template @surface})"
 *    when MINIM or ETREF is strictly below TOTAL
 *  - Others + indivisible → "{TOTAL} {unit}" or "{DISPO} {unit}"
 *
 * @FieldFormatter(
 *   id = "ps_surface_contextual",
 *   label = @Translation("PS surface — Contextual (matrix-driven)"),
 *   field_types = {
 *     "ps_surface_item"
 *   }
 * )
 */
final class SurfaceContextualFormatter extends FormatterBase {

  /**
   * Asset types whose offers are capacity-driven (no m² on surface field).
   */
  private const CAPACITY_DRIVEN_TYPES = ['COW'];

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode): array {
    $entity = $items->getEntity();

    if (!$entity instanceof NodeInterface || $entity->bundle() !== 'offer') {
      return $this->renderRaw($items);
    }

    $asset_type = $entity->hasField('field_asset_type')
      ? strtoupper((string) ($entity->get('field_asset_type')->value ?? ''))
      : '';

    if (in_array($asset_type, self::CAPACITY_DRIVEN_TYPES, TRUE)) {
      return [];
    }

    // Optional cross-module service: ps_surface must not hard-depend on ps_offer.
    // phpcs:disable DrupalPractice.Objects.GlobalClass
    if (\Drupal::hasService('ps_offer.surface_kpi_builder')) {
      $markup = \Drupal::service('ps_offer.surface_kpi_builder')->buildKpiRenderArray($entity, $items);
      if ($markup === []) {
        return [];
      }

      return [0 => $markup];
    }
    // phpcs:enable DrupalPractice.Objects.GlobalClass

    return $this->renderFallback($items, $entity, $asset_type);
  }

  /**
   * Fallback when ps_offer is not enabled.
   *
   * @return array<int, array<string, mixed>>
   *   Render array elements.
   */
  private function renderFallback(FieldItemListInterface $items, NodeInterface $entity, string $asset_type): array {
    $by_qual = [];
    foreach ($items as $item) {
      $qual = strtoupper((string) ($item->qualification ?? ''));
      if ($qual !== '') {
        $by_qual[$qual] = $item;
      }
    }

    $is_land = $asset_type === 'TER';
    $divisible = $entity->hasField('field_divisible') && (bool) $entity->get('field_divisible')->value;

    $primary = NULL;
    if ($is_land) {
      $primary = $by_qual['TOTAL'] ?? NULL;
    }
    elseif ($divisible) {
      $primary = $by_qual['TOTAL'] ?? NULL;
    }
    else {
      $primary = $by_qual['TOTAL'] ?? $by_qual['DISPO'] ?? NULL;
    }

    if ($primary === NULL || $primary->value === NULL || (float) $primary->value <= 0) {
      return [];
    }

    $unit_label = strtolower((string) ($primary->unit_code ?? 'M2')) === 'ha' ? 'ha' : 'm²';
    $text = $this->formatValue((float) $primary->value) . ' ' . $unit_label;

    if (!$is_land && $divisible) {
      foreach (['MINIM', 'ETREF'] as $qualification) {
        $min = $by_qual[$qualification] ?? NULL;
        if ($min !== NULL && $min->value !== NULL && (float) $min->value > 0 && (float) $min->value < (float) $primary->value) {
          $min_label = $this->formatValue((float) $min->value) . ' ' . $unit_label;
          $suffix = str_replace(
            '@surface',
            $min_label,
            (string) (\Drupal::config('ps_offer.settings')->get('surface_divisible_template') ?? 'Divisible from @surface'),
          );
          $first = mb_substr($suffix, 0, 1, 'UTF-8');
          $rest = mb_substr($suffix, 1, NULL, 'UTF-8');
          $text .= ' (' . mb_strtolower($first, 'UTF-8') . $rest . ')';
          break;
        }
      }
    }

    return [['#markup' => $text]];
  }

  /**
   * Fallback renderer used on non-offer entities (e.g. surface_division).
   *
   * @return array<int, array<string, mixed>>
   *   Render array elements.
   */
  private function renderRaw(FieldItemListInterface $items): array {
    $elements = [];
    foreach ($items as $delta => $item) {
      if ($item->value === NULL) {
        continue;
      }
      $qual = (string) ($item->qualification ?? '');
      $unit = strtolower((string) ($item->unit_code ?? 'M2'));
      $unit_label = $unit === 'ha' ? 'ha' : 'm²';
      $text = ($qual !== '' ? $qual . ': ' : '') . $this->formatValue((float) $item->value) . ' ' . $unit_label;
      $elements[$delta] = ['#plain_text' => $text];
    }
    return $elements;
  }

  /**
   * Formats a numeric surface value with a locale-appropriate separator.
   */
  private function formatValue(float $value): string {
    $decimals = fmod($value, 1.0) === 0.0 ? 0 : 2;
    return number_format($value, $decimals, ',', ' ');
  }

}
