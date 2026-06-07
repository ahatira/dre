<?php

declare(strict_types=1);

namespace Drupal\ps_surface\Plugin\Field\FieldFormatter;

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
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode): array {
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
        '#type' => 'table',
        '#header' => $header,
        '#rows' => $rows,
        '#attributes' => [
          'class' => ['ps-surface-division-table', 'table'],
          'id' => 'ps-surface-table',
        ],
        '#prefix' => '<div class="ps-offer-section ps-offer-section--surface-table"><h2 class="ps-offer-section__title">' . $this->t('Surface table') . '</h2>',
        '#suffix' => '</div>',
      ],
    ];
  }

  private function formatSurfaceValue(SurfaceDivision $division): string {
    if (!$division->hasField('surfaces') || $division->get('surfaces')->isEmpty()) {
      return '';
    }

    foreach ($division->get('surfaces') as $item) {
      $qualification = (string) ($item->qualification ?? '');
      if ($qualification !== '' && $qualification !== 'DISPO') {
        continue;
      }
      $value = $item->value ?? NULL;
      if ($value === NULL || $value === '') {
        continue;
      }
      $unit = strtolower((string) ($item->unit_code ?? 'M2')) === 'ha' ? 'ha' : 'm²';
      return number_format((float) $value, 1, ',', ' ') . ' ' . $unit;
    }

    return '';
  }

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
