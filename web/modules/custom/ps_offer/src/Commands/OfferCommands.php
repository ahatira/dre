<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Commands;

use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drush\Attributes as CLI;
use Drush\Commands\DrushCommands;

/**
 * Drush commands for ps_offer module.
 */
class OfferCommands extends DrushCommands {

  /**
   * Constructs the OfferCommands object.
   */
  public function __construct(
    protected EntityTypeManagerInterface $entityTypeManager,
    protected LoggerChannelFactoryInterface $loggerFactory,
    protected DateFormatterInterface $dateFormatter,
  ) {}

  /**
   * List all offers.
   *
   * @command ps:offer-list
   * @aliases ps-offer-list
   */
  #[CLI\Command(name: 'ps:offer-list')]
  #[CLI\Aliases(['ps-offer-list'])]
  public function listOffers(): void {
    $storage = $this->entityTypeManager->getStorage('node');
    $query = $storage->getQuery()
      ->condition('type', 'offer')
      ->accessCheck(FALSE);

    $results = $query->execute();

    if (empty($results)) {
      $this->logger()->notice('No offers found.');
      return;
    }

    $nodes = $storage->loadMultiple($results);
    $rows = [];

    foreach ($nodes as $node) {
      $rows[] = [
        $node->id(),
        $node->getTitle(),
        $node->isPublished() ? 'published' : 'unpublished',
        $this->dateFormatter->format($node->getCreatedTime(), 'short'),
      ];
    }

    $this->io()->table(['ID', 'Title', 'Status', 'Created'], $rows);
  }

  /**
   * Show details for a specific offer.
   *
   * @param int $offer_id
   *   The offer node ID.
   *
   * @command ps:offer-show
   * @aliases ps-offer-show
   */
  #[CLI\Command(name: 'ps:offer-show')]
  #[CLI\Aliases(['ps-offer-show'])]
  #[CLI\Argument(name: 'offer_id', description: 'The offer node ID')]
  public function showOffer(int $offer_id): void {
    $node = $this->entityTypeManager->getStorage('node')->load($offer_id);

    if (!$node || $node->getType() !== 'offer') {
      $this->logger()->error('Offer not found.');
      return;
    }

    $this->output()->writeln("Offer: {$node->getTitle()}");
    $this->output()->writeln("ID: {$node->id()}");
    $this->output()->writeln("Status: " . ($node->isPublished() ? 'Published' : 'Unpublished'));
    $this->output()->writeln("Created: " . $this->dateFormatter->format($node->getCreatedTime(), 'long'));

    if ($node->hasField('external_id') && !$node->get('external_id')->isEmpty()) {
      $this->output()->writeln("External ID: {$node->get('external_id')->value}");
    }
  }

}
