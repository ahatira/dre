<?php

declare(strict_types=1);

namespace Drupal\ps_search\Service\Isochrone;

use Drupal\Core\Config\ConfigFactoryInterface;
use GuzzleHttp\ClientInterface;
use Psr\Log\LoggerInterface;

/**
 * OpenRouteService isochrones API provider.
 *
 * @see https://openrouteservice.org/dev/#/api-docs/v2/isochrones/{profile}/post
 */
final class OpenRouteServiceIsochroneProvider implements IsochroneProviderInterface {

  private const API_URL = 'https://api.openrouteservice.org/v2/isochrones/';

  private const PROFILE_MAP = [
    'walking' => 'foot-walking',
    'bike' => 'cycling-regular',
    'car' => 'driving-car',
    'transports' => 'driving-car',
  ];

  public function __construct(
    private readonly ClientInterface $httpClient,
    private readonly ConfigFactoryInterface $configFactory,
    private readonly LoggerInterface $logger,
  ) {}

  /**
   *
   */
  public function id(): string {
    return 'ors';
  }

  /**
   *
   */
  public function supportsTransport(string $transport): bool {
    return isset(self::PROFILE_MAP[$transport]);
  }

  /**
   *
   */
  public function build(float $lat, float $lng, string $transport, int $minutes): ?array {
    $settings = $this->configFactory->get('ps_search.map_zone_settings');
    if (!($settings->get('ors_enabled') ?? FALSE)) {
      return NULL;
    }

    $apiKey = trim((string) ($settings->get('ors_api_key') ?? ''));
    if ($apiKey === '') {
      return NULL;
    }

    $profile = self::PROFILE_MAP[$transport] ?? NULL;
    if ($profile === NULL) {
      return NULL;
    }

    try {
      $response = $this->httpClient->request('POST', self::API_URL . $profile, [
        'headers' => [
          'Authorization' => $apiKey,
          'Accept' => 'application/geo+json;charset=UTF-8',
          'Content-Type' => 'application/json',
        ],
        'json' => [
          'locations' => [[$lng, $lat]],
          'range' => [$minutes * 60],
          'range_type' => 'time',
          'units' => 'm',
        ],
        'timeout' => 15,
        'http_errors' => FALSE,
      ]);
    }
    catch (\Throwable $exception) {
      $this->logger->warning('ORS isochrone request failed: @message', [
        '@message' => $exception->getMessage(),
      ]);
      return NULL;
    }

    if ($response->getStatusCode() !== 200) {
      $this->logger->warning('ORS isochrone HTTP @code: @body', [
        '@code' => $response->getStatusCode(),
        '@body' => (string) $response->getBody(),
      ]);
      return NULL;
    }

    $data = json_decode((string) $response->getBody(), TRUE);
    $ring = $this->extractOuterRing($data);
    if ($ring === NULL) {
      return NULL;
    }

    return IsochroneGeoHelper::payloadFromRing(
      $ring,
      $this->id(),
      $transport,
      $minutes,
      $lat,
      $lng,
    );
  }

  /**
   * @param array<string, mixed>|null $data
   *
   * @return list<array{0: float, 1: float}>|null
   */
  private function extractOuterRing(?array $data): ?array {
    if (!is_array($data)) {
      return NULL;
    }

    $coordinates = $data['features'][0]['geometry']['coordinates'][0] ?? NULL;
    if (!is_array($coordinates) || count($coordinates) < 4) {
      return NULL;
    }

    $ring = [];
    foreach ($coordinates as $pair) {
      if (!is_array($pair) || count($pair) < 2) {
        continue;
      }
      $ring[] = [(float) $pair[0], (float) $pair[1]];
    }

    return count($ring) >= 4 ? $ring : NULL;
  }

}
