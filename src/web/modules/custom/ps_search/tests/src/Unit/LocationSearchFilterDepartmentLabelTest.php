<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_search\Unit;

use Drupal\Core\Database\Connection;
use Drupal\KernelTests\KernelTestBase;
use Drupal\ps_search\Contract\GeoZoneRepositoryInterface;
use Drupal\ps_search\GeoZone\GeoZoneType;
use Drupal\ps_search\Service\LocationSearchFilter;
use Drupal\ps_search\ValueObject\GeoBoundingBox;
use Drupal\ps_search\ValueObject\GeoZone;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @coversDefaultClass \Drupal\ps_search\Service\LocationSearchFilter
 *
 * @group ps_search
 */
final class LocationSearchFilterDepartmentLabelTest extends KernelTestBase {

  protected static $modules = ['ps_search'];

  /**
   * @covers ::buildDepartmentLabel
   */
  public function testBuildDepartmentLabelUsesDepartmentNameOnly(): void {
    /** @var \Drupal\Core\Database\Connection&MockObject $database */
    $database = $this->createMock(Connection::class);
    $paris = $this->sampleDepartment('75', 'Paris', 'paris-75', 'department.com.75');
    /** @var \Drupal\ps_search\Contract\GeoZoneRepositoryInterface&MockObject $geoZoneRepository */
    $geoZoneRepository = $this->createMock(GeoZoneRepositoryInterface::class);
    $geoZoneRepository->method('findDepartmentByCode')->with('75', 'com')->willReturn($paris);

    $filter = new LocationSearchFilter($database, $geoZoneRepository);

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

    $region = $this->sampleRegion('ile-de-france', 'Île-de-France', 'region.com.ile-de-france');
    /** @var \Drupal\ps_search\Contract\GeoZoneRepositoryInterface&MockObject $geoZoneRepository */
    $geoZoneRepository = $this->createMock(GeoZoneRepositoryInterface::class);
    $geoZoneRepository->method('isRegionToken')->willReturnCallback(
      static fn (string $token): bool => str_starts_with($token, GeoZoneRepositoryInterface::REGION_TOKEN_PREFIX),
    );
    $geoZoneRepository->method('parseRegionToken')->willReturnCallback(
      static function (string $token): ?string {
        if (!str_starts_with($token, GeoZoneRepositoryInterface::REGION_TOKEN_PREFIX)) {
          return NULL;
        }
        $slug = substr($token, strlen(GeoZoneRepositoryInterface::REGION_TOKEN_PREFIX));
        return $slug !== '' ? $slug : NULL;
      },
    );
    $geoZoneRepository->method('findBySlug')->with('ile-de-france', 'com')->willReturn($region);
    $geoZoneRepository->method('children')->with('region.com.ile-de-france')->willReturn([]);

    $filter = new LocationSearchFilter($database, $geoZoneRepository);

    $meta = $filter->resolveTokenMetadata('region:ile-de-france');
    self::assertSame('region', $meta['type']);
    self::assertSame('Île-de-France', $meta['label']);
    self::assertSame('ile-de-france', $meta['region_slug']);
  }

  private function sampleDepartment(string $code, string $label, string $slug, string $id): GeoZone {
    $bbox = GeoBoundingBox::fromCenterAndRadiusKm(48.8566, 2.3522, 20.0);

    return new GeoZone(
      id: $id,
      type: GeoZoneType::Department,
      countryCode: 'com',
      code: $code,
      label: $label,
      slug: $slug,
      lat: 48.8566,
      lng: 2.3522,
      bbox: $bbox,
      postalPrefixes: [$code],
    );
  }

  private function sampleRegion(string $slug, string $label, string $id): GeoZone {
    $bbox = GeoBoundingBox::fromCenterAndRadiusKm(48.5, 2.5, 45.0);

    return new GeoZone(
      id: $id,
      type: GeoZoneType::Region,
      countryCode: 'com',
      code: strtoupper(str_replace('-', '_', $slug)),
      label: $label,
      slug: $slug,
      lat: 48.5,
      lng: 2.5,
      bbox: $bbox,
      postalPrefixes: ['75', '77', '78', '91', '92', '93', '94', '95'],
    );
  }

}
