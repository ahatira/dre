<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_search\Unit;

use Drupal\Core\Database\Connection;
use Drupal\KernelTests\KernelTestBase;
use Drupal\ps_search\Contract\GeoZoneRepositoryInterface;
use Drupal\ps_search\Service\AdministrativeRegionRegistry;
use Drupal\ps_search\Service\LocationSearchFilter;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @coversDefaultClass \Drupal\ps_search\Service\LocationSearchFilter
 *
 * @group ps_search
 */
final class LocationSearchFilterDepartmentLabelTest extends KernelTestBase {

  protected static $modules = ['ps_dictionary', 'ps_search'];

  /**
   * @covers ::buildDepartmentLabel
   */
  public function testBuildDepartmentLabelUsesDepartmentNameOnly(): void {
    /** @var \Drupal\Core\Database\Connection&MockObject $database */
    $database = $this->createMock(Connection::class);
    /** @var \Drupal\ps_search\Contract\GeoZoneRepositoryInterface&MockObject $geoZoneRepository */
    $geoZoneRepository = $this->createMock(GeoZoneRepositoryInterface::class);
    $regionRegistry = new AdministrativeRegionRegistry('modules/custom/ps_search');
    $filter = new LocationSearchFilter(
      $database,
      $this->container->get('ps_dictionary.resolver'),
      $regionRegistry,
      $geoZoneRepository,
    );

    self::assertSame('Paris (75)', $filter->buildDepartmentLabel('75'));
  }

  /**
   * @covers ::resolveTokenMetadata
   */
  public function testRegionTokenMetadata(): void {
    /** @var \Drupal\Core\Database\Connection&MockObject $database */
    $database = $this->createMock(Connection::class);
    $statement = $this->createMock(\Drupal\Core\Database\StatementInterface::class);
    $statement->method('fetchAssoc')->willReturn(FALSE);
    $select = $this->createMock(\Drupal\Core\Database\Query\SelectInterface::class);
    $select->method('fields')->willReturnSelf();
    $select->method('condition')->willReturnSelf();
    $select->method('range')->willReturnSelf();
    $select->method('addExpression')->willReturnSelf();
    $select->method('innerJoin')->willReturnSelf();
    $select->method('orConditionGroup')->willReturn(new \Drupal\Core\Database\Query\Condition('AND'));
    $select->method('execute')->willReturn($statement);
    $database->method('select')->willReturn($select);

    /** @var \Drupal\ps_search\Contract\GeoZoneRepositoryInterface&MockObject $geoZoneRepository */
    $geoZoneRepository = $this->createMock(GeoZoneRepositoryInterface::class);
    $regionRegistry = new AdministrativeRegionRegistry('modules/custom/ps_search');
    $filter = new LocationSearchFilter(
      $database,
      $this->container->get('ps_dictionary.resolver'),
      $regionRegistry,
      $geoZoneRepository,
    );

    $meta = $filter->resolveTokenMetadata('region:ile-de-france');
    self::assertSame('region', $meta['type']);
    self::assertSame('Île-de-France', $meta['label']);
    self::assertSame('ile-de-france', $meta['region_slug']);
  }

}
