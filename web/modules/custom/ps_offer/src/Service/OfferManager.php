<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\node\NodeInterface;

/**
 * Offer Manager service for CRUD operations and business logic.
 */
final class OfferManager implements OfferManagerInterface {

  /**
   * Constructor with dependency injection.
   */
  public function __construct(
    protected EntityTypeManagerInterface $entityTypeManager,
    protected LoggerChannelFactoryInterface $loggerFactory,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function createOffer(array $values = []): NodeInterface {
    $defaults = ['type' => 'offer'];
    $offer = $this->entityTypeManager->getStorage('node')->create($values + $defaults);
    return $offer;
  }

  /**
   * {@inheritdoc}
   */
  public function getOfferByExternalId(string $externalId): ?NodeInterface {
    $results = $this->entityTypeManager->getStorage('node')
      ->loadByProperties([
        'type' => 'offer',
        'external_id' => $externalId,
      ]);
    return !empty($results) ? reset($results) : NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function publishOffer(NodeInterface $offer): void {
    $offer->set('status', NodeInterface::PUBLISHED);
    $offer->save();
    $this->loggerFactory->get('ps_offer')
      ->info('Offer %id published', ['%id' => $offer->id()]);
  }

  /**
   * {@inheritdoc}
   */
  public function archiveOffer(NodeInterface $offer): void {
    $offer->set('status', NodeInterface::NOT_PUBLISHED);
    $offer->save();
    $this->loggerFactory->get('ps_offer')
      ->info('Offer %id archived', ['%id' => $offer->id()]);
  }

}
