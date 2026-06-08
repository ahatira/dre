<?php

declare(strict_types=1);

namespace Drupal\ps_search\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Builds search map location settings (center, radius) from request tokens.
 */
final class LocationCentroidResolver {

  public function __construct(
    private readonly LocationSearchFilter $locationSearchFilter,
    private readonly ConfigFactoryInterface $configFactory,
  ) {}

  /**
   * Resolves map settings for the primary location in the current request.
   *
   * @return array<string, mixed>|null
   *   Client settings or NULL when no location is selected.
   */
  public function resolveFromRequest(Request $request): ?array {
    $tokens = $this->locationSearchFilter->extractTokensFromRequest($request);
    if ($tokens === []) {
      return $this->resolveFromSeoPath($request);
    }

    return $this->buildMapSettings($tokens[0]);
  }

  /**
   * Resolves map settings from SEO path when query has no locality tokens.
   *
   * @return array<string, mixed>|null
   *   Map settings or NULL when the path has no postal segment.
   */
  private function resolveFromSeoPath(Request $request): ?array {
    $path = $request->getPathInfo();
    if (!preg_match('#/([a-z0-9-]+)-(\d{5})/?$#i', $path, $matches)) {
      return NULL;
    }

    return $this->buildMapSettings($matches[2]);
  }

  /**
   * Builds client map settings from a primary location token.
   *
   * @return array<string, mixed>
   *   Map center, radius and styling for drupalSettings.
   */
  private function buildMapSettings(string $primaryToken): array {
    $meta = $this->locationSearchFilter->resolveTokenMetadata($primaryToken);
    $mapConfig = $this->configFactory->get('ps_offer.map_settings');
    $locality = (string) ($meta['locality'] ?? '');
    $largeCities = array_filter(array_map('trim', explode("\n", (string) ($mapConfig->get('large_cities') ?? ''))));
    $isLargeCity = $locality !== '' && in_array($locality, $largeCities, TRUE);

    $radius = (int) ($isLargeCity
      ? ($mapConfig->get('circle_radius_large_cities_m') ?? 1000)
      : ($mapConfig->get('circle_radius_m') ?? 2500));

    $zoom = (int) ($isLargeCity
      ? ($mapConfig->get('default_zoom_approx_large_city') ?? 14)
      : ($mapConfig->get('default_zoom_approx') ?? 13));

    return [
      'label' => $meta['label'],
      'type' => $meta['type'],
      'locality' => $meta['locality'],
      'postalCode' => $meta['postal_code'],
      'lat' => $meta['lat'],
      'lng' => $meta['lng'],
      'radiusM' => $radius,
      'zoom' => $zoom,
      'circleColor' => (string) ($mapConfig->get('circle_color') ?? '#00915A'),
      'offerCount' => $meta['offer_count'],
    ];
  }

}
