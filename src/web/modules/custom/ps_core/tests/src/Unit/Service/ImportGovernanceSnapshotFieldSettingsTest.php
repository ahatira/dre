<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_core\Unit\Service;

use Drupal\Core\Config\Config;
use Drupal\Core\Config\Entity\ConfigEntityInterface;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\ps_core\ConfigEntityProtection\ConfigEntityProtectionRegistry;
use Drupal\ps_core\Service\ImportGovernanceSnapshotFieldResolver;
use Drupal\ps_core\Service\ImportGovernanceSnapshotFieldSettings;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;

/**
 * @coversDefaultClass \Drupal\ps_core\Service\ImportGovernanceSnapshotFieldSettings
 */
#[CoversClass(ImportGovernanceSnapshotFieldSettings::class)]
#[Group('ps_core')]
final class ImportGovernanceSnapshotFieldSettingsTest extends UnitTestCase {

  /**
   * @covers ::getConfiguredFields
   */
  public function testReadsSyncFieldsByEntity(): void {
    $config = $this->createMock(Config::class);
    $config->method('get')->willReturnMap([
      ['present_in_xml.sync_fields_by_entity', [
        'fb_feature_definition' => ['label', 'code'],
      ]],
      ['present_in_xml.sync_fields', NULL],
    ]);

    $settings = new ImportGovernanceSnapshotFieldSettings($this->createResolver(['label', 'code']));
    self::assertSame(['label', 'code'], $settings->getConfiguredFields($config, 'fb_feature_definition'));
  }

  /**
   * @covers ::getConfiguredFields
   */
  public function testFallsBackToLegacySyncFieldsForDefinitions(): void {
    $config = $this->createMock(Config::class);
    $config->method('get')->willReturnMap([
      ['present_in_xml.sync_fields_by_entity', NULL],
      ['present_in_xml.sync_fields', ['label', 'payload_defaults']],
    ]);

    $settings = new ImportGovernanceSnapshotFieldSettings(
      $this->createResolver(['label', 'payload_defaults']),
    );
    self::assertSame(
      ['label', 'payload_defaults'],
      $settings->getConfiguredFields($config, 'fb_feature_definition'),
    );
  }

  /**
   * @covers ::buildCheckboxDefaultValue
   */
  public function testBuildCheckboxDefaultValue(): void {
    $config = $this->createMock(Config::class);
    $config->method('get')->willReturnMap([
      ['present_in_xml.sync_fields_by_entity', ['node.offer' => ['label', 'code']]],
      ['present_in_xml.sync_fields', NULL],
    ]);

    $settings = new ImportGovernanceSnapshotFieldSettings($this->createResolver(['label', 'code']));
    self::assertSame(
      ['label' => 'label', 'code' => 'code'],
      $settings->buildCheckboxDefaultValue($config, 'node.offer'),
    );
  }

  /**
   * Builds a resolver that exposes the given config entity export properties.
   *
   * @param string[] $exportedProperties
   *   Config export property names.
   */
  private function createResolver(array $exportedProperties): ImportGovernanceSnapshotFieldResolver {
    $entityType = $this->createMock(EntityTypeInterface::class);
    $entityType->method('entityClassImplements')->willReturnCallback(
      static fn(string $interface): bool => $interface === ConfigEntityInterface::class,
    );
    $entityType->method('get')->with('config_export')->willReturn($exportedProperties);

    $entityTypeManager = $this->createMock(EntityTypeManagerInterface::class);
    $entityTypeManager->method('getDefinition')->willReturn($entityType);

    $registry = new ConfigEntityProtectionRegistry([]);

    return new ImportGovernanceSnapshotFieldResolver(
      $entityTypeManager,
      $this->createMock(EntityFieldManagerInterface::class),
      $registry,
    );
  }

}
