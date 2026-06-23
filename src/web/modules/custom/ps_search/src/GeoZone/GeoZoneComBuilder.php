<?php

declare(strict_types=1);

namespace Drupal\ps_search\GeoZone;

use Symfony\Component\Yaml\Yaml;

/**
 * Builds COM geo zones from the French referential (international FR site).
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
    $sourcePrefix = 'department.' . self::SOURCE_COUNTRY . '.';
    $targetPrefix = 'department.' . self::TARGET_COUNTRY . '.';

    $zones = [];
    foreach ($frPayload['zones'] ?? [] as $id => $data) {
      if (!is_string($id) || !is_array($data)) {
        continue;
      }

      if (!str_starts_with($id, $sourcePrefix)) {
        throw new \RuntimeException(sprintf('Unexpected French zone id "%s".', $id));
      }

      $suffix = substr($id, strlen($sourcePrefix));
      $zones[$targetPrefix . $suffix] = $data;
    }

    if ($zones === []) {
      throw new \RuntimeException('French geo zones source has no department zones.');
    }

    ksort($zones);

    $defaultZone = (string) ($frPayload['default_zone'] ?? '');
    if ($defaultZone === '' || !str_starts_with($defaultZone, $sourcePrefix)) {
      throw new \RuntimeException('French geo zones source has an invalid default_zone.');
    }

    return [
      'country' => self::TARGET_COUNTRY,
      'default_zone' => $targetPrefix . substr($defaultZone, strlen($sourcePrefix)),
      'zones' => $zones,
    ];
  }

  /**
   * Resolves a path under the module geo_zones data directory.
   */
  private function getModuleDataPath(string $relative): string {
    return \Drupal::root() . '/' . trim($this->moduleRelativePath, '/') . '/data/geo_zones/' . $relative;
  }

}
