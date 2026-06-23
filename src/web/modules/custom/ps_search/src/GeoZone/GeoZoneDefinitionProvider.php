<?php

declare(strict_types=1);

namespace Drupal\ps_search\GeoZone;

/**
 * Provides geo zone country definitions (static seed data for non-FR countries).
 */
final class GeoZoneDefinitionProvider {

  /**
   * @var array<string, array<string, mixed>>|null
   */
  private ?array $staticDefinitions = NULL;

  public function __construct(
    private readonly string $moduleRelativePath,
  ) {}

  /**
   * @return list<string>
   */
  public function getSupportedCountries(): array {
    $countries = array_keys($this->getStaticDefinitions());
    sort($countries);

    return array_values(array_unique(array_merge(['com', 'fr'], $countries)));
  }

  /**
   * @return array<string, mixed>
   */
  public function getDefinition(string $countryCode): array {
    $countryCode = strtolower(trim($countryCode));
    if ($countryCode === 'com') {
      throw new \InvalidArgumentException('COM is built by cloning FR; use GeoZoneBuilder::buildPayload("com").');
    }

    if ($countryCode === 'fr') {
      throw new \InvalidArgumentException('FR geo zones are maintained in data/geo_zones/fr.yml; use merge_fr_regions.php or GeoZoneBuilder::buildPayload("fr").');
    }

    $definitions = $this->getStaticDefinitions();
    if (!isset($definitions[$countryCode])) {
      throw new \InvalidArgumentException(sprintf('Unknown geo zone country "%s".', $countryCode));
    }

    return $definitions[$countryCode];
  }

  /**
   * @return array<string, array<string, mixed>>
   */
  private function getStaticDefinitions(): array {
    if ($this->staticDefinitions !== NULL) {
      return $this->staticDefinitions;
    }

    $path = $this->getModuleDataPath('country_definitions.php');
    if (!is_file($path)) {
      throw new \RuntimeException(sprintf('Country definitions file not found: %s', $path));
    }

    require $path;
    if (!function_exists('ps_geo_country_definitions')) {
      throw new \RuntimeException('Country definitions file is missing ps_geo_country_definitions().');
    }

    $definitions = ps_geo_country_definitions();
    if (!is_array($definitions)) {
      throw new \RuntimeException('Country definitions must return an array.');
    }

    $this->staticDefinitions = $definitions;

    return $this->staticDefinitions;
  }

  private function getModuleDataPath(string $relative): string {
    return \Drupal::root() . '/' . trim($this->moduleRelativePath, '/') . '/data/geo_zones/' . $relative;
  }

}
