<?php

declare(strict_types=1);

namespace Drupal\ps_search\Service\Isochrone;

use Drupal\Core\Config\ConfigFactoryInterface;
use GuzzleHttp\ClientInterface;
use Psr\Log\LoggerInterface;

/**
 * Google Routes API isochrone via radial route sampling.
 *
 * Google has no native isochrone polygon API. This provider probes reachable
 * points on several bearings using computeRoutes, then builds a polygon.
 *
 * @see https://developers.google.com/maps/documentation/routes/compute_route_directions
 */
final class GoogleRoutesIsochroneProvider implements IsochroneProviderInterface {

  private const ROUTES_URL = 'https://routes.googleapis.com/directions/v2:computeRoutes';

  private const BEARING_COUNT = 12;

  private const TRAVEL_MODE_MAP = [
    'walking' => 'WALK',
    'bike' => 'BICYCLE',
    'car' => 'DRIVE',
    'transports' => 'TRANSIT',
  ];

  public function __construct(
    private readonly ClientInterface $httpClient,
    private readonly ConfigFactoryInterface $configFactory,
    private readonly LoggerInterface $logger,
    private readonly IsochroneApproximationProvider $approximationProvider,
  ) {}

  /**
   *
   */
  public function id(): string {
    return 'google_routes';
  }

  /**
   *
   */
  public function supportsTransport(string $transport): bool {
    return isset(self::TRAVEL_MODE_MAP[$transport]);
  }

  /**
   *
   */
  public function build(float $lat, float $lng, string $transport, int $minutes): ?array {
    $apiKey = $this->resolveGoogleApiKey();
    if ($apiKey === '') {
      return NULL;
    }

    $travelMode = self::TRAVEL_MODE_MAP[$transport] ?? NULL;
    if ($travelMode === NULL) {
      return NULL;
    }

    $targetSeconds = $minutes * 60;
    $probeRadiusM = $this->approximationProvider->estimateRadiusMeters($transport, $minutes);
    $ring = [];

    for ($step = 0; $step < self::BEARING_COUNT; $step++) {
      $bearing = 360 * $step / self::BEARING_COUNT;
      $probe = IsochroneGeoHelper::destinationAtBearing($lat, $lng, $bearing, (float) $probeRadiusM);
      $duration = $this->fetchRouteDurationSeconds($lat, $lng, $probe['lat'], $probe['lng'], $travelMode, $apiKey);
      if ($duration === NULL || $duration <= 0) {
        continue;
      }

      $scale = min(1.0, $targetSeconds / $duration);
      $reachable = IsochroneGeoHelper::destinationAtBearing(
        $lat,
        $lng,
        $bearing,
        (float) $probeRadiusM * $scale,
      );
      $ring[] = [$reachable['lng'], $reachable['lat']];
    }

    if (count($ring) < 3) {
      $this->logger->warning('Google Routes isochrone returned insufficient probe points.');
      return NULL;
    }

    $ring[] = $ring[0];

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
   *
   */
  private function resolveGoogleApiKey(): string {
    $gmapKey = trim((string) ($this->configFactory->get('geofield_map.settings')->get('gmap_api_key') ?? ''));
    if ($gmapKey !== '') {
      return $gmapKey;
    }

    return trim((string) ($this->configFactory->get('geocoder.geocoder_provider.google_maps')->get('configuration.apiKey') ?? ''));
  }

  /**
   *
   */
  private function fetchRouteDurationSeconds(
    float $originLat,
    float $originLng,
    float $destLat,
    float $destLng,
    string $travelMode,
    string $apiKey,
  ): ?float {
    $body = [
      'origin' => [
        'location' => [
          'latLng' => [
            'latitude' => $originLat,
            'longitude' => $originLng,
          ],
        ],
      ],
      'destination' => [
        'location' => [
          'latLng' => [
            'latitude' => $destLat,
            'longitude' => $destLng,
          ],
        ],
      ],
      'travelMode' => $travelMode,
      'computeAlternativeRoutes' => FALSE,
    ];

    if ($travelMode === 'DRIVE') {
      $body['routingPreference'] = 'TRAFFIC_AWARE';
    }

    try {
      $response = $this->httpClient->request('POST', self::ROUTES_URL, [
        'headers' => [
          'Content-Type' => 'application/json',
          'X-Goog-Api-Key' => $apiKey,
          'X-Goog-FieldMask' => 'routes.duration',
        ],
        'json' => $body,
        'timeout' => 15,
        'http_errors' => FALSE,
      ]);
    }
    catch (\Throwable $exception) {
      $this->logger->warning('Google Routes isochrone request failed: @message', [
        '@message' => $exception->getMessage(),
      ]);
      return NULL;
    }

    if ($response->getStatusCode() !== 200) {
      $this->logger->warning('Google Routes isochrone HTTP @code: @body', [
        '@code' => $response->getStatusCode(),
        '@body' => (string) $response->getBody(),
      ]);
      return NULL;
    }

    $data = json_decode((string) $response->getBody(), TRUE);
    $durationRaw = $data['routes'][0]['duration'] ?? NULL;
    if (!is_string($durationRaw) || !str_ends_with($durationRaw, 's')) {
      return NULL;
    }

    return (float) rtrim($durationRaw, 's');
  }

}
