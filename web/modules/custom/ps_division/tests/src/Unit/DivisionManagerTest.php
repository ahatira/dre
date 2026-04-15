<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_division\Unit;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Logger\LoggerChannelInterface;
use Drupal\ps_dictionary\Service\DictionaryManagerInterface;
use Drupal\ps_division\Entity\DivisionInterface;
use Drupal\ps_division\Service\DivisionManager;
use Drupal\ps_surface\Service\SurfaceValidatorInterface;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\ps_division\Service\DivisionManager
 * @group ps_division
 */
final class DivisionManagerTest extends UnitTestCase {

  /**
   * The division manager under test.
   */
  private DivisionManager $divisionManager;

  /**
   * Mock entity type manager.
   */
  private EntityTypeManagerInterface $entityTypeManager;

  /**
   * Mock dictionary manager.
   */
  private DictionaryManagerInterface $dictionaryManager;

  /**
   * Mock cache backend.
   */
  private CacheBackendInterface $cache;

  /**
   * Mock surface validator.
   */
  private SurfaceValidatorInterface $surfaceValidator;

  /**
   * Mock config factory.
   */
  private ConfigFactoryInterface $configFactory;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->entityTypeManager = $this->createMock(EntityTypeManagerInterface::class);
    $this->dictionaryManager = $this->createMock(DictionaryManagerInterface::class);
    $this->cache = $this->createMock(CacheBackendInterface::class);
    $this->surfaceValidator = $this->createMock(SurfaceValidatorInterface::class);
    $this->configFactory = $this->createMock(ConfigFactoryInterface::class);

    $config = $this->createMock(ImmutableConfig::class);
    $config->method('get')->willReturnMap([
      ['dictionaries.division_type', 'surface_type'],
      ['dictionaries.division_nature', 'surface_nature'],
    ]);
    $this->configFactory->method('get')->with('ps_division.settings')->willReturn($config);

    $logger = $this->createMock(LoggerChannelInterface::class);
    $loggerFactory = $this->createMock(LoggerChannelFactoryInterface::class);
    $loggerFactory->method('get')->willReturn($logger);

    $this->divisionManager = new DivisionManager(
      $this->entityTypeManager,
      $this->dictionaryManager,
      $this->surfaceValidator,
      $this->configFactory,
      $this->cache,
      $loggerFactory,
    );
  }

  /**
   * @covers ::getSummary
   */
  public function testGetSummary(): void {
    $division = $this->createMock(DivisionInterface::class);
    $division->method('id')->willReturn(1);
    $division->method('getBuildingName')->willReturn('Building A');
    $division->method('getLot')->willReturn('LOT-001');
    $division->method('getTotalSurface')->willReturn(50.5);
    $division->method('hasField')->willReturn(FALSE);

    $summary = $this->divisionManager->getSummary($division);

    $this->assertEquals(1, $summary['id']);
    $this->assertEquals('Building A', $summary['building_name']);
    $this->assertEquals('LOT-001', $summary['lot']);
    $this->assertEquals(50.5, $summary['total_surface']);
  }

  /**
   * @covers ::validate
   */
  public function testValidate(): void {
    $division = $this->createMock(DivisionInterface::class);
    $division->method('hasField')->willReturn(FALSE);

    $errors = $this->divisionManager->validate($division);

    $this->assertIsArray($errors);
    $this->assertEmpty($errors);
  }

}
