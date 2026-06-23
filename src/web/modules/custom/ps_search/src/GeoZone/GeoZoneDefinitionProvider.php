<?php

declare(strict_types=1);

namespace Drupal\ps_search\GeoZone;

use Symfony\Component\Yaml\Yaml;

/**
 * Provides geo zone country definitions (static or derived from referentials).
 */
final class GeoZoneDefinitionProvider {

  private const DEFAULT_BBOX_RADIUS_KM = 45.0;

  /**
   * @var array<string, array<string, mixed>>|null
   */
  private ?array $staticDefinitions = NULL;

  public function __construct(
    private readonly GeoZoneSlugGenerator $slugGenerator,
    private readonly GeoZonePostalPrefixBuilder $postalPrefixBuilder,
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
      return $this->buildFrDefinition();
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

  /**
   * @return array<string, mixed>
   */
  private function buildFrDefinition(): array {
    $centroids = $this->loadCentroids();
    $divisions = [];
    foreach ($this->loadDictionaryDepartments() as $department) {
      $code = $department['code'];
      $label = $department['label'];
      $centroid = $centroids[$code] ?? NULL;
      if (!is_array($centroid)) {
        throw new \RuntimeException(sprintf('Missing centroid for French department "%s".', $code));
      }

      $divisions[] = [
        'code' => $code,
        'label' => $label,
        'slug' => $this->slugGenerator->build($label, $code),
        'lat' => (float) $centroid['lat'],
        'lng' => (float) $centroid['lng'],
        'radius_km' => (float) ($centroid['radius_km'] ?? self::DEFAULT_BBOX_RADIUS_KM),
        'postal_prefixes' => $this->postalPrefixBuilder->forDepartmentCode('fr', $code),
        'weight' => $this->departmentWeight($code),
      ];
    }

    return [
      'zone_type' => GeoZoneType::Department->value,
      'default_code' => '75',
      'divisions' => $divisions,
    ];
  }

  /**
   * @return list<array{code: string, label: string}>
   */
  private function loadDictionaryDepartments(): array {
    $csvPath = \Drupal::root() . '/modules/custom/ps_dictionary/data/dictionary_entries.csv';
    if (!is_file($csvPath)) {
      throw new \RuntimeException(sprintf('Dictionary CSV not found: %s', $csvPath));
    }

    $departments = [];
    $handle = fopen($csvPath, 'rb');
    if ($handle === FALSE) {
      throw new \RuntimeException('Unable to open dictionary CSV.');
    }

    while (($row = fgetcsv($handle)) !== FALSE) {
      if (($row[0] ?? '') !== 'department') {
        continue;
      }
      $code = strtoupper(trim((string) ($row[1] ?? '')));
      $label = trim((string) ($row[2] ?? ''));
      if ($code !== '' && $label !== '') {
        $departments[] = [
          'code' => $this->normalizeDepartmentCode($code),
          'label' => $label,
        ];
      }
    }

    fclose($handle);

    if ($departments === []) {
      throw new \RuntimeException('No department entries found in dictionary CSV.');
    }

    return $departments;
  }

  /**
   * @return array<string, array<string, float>>
   */
  private function loadCentroids(): array {
    $path = $this->getModuleDataPath('centroids/fr.departments.yml');
    if (!is_file($path)) {
      throw new \RuntimeException(sprintf('French centroids file not found: %s', $path));
    }

    $parsed = Yaml::parse((string) file_get_contents($path));
    if (!is_array($parsed)) {
      throw new \RuntimeException('French centroids file is invalid.');
    }

    $centroids = [];
    foreach ($parsed as $code => $data) {
      if (!is_array($data) || !isset($data['lat'], $data['lng'])) {
        continue;
      }
      $normalizedCode = $this->normalizeDepartmentCode($code);
      $centroids[$normalizedCode] = [
        'lat' => (float) $data['lat'],
        'lng' => (float) $data['lng'],
        'radius_km' => isset($data['radius_km']) ? (float) $data['radius_km'] : self::DEFAULT_BBOX_RADIUS_KM,
      ];
    }

    return $centroids;
  }

  private function normalizeDepartmentCode(mixed $code): string {
    $code = strtoupper(trim((string) $code));
    if (preg_match('/^\d{1,2}$/', $code) === 1) {
      return str_pad($code, 2, '0', STR_PAD_LEFT);
    }

    return $code;
  }

  private function departmentWeight(string $code): int {
    $digits = preg_replace('/\D/', '', $code);

    return $digits !== '' ? (int) $digits : 0;
  }

  private function getModuleDataPath(string $relative): string {
    return \Drupal::root() . '/' . trim($this->moduleRelativePath, '/') . '/data/geo_zones/' . $relative;
  }

}
