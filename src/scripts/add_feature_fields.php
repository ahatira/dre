<?php

use Drupal\search_api\Entity\Index;

// Load the search index.
$index = Index::load('offers');

if ($index) {
  $fields_to_add = [
    'feature_amenagements__tec_hall_daccueil',
    'feature_equipements__tec_cablage_informatique',
    'feature_hauteurs__tec_hauteur_libre',
  ];

  foreach ($fields_to_add as $field_id) {
    if (!$index->getField($field_id)) {
      $field = $index->getFieldsHelper()->createField($index, $field_id);
      $index->addField($field);
      echo "Added field: $field_id\n";
    } else {
      echo "Field already exists: $field_id\n";
    }
  }

  $index->save();
  echo "Index saved successfully.\n";
} else {
  echo "Index 'offers' not found.\n";
}
