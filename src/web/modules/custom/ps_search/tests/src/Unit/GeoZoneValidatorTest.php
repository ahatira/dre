<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_search\Unit;

use Drupal\ps_search\GeoZone\GeoZoneValidator;
use Drupal\ps_search\ValueObject\GeoBoundingBox;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\ps_search\GeoZone\GeoZoneValidator
 */
final class GeoZoneValidatorTest extends UnitTestCase {

  private GeoZoneValidator $validator;

  protected function setUp(): void {
    parent::setUp();
    $this->validator = new GeoZoneValidator();
  }

  /**
   * @covers ::validateCountryPayload
   */
  public function testValidDepartmentPayloadPasses(): void {
    $bbox = GeoBoundingBox::fromCenterAndRadiusKm(48.8566, 2.3522, 20.0);
    $zones = [
      'department.fr.75' => [
        'type' => 'department',
        'code' => '75',
        'label' => 'Paris',
        'slug' => 'paris-75',
        'lat' => 48.8566,
        'lng' => 2.3522,
        'bbox' => $bbox->toConfigArray(),
        'postal_prefixes' => ['75'],
      ],
    ];

    self::assertSame([], $this->validator->validateCountryPayload('fr', $zones, 'department.fr.75'));
  }

  /**
   * @covers ::validateCountryPayload
   */
  public function testDepartmentWithoutPostalPrefixesFails(): void {
    $bbox = GeoBoundingBox::fromCenterAndRadiusKm(48.8566, 2.3522, 20.0);
    $zones = [
      'department.fr.75' => [
        'type' => 'department',
        'code' => '75',
        'label' => 'Paris',
        'slug' => 'paris-75',
        'lat' => 48.8566,
        'lng' => 2.3522,
        'bbox' => $bbox->toConfigArray(),
        'postal_prefixes' => [],
      ],
    ];

    $errors = $this->validator->validateCountryPayload('fr', $zones);
    self::assertNotEmpty($errors);
    self::assertStringContainsString('postal_prefixes', $errors[0]);
  }

  /**
   * @covers ::validateCountryPayload
   */
  public function testDuplicateSlugFails(): void {
    $bbox = GeoBoundingBox::fromCenterAndRadiusKm(48.8566, 2.3522, 20.0);
    $zones = [
      'department.fr.75' => [
        'type' => 'department',
        'code' => '75',
        'label' => 'Paris',
        'slug' => 'paris-75',
        'lat' => 48.8566,
        'lng' => 2.3522,
        'bbox' => $bbox->toConfigArray(),
        'postal_prefixes' => ['75'],
      ],
      'department.fr.69' => [
        'type' => 'department',
        'code' => '69',
        'label' => 'Rhône',
        'slug' => 'paris-75',
        'lat' => 45.7640,
        'lng' => 4.8357,
        'bbox' => $bbox->toConfigArray(),
        'postal_prefixes' => ['69'],
      ],
    ];

    $errors = $this->validator->validateCountryPayload('fr', $zones);
    self::assertNotEmpty($errors);
    self::assertStringContainsString('Duplicate slug', $errors[0]);
  }

}
