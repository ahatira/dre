<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Service;

use Drupal\node\NodeInterface;

/**
 * Interface for Offer Manager service.
 */
interface OfferManagerInterface {

  /**
   * Creates a new offer node.
   *
   * @param array $values
   *   Field values for the offer.
   *
   * @return \Drupal\node\NodeInterface
   *   The created offer node (not saved).
   */
  public function createOffer(array $values = []): NodeInterface;

  /**
   * Loads an offer by external ID.
   *
   * @param string $externalId
   *   The CRM external identifier.
   *
   * @return \Drupal\node\NodeInterface|null
   *   The offer node or NULL if not found.
   */
  public function getOfferByExternalId(string $externalId): ?NodeInterface;

  /**
   * Publishes an offer.
   *
   * @param \Drupal\node\NodeInterface $offer
   *   The offer to publish.
   */
  public function publishOffer(NodeInterface $offer): void;

  /**
   * Archives an offer.
   *
   * @param \Drupal\node\NodeInterface $offer
   *   The offer to archive.
   */
  public function archiveOffer(NodeInterface $offer): void;

}
