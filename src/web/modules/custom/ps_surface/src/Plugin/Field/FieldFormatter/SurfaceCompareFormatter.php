<?php

declare(strict_types=1);

namespace Drupal\ps_surface\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\node\NodeInterface;
use Drupal\ui_suite_bnp\Utility\Bootstrap;

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
      /** @var \Drupal\ps_offer\Service\OfferSurfaceKpiBuilder $builder */
      $builder = \Drupal::service('ps_offer.surface_kpi_builder');
      $parts = $builder->buildKpiParts($entity, $items);
      if ($parts['primary'] === '') {
        return [];
      }

      return [
        0 => $this->buildCompareSurfaceCell($builder->buildPartsRenderArray($parts, ['ps-surface-kpi', 'ps-surface-compare', 'fw-semibold'])),
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

    $markup = $this->buildFallbackSurfaceMarkup($by_qual, $entity, $asset_type);
    if ($markup === []) {
      return [];
    }

    return [
      0 => $this->buildCompareSurfaceCell($markup),
    ];
  }

  /**
   * @param array<string, \Drupal\Core\Field\FieldItemInterface> $by_qual
   *
   * @return array<string, mixed>
   */
  private function buildFallbackSurfaceMarkup(array $by_qual, NodeInterface $entity, string $asset_type): array {
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

    $parts = ['primary' => '', 'suffix' => NULL];
    if ($primary === NULL || $primary->value === NULL || (float) $primary->value <= 0) {
      return [];
    }

    $unit_label = strtolower((string) ($primary->unit_code ?? 'M2')) === 'ha' ? 'ha' : 'm²';
    $parts['primary'] = $this->formatValue((float) $primary->value) . ' ' . $unit_label;

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
            $parts['suffix'] = '(' . mb_strtolower($first, 'UTF-8') . $rest . ')';
            break;
          }
        }
      }

    return [
      '#type' => 'container',
      '#attributes' => ['class' => ['ps-surface-kpi', 'ps-surface-compare', 'fw-semibold']],
      '#attached' => ['library' => ['ps_offer/surface_kpi']],
      'primary' => [
        '#type' => 'html_tag',
        '#tag' => 'span',
        '#value' => $parts['primary'],
        '#attributes' => ['class' => ['ps-surface-kpi__primary']],
      ],
    ] + ($parts['suffix'] ? [
      'suffix' => [
        '#type' => 'html_tag',
        '#tag' => 'span',
        '#value' => ' ' . $parts['suffix'],
        '#attributes' => ['class' => ['ps-surface-kpi__suffix']],
      ],
    ] : []);
  }

  /**
   * @param array<string, mixed> $text
   */
  private function buildCompareSurfaceCell(array $text): array {
    return [
      '#type' => 'container',
      '#attributes' => ['class' => ['ps-surface-compare-wrap']],
      'icon' => Bootstrap::icon('floors', 'bnp_custom', ['size' => '16px']),
      'text' => $text,
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
