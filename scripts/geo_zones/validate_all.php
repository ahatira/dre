#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Validates all module geo zone YAML sources.
 *
 * Usage (repo root):
 *   php scripts/geo_zones/validate_all.php
 */

$root = dirname(__DIR__, 2);
$autoload = $root . '/src/vendor/autoload.php';
require $autoload;

$moduleSrc = $root . '/src/web/modules/custom/ps_search/src';
require $moduleSrc . '/GeoZone/GeoZoneType.php';
require $moduleSrc . '/ValueObject/GeoBoundingBox.php';
require $moduleSrc . '/ValueObject/GeoZone.php';
require $moduleSrc . '/GeoZone/GeoZoneValidator.php';

use Drupal\ps_search\GeoZone\GeoZoneValidator;
use Symfony\Component\Yaml\Yaml;

$validator = new GeoZoneValidator();
$dataDir = $root . '/src/web/modules/custom/ps_search/data/geo_zones';
$files = glob($dataDir . '/*.yml') ?: [];
$files = array_filter(
  $files,
  static fn(string $path): bool => !str_contains($path, '/centroids/')
    && !str_contains(basename($path), 'seed'),
);

$failed = FALSE;
foreach ($files as $file) {
  $country = basename($file, '.yml');
  $payload = Yaml::parse((string) file_get_contents($file));
  if (!is_array($payload)) {
    fwrite(STDERR, "Invalid YAML: {$file}\n");
    $failed = TRUE;
    continue;
  }
  $errors = $validator->validateCountryPayload(
    $country,
    is_array($payload['zones'] ?? NULL) ? $payload['zones'] : [],
    is_string($payload['default_zone'] ?? NULL) ? $payload['default_zone'] : NULL,
  );
  if ($errors === []) {
    fwrite(STDOUT, "OK {$country} (" . count($payload['zones']) . " zones)\n");
    continue;
  }
  $failed = TRUE;
  fwrite(STDERR, "FAIL {$country}:\n- " . implode("\n- ", $errors) . "\n");
}

exit($failed ? 1 : 0);
