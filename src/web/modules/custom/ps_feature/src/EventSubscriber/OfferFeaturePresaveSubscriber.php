<?php

namespace Drupal\ps_feature\EventSubscriber;

use Drupal\Core\Entity\EntityStorageException;
use Drupal\ps_feature\Service\FeatureTypeManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\Core\Entity\EntityTypeEvents;
use Drupal\Core\Entity\EntityTypeEvent;

/**
 * Validates and normalizes offer feature payloads before save.
 */
class OfferFeaturePresaveSubscriber implements EventSubscriberInterface {

  /**
   * The feature type manager.
   *
   * @var \Drupal\ps_feature\Service\FeatureTypeManager
   */
  protected FeatureTypeManager $featureTypeManager;

  /**
   * Constructs a new OfferFeaturePresaveSubscriber.
   *
   * @param \Drupal\ps_feature\Service\FeatureTypeManager $feature_type_manager
   *   The feature type manager.
   */
  public function __construct(FeatureTypeManager $feature_type_manager) {
    $this->featureTypeManager = $feature_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {
    // Subscribe to entity presave events.
    return [
      'entity.entity_offer_feature.presave' => 'onOfferFeaturePresave',
    ];
  }

  /**
   * Validates and normalizes the feature payload before save.
   *
   * @param \Drupal\Core\Entity\EntityTypeEvent $event
   *   The entity event.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function onOfferFeaturePresave(EntityTypeEvent $event): void {
    /** @var \Drupal\ps_feature\Entity\OfferFeature $feature */
    $feature = $event->getEntity();

    if ($feature->getEntityTypeId() !== 'entity_offer_feature') {
      return;
    }

    $type_id = $feature->getFeatureType();
    $payload = $feature->getPayload();

    // Validate the payload.
    $errors = $this->featureTypeManager->validate($type_id, $payload);
    if (!empty($errors)) {
      throw new EntityStorageException(
        'Invalid feature payload: ' . implode(', ', $errors)
      );
    }

    // Normalize the payload.
    $normalized = $this->featureTypeManager->normalize($type_id, $payload);
    $feature->setPayload($normalized);
  }

}
