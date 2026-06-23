<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_search\Unit;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\ps_search\GeoZone\GeoZoneRepository;
use Drupal\ps_search\GeoZone\GeoZoneValidator;
use Drupal\ps_search\ValueObject\GeoBoundingBox;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\ps_search\GeoZone\GeoZoneRepository
 */
final class GeoZoneRepositoryTest extends UnitTestCase {

  /**
   * @covers ::findBySlug
   * @covers ::findByPostalPrefix
   * @covers ::getDefaultForCountry
   */
  public function testRepositoryIndexesSlugAndPostalPrefix(): void {
    $repository = new GeoZoneRepository($this->createConfigFactory([
      'ps_search.geo_zones.fr' => [
        'country' => 'fr',
        'default_zone' => 'department.fr.75',
        'zones' => [
          'department.fr.75' => $this->sampleDepartment75(),
          'department.fr.69' => $this->sampleDepartment69(),
        ],
      ],
    ]));

    $paris = $repository->findBySlug('paris-75', 'fr');
    self::assertNotNull($paris);
    self::assertSame('department.fr.75', $paris->id);

    $byPostal = $repository->findByPostalPrefix('75017', 'fr');
    self::assertNotNull($byPostal);
    self::assertSame('department.fr.75', $byPostal->id);

    $default = $repository->getDefaultForCountry('fr');
    self::assertNotNull($default);
    self::assertSame('Paris', $default->label);
  }

  /**
   * @return array<string, mixed>
   */
  private function sampleDepartment75(): array {
    $bbox = GeoBoundingBox::fromCenterAndRadiusKm(48.8566, 2.3522, 20.0);

    return [
      'type' => 'department',
      'code' => '75',
      'label' => 'Paris',
      'slug' => 'paris-75',
      'lat' => 48.8566,
      'lng' => 2.3522,
      'bbox' => $bbox->toConfigArray(),
      'postal_prefixes' => ['75'],
      'weight' => 75,
    ];
  }

  /**
   * @return array<string, mixed>
   */
  private function sampleDepartment69(): array {
    $bbox = GeoBoundingBox::fromCenterAndRadiusKm(45.7640, 4.8357, 45.0);

    return [
      'type' => 'department',
      'code' => '69',
      'label' => 'Rhône',
      'slug' => 'rhone-69',
      'lat' => 45.7640,
      'lng' => 4.8357,
      'bbox' => $bbox->toConfigArray(),
      'postal_prefixes' => ['69'],
      'weight' => 69,
    ];
  }

  /**
   * @param array<string, array<string, mixed>> $configs
   */
  private function createConfigFactory(array $configs): ConfigFactoryInterface {
    $factory = $this->createMock(ConfigFactoryInterface::class);
    $factory->method('listAll')->willReturn(array_keys($configs));
    $factory->method('get')->willReturnCallback(function (string $name) use ($configs): ImmutableConfig {
      $config = $this->createMock(ImmutableConfig::class);
      $data = $configs[$name] ?? ['zones' => []];
      $config->method('get')->willReturnCallback(function (string $key) use ($data) {
        return $data[$key] ?? NULL;
      });

      return $config;
    });

    return $factory;
  }

}
