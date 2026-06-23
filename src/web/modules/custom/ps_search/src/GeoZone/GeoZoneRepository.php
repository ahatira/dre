<?php

declare(strict_types=1);

namespace Drupal\ps_search\GeoZone;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\ps_search\Contract\GeoZoneRepositoryInterface;
use Drupal\ps_search\GeoZone\GeoZoneType;
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

  /**
   * @var array<string, array<string, GeoZone>>
   */
  private array $codeIndexByCountry = [];

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
   * {@inheritdoc}
   */
  public function findDepartmentByCode(string $code, string $countryCode): ?GeoZone {
    $countryCode = strtolower($countryCode);
    $normalized = strtoupper(trim($code));
    if ($normalized === '') {
      return NULL;
    }

    $this->loadCountry($countryCode);

    return $this->codeIndexByCountry[$countryCode][$normalized] ?? NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getRegionForZone(GeoZone $zone): ?GeoZone {
    if ($zone->parentId === NULL) {
      return NULL;
    }

    $parent = $this->get($zone->parentId);
    if ($parent !== NULL && $parent->type === GeoZoneType::Region) {
      return $parent;
    }

    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function searchByLabelPrefix(
    string $query,
    string $countryCode,
    GeoZoneType $type,
    int $limit = 5,
  ): array {
    $needle = $this->normalizeForSearch($query);
    if ($needle === '') {
      return [];
    }

    $matches = [];
    foreach ($this->allForCountry($countryCode) as $zone) {
      if ($zone->type !== $type) {
        continue;
      }
      if ($this->matchesSearchNeedle($needle, $zone->label, $zone->slug)) {
        $matches[] = $zone;
      }
    }

    usort($matches, static fn (GeoZone $a, GeoZone $b): int => strcasecmp($a->label, $b->label));

    return array_slice($matches, 0, max(1, $limit));
  }

  /**
   * {@inheritdoc}
   */
  public function buildRegionToken(string $slug): string {
    return self::REGION_TOKEN_PREFIX . strtolower(trim($slug));
  }

  /**
   * {@inheritdoc}
   */
  public function isRegionToken(string $token): bool {
    return str_starts_with($token, self::REGION_TOKEN_PREFIX);
  }

  /**
   * {@inheritdoc}
   */
  public function parseRegionToken(string $token): ?string {
    if (!$this->isRegionToken($token)) {
      return NULL;
    }

    $slug = substr($token, strlen(self::REGION_TOKEN_PREFIX));

    return trim($slug) !== '' ? $slug : NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function normalizeForSearch(string $value): string {
    $ascii = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);
    if ($ascii === FALSE) {
      $ascii = $value;
    }
    $ascii = strtolower($ascii);
    $ascii = preg_replace('/[^a-z0-9]+/', '-', $ascii);

    return trim((string) $ascii, '-');
  }

  /**
   * {@inheritdoc}
   */
  public function matchesSearchNeedle(string $needle, string $label, string $slug): bool {
    if ($needle === '') {
      return FALSE;
    }

    $labelNorm = $this->normalizeForSearch($label);
    $slugNorm = $this->normalizeForSearch(str_replace('-', ' ', $slug));

    return str_starts_with($labelNorm, $needle)
      || str_contains($labelNorm, '-' . $needle)
      || str_starts_with($slugNorm, $needle)
      || str_contains($slugNorm, $needle);
  }

  /**
   * Clears in-memory indexes (used after config import in tests).
   */
  public function resetCache(): void {
    $this->cacheByCountry = [];
    $this->slugIndexByCountry = [];
    $this->codeIndexByCountry = [];
  }

  private function loadCountry(string $countryCode): void {
    $countryCode = strtolower($countryCode);
    if (isset($this->cacheByCountry[$countryCode])) {
      return;
    }

    $this->cacheByCountry[$countryCode] = [];
    $this->slugIndexByCountry[$countryCode] = [];
    $this->codeIndexByCountry[$countryCode] = [];

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
      if ($zone->type === GeoZoneType::Department) {
        $this->codeIndexByCountry[$countryCode][strtoupper($zone->code)] = $zone;
      }
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
