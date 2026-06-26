<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_migrate\Unit;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\migrate\Plugin\MigrationPluginManagerInterface;
use Drupal\ps_migrate\Service\ImportPipelineMigrationOrderResolver;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\Group;
use Psr\Log\LoggerInterface;

/**
 * Tests CRM import migration order resolution.
 */
#[Group('ps_migrate')]
final class ImportPipelineMigrationOrderResolverTest extends UnitTestCase {

  /**
   * Ensures default full order is returned when config is empty.
   */
  public function testReturnsDefaultFullOrderWhenConfigEmpty(): void {
    $resolver = $this->createResolver([]);

    self::assertSame(
      ImportPipelineMigrationOrderResolver::DEFAULT_FULL_ORDER,
      $resolver->getOrder('full'),
    );
  }

  /**
   * Ensures configured order filters unknown migration IDs.
   */
  public function testFiltersUnknownMigrationIds(): void {
    $manager = $this->createMock(MigrationPluginManagerInterface::class);
    $manager->method('hasDefinition')
      ->willReturnMap([
        ['ps_offer_from_xml', TRUE],
        ['unknown_migration', FALSE],
      ]);

    $resolver = $this->createResolver(
      ['ps_offer_from_xml', 'unknown_migration'],
      $manager,
    );

    self::assertSame(['ps_offer_from_xml'], $resolver->getOrder('full'));
  }

  /**
   * Ensures delta mode reads the delta config key.
   */
  public function testReturnsConfiguredDeltaOrder(): void {
    $resolver = $this->createResolver(
      ['ps_offer_from_xml'],
      migrationOrderDelta: ['ps_offer_translations_from_xml'],
    );

    self::assertSame(['ps_offer_translations_from_xml'], $resolver->getOrder('delta'));
  }

  /**
   * Builds a resolver with mocked config and migration plugin manager.
   *
   * @param list<string> $migrationOrderFull
   *   Configured full import order.
   * @param \Drupal\migrate\Plugin\MigrationPluginManagerInterface|null $manager
   *   Optional migration plugin manager mock.
   * @param list<string> $migrationOrderDelta
   *   Configured delta import order.
   */
  private function createResolver(
    array $migrationOrderFull,
    ?MigrationPluginManagerInterface $manager = NULL,
    array $migrationOrderDelta = [],
  ): ImportPipelineMigrationOrderResolver {
    $config = $this->createMock(ImmutableConfig::class);
    $config->method('get')->willReturnMap([
      ['migration_order_full', $migrationOrderFull],
      ['migration_order_delta', $migrationOrderDelta],
    ]);

    $configFactory = $this->createMock(ConfigFactoryInterface::class);
    $configFactory->method('get')->with('ps_migrate.import_pipeline_settings')->willReturn($config);

    $manager ??= $this->createConfiguredMock(MigrationPluginManagerInterface::class, [
      'hasDefinition' => TRUE,
    ]);

    return new ImportPipelineMigrationOrderResolver(
      $configFactory,
      $manager,
      $this->createMock(LoggerInterface::class),
    );
  }

}
