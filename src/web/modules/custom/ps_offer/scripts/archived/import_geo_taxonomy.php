<?php

/**
 * Script d'import des termes taxonomy depuis CSV géographiques
 * 
 * Usage:
 *   drush php:script scripts/import_geo_taxonomy.php FR
 *   drush php:script scripts/import_geo_taxonomy.php BE
 *   drush php:script scripts/import_geo_taxonomy.php ALL
 * 
 * @file
 * Import taxonomy terms from geographic CSV files.
 */

use Drupal\taxonomy\Entity\Term;
use Drupal\Core\Entity\EntityStorageException;

// Get country code from arguments
$country_code = $extra[0] ?? 'FR';
$country_code = strtoupper($country_code);

// CSV directory
$csv_dir = '/var/www/html/imports/csv/taxonomy';

// Determine which countries to process
if ($country_code === 'ALL') {
  $countries = ['FR', 'BE', 'LU', 'CH'];
}
else {
  $countries = [$country_code];
}

// Statistics
$stats = [
  'regions' => 0,
  'departments' => 0,
  'cities' => 0,
  'errors' => 0,
];

$term_storage = \Drupal::entityTypeManager()->getStorage('taxonomy_term');

/**
 * Get or create a taxonomy term.
 */
function getOrCreateTerm($storage, $vid, $name, $extra_fields = []) {
  // Search for existing term
  $terms = $storage->loadByProperties([
    'vid' => $vid,
    'name' => $name,
  ]);

  if (!empty($terms)) {
    return reset($terms);
  }

  // Create new term
  $term_data = [
    'vid' => $vid,
    'name' => $name,
    'langcode' => 'en',
  ];

  // Add extra fields if provided
  foreach ($extra_fields as $field => $value) {
    $term_data[$field] = $value;
  }

  try {
    $term = Term::create($term_data);
    $term->save();
    return $term;
  }
  catch (EntityStorageException $e) {
    \Drupal::logger('ps_offer')->error('Error creating term: @message', ['@message' => $e->getMessage()]);
    return NULL;
  }
}

// Process each country
foreach ($countries as $country) {
  $csv_file = "{$csv_dir}/reg_dep_city_{$country}.csv";

  if (!file_exists($csv_file)) {
    echo "⚠️  Fichier non trouvé : {$csv_file}\n";
    continue;
  }

  echo "\n";
  echo "═══════════════════════════════════════════════════════════\n";
  echo "  Import {$country}\n";
  echo "═══════════════════════════════════════════════════════════\n\n";

  $handle = fopen($csv_file, 'r');
  $header = fgetcsv($handle);

  // Cache for created terms to avoid duplicates
  $region_cache = [];
  $dept_cache = [];
  $city_cache = [];

  $line_count = 0;

  while (($row = fgetcsv($handle)) !== FALSE) {
    $line_count++;
    $data = array_combine($header, $row);

    if (empty($data['city_name'])) {
      continue;
    }

    // Create Region if not in cache
    $region_key = $data['region_code'] . '|' . $data['region_name'];
    if (!isset($region_cache[$region_key]) && !empty($data['region_name'])) {
      $region = getOrCreateTerm($term_storage, 'region', $data['region_name']);
      if ($region) {
        $region_cache[$region_key] = $region->id();
        $stats['regions']++;
      }
    }

    // Create Department if not in cache
    $dept_key = $data['department_code'] . '|' . $data['department_name'];
    if (!isset($dept_cache[$dept_key]) && !empty($data['department_name'])) {
      $dept = getOrCreateTerm($term_storage, 'department', $data['department_code']);
      if ($dept) {
        $dept_cache[$dept_key] = $dept->id();
        $stats['departments']++;
      }
    }

    // Create City
    $city_key = $data['city_code'] . '|' . $data['city_name'];
    if (!isset($city_cache[$city_key])) {
      $city = getOrCreateTerm($term_storage, 'city', $data['city_name']);
      if ($city) {
        $city_cache[$city_key] = $city->id();
        $stats['cities']++;
      }
      else {
        $stats['errors']++;
      }
    }

    // Progress indicator every 500 lines
    if ($line_count % 500 === 0) {
      echo "  📊 {$line_count} lignes traitées...\n";
    }
  }

  fclose($handle);

  echo "  ✅ {$line_count} lignes traitées pour {$country}\n\n";
}

// Display final statistics
echo "═══════════════════════════════════════════════════════════\n";
echo "  Statistiques d'import\n";
echo "═══════════════════════════════════════════════════════════\n\n";
echo "  🌍 Régions créées     : {$stats['regions']}\n";
echo "  🗺️  Départements créés : {$stats['departments']}\n";
echo "  🏙️  Villes créées      : {$stats['cities']}\n";

if ($stats['errors'] > 0) {
  echo "  ⚠️  Erreurs           : {$stats['errors']}\n";
}

echo "\n✅ Import terminé\n\n";
