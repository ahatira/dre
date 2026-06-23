<?php

declare(strict_types=1);

namespace Drupal\ps_search\GeoZone;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\StorageInterface;
use Drupal\Core\Extension\ModuleExtensionList;
use Symfony\Component\Yaml\Yaml;

/**
 * Imports geo zone YAML sources into exportable Drupal config.
 */
final class GeoZoneImporter {

  public function __construct(
    private readonly ConfigFactoryInterface $configFactory,
    private readonly StorageInterface $configStorage,
    private readonly ModuleExtensionList $moduleExtensionList,
    private readonly GeoZoneValidator $validator,
    private readonly GeoZoneRepository $repository,
  ) {}

  /**
   * Imports one country file into active config.
   *
   * @return array{country: string, zone_count: int, config_name: string}
   */
  public function importCountry(string $countryCode, ?string $sourcePath = NULL): array {
    $countryCode = strtolower(trim($countryCode));
    $payload = $this->loadSourcePayload($countryCode, $sourcePath);
    $zones = is_array($payload['zones'] ?? NULL) ? $payload['zones'] : [];
    $defaultZone = isset($payload['default_zone']) && is_string($payload['default_zone'])
      ? $payload['default_zone']
      : NULL;

    $errors = $this->validator->validateCountryPayload($countryCode, $zones, $defaultZone);
    if ($errors !== []) {
      throw new \InvalidArgumentException("Geo zone validation failed for \"{$countryCode}\":\n- " . implode("\n- ", $errors));
    }

    $configName = 'ps_search.geo_zones.' . $countryCode;
    $data = [
      'country' => $countryCode,
      'default_zone' => $defaultZone,
      'zones' => $this->encodeZonesForConfig($zones),
    ];

    $this->configFactory->getEditable($configName)->setData($data)->save(TRUE);
    $this->repository->resetCache();

    return [
      'country' => $countryCode,
      'zone_count' => count($zones),
      'config_name' => $configName,
    ];
  }

  /**
   * Validates one country without writing config.
   *
   * @return list<string>
   */
  public function validateCountry(string $countryCode, ?string $sourcePath = NULL): array {
    $countryCode = strtolower(trim($countryCode));

    if ($sourcePath === NULL) {
      if (!$this->configStorage->exists('ps_search.geo_zones.' . $countryCode)) {
        return [sprintf('Config ps_search.geo_zones.%s does not exist.', $countryCode)];
      }

      $data = $this->configFactory->get('ps_search.geo_zones.' . $countryCode)->getRawData();

      return $this->validator->validateCountryPayload(
        $countryCode,
        $this->decodeZonesFromConfig(is_array($data['zones'] ?? NULL) ? $data['zones'] : []),
        is_string($data['default_zone'] ?? NULL) ? $data['default_zone'] : NULL,
      );
    }

    $payload = $this->loadSourcePayload($countryCode, $sourcePath);

    return $this->validator->validateCountryPayload(
      $countryCode,
      is_array($payload['zones'] ?? NULL) ? $payload['zones'] : [],
      is_string($payload['default_zone'] ?? NULL) ? $payload['default_zone'] : NULL,
    );
  }

  /**
   * Validates all installed geo zone configs.
   *
   * @return array<string, list<string>>
   */
  public function validateAllInstalled(): array {
    $results = [];
    foreach ($this->repository->getSupportedCountries() as $countryCode) {
      $errors = $this->validateCountry($countryCode);
      if ($errors !== []) {
        $results[$countryCode] = $errors;
      }
    }

    return $results;
  }

  /**
   * Prepares zone payloads for Drupal config persistence.
   *
   * @param array<string, array<string, mixed>> $zones
   *
   * @return array<string, array<string, mixed>>
   */
  private function encodeZonesForConfig(array $zones): array {
    $encoded = [];
    foreach ($zones as $id => $zoneData) {
      if (!is_array($zoneData)) {
        continue;
      }
      $zoneData['id'] = $id;
      $encoded[GeoZoneConfigKeys::encodeStorageKey($id)] = $zoneData;
    }

    return $encoded;
  }

  /**
   * Rebuilds canonical zone ids from persisted config mapping keys.
   *
   * @param array<string, array<string, mixed>> $zones
   *
   * @return array<string, array<string, mixed>>
   */
  private function decodeZonesFromConfig(array $zones): array {
    $decoded = [];
    foreach ($zones as $storageKey => $zoneData) {
      if (!is_array($zoneData)) {
        continue;
      }
      $id = isset($zoneData['id']) && is_string($zoneData['id']) && $zoneData['id'] !== ''
        ? $zoneData['id']
        : GeoZoneConfigKeys::decodeStorageKey($storageKey);
      $decoded[$id] = $zoneData;
    }

    return $decoded;
  }

  /**
   * @return array<string, mixed>
   */
  private function loadSourcePayload(string $countryCode, ?string $sourcePath): array {
    $path = $sourcePath ?? $this->resolveSourcePath($countryCode);
    if (!is_file($path)) {
      throw new \InvalidArgumentException(sprintf('Geo zone source file not found: %s', $path));
    }

    $parsed = Yaml::parse((string) file_get_contents($path));
    if (!is_array($parsed)) {
      throw new \InvalidArgumentException(sprintf('Geo zone source file is empty or invalid: %s', $path));
    }

    return $parsed;
  }

  /**
   * Resolves YAML source path under the module data directory.
   */
  public function resolveSourcePath(string $countryCode): string {
    $countryCode = strtolower($countryCode);
    $fileName = $countryCode . '.yml';
    $modulePath = $this->moduleExtensionList->getPath('ps_search');

    return \Drupal::root() . '/' . $modulePath . '/data/geo_zones/' . $fileName;
  }

}
