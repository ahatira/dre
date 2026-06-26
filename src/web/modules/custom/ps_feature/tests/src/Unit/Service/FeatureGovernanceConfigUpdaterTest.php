<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_feature\Unit\Service;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\ps_feature\Entity\FeatureDefinition;
use Drupal\ps_feature\Service\FeatureDefinitionSource;
use Drupal\ps_feature\Service\FeatureGovernanceConfigUpdater;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\ps_feature\Service\FeatureGovernanceConfigUpdater
 */
final class FeatureGovernanceConfigUpdaterTest extends UnitTestCase {

  /**
   * @covers ::migrateDefinition
   */
  public function testMigrateDefinitionMapsLegacyBoSourceToInternalLock(): void {
    $definition = $this->createMock(FeatureDefinition::class);
    $definition->method('getSource')->willReturn(FeatureDefinitionSource::BO);
    $definition->method('isInternallyLocked')->willReturnOnConsecutiveCalls(FALSE, TRUE);
    $definition->method('isTypeLocked')->willReturn(FALSE);
    $definition->method('isFieldLocked')->with('type_driver')->willReturn(FALSE);
    $definition->method('getSourceTracking')->willReturn('');
    $definition->method('id')->willReturn('tec_surface_totale');

    $definition->expects(self::once())->method('setInternallyLocked')->with(TRUE)->willReturnSelf();
    $definition->expects(self::once())->method('setSourceTracking')->with(self::callback(static function (string $json): bool {
      $data = json_decode($json, TRUE, 512, JSON_THROW_ON_ERROR);
      return ($data['source_system'] ?? '') === 'PS_BO'
        && ($data['legacy_source'] ?? '') === FeatureDefinitionSource::BO;
    }))->willReturnSelf();

    $updater = new FeatureGovernanceConfigUpdater($this->createMock(EntityTypeManagerInterface::class));
    self::assertTrue($updater->migrateDefinition($definition));
  }

  /**
   * @covers ::migrateDefinition
   */
  public function testMigrateDefinitionMapsLegacyTypeLockedToFieldLocks(): void {
    $definition = $this->createMock(FeatureDefinition::class);
    $definition->method('getSource')->willReturn(FeatureDefinitionSource::XML);
    $definition->method('isInternallyLocked')->willReturn(FALSE);
    $definition->method('isTypeLocked')->willReturn(TRUE);
    $definition->method('isFieldLocked')->with('type_driver')->willReturn(FALSE);
    $definition->method('getSourceTracking')->willReturn('{"source_system":"CRM_XML"}');
    $definition->method('id')->willReturn('tec_climatisation');

    $definition->expects(self::never())->method('setInternallyLocked');
    $definition->expects(self::once())->method('setFieldLocked')->with('type_driver', TRUE)->willReturnSelf();
    $definition->expects(self::never())->method('setSourceTracking');

    $updater = new FeatureGovernanceConfigUpdater($this->createMock(EntityTypeManagerInterface::class));
    self::assertTrue($updater->migrateDefinition($definition));
  }

}
