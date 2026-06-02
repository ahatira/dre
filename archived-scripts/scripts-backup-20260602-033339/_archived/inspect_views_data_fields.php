<?php

declare(strict_types=1);

$viewsData = \Drupal::service('views.views_data')->get('search_api_index_offers');
$featureFields = [];

foreach ($viewsData as $key => $definition) {
  if (str_starts_with((string) $key, 'feature_')) {
    $featureFields[] = $key;
  }
}

sort($featureFields);

echo 'feature_fields_count=' . count($featureFields) . "\n";
foreach ($featureFields as $name) {
  echo $name . "\n";
}
