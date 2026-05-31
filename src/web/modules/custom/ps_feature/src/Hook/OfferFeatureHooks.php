<?php

declare(strict_types=1);

namespace Drupal\ps_feature\Hook;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityStorageException;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\ps_feature\Service\FeatureTypeManager;

/**
 * Entity presave hook for OfferFeature entities.
 */
final class OfferFeatureHooks {

  public function __construct(
    private readonly FeatureTypeManager $featureTypeManager,
  ) {}

  /**
   * Implements hook_entity_presave().
   */
  #[Hook('entity_presave')]
  public function entityPresave(EntityInterface $entity): void {
    if ($entity->getEntityTypeId() !== 'entity_offer_feature') {
      return;
    }

    /** @var \Drupal\ps_feature\Entity\OfferFeature $feature */
    $feature = $entity;

    $type_id = $feature->getFeatureType();

    // Get payload — decode if stored as JSON string.
    $payload_raw = $feature->get('payload')->value;
    $payload = is_string($payload_raw) ? (json_decode($payload_raw, TRUE) ?? []) : $payload_raw;

    // Normalize first (cast types, clean data), then validate.
    $normalized = $this->featureTypeManager->normalize($type_id, $payload);

    $errors = $this->featureTypeManager->validate($type_id, $normalized);
    if (!empty($errors)) {
      throw new EntityStorageException(
        'Invalid feature payload: ' . implode(', ', $errors)
      );
    }

    $feature->set('payload', json_encode($normalized, JSON_UNESCAPED_UNICODE));
  }

}
