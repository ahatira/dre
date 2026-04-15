<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Service;

use Drupal\node\NodeInterface;

/**
 * Resolves stable surface/division values for offer indexing.
 */
final class OfferSurfaceDivisionSearchValueResolver {

  /**
   * Resolves derived values used by Search API.
   *
   * @return array{main_surface_value:?float,main_surface_unit:?string,total_surface_divisions:?float,surface_consistency_status:string}
   *   Derived values for indexing.
   */
  public function resolve(NodeInterface $node): array {
    [$mainValue, $mainUnit] = $this->resolveMainSurface($node);
    $totalDivisions = $this->resolveDivisionSurfaceTotalM2($node);

    return [
      'main_surface_value' => $mainValue,
      'main_surface_unit' => $mainUnit,
      'total_surface_divisions' => $totalDivisions,
      'surface_consistency_status' => $this->resolveConsistencyStatus($mainValue, $mainUnit, $totalDivisions),
    ];
  }

  /**
   * Picks the main offer surface.
   *
   * Current rule: first positive M2 value from field_surfaces.
   *
   * @return array{0:?float,1:?string}
   *   Main surface value and unit.
   */
  private function resolveMainSurface(NodeInterface $node): array {
    if (!$node->hasField('field_surfaces')) {
      return [NULL, NULL];
    }

    foreach ($node->get('field_surfaces') as $surface) {
      $value = $surface->get('value')->getValue();
      $unit = strtoupper((string) ($surface->get('unit')->getValue() ?? ''));

      if (!is_numeric($value) || (float) $value <= 0.0) {
        continue;
      }

      if ($unit === '' || $unit === 'M2') {
        return [(float) $value, 'M2'];
      }
    }

    return [NULL, NULL];
  }

  /**
   * Sums division surfaces in M2 only.
   */
  private function resolveDivisionSurfaceTotalM2(NodeInterface $node): ?float {
    if (!$node->hasField('field_divisions')) {
      return NULL;
    }

    $total = 0.0;
    $hasAnyValid = FALSE;

    foreach ($node->get('field_divisions') as $divisionReference) {
      $division = $divisionReference->entity;
      if (!$division || !$division->hasField('surfaces')) {
        continue;
      }

      foreach ($division->get('surfaces') as $surface) {
        $value = $surface->get('value')->getValue();
        $unit = strtoupper((string) ($surface->get('unit')->getValue() ?? ''));

        if (!is_numeric($value) || (float) $value <= 0.0) {
          continue;
        }

        // Restrict to M2 to avoid unsafe cross-unit additions.
        if ($unit !== '' && $unit !== 'M2') {
          continue;
        }

        $total += (float) $value;
        $hasAnyValid = TRUE;
      }
    }

    return $hasAnyValid ? $total : NULL;
  }

  /**
   * Builds consistency status between global and division surfaces.
   */
  private function resolveConsistencyStatus(?float $mainValue, ?string $mainUnit, ?float $divisionsTotal): string {
    if ($mainValue === NULL && $divisionsTotal === NULL) {
      return 'unknown';
    }

    if ($mainValue === NULL || $divisionsTotal === NULL) {
      return 'warning';
    }

    if ($mainUnit !== 'M2') {
      return 'warning';
    }

    if ($mainValue <= 0.0) {
      return 'warning';
    }

    $deltaRatio = abs($mainValue - $divisionsTotal) / $mainValue;
    if ($deltaRatio <= 0.05) {
      return 'ok';
    }
    if ($deltaRatio <= 0.20) {
      return 'warning';
    }

    return 'mismatch';
  }

}
