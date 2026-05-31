<?php

declare(strict_types=1);

namespace Drupal\ps_surface\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\node\NodeInterface;

/**
 * Contextual surface formatter driven by the offer context matrix (DEC-0022).
 *
 * Reads field_asset_type and field_divisible from the parent offer node to
 * select the appropriate display variant:
 *
 *  - COW                  → empty (capacity-driven offer, no surface display)
 *  - TER                  → "{TOTAL} {unit}" using the item's unit_code
 *  - Others + divisible   → "{TOTAL} {unit} · Available: {DISPO} {unit}"
 *  - Others + indivisible → "{TOTAL} {unit}"
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

  /** Asset types whose offers are capacity-driven (no m² display). */
  private const CAPACITY_DRIVEN_TYPES = ['COW'];

  /** Asset types that use land-area logic (TER = terrain). */
  private const LAND_TYPES = ['TER'];

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode): array {
    $entity = $items->getEntity();

    // Only process offer nodes.
    if (!$entity instanceof NodeInterface || $entity->bundle() !== 'offer') {
      return $this->renderRaw($items);
    }

    $asset_type = $entity->hasField('field_asset_type')
      ? (string) ($entity->get('field_asset_type')->value ?? '')
      : '';

    // Capacity-driven offers: surface display is suppressed by ps_context.
    // Return empty to avoid redundant rendering.
    if (in_array($asset_type, self::CAPACITY_DRIVEN_TYPES, TRUE)) {
      return [];
    }

    $divisible = $entity->hasField('field_divisible')
      && (bool) $entity->get('field_divisible')->value;

    // Index items by qualification.
    $by_qual = [];
    foreach ($items as $item) {
      $qual = (string) ($item->qualification ?? '');
      if ($qual !== '') {
        $by_qual[$qual] = $item;
      }
    }

    $total = $by_qual['TOTAL'] ?? NULL;
    if ($total === NULL || $total->value === NULL) {
      return [];
    }

    $unit_code = strtolower((string) ($total->unit_code ?? 'M2'));
    $unit_label = $unit_code === 'ha' ? 'ha' : 'm²';

    $is_land = in_array($asset_type, self::LAND_TYPES, TRUE);

    if ($is_land || !$divisible) {
      $text = $this->formatValue((float) $total->value) . "\u{00A0}" . $unit_label;
    }
    else {
      $dispo = $by_qual['DISPO'] ?? NULL;
      $text = $this->formatValue((float) $total->value) . "\u{00A0}" . $unit_label;

      if ($dispo !== NULL && $dispo->value !== NULL && (float) $dispo->value > 0) {
        $text .= ' · ' . $this->t(
          'Available: @dispo @unit',
          ['@dispo' => $this->formatValue((float) $dispo->value), '@unit' => $unit_label],
        );
      }
    }

    return [['#markup' => $text]];
  }

  /**
   * Fallback renderer used on non-offer entities (e.g. surface_division).
   *
   * @return array<int, array<string, mixed>>
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
      $text = ($qual !== '' ? $qual . ': ' : '') . $this->formatValue((float) $item->value) . "\u{00A0}" . $unit_label;
      $elements[$delta] = ['#plain_text' => $text];
    }
    return $elements;
  }

  /**
   * Formats a numeric surface value with a locale-appropriate separator.
   */
  private function formatValue(float $value): string {
    return number_format($value, $value == floor($value) ? 0 : 2, '.', "\u{202F}");
  }

}
