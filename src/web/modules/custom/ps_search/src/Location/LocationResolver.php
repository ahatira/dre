<?php

declare(strict_types=1);

namespace Drupal\ps_search\Location;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Site\Settings;
use Drupal\ps_search\Contract\GeoZoneRepositoryInterface;
use Drupal\ps_search\Contract\GeocodingProviderInterface;
use Drupal\ps_search\Contract\LocationResolverInterface;
use Drupal\ps_search\Search\Context\GeoContextFactory;
use Drupal\ps_search\ValueObject\GeoContext;
use Drupal\ps_search\ValueObject\LocationResolveResult;

/**
 * Orchestrates geocoding providers for suggest, apply and resolve API (L3).
 */
final class LocationResolver implements LocationResolverInterface {

  /**
   * @param iterable<GeocodingProviderInterface> $providers
   */
  public function __construct(
    private readonly iterable $providers,
    private readonly GeoZoneRepositoryInterface $geoZoneRepository,
    private readonly ConfigFactoryInterface $configFactory,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function resolveQuery(
    string $query,
    string $countryCode,
    string $langcode,
  ): LocationResolveResult {
    $query = trim($query);
    if ($query === '') {
      return new LocationResolveResult(NULL, FALSE, []);
    }

    foreach ($this->getEnabledProviders() as $provider) {
      $result = $provider->resolve($query, $countryCode, $langcode);
      if ($result->geo !== NULL || $result->ambiguous) {
        return $result;
      }
    }

    return new LocationResolveResult(NULL, FALSE, []);
  }

  /**
   * {@inheritdoc}
   */
  public function resolveGeoZone(string $zoneIdOrSlug, string $countryCode): ?GeoContext {
    $countryCode = strtolower($countryCode);
    $zoneIdOrSlug = trim($zoneIdOrSlug);
    if ($zoneIdOrSlug === '') {
      return NULL;
    }

    $zone = $this->geoZoneRepository->get($zoneIdOrSlug)
      ?? $this->geoZoneRepository->findBySlug(strtolower($zoneIdOrSlug), $countryCode);

    return $zone !== NULL ? GeoContextFactory::fromGeoZone($zone, 'geo_zone') : NULL;
  }

  /**
   * @return list<GeocodingProviderInterface>
   */
  private function getEnabledProviders(): array {
    $config = $this->configFactory->get('ps_search.engine_settings');
    $configured = $config->get('geocoding_providers') ?? [];
    if (!is_array($configured)) {
      return iterator_to_array($this->providers);
    }

    $enabledIds = [];
    $weights = [];
    foreach ($configured as $entry) {
      if (!is_array($entry) || empty($entry['enabled'])) {
        continue;
      }
      $id = is_string($entry['id'] ?? NULL) ? $entry['id'] : '';
      if ($id === '') {
        continue;
      }
      $enabledIds[$id] = TRUE;
      $weights[$id] = (int) ($entry['weight'] ?? 0);
    }

    $providers = [];
    foreach ($this->providers as $provider) {
      if (isset($enabledIds[$provider->id()])) {
        $providers[] = $provider;
      }
    }

    usort($providers, static function (GeocodingProviderInterface $a, GeocodingProviderInterface $b) use ($weights): int {
      return ($weights[$a->id()] ?? 0) <=> ($weights[$b->id()] ?? 0);
    });

    return $providers;
  }

  /**
   * Resolves the active site country code.
   */
  public function resolveCountryCode(): string {
    $code = Settings::get('ps_country_code');
    return is_string($code) && $code !== '' ? strtolower($code) : 'com';
  }

}
