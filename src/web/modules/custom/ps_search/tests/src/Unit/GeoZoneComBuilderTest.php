<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_search\Unit;

use Drupal\ps_search\GeoZone\GeoZoneComBuilder;
use Drupal\ps_search\GeoZone\GeoZoneValidator;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\ps_search\GeoZone\GeoZoneComBuilder
 */
final class GeoZoneComBuilderTest extends UnitTestCase {

  /**
   * @covers ::clonePayloadForCountry
   */
  public function testClonePayloadForCountryRewritesIdsAndDefaultZone(): void {
    $builder = new GeoZoneComBuilder('modules/custom/ps_search');
    $payload = $builder->clonePayloadForCountry([
      'country' => 'fr',
      'default_zone' => 'department.fr.75',
      'zones' => [
        'department.fr.75' => [
          'type' => 'department',
          'code' => '75',
          'label' => 'Paris',
          'slug' => 'paris-75',
          'lat' => 48.8589,
          'lng' => 2.347,
          'bbox' => [
            'sw_lat' => 48.0,
            'sw_lng' => 2.0,
            'ne_lat' => 49.0,
            'ne_lng' => 3.0,
          ],
          'postal_prefixes' => ['75'],
          'weight' => 75,
        ],
      ],
    ]);

    self::assertSame('com', $payload['country']);
    self::assertSame('department.com.75', $payload['default_zone']);
    self::assertArrayHasKey('department.com.75', $payload['zones']);
    self::assertSame('paris-75', $payload['zones']['department.com.75']['slug']);

    $validator = new GeoZoneValidator();
    $errors = $validator->validateCountryPayload(
      'com',
      $payload['zones'],
      $payload['default_zone'],
    );
    self::assertSame([], $errors);
  }

}
