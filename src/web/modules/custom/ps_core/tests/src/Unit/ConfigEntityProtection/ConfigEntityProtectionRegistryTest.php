<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_core\Unit\ConfigEntityProtection;

use Drupal\ps_core\ConfigEntityProtection\ConfigEntityProtectionDefinition;
use Drupal\ps_core\ConfigEntityProtection\ConfigEntityProtectionRegistry;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\ps_core\ConfigEntityProtection\ConfigEntityProtectionRegistry
 */
final class ConfigEntityProtectionRegistryTest extends UnitTestCase {

  /**
   * @covers ::supportsEntityType
   * @covers ::getDefinition
   */
  public function testRegistryCollectsTaggedDefinitions(): void {
    $registry = new ConfigEntityProtectionRegistry([
      new ConfigEntityProtectionDefinition('fb_feature_definition'),
      new ConfigEntityProtectionDefinition('fb_feature_group'),
    ]);

    self::assertTrue($registry->supportsEntityType('fb_feature_definition'));
    self::assertTrue($registry->supportsEntityType('fb_feature_group'));
    self::assertFalse($registry->supportsEntityType('node'));

    $definition = $registry->getDefinition('fb_feature_definition');
    self::assertNotNull($definition);
    self::assertSame('internal_lock', $definition->getLockProperty());
    self::assertSame('field_locks', $definition->getFieldLocksProperty());
  }

}
