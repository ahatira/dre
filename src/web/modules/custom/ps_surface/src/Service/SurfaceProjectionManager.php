<?php

declare(strict_types=1);

namespace Drupal\ps_surface\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\node\NodeInterface;

final class SurfaceProjectionManager implements SurfaceProjectionManagerInterface {

  private const QUALIFICATIONS = ['TOTAL', 'DISPO', 'ETREF'];

  public function __construct(
    private readonly EntityTypeManagerInterface $entityTypeManager,
  ) {}

  public function rebuildForOffer(int $offer_id): void {
    if ($offer_id <= 0) {
      return;
    }

    $offer_storage = $this->entityTypeManager->getStorage('node');
    $offer = $offer_storage->load($offer_id);
    if (!$offer instanceof NodeInterface || $offer->bundle() !== 'offer') {
      return;
    }

    $projectedTotals = [
      'TOTAL' => 0.0,
      'DISPO' => 0.0,
      'ETREF' => 0.0,
    ];

    $hasDivisionSurfaceData = FALSE;

    // Load divisions from the offer field_divisions reference field.
    if ($offer->hasField('field_divisions')) {
      $divisions = $offer->get('field_divisions')->referencedEntities();
      foreach ($divisions as $division) {
        if (!$division->hasField('surfaces')) {
          continue;
        }
        foreach ($division->get('surfaces')->getValue() as $surface_row) {
          $qualification = mb_strtoupper((string) ($surface_row['qualification'] ?? ''));
          if (!array_key_exists($qualification, $projectedTotals)) {
            continue;
          }
          $raw_value = $surface_row['value'] ?? NULL;
          if ($raw_value === NULL || $raw_value === '') {
            continue;
          }
          $projectedTotals[$qualification] += (float) $raw_value;
          $hasDivisionSurfaceData = TRUE;
        }
      }
    }

    if (!$offer->hasField('field_surfaces')) {
      return;
    }

    $currentByQualification = [];
    $extraRows = [];
    foreach ($offer->get('field_surfaces')->getValue() as $row) {
      $qualification = (string) ($row['qualification'] ?? '');
      if (!in_array($qualification, self::QUALIFICATIONS, TRUE)) {
        if ($qualification !== '') {
          $extraRows[] = [
            'qualification' => $qualification,
            'value' => isset($row['value']) && $row['value'] !== '' ? round((float) $row['value'], 2) : 0.0,
            'unit_code' => (string) ($row['unit_code'] ?? 'M2'),
          ];
        }
        continue;
      }
      $currentByQualification[$qualification] = [
        'value' => isset($row['value']) && $row['value'] !== '' ? (float) $row['value'] : 0.0,
        'unit_code' => (string) ($row['unit_code'] ?? 'M2'),
      ];
    }

    // Business contract:
    // - TOTAL and ETREF remain sourced from offer-level imported surfaces.
    // - DISPO may be projected from divisions when division data is present.
    $resolvedValues = [
      'TOTAL' => $currentByQualification['TOTAL']['value'] ?? 0.0,
      'DISPO' => $currentByQualification['DISPO']['value'] ?? 0.0,
      'ETREF' => $currentByQualification['ETREF']['value'] ?? 0.0,
    ];

    if ($hasDivisionSurfaceData && $projectedTotals['DISPO'] > 0.0) {
      $resolvedValues['DISPO'] = $projectedTotals['DISPO'];
    }

    $resolvedUnits = [
      'TOTAL' => $currentByQualification['TOTAL']['unit_code'] ?? 'M2',
      'DISPO' => $currentByQualification['DISPO']['unit_code'] ?? 'M2',
      'ETREF' => $currentByQualification['ETREF']['unit_code'] ?? 'M2',
    ];

    $new_surfaces = [
      ['qualification' => 'TOTAL', 'value' => round($resolvedValues['TOTAL'], 2), 'unit_code' => $resolvedUnits['TOTAL']],
      ['qualification' => 'DISPO', 'value' => round($resolvedValues['DISPO'], 2), 'unit_code' => $resolvedUnits['DISPO']],
      ['qualification' => 'ETREF', 'value' => round($resolvedValues['ETREF'], 2), 'unit_code' => $resolvedUnits['ETREF']],
    ];

    foreach ($extraRows as $extraRow) {
      $new_surfaces[] = $extraRow;
    }

    $current = array_map(
      static fn(array $item): array => [
        'qualification' => $item['qualification'] ?? '',
        'value' => round((float) ($item['value'] ?? 0), 2),
        'unit_code' => $item['unit_code'] ?? '',
      ],
      $offer->get('field_surfaces')->getValue(),
    );

    if ($current === $new_surfaces) {
      return;
    }

    $offer->set('field_surfaces', $new_surfaces);
    $offer->save();
  }

}
