<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_search\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\ps_search\Service\FeatureSearchFilterRegistry;

/**
 * @coversDefaultClass \Drupal\ps_search\Service\FeatureSearchFilterRegistry
 *
 * @group ps_search
 */
final class FeatureSearchFilterRegistryTransportKernelTest extends KernelTestBase {

  protected static $modules = [
    'system',
    'user',
    'node',
    'field',
    'text',
    'options',
    'ps_core',
    'ps_feature',
    'ps_dictionary',
    'ps_search',
  ];

  private FeatureSearchFilterRegistry $registry;

  protected function setUp(): void {
    parent::setUp();
    $this->installEntitySchema('node');
    $this->installEntitySchema('fb_feature_group');
    $this->installEntitySchema('fb_feature_definition');
    $this->registry = $this->container->get('ps_search.feature_filter_registry');
  }

  /**
   * @covers ::getExposedFilters
   */
  public function testTransportGroupDefinitionsExcludedFromExposedFilters(): void {
    $filters = $this->registry->getExposedFilters(NULL, FALSE);
    $transportGroupId = $this->registry->getTransportGroupId();

    foreach ($filters as $definitionId => $filter) {
      $groupId = (string) ($filter['group_id'] ?? '');
      self::assertNotSame(
        $transportGroupId,
        $groupId,
        sprintf('Transport group definition %s must not be an exposed per-feature filter', $definitionId),
      );
    }
  }

}
