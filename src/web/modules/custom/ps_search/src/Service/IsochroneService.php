<?php

declare(strict_types=1);

namespace Drupal\ps_search\Service;

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
    $settings = $this->configFactory->get('ps_search.map_zone_settings');
    $providerId = (string) ($settings->get('isochrone_provider') ?? 'approximation');
    if ($providerId === 'ors' && !($settings->get('ors_enabled') ?? FALSE)) {
      $providerId = 'approximation';
    }
    $fallback = (bool) ($settings->get('isochrone_fallback') ?? TRUE);

    $provider = $this->resolveProvider($providerId);
    $payload = $provider->build($lat, $lng, $transport, $minutes);

    if ($payload !== NULL) {
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

}
