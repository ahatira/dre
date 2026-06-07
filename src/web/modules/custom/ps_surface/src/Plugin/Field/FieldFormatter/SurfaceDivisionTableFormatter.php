<?php

declare(strict_types=1);

namespace Drupal\ps_surface\Plugin\Field\FieldFormatter;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldFormatter\EntityReferenceFormatterBase;
use Drupal\ps_surface\Entity\SurfaceDivision;

/**
 * Renders surface divisions as a data table.
 *
 * @FieldFormatter(
 *   id = "ps_surface_division_table",
 *   label = @Translation("Surface division table"),
 *   field_types = {
 *     "entity_reference"
 *   }
 * )
 */
final class SurfaceDivisionTableFormatter extends EntityReferenceFormatterBase {

  /**
   * Asset types where surface divisions are not displayed on the offer page.
   */
  private const CAPACITY_DRIVEN_ASSET_TYPES = ['COW'];

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode): array {
    if ($this->shouldHideForParentEntity($items->getEntity())) {
      return [];
    }

    $entities = $this->getEntitiesToView($items, $langcode);
    if ($entities === []) {
      return [];
    }

    $header = [
      $this->t('Lot'),
      $this->t('Floor / nature'),
      $this->t('Surface'),
      $this->t('Availability'),
    ];

    $rows = [];
    foreach ($entities as $entity) {
      if (!$entity instanceof SurfaceDivision) {
        continue;
      }
      $rows[] = [
        $entity->getDivisionReference(),
        $entity->label(),
        $this->formatSurfaceValue($entity),
        $this->formatAvailability($entity),
      ];
    }

    if ($rows === []) {
      return [];
    }

    return [
      0 => [
        '#type' => 'container',
        '#attributes' => [
          'class' => ['ps-offer-section', 'ps-offer-section--surface-table'],
        ],
        'title' => [
          '#type' => 'html_tag',
          '#tag' => 'h2',
          '#value' => $this->t('Surface table'),
          '#attributes' => ['class' => ['ps-offer-section__title']],
        ],
        'table_wrapper' => [
          '#type' => 'container',
          '#attributes' => ['class' => ['ps-surface-division-table__wrapper']],
          'table' => [
            '#type' => 'table',
            '#header' => $header,
            '#rows' => $rows,
            '#attributes' => [
              'class' => ['ps-surface-division-table', 'table'],
              'id' => 'ps-surface-table',
            ],
          ],
        ],
      ],
    ];
  }

  /**
   * Whether the parent offer should not expose the surface table.
   */
  private function shouldHideForParentEntity(EntityInterface $entity): bool {
    if (!$entity->hasField('field_asset_type') || $entity->get('field_asset_type')->isEmpty()) {
      return FALSE;
    }

    return in_array((string) $entity->get('field_asset_type')->value, self::CAPACITY_DRIVEN_ASSET_TYPES, TRUE);
  }

  /**
   * Formats the primary surface value for a division row.
   */
  private function formatSurfaceValue(SurfaceDivision $division): string {
    if (!$division->hasField('surfaces') || $division->get('surfaces')->isEmpty()) {
      return '';
    }

    $fallback = '';
    foreach ($division->get('surfaces') as $item) {
      $qualification = (string) ($item->qualification ?? '');
      $value = $item->value ?? NULL;
      if ($value === NULL || $value === '') {
        continue;
      }

      $formatted = $this->formatSurfaceAmount((float) $value, (string) ($item->unit_code ?? 'M2'));
      if ($qualification === 'DISPO' || $qualification === '') {
        return $formatted;
      }
      if ($fallback === '' && ($qualification === 'TOTAL' || $qualification === 'ETREF')) {
        $fallback = $formatted;
      }
    }

    return $fallback;
  }

  /**
   * Formats a numeric surface amount with its unit.
   */
  private function formatSurfaceAmount(float $value, string $unit_code): string {
    $unit = strtolower($unit_code) === 'ha' ? 'ha' : 'm²';
    return number_format($value, 1, ',', ' ') . ' ' . $unit;
  }

  /**
   * Formats division availability for display.
   */
  private function formatAvailability(SurfaceDivision $division): string {
    if (!$division->get('availability_text')->isEmpty()) {
      return (string) $division->get('availability_text')->value;
    }

    if (!$division->get('division_status')->isEmpty()) {
      $value = (string) $division->get('division_status')->value;
      return match ($value) {
        'AVAILABLE' => (string) $this->t('Available'),
        'PARTIAL' => (string) $this->t('Partial'),
        'UNAVAILABLE' => (string) $this->t('Unavailable'),
        default => (string) $this->t('Unknown'),
      };
    }

    return '';
  }

}
