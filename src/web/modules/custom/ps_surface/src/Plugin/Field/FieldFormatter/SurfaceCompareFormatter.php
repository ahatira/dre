<?php

declare(strict_types=1);

namespace Drupal\ps_surface\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\node\NodeInterface;

/**
 * Compact surface formatter for comparison tables.
 *
 * @FieldFormatter(
 *   id = "ps_surface_compare",
 *   label = @Translation("PS surface — Compare table"),
 *   field_types = {
 *     "ps_surface_item"
 *   }
 * )
 */
final class SurfaceCompareFormatter extends FormatterBase {

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

    // phpcs:disable DrupalPractice.Objects.GlobalClass
    if (\Drupal::hasService('ps_offer.surface_kpi_builder')) {
      $text = \Drupal::service('ps_offer.surface_kpi_builder')->buildKpiSummary($entity, $items);
      if ($text === '') {
        return [];
      }

      return [
        0 => [
          '#type' => 'html_tag',
          '#tag' => 'span',
          '#value' => $text,
          '#attributes' => ['class' => ['ps-surface-compare', 'fw-semibold']],
        ],
      ];
    }
    // phpcs:enable DrupalPractice.Objects.GlobalClass

    return $this->renderFallback($items, $entity, $asset_type);
  }

  /**
   * @return array<int, array<string, mixed>>
   */
  private function renderFallback(FieldItemListInterface $items, NodeInterface $entity, string $asset_type): array {
    $by_qual = [];
    foreach ($items as $item) {
      $qual = strtoupper((string) ($item->qualification ?? ''));
      if ($qual !== '') {
        $by_qual[$qual] = $item;
      }
    }

    $total = $by_qual['TOTAL'] ?? NULL;
    if ($total === NULL || $total->value === NULL) {
      return [];
    }

    $unit_label = strtolower((string) ($total->unit_code ?? 'M2')) === 'ha' ? 'ha' : 'm²';
    $text = $this->formatValue((float) $total->value) . ' ' . $unit_label;

    $is_land = $asset_type === 'TER';
    $divisible = $entity->hasField('field_divisible') && (bool) $entity->get('field_divisible')->value;

    if (!$is_land && $divisible) {
      foreach (['MINIM', 'DISPO'] as $qualification) {
        $min = $by_qual[$qualification] ?? NULL;
        if ($min !== NULL && $min->value !== NULL && (float) $min->value > 0 && (float) $min->value < (float) $total->value) {
          $text .= ' · ' . $this->t(
            'from @surface',
            ['@surface' => $this->formatValue((float) $min->value) . ' ' . $unit_label],
          );
          break;
        }
      }
    }

    return [
      0 => [
        '#type' => 'html_tag',
        '#tag' => 'span',
        '#value' => $text,
        '#attributes' => ['class' => ['ps-surface-compare', 'fw-semibold']],
      ],
    ];
  }

  /**
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
      $text = ($qual !== '' ? $qual . ': ' : '') . $this->formatValue((float) $item->value) . ' ' . $unit_label;
      $elements[$delta] = [
        '#type' => 'html_tag',
        '#tag' => 'span',
        '#value' => $text,
        '#attributes' => ['class' => ['ps-surface-compare']],
      ];
    }
    return $elements;
  }

  private function formatValue(float $value): string {
    $decimals = fmod($value, 1.0) === 0.0 ? 0 : 2;
    return number_format($value, $decimals, ',', ' ');
  }

}
