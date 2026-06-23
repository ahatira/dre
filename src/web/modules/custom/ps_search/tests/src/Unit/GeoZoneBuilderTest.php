<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_search\Unit;

use Drupal\ps_search\GeoZone\GeoZoneBuilder;
use Drupal\ps_search\GeoZone\GeoZoneComBuilder;
use Drupal\ps_search\GeoZone\GeoZoneDefinitionProvider;
use Drupal\ps_search\GeoZone\GeoZonePostalPrefixBuilder;
use Drupal\ps_search\GeoZone\GeoZoneSlugGenerator;
use Drupal\ps_search\GeoZone\GeoZoneValidator;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\ps_search\GeoZone\GeoZoneBuilder
 */
final class GeoZoneBuilderTest extends UnitTestCase {

  /**
   * @covers ::buildFromDefinition
   */
  public function testBuildFromDefinitionCreatesValidPayload(): void {
    $builder = new GeoZoneBuilder(
      $this->createMock(GeoZoneDefinitionProvider::class),
      new GeoZoneComBuilder('modules/custom/ps_search'),
      'modules/custom/ps_search',
    );

    $payload = $builder->buildFromDefinition('be', [
      'zone_type' => 'region',
      'default_code' => 'BRU',
      'divisions' => [
        [
          'code' => 'BRU',
          'label' => 'Brussels-Capital Region',
          'slug' => 'brussels',
          'lat' => 50.8503,
          'lng' => 4.3517,
          'postal_prefixes' => ['10'],
          'radius_km' => 20,
        ],
      ],
    ]);

    self::assertSame('be', $payload['country']);
    self::assertSame('region.be.bru', $payload['default_zone']);
    self::assertArrayHasKey('region.be.bru', $payload['zones']);

    $validator = new GeoZoneValidator();
    $errors = $validator->validateCountryPayload(
      'be',
      $payload['zones'],
      $payload['default_zone'],
    );
    self::assertSame([], $errors);
  }

  /**
   * @covers ::buildFromDefinition
   */
  public function testBuildFromDefinitionUsesExplicitWeight(): void {
    $builder = new GeoZoneBuilder(
      $this->createMock(GeoZoneDefinitionProvider::class),
      new GeoZoneComBuilder('modules/custom/ps_search'),
      'modules/custom/ps_search',
    );

    $payload = $builder->buildFromDefinition('fr', [
      'zone_type' => 'department',
      'default_code' => '75',
      'divisions' => [
        [
          'code' => '75',
          'label' => 'Paris',
          'slug' => 'paris-75',
          'lat' => 48.8589,
          'lng' => 2.347,
          'postal_prefixes' => ['75'],
          'radius_km' => 45,
          'weight' => 75,
        ],
      ],
    ]);

    self::assertSame(75, $payload['zones']['department.fr.75']['weight']);
  }

}
