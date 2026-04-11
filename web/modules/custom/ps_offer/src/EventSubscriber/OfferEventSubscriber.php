<?php

declare(strict_types=1);

namespace Drupal\ps_offer\EventSubscriber;

use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\node\NodeInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\core_events\Event\EntityPresaveEvent;

/**
 * Offer event subscriber for pre-save logic.
 */
class OfferEventSubscriber implements EventSubscriberInterface {

  /**
   * Constructs the OfferEventSubscriber object.
   */
  public function __construct(
    protected LoggerChannelFactoryInterface $loggerFactory,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {
    return [
      'entity_presave' => ['onOfferPresave'],
    ];
  }

  /**
   * Handle offer node pre-save event.
   *
   * @param \Drupal\core_events\Event\EntityPresaveEvent $event
   *   The entity presave event.
   */
  public function onOfferPresave(EntityPresaveEvent $event): void {
    $entity = $event->getEntity();

    if (!$entity instanceof NodeInterface || $entity->getType() !== 'offer') {
      return;
    }

    // Log offer changes
    $this->loggerFactory->get('ps_offer')->info(
      'Offer %offer_id being saved',
      ['%offer_id' => $entity->id() ?? 'new'],
    );
  }

}
