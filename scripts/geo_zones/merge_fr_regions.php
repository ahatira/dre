#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Merges fr.regions_seed.yml into data/geo_zones/fr.yml as region zones
 * and links departments via parent. Regenerates com.yml from fr.yml.
 *
 * Usage (repo root):
 *   php scripts/geo_zones/merge_fr_regions.php
 */

$root = dirname(__DIR__, 2);
$autoload = $root . '/src/vendor/autoload.php';
if (!is_file($autoload)) {
  fwrite(STDERR, "Composer autoload not found.\n");
  exit(1);
}
require $autoload;

use Symfony\Component\Yaml\Yaml;

$moduleData = $root . '/src/web/modules/custom/ps_search/data';
$frPath = $moduleData . '/geo_zones/fr.yml';
$regionsPath = $moduleData . '/geo_zones/fr.regions_seed.yml';
$comPath = $moduleData . '/geo_zones/com.yml';

if (!is_file($frPath) || !is_file($regionsPath)) {
  fwrite(STDERR, "Missing fr.yml or geo_zones/fr.regions_seed.yml.\n");
  exit(1);
}

$frPayload = Yaml::parseFile($frPath);
$regionsData = Yaml::parseFile($regionsPath);
if (!is_array($frPayload) || !is_array($regionsData)) {
  fwrite(STDERR, "Invalid YAML input.\n");
  exit(1);
}

$zones = $frPayload['zones'] ?? [];
if (!is_array($zones)) {
  fwrite(STDERR, "fr.yml has no zones.\n");
  exit(1);
}

/** @var array<string, array<string, mixed>> $departmentByCode */
$departmentByCode = [];
foreach ($zones as $id => $data) {
  if (!is_string($id) || !is_array($data)) {
    continue;
  }
  if (($data['type'] ?? '') !== 'department') {
    continue;
  }
  $code = strtoupper(trim((string) ($data['code'] ?? '')));
  if ($code !== '') {
    $departmentByCode[$code] = ['id' => $id, 'data' => $data];
  }
}

$regionZones = [];
$weight = 1;
foreach ($regionsData as $slug => $regionDef) {
  if (!is_string($slug) || !is_array($regionDef)) {
    continue;
  }
  $label = trim((string) ($regionDef['label'] ?? ''));
  if ($label === '') {
    continue;
  }

  $regionSlug = strtolower(trim($slug));
  $regionId = 'region.fr.' . $regionSlug;
  $departmentCodes = [];
  foreach ($regionDef['departments'] ?? [] as $code) {
    if (is_string($code) || is_numeric($code)) {
      $normalized = strtoupper(trim((string) $code));
      if ($normalized !== '') {
        $departmentCodes[] = $normalized;
      }
    }
  }

  $childLats = [];
  $childLngs = [];
  $swLat = NULL;
  $swLng = NULL;
  $neLat = NULL;
  $neLng = NULL;
  $postalPrefixes = [];

  foreach ($departmentCodes as $deptCode) {
    $dept = $departmentByCode[$deptCode] ?? NULL;
    if ($dept === NULL) {
      $postalPrefixes[] = $deptCode;
      continue;
    }

    $deptData = $dept['data'];
    $deptId = $dept['id'];
    $deptData['parent'] = $regionId;
    $zones[$deptId] = $deptData;

    if (isset($deptData['lat'], $deptData['lng']) && is_numeric($deptData['lat']) && is_numeric($deptData['lng'])) {
      $childLats[] = (float) $deptData['lat'];
      $childLngs[] = (float) $deptData['lng'];
    }

    $bbox = is_array($deptData['bbox'] ?? NULL) ? $deptData['bbox'] : [];
    if (isset($bbox['sw_lat'], $bbox['sw_lng'], $bbox['ne_lat'], $bbox['ne_lng'])) {
      $swLat = $swLat === NULL ? (float) $bbox['sw_lat'] : min($swLat, (float) $bbox['sw_lat']);
      $swLng = $swLng === NULL ? (float) $bbox['sw_lng'] : min($swLng, (float) $bbox['sw_lng']);
      $neLat = $neLat === NULL ? (float) $bbox['ne_lat'] : max($neLat, (float) $bbox['ne_lat']);
      $neLng = $neLng === NULL ? (float) $bbox['ne_lng'] : max($neLng, (float) $bbox['ne_lng']);
    }

    foreach ($deptData['postal_prefixes'] ?? [] as $prefix) {
      if (is_string($prefix) && $prefix !== '') {
        $postalPrefixes[] = $prefix;
      }
    }
  }

  $postalPrefixes = array_values(array_unique($postalPrefixes));
  if ($postalPrefixes === [] && $departmentCodes !== []) {
    $postalPrefixes = $departmentCodes;
  }

  $lat = $childLats !== [] ? array_sum($childLats) / count($childLats) : 46.603354;
  $lng = $childLngs !== [] ? array_sum($childLngs) / count($childLngs) : 1.888334;

  if ($swLat === NULL || $swLng === NULL || $neLat === NULL || $neLng === NULL) {
    $radius = 0.45;
    $swLat = $lat - $radius;
    $swLng = $lng - $radius;
    $neLat = $lat + $radius;
    $neLng = $lng + $radius;
  }

  $regionZones[$regionId] = [
    'type' => 'region',
    'code' => strtoupper(str_replace('-', '_', $regionSlug)),
    'label' => $label,
    'slug' => $regionSlug,
    'lat' => round($lat, 4),
    'lng' => round($lng, 4),
    'bbox' => [
      'sw_lat' => $swLat,
      'sw_lng' => $swLng,
      'ne_lat' => $neLat,
      'ne_lng' => $neLng,
    ],
    'postal_prefixes' => $postalPrefixes,
    'weight' => $weight,
  ];
  $weight++;
}

$mergedZones = array_merge($regionZones, $zones);
ksort($mergedZones, SORT_STRING);

$frPayload['zones'] = $mergedZones;
file_put_contents($frPath, Yaml::dump($frPayload, 4, 2));
fwrite(STDOUT, "OK fr.yml: " . count($regionZones) . " regions + " . count($departmentByCode) . " departments\n");

$sourceCountry = 'fr';
$targetCountry = 'com';
$comZones = [];
foreach ($mergedZones as $id => $data) {
  if (!is_string($id) || !is_array($data)) {
    continue;
  }
  $parts = explode('.', $id, 3);
  if (count($parts) !== 3 || $parts[1] !== $sourceCountry) {
    throw new RuntimeException(sprintf('Unexpected zone id "%s".', $id));
  }
  $comId = $parts[0] . '.' . $targetCountry . '.' . $parts[2];
  $comZones[$comId] = $data;
  if (isset($data['parent']) && is_string($data['parent'])) {
    $comZones[$comId]['parent'] = preg_replace(
      '/\.fr\./',
      '.' . $targetCountry . '.',
      $data['parent'],
      1,
    );
  }
}
ksort($comZones, SORT_STRING);

$defaultZone = (string) ($frPayload['default_zone'] ?? '');
$comDefault = preg_replace('/\.fr\./', '.com.', $defaultZone, 1);

$comPayload = [
  'country' => $targetCountry,
  'default_zone' => $comDefault,
  'zones' => $comZones,
];
file_put_contents($comPath, Yaml::dump($comPayload, 4, 2));
fwrite(STDOUT, "OK com.yml: " . count($comZones) . " zones\n");
