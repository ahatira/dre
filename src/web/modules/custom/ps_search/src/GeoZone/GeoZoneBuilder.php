<?php

declare(strict_types=1);

namespace Drupal\ps_search\GeoZone;

use Drupal\ps_search\ValueObject\GeoBoundingBox;
use Symfony\Component\Yaml\Yaml;

/**
 * Builds geo zone YAML payloads for any supported PS country.
 */
final class GeoZoneBuilder {

  private const DEFAULT_BBOX_RADIUS_KM = 45.0;

  public function __construct(
    private readonly GeoZoneDefinitionProvider $definitionProvider,
    private readonly GeoZoneComBuilder $comBuilder,
    private readonly string $moduleRelativePath,
  ) {}

  /**
   * @return list<string>
   *   Lowercase country codes supported by the builder.
   */
  public function getSupportedCountries(): array {
    return $this->definitionProvider->getSupportedCountries();
  }

  /**
   * @return array<string, mixed>
   *   Country payload ready for YAML export or config import.
   */
  public function buildPayload(string $countryCode): array {
    $countryCode = strtolower(trim($countryCode));
    if ($countryCode === 'com') {
      return $this->comBuilder->buildPayload();
    }

    if ($countryCode === 'fr') {
      return $this->loadPayloadFromModuleData('fr');
    }

    return $this->buildFromDefinition(
      $countryCode,
      $this->definitionProvider->getDefinition($countryCode),
    );
  }

  /**
   * Writes the generated payload to the module data directory.
   *
   * @param array<string, mixed>|null $payload
   *   Pre-built payload; built from definitions when omitted.
   */
  public function exportToModuleData(string $countryCode, ?array $payload = NULL): string {
    $countryCode = strtolower(trim($countryCode));
    $payload ??= $this->buildPayload($countryCode);
    $path = $this->getModuleDataPath($countryCode . '.yml');
    $dir = dirname($path);
    if (!is_dir($dir) && !mkdir($dir, 0775, TRUE) && !is_dir($dir)) {
      throw new \RuntimeException(sprintf('Unable to create directory: %s', $dir));
    }

    file_put_contents($path, Yaml::dump($payload, 4, 2));

    return $path;
  }

  /**
   * @param array<string, mixed> $definition
   *   Country definition from GeoZoneDefinitionProvider.
   *
   * @return array<string, mixed>
   */
  public function buildFromDefinition(string $countryCode, array $definition): array {
    $countryCode = strtolower(trim($countryCode));
    $zoneType = (string) ($definition['zone_type'] ?? 'region');
    $defaultCode = strtolower((string) ($definition['default_code'] ?? ''));
    $divisions = $definition['divisions'] ?? [];

    $zones = [];
    $sequentialWeight = 0;
    foreach ($divisions as $division) {
      if (!is_array($division)) {
        continue;
      }

      $code = strtoupper(trim((string) ($division['code'] ?? '')));
      $codeKey = strtolower($code);
      $label = trim((string) ($division['label'] ?? ''));
      $slug = trim((string) ($division['slug'] ?? ''));
      $radiusKm = (float) ($division['radius_km'] ?? self::DEFAULT_BBOX_RADIUS_KM);
      $prefixes = array_values(array_filter(array_map('strval', $division['postal_prefixes'] ?? [])));
      $lat = $division['lat'] ?? NULL;
      $lng = $division['lng'] ?? NULL;

      if ($code === '' || $label === '' || $slug === '' || !is_numeric($lat) || !is_numeric($lng)) {
        throw new \InvalidArgumentException(sprintf('Invalid division for country "%s" (code "%s").', $countryCode, $code));
      }

      $weight = isset($division['weight']) && is_numeric($division['weight'])
        ? (int) $division['weight']
        : ++$sequentialWeight;

      $zoneId = $zoneType . '.' . $countryCode . '.' . $codeKey;
      $bbox = GeoBoundingBox::fromCenterAndRadiusKm((float) $lat, (float) $lng, $radiusKm);
      $zones[$zoneId] = [
        'type' => $zoneType,
        'code' => $code,
        'label' => $label,
        'slug' => $slug,
        'lat' => round((float) $lat, 6),
        'lng' => round((float) $lng, 6),
        'bbox' => $bbox->toConfigArray(),
        'postal_prefixes' => $prefixes,
        'weight' => $weight,
      ];
    }

    if ($zones === []) {
      throw new \InvalidArgumentException(sprintf('No zones built for country "%s".', $countryCode));
    }

    ksort($zones);

    $defaultZone = $zoneType . '.' . $countryCode . '.' . $defaultCode;
    if (!isset($zones[$defaultZone])) {
      $defaultZone = (string) array_key_first($zones);
    }

    return [
      'country' => $countryCode,
      'default_zone' => $defaultZone,
      'zones' => $zones,
    ];
  }

  /**
   * @return array<string, mixed>
   */
  private function loadPayloadFromModuleData(string $countryCode): array {
    $path = $this->getModuleDataPath($countryCode . '.yml');
    if (!is_file($path)) {
      throw new \RuntimeException(sprintf('Geo zone YAML not found: %s', $path));
    }

    $payload = Yaml::parse((string) file_get_contents($path));
    if (!is_array($payload)) {
      throw new \RuntimeException(sprintf('Invalid geo zone YAML: %s', $path));
    }

    return $payload;
  }

  private function getModuleDataPath(string $relative): string {
    return \Drupal::root() . '/' . trim($this->moduleRelativePath, '/') . '/data/geo_zones/' . $relative;
  }

}
