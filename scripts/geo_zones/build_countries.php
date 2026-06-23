#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Builds geo zone YAML for PS countries via GeoZoneBuilder.
 *
 * Usage (repo root):
 *   php scripts/geo_zones/build_countries.php
 *   php scripts/geo_zones/build_countries.php be nl
 *   php scripts/geo_zones/build_countries.php fr com
 */

$root = dirname(__DIR__, 2);
$autoload = $root . '/src/vendor/autoload.php';
if (!is_file($autoload)) {
  fwrite(STDERR, "Composer autoload not found.\n");
  exit(1);
}
require $autoload;

$moduleSrc = $root . '/src/web/modules/custom/ps_search/src';
require $moduleSrc . '/GeoZone/GeoZoneType.php';
require $moduleSrc . '/ValueObject/GeoBoundingBox.php';
require $moduleSrc . '/ValueObject/GeoZone.php';
require $moduleSrc . '/GeoZone/GeoZoneValidator.php';
require $moduleSrc . '/GeoZone/GeoZoneComBuilder.php';
require $moduleSrc . '/GeoZone/GeoZoneDefinitionProvider.php';
require $moduleSrc . '/GeoZone/GeoZoneBuilder.php';

use Drupal\ps_search\GeoZone\GeoZoneBuilder;
use Drupal\ps_search\GeoZone\GeoZoneComBuilder;
use Drupal\ps_search\GeoZone\GeoZoneDefinitionProvider;
use Drupal\ps_search\GeoZone\GeoZoneValidator;

if (!class_exists('Drupal', FALSE)) {
  final class Drupal {
    public static function root(): string {
      return dirname(__DIR__, 2) . '/src/web';
    }
  }
}

$definitionProvider = new GeoZoneDefinitionProvider('modules/custom/ps_search');
$builder = new GeoZoneBuilder(
  $definitionProvider,
  new GeoZoneComBuilder('modules/custom/ps_search'),
  'modules/custom/ps_search',
);
$validator = new GeoZoneValidator();

$requested = array_slice($argv, 1);
$countries = $requested !== [] ? array_map('strtolower', $requested) : $builder->getSupportedCountries();

$failed = FALSE;
foreach ($countries as $countryCode) {
  try {
    $payload = $builder->buildPayload($countryCode);
    $errors = $validator->validateCountryPayload(
      $countryCode,
      is_array($payload['zones'] ?? NULL) ? $payload['zones'] : [],
      is_string($payload['default_zone'] ?? NULL) ? $payload['default_zone'] : NULL,
    );
    if ($errors !== []) {
      fwrite(STDERR, "Validation failed for {$countryCode}:\n- " . implode("\n- ", $errors) . "\n");
      $failed = TRUE;
      continue;
    }

    $path = $builder->exportToModuleData($countryCode, $payload);
    fwrite(STDOUT, "OK {$countryCode}: " . count($payload['zones']) . " zones → {$path}\n");
  }
  catch (\Throwable $exception) {
    fwrite(STDERR, "Failed {$countryCode}: {$exception->getMessage()}\n");
    $failed = TRUE;
  }
}

exit($failed ? 1 : 0);
