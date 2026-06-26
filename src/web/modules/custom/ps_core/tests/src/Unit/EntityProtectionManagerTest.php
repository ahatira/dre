<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_core\Unit;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Config\Entity\ConfigEntityInterface;
use Drupal\ps_core\ConfigEntityProtection\ConfigEntityProtectionDefinition;
use Drupal\ps_core\ConfigEntityProtection\ConfigEntityProtectionRegistry;
use Drupal\ps_core\Service\EntityProtectionManager;
use Drupal\Tests\UnitTestCase;
use Psr\Log\LoggerInterface;

/**
 * @coversDefaultClass \Drupal\ps_core\Service\EntityProtectionManager
 */
final class EntityProtectionManagerTest extends UnitTestCase {

  private const ENTITY_TYPE_ID = 'test_config_entity';

  /**
   * Builds a manager with a single test config entity definition.
   */
  private function buildManager(): EntityProtectionManager {
    $registry = new ConfigEntityProtectionRegistry([
      new ConfigEntityProtectionDefinition(self::ENTITY_TYPE_ID),
    ]);

    $time = $this->createMock(TimeInterface::class);
    $time->method('getRequestTime')->willReturn(1_700_000_000);

    return new EntityProtectionManager(
      $this->createMock(LoggerInterface::class),
      $registry,
      $time,
    );
  }

  /**
   * Builds a mutable config entity stub.
   *
   * @param array<string, mixed> $values
   *   Initial entity property values.
   */
  private function buildEntity(array $values = []): ConfigEntityInterface {
    $values += [
      'id' => 'test_definition',
      'internal_lock' => FALSE,
      'source_tracking' => '',
      'checksum' => '',
      'field_locks' => [],
      'label' => 'Original label',
      'type_driver' => 'flag',
    ];

    $entity = $this->createMock(ConfigEntityInterface::class);
    $entity->method('getEntityTypeId')->willReturn(self::ENTITY_TYPE_ID);
    $entity->method('id')->willReturn((string) $values['id']);
    $entity->method('isNew')->willReturn(FALSE);
    $entity->method('get')->willReturnCallback(static function (string $property) use (&$values): mixed {
      return $values[$property] ?? NULL;
    });
    $entity->method('set')->willReturnCallback(static function (string $property, mixed $value) use (&$values, $entity): ConfigEntityInterface {
      $values[$property] = $value;
      return $entity;
    });

    return $entity;
  }

  /**
   * @covers ::supports
   */
  public function testSupportsRegisteredConfigEntity(): void {
    $manager = $this->buildManager();
    self::assertTrue($manager->supports($this->buildEntity()));
  }

  /**
   * @covers ::protect
   * @covers ::unprotect
   * @covers ::isProtected
   */
  public function testProtectAndUnprotectConfigEntity(): void {
    $manager = $this->buildManager();
    $entity = $this->buildEntity();

    self::assertFalse($manager->isProtected($entity));
    $manager->protect($entity);
    self::assertTrue($manager->isProtected($entity));
    $manager->unprotect($entity);
    self::assertFalse($manager->isProtected($entity));
  }

  /**
   * @covers ::isCatalogueProtected
   */
  public function testIsCatalogueProtectedFallsBackToInternalLock(): void {
    $manager = $this->buildManager();
    $entity = $this->buildEntity(['internal_lock' => TRUE]);

    self::assertTrue($manager->isCatalogueProtected($entity));
  }

  /**
   * @covers ::isCatalogueProtected
   */
  public function testIsCatalogueProtectedUsesEntityMethodWhenAvailable(): void {
    $manager = $this->buildManager();
    $entity = $this->getMockBuilder(ConfigEntityInterface::class)
      ->addMethods(['isCatalogueProtected'])
      ->onlyMethods(['getEntityTypeId'])
      ->getMockForAbstractClass();
    $entity->method('getEntityTypeId')->willReturn(self::ENTITY_TYPE_ID);
    $entity->method('isCatalogueProtected')->willReturn(TRUE);

    self::assertTrue($manager->isCatalogueProtected($entity));
  }

  /**
   * @covers ::setFieldLocked
   * @covers ::isFieldLocked
   * @covers ::getFieldLocks
   */
  public function testFieldLocksOnConfigEntity(): void {
    $manager = $this->buildManager();
    $entity = $this->buildEntity();

    self::assertSame([], $manager->getFieldLocks($entity));
    $manager->setFieldLocked($entity, 'type_driver', TRUE);
    self::assertTrue($manager->isFieldLocked($entity, 'type_driver'));
    self::assertSame(['type_driver' => TRUE], $manager->getFieldLocks($entity));

    $manager->setFieldLocked($entity, 'type_driver', FALSE);
    self::assertFalse($manager->isFieldLocked($entity, 'type_driver'));
  }

  /**
   * @covers ::applyMergeStrategy
   */
  public function testApplyMergeStrategySkipsProtectedEntity(): void {
    $manager = $this->buildManager();
    $entity = $this->buildEntity(['internal_lock' => TRUE, 'label' => 'Internal']);

    $updated = $manager->applyMergeStrategy(
      $entity,
      ['label' => 'External'],
      'label',
      'EXTERNAL_WINS',
    );

    self::assertFalse($updated);
    self::assertSame('Internal', $entity->get('label'));
  }

  /**
   * @covers ::applyMergeStrategy
   */
  public function testApplyMergeStrategySkipsLockedField(): void {
    $manager = $this->buildManager();
    $entity = $this->buildEntity([
      'field_locks' => ['type_driver' => TRUE],
      'type_driver' => 'numeric',
    ]);

    $updated = $manager->applyMergeStrategy(
      $entity,
      ['type_driver' => 'text'],
      'type_driver',
      'EXTERNAL_WINS',
    );

    self::assertFalse($updated);
    self::assertSame('numeric', $entity->get('type_driver'));
  }

  /**
   * @covers ::applyMergeStrategy
   */
  public function testApplyMergeStrategyUpdatesUnlockedConfigProperty(): void {
    $manager = $this->buildManager();
    $entity = $this->buildEntity(['label' => 'Internal']);

    $updated = $manager->applyMergeStrategy(
      $entity,
      ['label' => 'External'],
      'label',
      'EXTERNAL_WINS',
    );

    self::assertTrue($updated);
    self::assertSame('External', $entity->get('label'));
  }

  /**
   * @covers ::trackSource
   */
  public function testTrackSourceOnConfigEntity(): void {
    $manager = $this->buildManager();
    $entity = $this->buildEntity();

    $manager->trackSource($entity, [
      'source_system' => 'CRM_XML',
      'source_id' => 'TEC_SURFACE',
    ]);

    $tracking = json_decode((string) $entity->get('source_tracking'), TRUE, 512, JSON_THROW_ON_ERROR);
    self::assertSame('CRM_XML', $tracking['source_system']);
    self::assertSame('TEC_SURFACE', $tracking['source_id']);
    self::assertSame(1_700_000_000, $tracking['tracked_at']);
  }

  /**
   * @covers ::storeChecksum
   * @covers ::getStoredChecksum
   * @covers ::hasConflict
   */
  public function testChecksumOnConfigEntity(): void {
    $manager = $this->buildManager();
    $entity = $this->buildEntity();

    $checksum = $manager->computeChecksum(['code' => 'TEC_SURFACE']);
    $manager->storeChecksum($entity, $checksum);

    self::assertSame($checksum, $manager->getStoredChecksum($entity));
    self::assertFalse($manager->hasConflict($entity, ['checksum' => $checksum]));
    self::assertTrue($manager->hasConflict($entity, ['checksum' => 'different']));
  }

}
