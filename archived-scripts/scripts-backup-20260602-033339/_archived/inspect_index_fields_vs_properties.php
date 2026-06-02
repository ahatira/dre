<?php

declare(strict_types=1);

$index = \Drupal\search_api\Entity\Index::load('offers');
if (!$index) {
  echo "Index not found\n";
  return;
}

// Get all fields configured in field_settings
$configured_fields = $index->getFields();
echo "=== CONFIGURED FIELDS ===\n";
foreach ($configured_fields as $field_id => $field) {
  if (str_starts_with($field_id, 'feature_')) {
    echo $field_id . ' => ' . $field->getLabel() . ' (type: ' . $field->getType() . ")\n";
  }
}

echo "\n=== AVAILABLE PROPERTIES (from processors) ===\n";
$properties = $index->getPropertyDefinitions(NULL);
foreach ($properties as $prop_id => $property) {
  if (str_starts_with($prop_id, 'feature_')) {
    echo $prop_id . ' => ' . $property->getLabel() . ' (type: ' . $property->getDataType() . ")\n";
  }
}

echo "\n=== MISSING: properties not in field_settings ===\n";
$configured_ids = array_keys($configured_fields);
foreach (array_keys($properties) as $prop_id) {
  if (str_starts_with($prop_id, 'feature_') && !in_array($prop_id, $configured_ids, TRUE)) {
    echo $prop_id . " (NOT CONFIGURED)\n";
  }
}
