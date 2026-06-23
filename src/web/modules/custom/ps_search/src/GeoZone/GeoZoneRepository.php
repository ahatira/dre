<?php

declare(strict_types=1);

namespace Drupal\ps_search\GeoZone;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\ps_search\Contract\GeoZoneRepositoryInterface;
use Drupal\ps_search\ValueObject\GeoZone;

/**
 * Loads geo zones from exportable config (one config object per country).
 */
final class GeoZoneRepository implements GeoZoneRepositoryInterface {

  private const CONFIG_PREFIX = 'ps_search.geo_zones.';

  /**
   * @var array<string, array<string, GeoZone>>
   */
  private array $cacheByCountry = [];

  /**
   * @var array<string, array<string, GeoZone>>
   */
  private array $slugIndexByCountry = [];

  public function __construct(
    private readonly ConfigFactoryInterface $configFactory,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function get(string $id): ?GeoZone {
    $countryCode = $this->extractCountryFromId($id);
    if ($countryCode === NULL) {
      return NULL;
    }

    $this->loadCountry($countryCode);

    return $this->cacheByCountry[$countryCode][$id] ?? NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function findBySlug(string $slug, string $countryCode): ?GeoZone {
    $countryCode = strtolower($countryCode);
    $this->loadCountry($countryCode);

    $normalized = strtolower(trim($slug));
    if ($normalized === '') {
      return NULL;
    }

    return $this->slugIndexByCountry[$countryCode][$normalized] ?? NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function findByPostalPrefix(string $prefix, string $countryCode): ?GeoZone {
    $countryCode = strtolower($countryCode);
    $this->loadCountry($countryCode);

    $normalized = trim($prefix);
    if ($normalized === '') {
      return NULL;
    }

    $matches = [];
    foreach ($this->cacheByCountry[$countryCode] ?? [] as $zone) {
      foreach ($zone->postalPrefixes as $zonePrefix) {
        if ($normalized === $zonePrefix || str_starts_with($normalized, $zonePrefix)) {
          $matches[] = $zone;
          break;
        }
      }
    }

    if ($matches === []) {
      return NULL;
    }

    usort($matches, static function (GeoZone $a, GeoZone $b): int {
      $depthA = max(array_map(strlen(...), $a->postalPrefixes ?: ['']));
      $depthB = max(array_map(strlen(...), $b->postalPrefixes ?: ['']));
      if ($depthA !== $depthB) {
        return $depthB <=> $depthA;
      }

      return $a->weight <=> $b->weight;
    });

    return $matches[0];
  }

  /**
   * {@inheritdoc}
   */
  public function getDefaultForCountry(string $countryCode): ?GeoZone {
    $countryCode = strtolower($countryCode);
    $config = $this->configFactory->get($this->configName($countryCode));
    $defaultId = trim((string) ($config->get('default_zone') ?? ''));
    if ($defaultId === '') {
      return NULL;
    }

    return $this->get($defaultId);
  }

  /**
   * {@inheritdoc}
   */
  public function children(string $parentId): array {
    $countryCode = $this->extractCountryFromId($parentId);
    if ($countryCode === NULL) {
      return [];
    }

    $this->loadCountry($countryCode);

    $children = [];
    foreach ($this->cacheByCountry[$countryCode] ?? [] as $zone) {
      if ($zone->parentId === $parentId) {
        $children[] = $zone;
      }
    }

    usort($children, static fn (GeoZone $a, GeoZone $b): int => $a->weight <=> $b->weight);

    return $children;
  }

  /**
   * {@inheritdoc}
   */
  public function allForCountry(string $countryCode): array {
    $countryCode = strtolower($countryCode);
    $this->loadCountry($countryCode);

    $zones = array_values($this->cacheByCountry[$countryCode] ?? []);
    usort($zones, static fn (GeoZone $a, GeoZone $b): int => $a->weight <=> $b->weight);

    return $zones;
  }

  /**
   * {@inheritdoc}
   */
  public function getSupportedCountries(): array {
    $countries = [];
    foreach ($this->configFactory->listAll(self::CONFIG_PREFIX) as $name) {
      $suffix = substr($name, strlen(self::CONFIG_PREFIX));
      if ($suffix !== '') {
        $countries[] = strtolower($suffix);
      }
    }

    sort($countries);

    return $countries;
  }

  /**
   * Clears in-memory indexes (used after config import in tests).
   */
  public function resetCache(): void {
    $this->cacheByCountry = [];
    $this->slugIndexByCountry = [];
  }

  private function loadCountry(string $countryCode): void {
    $countryCode = strtolower($countryCode);
    if (isset($this->cacheByCountry[$countryCode])) {
      return;
    }

    $this->cacheByCountry[$countryCode] = [];
    $this->slugIndexByCountry[$countryCode] = [];

    $config = $this->configFactory->get($this->configName($countryCode));
    $zones = $config->get('zones') ?? [];
    if (!is_array($zones)) {
      return;
    }

    foreach ($zones as $storageKey => $data) {
      if (!is_string($storageKey) || !is_array($data)) {
        continue;
      }

      $id = isset($data['id']) && is_string($data['id']) && $data['id'] !== ''
        ? $data['id']
        : GeoZoneConfigKeys::decodeStorageKey($storageKey);

      $zone = GeoZone::fromConfigArray($id, $data, $countryCode);
      if (!$zone instanceof GeoZone) {
        continue;
      }

      $this->cacheByCountry[$countryCode][$id] = $zone;
      $this->slugIndexByCountry[$countryCode][strtolower($zone->slug)] = $zone;
    }
  }

  private function configName(string $countryCode): string {
    return self::CONFIG_PREFIX . strtolower($countryCode);
  }

  private function extractCountryFromId(string $id): ?string {
    $parts = explode('.', $id);
    if (count($parts) < 3) {
      return NULL;
    }

    return strtolower($parts[1]);
  }

}
