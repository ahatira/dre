<?php

declare(strict_types=1);

namespace Drupal\ps_compare\Service;

use Drupal\Core\State\StateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;
use Drupal\geofield\Plugin\Field\FieldType\GeofieldItem;
use Drupal\node\NodeInterface;
use Drupal\ps_compare\CompareRenderContext;
use Drupal\ps_offer\Service\GoogleMapsSettings;
use Drupal\ps_offer\Service\OfferMapLocationBuilder;
use Drupal\ps_offer\Service\OfferMapSettings;
use GuzzleHttp\ClientInterface;

/**
 * Builds compact location cells with static mini-maps for the compare table.
 */
final class CompareMiniMapBuilder {

  use StringTranslationTrait;

  private const MAP_WIDTH = 280;

  private const MAP_HEIGHT = 120;

  private const GOOGLE_STATIC_MAP_STATE_KEY = 'ps_compare.google_static_map_status';

  private const GOOGLE_STATIC_MAP_RECHECK_SECONDS = 86400;

  public function __construct(
    private readonly OfferMapLocationBuilder $mapLocationBuilder,
    private readonly OfferMapSettings $mapSettings,
    private readonly GoogleMapsSettings $googleMapsSettings,
    private readonly CompareEmailImageEncoder $emailImageEncoder,
    private readonly ClientInterface $httpClient,
    private readonly StateInterface $state,
  ) {}

  /**
   * Builds a location table cell (address line + optional static map).
   *
   * @return array<string, mixed>
   */
  public function buildLocationCell(NodeInterface $offer, string $context = CompareRenderContext::PAGE): array {
    $label = $this->mapLocationBuilder->buildLocationLine($offer);
    $map = $this->buildMapElement($offer, $context);

    if ($label === '' && $map === []) {
      return [
        '#markup' => $this->t('—'),
        '#allowed_tags' => [],
        '#ps_compare_empty' => TRUE,
      ];
    }

    return [
      '#theme' => 'ps_compare_location_cell',
      '#label' => $label,
      '#map' => $map,
      '#cache' => [
        'tags' => array_merge(
          $offer->getCacheTags(),
          $this->mapSettings->getCacheTags(),
          ['config:geofield_map.settings', 'config:geocoder.geocoder_provider.google_maps'],
        ),
      ],
    ];
  }

  /**
   * @return array<string, mixed>
   */
  private function buildMapElement(NodeInterface $offer, string $context): array {
    $coordinates = $this->resolveCoordinates($offer);
    if ($coordinates === NULL) {
      return [];
    }

    $exact = $this->mapLocationBuilder->showsExactAddress($offer);
    $lat = $coordinates['lat'];
    $lng = $coordinates['lng'];
    $zoom = $this->resolveMapZoom($offer, $exact);

    $locality = $this->mapLocationBuilder->buildLocalityLabel($offer);
    $alt = $locality !== ''
      ? (string) $this->t('Map for @locality', ['@locality' => $locality])
      : (string) $this->t('Property location map');

    $mapsLink = Url::fromUri(sprintf(
      'https://www.google.com/maps/search/?api=1&query=%F,%F',
      $lat,
      $lng,
    ));

    if ($context === CompareRenderContext::EMAIL) {
      return $this->buildEmailMapElement($offer, $lat, $lng, $exact, $alt);
    }

    $apiKey = $this->googleMapsSettings->getApiKey();
    if ($apiKey !== '') {
      $googleUrl = $this->buildGoogleStaticMapUrl($offer, $lat, $lng, $exact, $apiKey);
      if ($this->isGoogleStaticMapUrlAvailable($googleUrl)) {
        return $this->buildLinkedStaticMapElement($googleUrl, $mapsLink, $alt, 'img');
      }
    }

    return $this->buildLinkedStaticMapElement(
      $this->buildOsmEmbedUrl($lat, $lng, $zoom, $exact),
      $mapsLink,
      $alt,
      'iframe',
    );
  }

  /**
   * @return array<string, mixed>
   */
  private function buildEmailMapElement(
    NodeInterface $offer,
    float $lat,
    float $lng,
    bool $exact,
    string $alt,
  ): array {
    $apiKey = $this->googleMapsSettings->getApiKey();
    $mapUrl = NULL;
    if ($apiKey !== '') {
      $googleUrl = $this->buildGoogleStaticMapUrl($offer, $lat, $lng, $exact, $apiKey);
      $mapUrl = $this->emailImageEncoder->encodeRemoteImage($googleUrl);
    }

    if ($mapUrl === NULL) {
      $mapUrl = $this->emailImageEncoder->encodeRemoteImage(
        $this->buildOsmStaticMapUrl($lat, $lng),
      );
    }

    if ($mapUrl === NULL) {
      return [];
    }

    return [
      '#type' => 'html_tag',
      '#tag' => 'img',
      '#attributes' => [
        'src' => $mapUrl,
        'alt' => $alt,
        'class' => ['ps-compare-location-cell__map'],
        'width' => (string) self::MAP_WIDTH,
        'height' => (string) self::MAP_HEIGHT,
      ],
    ];
  }

  /**
   * @return array<string, mixed>
   */
  private function buildLinkedStaticMapElement(
    string $mapUrl,
    Url $mapsLink,
    string $alt,
    string $tag,
  ): array {
    $attributes = [
      'src' => $mapUrl,
      'class' => ['ps-compare-location-cell__map'],
      'width' => (string) self::MAP_WIDTH,
      'height' => (string) self::MAP_HEIGHT,
      'loading' => 'lazy',
    ];

    if ($tag === 'img') {
      $attributes['alt'] = $alt;
      $attributes['decoding'] = 'async';
    }
    else {
      $attributes['title'] = $alt;
    }

    return [
      '#type' => 'link',
      '#title' => [
        '#type' => 'html_tag',
        '#tag' => $tag,
        '#attributes' => $attributes,
      ],
      '#url' => $mapsLink,
      '#attributes' => [
        'class' => ['ps-compare-location-cell__map-link', 'd-inline-block'],
        'target' => '_blank',
        'rel' => 'noopener noreferrer',
        'aria-label' => $alt,
      ],
    ];
  }

  /**
   * @return array{lat: float, lng: float}|null
   */
  private function resolveCoordinates(NodeInterface $offer): ?array {
    if (!$offer->hasField('field_geo') || $offer->get('field_geo')->isEmpty()) {
      return NULL;
    }

    $item = $offer->get('field_geo')->first();
    if (!$item instanceof GeofieldItem) {
      return NULL;
    }

    $lat = $item->lat;
    $lng = $item->lon;
    if ($lat === NULL || $lng === NULL) {
      return NULL;
    }

    return ['lat' => (float) $lat, 'lng' => (float) $lng];
  }

  private function resolveMapZoom(NodeInterface $offer, bool $exact): int {
    $settings = $this->mapSettings->getClientMapSettings($offer);
    $locality = '';
    if ($offer->hasField('field_address') && !$offer->get('field_address')->isEmpty()) {
      $locality = trim((string) ($offer->get('field_address')->first()->locality ?? ''));
    }
    $isLargeCity = (bool) ($settings['isLargeCity'] ?? FALSE);

    $zoom = $exact
      ? (int) ($settings['zoomExact'] ?? 15)
      : ($isLargeCity
        ? (int) ($settings['zoomApproxLargeCity'] ?? 14)
        : (int) ($settings['zoomApprox'] ?? 13));

    return max(8, min(18, $zoom));
  }

  private function isGoogleStaticMapUrlAvailable(string $url): bool {
    $status = $this->state->get(self::GOOGLE_STATIC_MAP_STATE_KEY);
    if (is_array($status)
      && isset($status['available'], $status['checked'])
      && (time() - (int) $status['checked']) < self::GOOGLE_STATIC_MAP_RECHECK_SECONDS) {
      return (bool) $status['available'];
    }

    $available = FALSE;
    try {
      $response = $this->httpClient->request('GET', $url, [
        'timeout' => 5,
        'http_errors' => FALSE,
      ]);
      $contentType = $response->getHeaderLine('Content-Type');
      $available = $response->getStatusCode() === 200
        && str_starts_with($contentType, 'image/');
    }
    catch (\Throwable) {
      $available = FALSE;
    }

    $this->state->set(self::GOOGLE_STATIC_MAP_STATE_KEY, [
      'available' => $available,
      'checked' => time(),
    ]);

    return $available;
  }

  private function buildOsmEmbedUrl(float $lat, float $lng, int $zoom, bool $exact): string {
    $lngDelta = 0.03 * (14 / max(8, $zoom));
    $latDelta = 0.02 * (14 / max(8, $zoom));

    $params = [
      'bbox' => sprintf('%F,%F,%F,%F', $lng - $lngDelta, $lat - $latDelta, $lng + $lngDelta, $lat + $latDelta),
      'layer' => 'mapnik',
    ];

    if ($exact) {
      $params['marker'] = sprintf('%F,%F', $lat, $lng);
    }

    return 'https://www.openstreetmap.org/export/embed.html?' . http_build_query($params, '', '&', PHP_QUERY_RFC3986);
  }

  private function buildGoogleStaticMapUrl(
    NodeInterface $offer,
    float $lat,
    float $lng,
    bool $exact,
    string $apiKey,
  ): string {
    $settings = $this->mapSettings->getClientMapSettings($offer);
    $isLargeCity = (bool) ($settings['isLargeCity'] ?? FALSE);
    $zoom = $this->resolveMapZoom($offer, $exact);

    $params = [
      'center' => sprintf('%F,%F', $lat, $lng),
      'zoom' => (string) $zoom,
      'size' => self::MAP_WIDTH . 'x' . self::MAP_HEIGHT,
      'scale' => '2',
      'maptype' => 'roadmap',
      'key' => $apiKey,
    ];

    if ($exact) {
      $params['markers'] = sprintf('color:green|%F,%F', $lat, $lng);
    }
    else {
      $radius = $isLargeCity
        ? (int) ($settings['circleRadiusLargeCity'] ?? 1000)
        : (int) ($settings['circleRadius'] ?? 2500);
      $params['path'] = $this->buildCirclePath($lat, $lng, max(250, $radius));
    }

    return 'https://maps.googleapis.com/maps/api/staticmap?' . http_build_query($params, '', '&', PHP_QUERY_RFC3986);
  }

  private function buildOsmStaticMapUrl(float $lat, float $lng): string {
    return sprintf(
      'https://staticmap.openstreetmap.de/staticmap.php?center=%F,%F&zoom=13&size=%dx%d&markers=%F,%F,red',
      $lat,
      $lng,
      self::MAP_WIDTH,
      self::MAP_HEIGHT,
      $lat,
      $lng,
    );
  }

  /**
   * Encodes an approximate location circle for Google Static Maps.
   */
  private function buildCirclePath(float $lat, float $lng, int $radiusMeters): string {
    $points = [];
    $steps = 24;
    $latRadians = deg2rad($lat);
    $metersPerDegreeLat = 111320.0;
    $metersPerDegreeLng = max(cos($latRadians) * $metersPerDegreeLat, 1.0);

    for ($step = 0; $step <= $steps; $step++) {
      $angle = (2 * M_PI * $step) / $steps;
      $pointLat = $lat + (($radiusMeters / $metersPerDegreeLat) * cos($angle));
      $pointLng = $lng + (($radiusMeters / $metersPerDegreeLng) * sin($angle));
      $points[] = sprintf('%F,%F', $pointLat, $pointLng);
    }

    return 'fillcolor:0x00915A33|color:0x00915A00|weight:1|' . implode('|', $points);
  }

}
