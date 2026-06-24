<?php

declare(strict_types=1);

use Symfony\Component\Yaml\Yaml;

/**
 * Config names stored as partial YAML under config/env/sites/{code}/.
 */
const PS_PARTIAL_SITE_CONFIGS = [
  'field.field.node.offer.field_address',
];

/**
 * Fallback install YAML when merging a partial site override into an empty target.
 */
const PS_PARTIAL_SITE_CONFIG_BASE = [
  'field.field.node.offer.field_address' => 'web/modules/custom/ps_offer/config/install/field.field.node.offer.field_address.yml',
];

/**
 * Merges a partial config array into a base config array.
 *
 * @param array<string, mixed> $base
 * @param array<string, mixed> $override
 *
 * @return array<string, mixed>
 */
function ps_config_merge_partial(array $base, array $override): array {
  return array_replace_recursive($base, $override);
}

/**
 * Reads YAML config data from a file path.
 *
 * @return array<string, mixed>
 */
function ps_config_read_yaml_file(string $path): array {
  if (!is_file($path)) {
    throw new \RuntimeException(sprintf('Missing YAML file: %s', $path));
  }

  $data = Yaml::parseFile($path);
  if (!is_array($data)) {
    throw new \RuntimeException(sprintf('Invalid YAML file: %s', $path));
  }

  return $data;
}

/**
 * Writes config data as YAML to a file path.
 *
 * @param array<string, mixed> $data
 */
function ps_config_write_yaml_file(string $path, array $data): void {
  $dir = dirname($path);
  if (!is_dir($dir) && !mkdir($dir, 0775, TRUE) && !is_dir($dir)) {
    throw new \RuntimeException(sprintf('Could not create directory: %s', $dir));
  }

  file_put_contents($path, Yaml::dump($data, 4, 2));
}

/**
 * Merges a partial override YAML file into a target CMI file.
 *
 * When the target file is missing, the module install YAML is used as base.
 */
function ps_config_merge_yaml_files(string $targetPath, string $overridePath, string $srcRoot): void {
  if (!is_file($overridePath)) {
    throw new \RuntimeException(sprintf('Missing override YAML: %s', $overridePath));
  }

  $configName = basename($overridePath, '.yml');
  $override = ps_config_read_yaml_file($overridePath);
  unset($override['_comment']);

  $base = [];
  if (is_file($targetPath)) {
    $base = ps_config_read_yaml_file($targetPath);
  }
  elseif (isset(PS_PARTIAL_SITE_CONFIG_BASE[$configName])) {
    $basePath = $srcRoot . '/' . PS_PARTIAL_SITE_CONFIG_BASE[$configName];
    $base = ps_config_read_yaml_file($basePath);
  }

  ps_config_write_yaml_file($targetPath, ps_config_merge_partial($base, $override));
}

/**
 * Resolves allowed Address ISO codes for a PS country code.
 *
 * @return string[]
 */
function ps_config_resolve_offer_address_countries(string $psCountryCode): array {
  $psCountryCode = strtolower(trim($psCountryCode));
  if ($psCountryCode === '' || $psCountryCode === 'com') {
    return [];
  }

  return [strtoupper($psCountryCode)];
}

/**
 * Renders partial YAML for offer address available_countries.
 */
function ps_config_render_offer_address_override_yaml(string $psCountryCode): string {
  $countries = ps_config_resolve_offer_address_countries($psCountryCode);
  $lines = [
    '# GENERATED — do not edit directly.',
    '# Source: scripts/multisite/countries.yml via make generate-multisite',
    '# Partial CMI override (merged at seed / install-from-conf).',
    'settings:',
  ];

  if ($countries === []) {
    $lines[] = '  available_countries: {  }';
  }
  else {
    $lines[] = '  available_countries:';
    foreach ($countries as $country) {
      $lines[] = '    - ' . $country;
    }
  }

  return implode("\n", $lines) . "\n";
}
