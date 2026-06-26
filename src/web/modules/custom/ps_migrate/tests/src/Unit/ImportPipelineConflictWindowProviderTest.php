<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_migrate\Unit;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\ps_migrate\Service\ImportPipelineConflictWindowProvider;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\ps_migrate\Service\ImportPipelineConflictWindowProvider
 */
final class ImportPipelineConflictWindowProviderTest extends UnitTestCase {

  /**
   * @covers ::getConflictWindowSeconds
   */
  public function testReturnsConfiguredValue(): void {
    $provider = new ImportPipelineConflictWindowProvider($this->createConfigFactory(300));
    self::assertSame(300, $provider->getConflictWindowSeconds());
  }

  /**
   * @covers ::getConflictWindowSeconds
   */
  public function testClampsNegativeValuesToZero(): void {
    $provider = new ImportPipelineConflictWindowProvider($this->createConfigFactory(-10));
    self::assertSame(0, $provider->getConflictWindowSeconds());
  }

  /**
   * @covers ::getConflictWindowSeconds
   */
  public function testDefaultsToThreeHundredWhenMissing(): void {
    $provider = new ImportPipelineConflictWindowProvider($this->createConfigFactory(NULL));
    self::assertSame(300, $provider->getConflictWindowSeconds());
  }

  private function createConfigFactory(?int $window): ConfigFactoryInterface {
    $config = $this->createMock(ImmutableConfig::class);
    $config->method('get')->with('conflict_window_seconds')->willReturn($window);

    $configFactory = $this->createMock(ConfigFactoryInterface::class);
    $configFactory->method('get')->with('ps_migrate.import_pipeline_settings')->willReturn($config);

    return $configFactory;
  }

}
