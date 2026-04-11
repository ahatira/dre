<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_features\Unit\Service;

use Drupal\Core\Config\Config;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\ps_features\Entity\FeatureInterface;
use Drupal\ps_features\Service\CompareBuilder;
use Drupal\ps_features\Service\FeatureManagerInterface;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\ps_features\Service\CompareBuilder
 * @group ps_features
 */
final class CompareBuilderTest extends UnitTestCase {

  /**
   * The compare builder service.
   */
  private CompareBuilder $builder;

  /**
   * Set up test environment.
   */
  protected function setUp(): void {
    parent::setUp();

    $config = $this->createMock(Config::class);
    $config->method('get')
      ->with('compare_sections')
      ->willReturn(['general', 'comfort']);

    $configFactory = $this->createMock(ConfigFactoryInterface::class);
    $configFactory->method('get')
      ->with('ps_features.settings')
      ->willReturn($config);

    $featureManager = $this->createMock(FeatureManagerInterface::class);

    $this->builder = new CompareBuilder($configFactory, $featureManager);
  }

  /**
   * @covers ::build
   * @covers ::getSections
   */
  public function testBuildOrdersSectionsAndFeatures(): void {
    $general = $this->featureStub('general', 2);
    $comfort = $this->featureStub('comfort', 1);
    $other = $this->featureStub('extra', 0);

    $sections = $this->builder->build([$general, $comfort, $other]);

    $this->assertSame(['general', 'comfort', 'extra'], array_keys($sections));
    $this->assertSame([$general], $sections['general']['features']);
    $this->assertSame([$comfort], $sections['comfort']['features']);
    $this->assertSame([$other], $sections['extra']['features']);
  }

  /**
   * Creates a feature stub with group and weight.
   *
   * @param string $group
   *   The feature group.
   * @param int $weight
   *   The feature weight.
   *
   * @return \Drupal\ps_features\Entity\FeatureInterface
   *   The feature stub.
   */
  private function featureStub(string $group, int $weight): FeatureInterface {
    $feature = $this->createStub(FeatureInterface::class);
    $feature->method('getGroup')->willReturn($group);
    $feature->method('getWeight')->willReturn($weight);
    return $feature;
  }

}
