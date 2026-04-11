<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_features\Kernel;

use Drupal\KernelTests\KernelTestBase;

/**
 * Kernel coverage for default feature definitions.
 *
 * @group ps_features
 */
final class FeatureDefinitionsKernelTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'system',
    'user',
    'ps',
    'ps_dictionary',
    'ps_features',
  ];

  /**
   * Set up test environment.
   */
  protected function setUp(): void {
    parent::setUp();
    $this->installConfig([
      'ps',
      'ps_dictionary',
      'ps_features',
    ]);
  }

  /**
   * Tests that default features are loaded from configuration.
   */
  public function testDefaultFeaturesLoaded(): void {
    /** @var \Drupal\ps_features\Service\FeatureManagerInterface $manager */
    $manager = $this->container->get('ps_features.manager');
    $features = $manager->getFeatures();

    $this->assertCount(17, $features);
    $this->assertArrayHasKey('has_elevator', $features);
    $this->assertArrayHasKey('air_conditioning', $features);
  }

  /**
   * Tests that features are grouped correctly.
   */
  public function testFeaturesGroupedByDictionary(): void {
    /** @var \Drupal\ps_features\Service\FeatureManagerInterface $manager */
    $manager = $this->container->get('ps_features.manager');
    $grouped = $manager->getFeaturesByGroup();

    $this->assertArrayHasKey('equipments', $grouped);
    $this->assertNotEmpty($grouped['equipments']['features']);
    $this->assertArrayHasKey('services', $grouped);
    $this->assertNotEmpty($grouped['services']['features']);
  }

}
