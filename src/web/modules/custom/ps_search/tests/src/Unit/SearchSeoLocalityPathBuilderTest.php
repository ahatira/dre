<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_search\Unit;

use Drupal\Core\Database\Connection;
use Drupal\KernelTests\KernelTestBase;
use Drupal\ps_search\Contract\GeoZoneRepositoryInterface;
use Drupal\ps_search\Service\AdministrativeRegionRegistry;
use Drupal\ps_search\Service\SearchSeoLocalityPathBuilder;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @coversDefaultClass \Drupal\ps_search\Service\SearchSeoLocalityPathBuilder
 *
 * @group ps_search
 */
final class SearchSeoLocalityPathBuilderTest extends KernelTestBase {

  protected static $modules = ['ps_dictionary', 'ps_search'];

  private SearchSeoLocalityPathBuilder $builder;

  protected function setUp(): void {
    parent::setUp();
    /** @var \Drupal\Core\Database\Connection&MockObject $database */
    $database = $this->createMock(Connection::class);
    /** @var \Drupal\ps_search\Contract\GeoZoneRepositoryInterface&MockObject $geoZoneRepository */
    $geoZoneRepository = $this->createMock(GeoZoneRepositoryInterface::class);
    $this->builder = new SearchSeoLocalityPathBuilder(
      $database,
      $this->container->get('ps_dictionary.resolver'),
      $geoZoneRepository,
      new AdministrativeRegionRegistry('modules/custom/ps_search'),
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

}
