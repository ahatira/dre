<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_feature\Unit\Service;

use Drupal\ps_feature\Service\FeatureDefinitionSource;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * @coversDefaultClass \Drupal\ps_feature\Service\FeatureDefinitionSource
 */
#[CoversClass(FeatureDefinitionSource::class)]
final class FeatureDefinitionSourceTest extends UnitTestCase {

  /**
   * @covers ::isValid
   */
  public function testAllowedSources(): void {
    self::assertTrue(FeatureDefinitionSource::isValid(FeatureDefinitionSource::BO));
    self::assertTrue(FeatureDefinitionSource::isValid(FeatureDefinitionSource::XML));
    self::assertTrue(FeatureDefinitionSource::isValid(FeatureDefinitionSource::LEGACY));
    self::assertFalse(FeatureDefinitionSource::isValid('unknown'));
  }

}
