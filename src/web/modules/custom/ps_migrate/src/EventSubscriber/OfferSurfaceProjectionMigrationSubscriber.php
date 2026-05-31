<?php

declare(strict_types=1);

namespace Drupal\ps_migrate\EventSubscriber;

use Drupal\Core\State\StateInterface;
use Drupal\migrate\Event\MigrateEvents;
use Drupal\migrate\Event\MigrateImportEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Temporarily disables offer surface projection during offer migration imports.
 */
final class OfferSurfaceProjectionMigrationSubscriber implements EventSubscriberInterface {

  private const MIGRATIONS_WITH_DISABLED_PROJECTION = [
    'ps_offer_from_xml',
  ];

  public function __construct(
    private readonly StateInterface $state,
    private readonly LoggerInterface $logger,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {
    return [
      MigrateEvents::PRE_IMPORT => ['onPreImport', 200],
      MigrateEvents::POST_IMPORT => ['onPostImport', -200],
    ];
  }

  public function onPreImport(MigrateImportEvent $event): void {
    $migrationId = $event->getMigration()->id();
    if (!in_array($migrationId, self::MIGRATIONS_WITH_DISABLED_PROJECTION, TRUE)) {
      return;
    }

    $this->state->set('ps_offer.skip_projection', TRUE);
    $this->logger->info('Surface projection disabled for migration @migration.', ['@migration' => $migrationId]);
  }

  public function onPostImport(MigrateImportEvent $event): void {
    $migrationId = $event->getMigration()->id();
    if (!in_array($migrationId, self::MIGRATIONS_WITH_DISABLED_PROJECTION, TRUE)) {
      return;
    }

    $this->state->set('ps_offer.skip_projection', FALSE);
    $this->logger->info('Surface projection re-enabled after migration @migration.', ['@migration' => $migrationId]);
  }

}
