<?php

declare(strict_types=1);

namespace Drupal\ps_search\Service;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\ps_search\Service\Isochrone\GoogleRoutesIsochroneProvider;
use Drupal\ps_search\Service\Isochrone\IsochroneApproximationProvider;
use Drupal\ps_search\Service\Isochrone\IsochroneProviderInterface;
use Drupal\ps_search\Service\Isochrone\OpenRouteServiceIsochroneProvider;
use Psr\Log\LoggerInterface;

/**
 * Orchestrates external isochrone providers with approximation fallback.
 */
final class IsochroneService {

  private const TRANSPORTS = ['walking', 'transports', 'bike', 'car'];

  private const MIN_MINUTES = 1;

  private const MAX_MINUTES = 120;

  public function __construct(
    private readonly ConfigFactoryInterface $configFactory,
    private readonly IsochroneApproximationProvider $approximationProvider,
    private readonly OpenRouteServiceIsochroneProvider $orsProvider,
    private readonly GoogleRoutesIsochroneProvider $googleProvider,
    private readonly LoggerInterface $logger,
    private readonly CacheBackendInterface $cache,
  ) {}

  /**
   * Builds isochrone geometry and map_bounds for filtering.
   *
   * @return array<string, mixed>
   */
  public function build(float $lat, float $lng, string $transport, int $minutes): array {
    if ($lat < -90 || $lat > 90 || $lng < -180 || $lng > 180) {
      throw new \InvalidArgumentException('Invalid center coordinates.');
    }

    if (!in_array($transport, self::TRANSPORTS, TRUE)) {
      throw new \InvalidArgumentException('Invalid transport mode.');
    }

    $minutes = max(self::MIN_MINUTES, min(self::MAX_MINUTES, $minutes));

    $cacheKey = $this->buildCacheKey($lat, $lng, $transport, $minutes);
    $cached = $this->cache->get($cacheKey);
    if ($cached !== FALSE && is_array($cached->data)) {
      return $cached->data;
    }

    $settings = $this->configFactory->get('ps_search.map_zone_settings');
    $providerId = (string) ($settings->get('isochrone_provider') ?? 'approximation');
    if ($providerId === 'ors' && !($settings->get('ors_enabled') ?? FALSE)) {
      $providerId = 'approximation';
    }
    $fallback = (bool) ($settings->get('isochrone_fallback') ?? TRUE);

    $provider = $this->resolveProvider($providerId);
    $payload = $provider->build($lat, $lng, $transport, $minutes);

    if ($payload !== NULL) {
      $this->storeCache($cacheKey, $payload);
      return $payload;
    }

    if ($fallback && $provider->id() !== $this->approximationProvider->id()) {
      $this->logger->notice('Isochrone provider @provider unavailable, falling back to approximation.', [
        '@provider' => $provider->id(),
      ]);
      $payload = $this->approximationProvider->build($lat, $lng, $transport, $minutes);
      if ($payload !== NULL) {
        $payload['requested_provider'] = $provider->id();
        $payload['fallback'] = TRUE;
        $this->storeCache($cacheKey, $payload);
        return $payload;
      }
    }

    throw new \RuntimeException('Isochrone could not be computed.');
  }

  /**
   *
   */
  private function resolveProvider(string $providerId): IsochroneProviderInterface {
    return match ($providerId) {
      'ors' => $this->orsProvider,
      'google', 'google_routes' => $this->googleProvider,
      default => $this->approximationProvider,
    };
  }

  /**
   * @param array<string, mixed> $payload
   */
  private function storeCache(string $cacheKey, array $payload): void {
    $ttl = max(60, (int) ($this->configFactory->get('ps_search.api_cache_settings')->get('isochrone_ttl') ?? 86400));
    $this->cache->set($cacheKey, $payload, time() + $ttl, ['config:ps_search.api_cache_settings']);
  }

  private function buildCacheKey(float $lat, float $lng, string $transport, int $minutes): string {
    $precision = max(2, min(6, (int) ($this->configFactory->get('ps_search.api_cache_settings')->get('isochrone_coordinate_precision') ?? 4)));
    $latKey = number_format($lat, $precision, '.', '');
    $lngKey = number_format($lng, $precision, '.', '');
    return 'ps_search:isochrone:' . $transport . ':' . $minutes . ':' . $latKey . ':' . $lngKey;
  }

}
