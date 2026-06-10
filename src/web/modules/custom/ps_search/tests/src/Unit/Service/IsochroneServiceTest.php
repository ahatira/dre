<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_search\Unit\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\Logger\LoggerChannelInterface;
use Drupal\ps_search\Service\Isochrone\GoogleRoutesIsochroneProvider;
use Drupal\ps_search\Service\Isochrone\IsochroneApproximationProvider;
use Drupal\ps_search\Service\Isochrone\OpenRouteServiceIsochroneProvider;
use Drupal\ps_search\Service\IsochroneService;
use Drupal\Tests\UnitTestCase;
use GuzzleHttp\ClientInterface;

/**
 * @coversDefaultClass \Drupal\ps_search\Service\IsochroneService
 * @group ps_search
 */
final class IsochroneServiceTest extends UnitTestCase {

  /**
   * @covers ::build
   */
  public function testBuildWalkingIsochrone(): void {
    $service = $this->createService();

    $payload = $service->build(48.8566, 2.3522, 'walking', 5);

    $this->assertSame('approximation', $payload['provider']);
    $this->assertSame('walking', $payload['transport']);
    $this->assertSame(5, $payload['minutes']);
    $this->assertSame(400, $payload['radius_m']);
    $this->assertNotEmpty($payload['map_bounds']);
    $this->assertCount(1, $payload['polygon']);
    $this->assertGreaterThanOrEqual(4, count($payload['polygon'][0]));
  }

  /**
   * @covers ::build
   */
  public function testBuildRejectsInvalidTransport(): void {
    $service = $this->createService();
    $this->expectException(\InvalidArgumentException::class);
    $service->build(48.8566, 2.3522, 'plane', 5);
  }

  /**
   * @covers ::build
   */
  public function testBuildFallsBackWhenOrsUnavailable(): void {
    $service = $this->createService('ors', TRUE);
    $payload = $service->build(48.8566, 2.3522, 'walking', 5);

    $this->assertSame('approximation', $payload['provider']);
    $this->assertTrue($payload['fallback']);
    $this->assertSame('ors', $payload['requested_provider']);
  }

  /**
   * @covers ::build
   */
  public function testBuildUsesApproximationWhenOrsDisabled(): void {
    $service = $this->createService('ors', FALSE);
    $payload = $service->build(48.8566, 2.3522, 'walking', 5);

    $this->assertSame('approximation', $payload['provider']);
    $this->assertArrayNotHasKey('fallback', $payload);
  }

  private function createService(string $provider = 'approximation', bool $orsEnabled = FALSE): IsochroneService {
    $config = $this->createMock(ImmutableConfig::class);
    $config->method('get')->willReturnMap([
      ['isochrone_provider', $provider],
      ['isochrone_fallback', TRUE],
      ['ors_enabled', $orsEnabled],
      ['ors_api_key', ''],
      ['google_routes_api_key', ''],
      ['gmap_api_key', ''],
    ]);

    $configFactory = $this->createMock(ConfigFactoryInterface::class);
    $configFactory->method('get')->willReturnCallback(static function (string $name) use ($config) {
      if ($name === 'ps_search.map_zone_settings') {
        return $config;
      }
      return $config;
    });

    $logger = $this->createMock(LoggerChannelInterface::class);
    $httpClient = $this->createMock(ClientInterface::class);
    $approximation = new IsochroneApproximationProvider();

    return new IsochroneService(
      $configFactory,
      $approximation,
      new OpenRouteServiceIsochroneProvider($httpClient, $configFactory, $logger),
      new GoogleRoutesIsochroneProvider($httpClient, $configFactory, $logger, $approximation),
      $logger,
    );
  }

}
