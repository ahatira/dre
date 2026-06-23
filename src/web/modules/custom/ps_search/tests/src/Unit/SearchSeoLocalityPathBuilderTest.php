<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_search\Unit;

use Drupal\Core\Database\Connection;
use Drupal\KernelTests\KernelTestBase;
use Drupal\ps_search\Contract\GeoZoneRepositoryInterface;
use Drupal\ps_search\GeoZone\GeoZoneType;
use Drupal\ps_search\Service\SearchSeoLocalityPathBuilder;
use Drupal\ps_search\ValueObject\GeoBoundingBox;
use Drupal\ps_search\ValueObject\GeoZone;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @coversDefaultClass \Drupal\ps_search\Service\SearchSeoLocalityPathBuilder
 *
 * @group ps_search
 */
final class SearchSeoLocalityPathBuilderTest extends KernelTestBase {

  protected static $modules = ['ps_search'];

  private SearchSeoLocalityPathBuilder $builder;

  protected function setUp(): void {
    parent::setUp();
    /** @var \Drupal\Core\Database\Connection&MockObject $database */
    $database = $this->createMock(Connection::class);
    $this->builder = new SearchSeoLocalityPathBuilder(
      $database,
      $this->createGeoZoneRepository(),
    );
  }

  /**
   * @covers ::singleSegmentToToken
   * @covers ::parseLocalitySegments
   * @dataProvider departmentSegmentProvider
   */
  public function testDepartmentSegmentResolvesToDepartmentToken(string $segment, string $expected): void {
    self::assertSame($expected, $this->builder->singleSegmentToToken($segment));
    self::assertSame($expected, $this->builder->parseLocalitySegments([$segment]));
  }

  /**
   * @return iterable<string, array{string, string}>
   */
  public static function departmentSegmentProvider(): iterable {
    yield 'paris dept slug' => ['paris-75', '75'];
    yield 'rhone dept slug' => ['rhone-69', '69'];
    yield 'bouches du rhone dept slug' => ['bouches-du-rhone-13', '13'];
  }

  /**
   * @covers ::singleSegmentToToken
   * @covers ::parseLocalitySegments
   */
  public function testRegionSegmentResolvesToRegionToken(): void {
    self::assertSame('region:ile-de-france', $this->builder->singleSegmentToToken('ile-de-france'));
    self::assertSame('region:ile-de-france', $this->builder->parseLocalitySegments(['ile-de-france']));
  }

  /**
   * @covers ::parseLocalitySegments
   */
  public function testDeptAndCitySegmentsResolveToPostalToken(): void {
    self::assertSame('75009', $this->builder->parseLocalitySegments(['paris-75', 'paris-9-75009']));
    self::assertSame('13001', $this->builder->parseLocalitySegments(['bouches-du-rhone-13', 'marseille-1-13001']));
  }

  /**
   * @covers ::citySegmentToToken
   * @dataProvider citySegmentProvider
   */
  public function testCitySegmentParsing(string $segment, string $expected): void {
    self::assertSame($expected, $this->builder->citySegmentToToken($segment));
  }

  /**
   * @return iterable<string, array{string, string}>
   */
  public static function citySegmentProvider(): iterable {
    yield 'postal code' => ['paris-75015', '75015'];
    yield 'arrondissement' => ['paris-17-75017', '75017'];
    yield 'city slug only' => ['lyon', 'Lyon'];
  }

  /**
   * @covers ::singleSegmentToToken
   */
  public function testLegacyRegionDeptSlugStillParsesAsDepartment(): void {
    self::assertSame('75', $this->builder->singleSegmentToToken('ile-de-france-75'));
  }

  /**
   * @return \Drupal\ps_search\Contract\GeoZoneRepositoryInterface&MockObject
   */
  private function createGeoZoneRepository(): GeoZoneRepositoryInterface {
    $bbox = GeoBoundingBox::fromCenterAndRadiusKm(48.8566, 2.3522, 20.0);
    $zones = [
      'paris-75' => new GeoZone(
        id: 'department.com.75',
        type: GeoZoneType::Department,
        countryCode: 'com',
        code: '75',
        label: 'Paris',
        slug: 'paris-75',
        lat: 48.8566,
        lng: 2.3522,
        bbox: $bbox,
        postalPrefixes: ['75'],
      ),
      'rhone-69' => new GeoZone(
        id: 'department.com.69',
        type: GeoZoneType::Department,
        countryCode: 'com',
        code: '69',
        label: 'Rhône',
        slug: 'rhone-69',
        lat: 45.7640,
        lng: 4.8357,
        bbox: $bbox,
        postalPrefixes: ['69'],
      ),
      'bouches-du-rhone-13' => new GeoZone(
        id: 'department.com.13',
        type: GeoZoneType::Department,
        countryCode: 'com',
        code: '13',
        label: 'Bouches-du-Rhône',
        slug: 'bouches-du-rhone-13',
        lat: 43.3,
        lng: 5.4,
        bbox: $bbox,
        postalPrefixes: ['13'],
      ),
      'ile-de-france' => new GeoZone(
        id: 'region.com.ile-de-france',
        type: GeoZoneType::Region,
        countryCode: 'com',
        code: 'ILE_DE_FRANCE',
        label: 'Île-de-France',
        slug: 'ile-de-france',
        lat: 48.5,
        lng: 2.5,
        bbox: $bbox,
        postalPrefixes: ['75'],
      ),
    ];

    /** @var \Drupal\ps_search\Contract\GeoZoneRepositoryInterface&MockObject $repository */
    $repository = $this->createMock(GeoZoneRepositoryInterface::class);
    $repository->method('findBySlug')->willReturnCallback(
      static function (string $slug, string $countryCode) use ($zones): ?GeoZone {
        return $countryCode === 'com' ? ($zones[$slug] ?? NULL) : NULL;
      },
    );
    $repository->method('findByPostalPrefix')->willReturnCallback(
      static function (string $prefix, string $countryCode) use ($zones): ?GeoZone {
        if ($countryCode !== 'com') {
          return NULL;
        }
        foreach ($zones as $zone) {
          if ($zone->type === GeoZoneType::Department && $zone->code === $prefix) {
            return $zone;
          }
        }
        return NULL;
      },
    );
    $repository->method('findDepartmentByCode')->willReturnCallback(
      static function (string $code, string $countryCode) use ($zones): ?GeoZone {
        if ($countryCode !== 'com') {
          return NULL;
        }
        foreach ($zones as $zone) {
          if ($zone->type === GeoZoneType::Department && $zone->code === $code) {
            return $zone;
          }
        }
        return NULL;
      },
    );
    $repository->method('buildRegionToken')->willReturnCallback(
      static fn (string $slug): string => GeoZoneRepositoryInterface::REGION_TOKEN_PREFIX . $slug,
    );
    $repository->method('isRegionToken')->willReturnCallback(
      static fn (string $token): bool => str_starts_with($token, GeoZoneRepositoryInterface::REGION_TOKEN_PREFIX),
    );
    $repository->method('parseRegionToken')->willReturnCallback(
      static function (string $token): ?string {
        if (!str_starts_with($token, GeoZoneRepositoryInterface::REGION_TOKEN_PREFIX)) {
          return NULL;
        }
        $slug = substr($token, strlen(GeoZoneRepositoryInterface::REGION_TOKEN_PREFIX));
        return $slug !== '' ? $slug : NULL;
      },
    );

    return $repository;
  }

}
