<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_migrate\Unit\EventSubscriber;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\Config\Entity\ConfigEntityInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\migrate\Event\MigratePostRowSaveEvent;
use Drupal\migrate\Event\MigratePreRowSaveEvent;
use Drupal\migrate\MigrateMessageInterface;
use Drupal\migrate\MigrateSkipRowException;
use Drupal\migrate\Plugin\MigrateIdMapInterface;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate\Row;
use Drupal\ps_core\Service\EntityProtectionManagerInterface;
use Drupal\ps_core\Service\ImportGovernanceRegistry;
use Drupal\ps_core\Plugin\ImportGovernance\ImportGovernancePolicyInterface;
use Drupal\ps_migrate\EventSubscriber\EntityProtectionSubscriber;
use Drupal\ps_migrate\Service\ImportPipelineLockStrategy;
use Drupal\Tests\UnitTestCase;
use Psr\Log\LoggerInterface;

/**
 * @coversDefaultClass \Drupal\ps_migrate\EventSubscriber\EntityProtectionSubscriber
 */
final class EntityProtectionSubscriberTest extends UnitTestCase {

  /**
   * @covers ::onPreRowSave
   */
  public function testPreRowSaveSkipsProtectedConfigEntityRow(): void {
    $entity = $this->buildConfigEntity('tec_surface_totale', [
      'label' => 'Protected label',
      'internal_lock' => TRUE,
    ]);

    $subscriber = $this->buildSubscriber(
      protectionManager: $this->buildProtectionManager(
        supports: TRUE,
        isCatalogueProtected: TRUE,
        entity: $entity,
      ),
      entityTypeManager: $this->buildEntityTypeManager('fb_feature_definition', $entity),
      lockStrategy: $this->buildLockStrategy(skipRow: TRUE),
      governanceRegistry: $this->buildGovernanceRegistry(skipRow: TRUE),
    );

    $this->expectException(MigrateSkipRowException::class);
    $subscriber->onPreRowSave($this->buildPreRowSaveEvent(
      entityType: 'fb_feature_definition',
      destinationId: 'tec_surface_totale',
      destination: ['id' => 'tec_surface_totale', 'label' => 'CRM label'],
    ));
  }

  /**
   * @covers ::onPreRowSave
   */
  public function testPreRowSavePreservesLockedFieldOnConfigEntity(): void {
    $entity = $this->buildConfigEntity('tec_climatisation', [
      'type_driver' => 'numeric',
      'internal_lock' => FALSE,
      'field_locks' => ['type_driver' => TRUE],
    ]);

    $protectionManager = $this->buildProtectionManager(
      supports: TRUE,
      isCatalogueProtected: FALSE,
      entity: $entity,
      fieldLocks: ['type_driver' => TRUE],
    );

    $row = new Row(
      ['definition_id' => 'tec_climatisation'],
      ['definition_id' => 'string'],
    );
    $row->setDestinationProperty('id', 'tec_climatisation');
    $row->setDestinationProperty('type_driver', 'text');

    $subscriber = $this->buildSubscriber(
      protectionManager: $protectionManager,
      entityTypeManager: $this->buildEntityTypeManager('fb_feature_definition', $entity),
      lockStrategy: $this->buildLockStrategy(),
      governanceRegistry: $this->buildGovernanceRegistry(),
    );

    $subscriber->onPreRowSave($this->buildPreRowSaveEvent(
      entityType: 'fb_feature_definition',
      destinationId: 'tec_climatisation',
      row: $row,
    ));

    self::assertSame('numeric', $row->getDestinationProperty('type_driver'));
  }

  /**
   * @covers ::onPostRowSave
   */
  public function testPostRowSaveTracksSourceForConfigEntity(): void {
    $entity = $this->buildConfigEntity('tec_accueil', [
      'source_tracking' => '',
      'checksum' => '',
    ]);

    $protectionManager = $this->createMock(EntityProtectionManagerInterface::class);
    $protectionManager->method('supports')->willReturn(TRUE);
    $protectionManager->method('computeChecksum')->willReturn('abc123checksum');
    $protectionManager->expects(self::once())->method('trackSource')->with(
      $entity,
      self::callback(static function (array $metadata): bool {
        return ($metadata['source_system'] ?? '') === 'CRM_XML'
          && ($metadata['source_id'] ?? '') === 'tec_accueil'
          && ($metadata['migration_id'] ?? '') === 'ps_feature_definitions_from_xml';
      }),
    );
    $protectionManager->expects(self::once())->method('storeChecksum')->with($entity, 'abc123checksum');
    $entity->expects(self::once())->method('save');

    $subscriber = $this->buildSubscriber(
      protectionManager: $protectionManager,
      entityTypeManager: $this->buildEntityTypeManager('fb_feature_definition', $entity),
      lockStrategy: $this->buildLockStrategy(),
      governanceRegistry: $this->buildGovernanceRegistry(),
    );

    $subscriber->onPostRowSave(new MigratePostRowSaveEvent(
      $this->buildMigration('ps_feature_definitions_from_xml', 'fb_feature_definition'),
      $this->createMock(MigrateMessageInterface::class),
      new Row(['definition_id' => 'tec_accueil'], ['definition_id' => 'string']),
      ['tec_accueil'],
    ));
  }

  /**
   * @covers ::onPreRowSave
   */
  public function testPreRowSaveLoadsStringDestinationIdWithoutCastingToInt(): void {
    $entity = $this->buildConfigEntity('equipment__tec_surface', [
      'label' => 'Internal label',
      'internal_lock' => TRUE,
    ]);

    $storage = $this->createMock(EntityStorageInterface::class);
    $storage->expects(self::once())
      ->method('load')
      ->with('equipment__tec_surface')
      ->willReturn($entity);

    $entityTypeManager = $this->createMock(EntityTypeManagerInterface::class);
    $entityTypeManager->method('getStorage')->willReturn($storage);

    $protectionManager = $this->buildProtectionManager(
      supports: TRUE,
      isCatalogueProtected: TRUE,
      entity: $entity,
    );

    $subscriber = $this->buildSubscriber(
      protectionManager: $protectionManager,
      entityTypeManager: $entityTypeManager,
      lockStrategy: $this->buildLockStrategy(skipRow: TRUE),
      governanceRegistry: $this->buildGovernanceRegistry(skipRow: TRUE),
    );

    $this->expectException(MigrateSkipRowException::class);
    $subscriber->onPreRowSave($this->buildPreRowSaveEvent(
      entityType: 'fb_feature_definition',
      destinationId: 'equipment__tec_surface',
      destination: ['id' => 'equipment__tec_surface'],
    ));
  }

  /**
   * Builds the subscriber under test.
   */
  private function buildSubscriber(
    EntityProtectionManagerInterface $protectionManager,
    EntityTypeManagerInterface $entityTypeManager,
    ImportPipelineLockStrategy $lockStrategy,
    ImportGovernanceRegistry $governanceRegistry,
  ): EntityProtectionSubscriber {
    $time = $this->createMock(TimeInterface::class);
    $time->method('getCurrentTime')->willReturn(1_700_000_100);

    return new EntityProtectionSubscriber(
      $protectionManager,
      $entityTypeManager,
      $this->createMock(LoggerInterface::class),
      $time,
      $lockStrategy,
      $governanceRegistry,
    );
  }

  /**
   * Builds a mocked import governance registry.
   */
  private function buildGovernanceRegistry(bool $skipRow = FALSE, bool $preserveFields = FALSE): ImportGovernanceRegistry {
    $policy = $this->createMock(ImportGovernancePolicyInterface::class);
    $policy->method('getEntityTypeIds')->willReturn([
      'fb_feature_definition',
      'fb_feature_group',
    ]);
    $policy->method('shouldSkipProtectedRow')->willReturn($skipRow);
    $policy->method('shouldPreserveProtectedFields')->willReturn($preserveFields);
    $policy->method('resolveEffectiveLockStrategy')->willReturn(
      $skipRow ? 'skip_row' : ($preserveFields ? 'skip_field' : 'log_only'),
    );
    $policy->method('getAdditionalPreservedProperties')->willReturn([]);

    $registry = $this->createMock(ImportGovernanceRegistry::class);
    $registry->method('getPolicyForEntity')->willReturnCallback(
      static fn(EntityInterface $entity): ?ImportGovernancePolicyInterface => in_array(
        $entity->getEntityTypeId(),
        ['fb_feature_definition', 'fb_feature_group'],
        TRUE,
      ) ? $policy : NULL,
    );

    return $registry;
  }

  /**
   * Builds a mocked protection manager.
   */
  private function buildProtectionManager(
    bool $supports,
    bool $isCatalogueProtected = FALSE,
    ?ConfigEntityInterface $entity = NULL,
    array $fieldLocks = [],
  ): EntityProtectionManagerInterface {
    $protectionManager = $this->createMock(EntityProtectionManagerInterface::class);
    $protectionManager->method('supports')->willReturn($supports);
    $protectionManager->method('isCatalogueProtected')->willReturn($isCatalogueProtected);
    $protectionManager->method('computeChecksum')->willReturn('checksum');
    $protectionManager->method('hasConflict')->willReturn(FALSE);
    $protectionManager->method('isFieldLocked')->willReturnCallback(
      static fn($checkedEntity, string $fieldName): bool => !empty($fieldLocks[$fieldName]),
    );

    return $protectionManager;
  }

  /**
   * Builds a mocked entity type manager returning one entity.
   */
  private function buildEntityTypeManager(string $entityTypeId, ConfigEntityInterface $entity): EntityTypeManagerInterface {
    $storage = $this->createMock(EntityStorageInterface::class);
    $storage->method('load')->willReturnCallback(static fn(string $id) => $entity->id() === $id ? $entity : NULL);

    $entityTypeManager = $this->createMock(EntityTypeManagerInterface::class);
    $entityTypeManager->method('getStorage')->with($entityTypeId)->willReturn($storage);

    return $entityTypeManager;
  }

  /**
   * Builds a real lock strategy backed by pipeline settings config.
   */
  private function buildLockStrategy(bool $skipRow = FALSE, bool $preserveFields = FALSE): ImportPipelineLockStrategy {
    $strategy = $skipRow
      ? ImportPipelineLockStrategy::STRATEGY_SKIP_ROW
      : ($preserveFields
        ? ImportPipelineLockStrategy::STRATEGY_SKIP_FIELD
        : ImportPipelineLockStrategy::STRATEGY_LOG_ONLY);

    $config = $this->createMock(ImmutableConfig::class);
    $config->method('get')->willReturnCallback(static function (string $key) use ($strategy): mixed {
      return match ($key) {
        'lock_strategy_default' => $strategy,
        'lock_field_strategies' => [],
        default => NULL,
      };
    });

    $configFactory = $this->createMock(ConfigFactoryInterface::class);
    $configFactory->method('get')->with('ps_migrate.import_pipeline_settings')->willReturn($config);

    return new ImportPipelineLockStrategy($configFactory);
  }

  /**
   * Builds a config entity stub with mutable property storage.
   *
   * @param string $id
   *   Entity ID.
   * @param array<string, mixed> $values
   *   Initial property values.
   */
  private function buildConfigEntity(string $id, array $values = []): ConfigEntityInterface {
    $values += [
      'id' => $id,
      'internal_lock' => FALSE,
      'field_locks' => [],
    ];

    $entity = $this->createMock(ConfigEntityInterface::class);
    $entity->method('id')->willReturn($id);
    $entity->method('getEntityTypeId')->willReturn('fb_feature_definition');
    $entity->method('toArray')->willReturnCallback(static fn(): array => $values);
    $entity->method('get')->willReturnCallback(static function (string $property) use (&$values): mixed {
      return $values[$property] ?? NULL;
    });
    $entity->method('set')->willReturnSelf();

    return $entity;
  }

  /**
   * Builds a PRE_ROW_SAVE migrate event.
   */
  private function buildPreRowSaveEvent(
    string $entityType,
    string $destinationId,
    array $destination = [],
    ?Row $row = NULL,
  ): MigratePreRowSaveEvent {
    $row ??= new Row(['definition_id' => $destinationId], ['definition_id' => 'string']);
    foreach ($destination as $property => $value) {
      $row->setDestinationProperty($property, $value);
    }

    return new MigratePreRowSaveEvent(
      $this->buildMigration('ps_feature_definitions_from_xml', $entityType),
      $this->createMock(MigrateMessageInterface::class),
      $row,
    );
  }

  /**
   * Builds a mocked migration plugin.
   */
  private function buildMigration(string $migrationId, string $entityType): MigrationInterface {
    $idMap = $this->createMock(MigrateIdMapInterface::class);
    $idMap->method('lookupDestinationIds')->willReturn([]);

    $migration = $this->createMock(MigrationInterface::class);
    $migration->method('id')->willReturn($migrationId);
    $migration->method('getDestinationConfiguration')->willReturn([
      'plugin' => 'entity:' . $entityType,
    ]);
    $migration->method('getSourceConfiguration')->willReturn([
      'files' => ['public://crm/offers.xml'],
    ]);
    $migration->method('getIdMap')->willReturn($idMap);

    return $migration;
  }

}
