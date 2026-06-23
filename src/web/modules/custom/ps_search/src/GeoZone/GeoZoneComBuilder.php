<?php

declare(strict_types=1);

namespace Drupal\ps_search\GeoZone;

use Symfony\Component\Yaml\Yaml;

/**
 * Builds COM geo zones from the French YAML source (international FR site).
 */
final class GeoZoneComBuilder {

  private const SOURCE_COUNTRY = 'fr';

  private const TARGET_COUNTRY = 'com';

  public function __construct(
    private readonly string $moduleRelativePath,
  ) {}

  /**
   * Builds COM geo zones from the French YAML source file.
   *
   * @return array<string, mixed>
   *   Country payload ready for YAML export or config import.
   */
  public function buildPayload(): array {
    $sourcePath = $this->getModuleDataPath(self::SOURCE_COUNTRY . '.yml');
    if (!is_file($sourcePath)) {
      throw new \RuntimeException(sprintf('French geo zones source not found: %s', $sourcePath));
    }

    $payload = Yaml::parse((string) file_get_contents($sourcePath));
    if (!is_array($payload)) {
      throw new \RuntimeException('French geo zones source is invalid.');
    }

    return $this->clonePayloadForCountry($payload);
  }

  /**
   * Clones a French geo zone payload for the COM country code.
   *
   * @param array<string, mixed> $frPayload
   *   French geo zone YAML payload.
   *
   * @return array<string, mixed>
   *   COM geo zone payload.
   */
  public function clonePayloadForCountry(array $frPayload): array {
    $zones = [];
    foreach ($frPayload['zones'] ?? [] as $id => $data) {
      if (!is_string($id) || !is_array($data)) {
        continue;
      }

      $parts = explode('.', $id, 3);
      if (count($parts) !== 3 || $parts[1] !== self::SOURCE_COUNTRY) {
        throw new \RuntimeException(sprintf('Unexpected French zone id "%s".', $id));
      }

      $comId = $parts[0] . '.' . self::TARGET_COUNTRY . '.' . $parts[2];
      $zoneData = $data;
      if (isset($zoneData['parent']) && is_string($zoneData['parent'])) {
        $zoneData['parent'] = $this->translateZoneId($zoneData['parent']);
      }
      $zones[$comId] = $zoneData;
    }

    if ($zones === []) {
      throw new \RuntimeException('French geo zones source has no zones.');
    }

    ksort($zones);

    $defaultZone = (string) ($frPayload['default_zone'] ?? '');
    if ($defaultZone === '' || !str_contains($defaultZone, '.' . self::SOURCE_COUNTRY . '.')) {
      throw new \RuntimeException('French geo zones source has an invalid default_zone.');
    }

    return [
      'country' => self::TARGET_COUNTRY,
      'default_zone' => $this->translateZoneId($defaultZone),
      'zones' => $zones,
    ];
  }

  private function translateZoneId(string $id): string {
    return (string) preg_replace(
      '/\.' . self::SOURCE_COUNTRY . '\./',
      '.' . self::TARGET_COUNTRY . '.',
      $id,
      1,
    );
  }

  /**
   * Resolves a path under the module geo_zones data directory.
   */
  private function getModuleDataPath(string $relative): string {
    return \Drupal::root() . '/' . trim($this->moduleRelativePath, '/') . '/data/geo_zones/' . $relative;
  }

}
