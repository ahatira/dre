<?php

declare(strict_types=1);

namespace Drupal\ps_surface\Hook;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\ps_surface\Entity\SurfaceDivisionInterface;
use Drupal\ps_surface\Service\SurfaceProjectionManagerInterface;

final class SurfaceDivisionHooks {

  public function __construct(
    private readonly SurfaceProjectionManagerInterface $surfaceProjectionManager,
  ) {}

  #[Hook('entity_presave')]
  public function entityPresave(EntityInterface $entity): void {
    if (!$entity instanceof SurfaceDivisionInterface) {
      return;
    }

    $this->validateSurfaceCoherence($entity);
  }

  #[Hook('entity_insert')]
  public function entityInsert(EntityInterface $entity): void {
    $this->rebuildOfferProjection($entity);
  }

  #[Hook('entity_update')]
  public function entityUpdate(EntityInterface $entity): void {
    $this->rebuildOfferProjection($entity);
  }

  #[Hook('entity_delete')]
  public function entityDelete(EntityInterface $entity): void {
    $this->rebuildOfferProjection($entity);
  }

  /**
   * Automatically rebuild surface projections for all offers referencing this division.
   */
  private function rebuildOfferProjection(EntityInterface $entity): void {
    if (!$entity instanceof SurfaceDivisionInterface) {
      return;
    }

    // Find all offers that reference this division via field_divisions.
    $storage = \Drupal::entityTypeManager()->getStorage('node');
    $query = $storage->getQuery()
      ->condition('type', 'offer')
      ->condition('field_divisions', $entity->id())
      ->accessCheck(FALSE);
    
    $offer_ids = $query->execute();
    
    if (empty($offer_ids)) {
      return;
    }

    // Rebuild projection for each affected offer.
    foreach ($offer_ids as $offer_id) {
      $this->surfaceProjectionManager->rebuildForOffer((int) $offer_id);
    }
  }

  /**
   * Validates surface coherence rules for a division.
   *
   * Rules:
   * - ETREF (reference surface) must be ≤ TOTAL surface
   * - DISPO (available surface) must be ≤ TOTAL surface
   * - All surface values must be ≥ 0
   */
  private function validateSurfaceCoherence(SurfaceDivisionInterface $entity): void {
    if (!$entity->hasField('surfaces')) {
      return;
    }

    $surfaces = [];
    foreach ($entity->get('surfaces')->getValue() as $row) {
      $qual = $row['qualification'] ?? '';
      $value = isset($row['value']) ? (float) $row['value'] : NULL;
      
      if ($qual !== '' && $value !== NULL) {
        $surfaces[$qual] = $value;
        
        // Validate: all values must be >= 0
        if ($value < 0) {
          \Drupal::messenger()->addWarning(t(
            'Surface @qual has negative value (@value). Negative surfaces are not allowed.',
            ['@qual' => $qual, '@value' => $value]
          ));
        }
      }
    }

    // Validate: ETREF <= TOTAL
    if (isset($surfaces['ETREF']) && isset($surfaces['TOTAL'])) {
      if ($surfaces['ETREF'] > $surfaces['TOTAL']) {
        \Drupal::messenger()->addWarning(t(
          'ETREF surface (@etref m²) exceeds TOTAL surface (@total m²). ETREF should be less than or equal to TOTAL.',
          ['@etref' => $surfaces['ETREF'], '@total' => $surfaces['TOTAL']]
        ));
      }
    }

    // Validate: DISPO <= TOTAL
    if (isset($surfaces['DISPO']) && isset($surfaces['TOTAL'])) {
      if ($surfaces['DISPO'] > $surfaces['TOTAL']) {
        \Drupal::messenger()->addWarning(t(
          'DISPO surface (@dispo m²) exceeds TOTAL surface (@total m²). Available surface cannot be greater than total.',
          ['@dispo' => $surfaces['DISPO'], '@total' => $surfaces['TOTAL']]
        ));
      }
    }
  }

}
