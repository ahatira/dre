<?php

declare(strict_types=1);

namespace Drupal\ps_migrate\EventSubscriber;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\migrate\Event\MigrateEvents;
use Drupal\migrate\Event\MigratePostRowSaveEvent;
use Drupal\migrate\Event\MigratePreRowSaveEvent;
use Drupal\ps_core\Service\EntityProtectionManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Integrates entity protection with Migrate imports.
 */
final class EntityProtectionSubscriber implements EventSubscriberInterface {

  /**
   * Constructs an EntityProtectionSubscriber object.
   */
  public function __construct(
    private readonly EntityProtectionManagerInterface $protectionManager,
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly LoggerInterface $logger,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {
    return [
      MigrateEvents::PRE_ROW_SAVE => ['onPreRowSave', 100],
      MigrateEvents::POST_ROW_SAVE => ['onPostRowSave', -100],
    ];
  }

  /**
   * Before entity save: check protection and conflicts.
   */
  public function onPreRowSave(MigratePreRowSaveEvent $event): void {
    $row = $event->getRow();

    $destination_ids = $row->getDestinationProperty('entity_id');
    
    if (empty($destination_ids)) {
      return; // New entity, no protection check needed.
    }

    // Load existing entity.
    $migration = $event->getMigration();
    $destination_config = $migration->getDestinationConfiguration();
    $entity_type = $destination_config['entity_type'] ?? NULL;
    
    if (!$entity_type) {
      return;
    }

    try {
      $storage = $this->entityTypeManager->getStorage($entity_type);
      $entity = $storage->load($destination_ids[0]);
      
      if (!$entity instanceof EntityInterface) {
        return;
      }

      // Check if entity is protected.
      if ($this->protectionManager->isProtected($entity)) {
        $this->logger->warning(
          'Migration @migration: Entity @type:@id is protected - import will be skipped or merged',
          [
            '@migration' => $migration->id(),
            '@type' => $entity_type,
            '@id' => $entity->id(),
          ]
        );
        
        // Set a flag in the row to handle protection in destination.
        $row->setDestinationProperty('_is_protected', TRUE);
      }

      // Check for conflicts.
      $source_data = $row->getSource();
      if ($this->protectionManager->hasConflict($entity, $source_data)) {
        $this->logger->warning(
          'Migration @migration: Conflict detected for @type:@id',
          [
            '@migration' => $migration->id(),
            '@type' => $entity_type,
            '@id' => $entity->id(),
          ]
        );
        
        $row->setDestinationProperty('_has_conflict', TRUE);
      }
    }
    catch (\Exception $e) {
      $this->logger->error('Error checking entity protection: @message', [
        '@message' => $e->getMessage(),
      ]);
    }
  }

  /**
   * After entity save: track source and compute checksum.
   */
  public function onPostRowSave(MigratePostRowSaveEvent $event): void {
    $row = $event->getRow();
    $destination_ids = $event->getDestinationIdValues();
    
    if (empty($destination_ids)) {
      $this->logger->debug('POST_ROW_SAVE: No destination IDs');
      return;
    }

    $migration = $event->getMigration();
    $destination_config = $migration->getDestinationConfiguration();
    $entity_type = $destination_config['entity_type'] ?? NULL;
    
    if (!$entity_type) {
      $this->logger->debug('POST_ROW_SAVE: No entity type in destination config');
      return;
    }

    try {
      $storage = $this->entityTypeManager->getStorage($entity_type);
      $entity_id = is_array($destination_ids) ? reset($destination_ids) : $destination_ids;
      $entity = $storage->load($entity_id);
      
      if (!$entity instanceof EntityInterface) {
        $this->logger->warning('POST_ROW_SAVE: Could not load entity @type:@id', [
          '@type' => $entity_type,
          '@id' => $entity_id,
        ]);
        return;
      }

      // Only process entities with protection fields.
      if (!$entity->hasField('source_tracking') && !$entity->hasField('checksum')) {
        $this->logger->debug('POST_ROW_SAVE: Entity @type:@id has no protection fields', [
          '@type' => $entity_type,
          '@id' => $entity_id,
        ]);
        return;
      }

      // Extract source identifier from row.
      $source_ids = $row->getSourceIdValues();
      $source_id = is_array($source_ids) ? implode(':', $source_ids) : (string) $source_ids;
      
      // Track source metadata.
      $source_config = $migration->getSourceConfiguration();
      $source_data = [
        'source_system' => 'CRM_XML',
        'source_file' => $source_config['urls'][0] ?? 'unknown',
        'source_id' => $source_id,
        'import_timestamp' => \Drupal::time()->getRequestTime(),
        'migration_id' => $migration->id(),
      ];
      
      // Compute checksum from all source data.
      $checksum = $this->protectionManager->computeChecksum($row->getSource());
      
      // Update entity fields.
      $needs_save = FALSE;
      
      if ($entity->hasField('source_tracking')) {
        $entity->set('source_tracking', json_encode($source_data, JSON_THROW_ON_ERROR));
        $needs_save = TRUE;
      }
      
      if ($entity->hasField('checksum')) {
        $entity->set('checksum', $checksum);
        $needs_save = TRUE;
      }
      
      if ($needs_save) {
        $entity->save();
        
        $this->logger->info(
          'Migration @migration: Protection tracking saved for @type:@id (checksum: @checksum)',
          [
            '@migration' => $migration->id(),
            '@type' => $entity_type,
            '@id' => $entity->id(),
            '@checksum' => substr($checksum, 0, 8),
          ]
        );
      }
    }
    catch (\Exception $e) {
      $this->logger->error('Error tracking entity source: @message | @trace', [
        '@message' => $e->getMessage(),
        '@trace' => $e->getTraceAsString(),
      ]);
    }
  }

}
