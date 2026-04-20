<?php

/**
 * @file
 * Temporary script: apply ISO maquette config changes to active Drupal config.
 *
 * Usage: vendor/bin/drush php:script scripts/apply-offer-search-config.php
 */

use Drupal\Core\Serialization\Yaml;

$config_factory = \Drupal::configFactory();

// ---------------------------------------------------------------------------
// 1. Dictionary entries.
// ---------------------------------------------------------------------------
$dict_entries = [
  'property_type_cow' => [
    'id' => 'property_type_cow',
    'dictionary_type' => 'property_type',
    'code' => 'COW',
    'label' => 'Coworking',
    'description' => 'Coworking or shared workspace property type',
    'weight' => 2,
    'status' => TRUE,
    'deprecated' => FALSE,
    'metadata' => ['icon' => ''],
  ],
  'property_type_com' => [
    'id' => 'property_type_com',
    'dictionary_type' => 'property_type',
    'code' => 'COM',
    'label' => 'Retail / Commercial',
    'description' => 'Retail or commercial property type',
    'weight' => 3,
    'status' => TRUE,
    'deprecated' => FALSE,
    'metadata' => ['icon' => ''],
  ],
  'property_type_res' => [
    'id' => 'property_type_res',
    'dictionary_type' => 'property_type',
    'code' => 'RES',
    'label' => 'Residential',
    'description' => 'Residential property type',
    'weight' => 4,
    'status' => TRUE,
    'deprecated' => FALSE,
    'metadata' => ['icon' => ''],
  ],
  'property_type_ter' => [
    'id' => 'property_type_ter',
    'dictionary_type' => 'property_type',
    'code' => 'TER',
    'label' => 'Land',
    'description' => 'Land or terrain property type',
    'weight' => 5,
    'status' => TRUE,
    'deprecated' => FALSE,
    'metadata' => ['icon' => ''],
  ],
  'property_type_log' => [
    'id' => 'property_type_log',
    'dictionary_type' => 'property_type',
    'code' => 'LOG',
    'label' => 'Industrial / Logistics',
    'description' => 'Industrial or logistics warehouse property type',
    'weight' => 6,
    'status' => TRUE,
    'deprecated' => FALSE,
    'metadata' => ['icon' => ''],
  ],
  'transaction_type_ven' => [
    'id' => 'transaction_type_ven',
    'dictionary_type' => 'transaction_type',
    'code' => 'VEN',
    'label' => 'Sale',
    'description' => 'Property for sale',
    'weight' => 1,
    'status' => TRUE,
    'deprecated' => FALSE,
    'metadata' => ['icon' => ''],
  ],
];

foreach ($dict_entries as $entry_id => $data) {
  $config_name = 'ps_dictionary.entry.' . $entry_id;
  $existing = $config_factory->get($config_name);
  if ($existing->isNew()) {
    $config_factory->getEditable($config_name)->setData($data)->save();
    echo "[ok] Created $config_name\n";
  }
  else {
    echo "[skip] $config_name already exists\n";
  }
}

// ---------------------------------------------------------------------------
// 2. Search API index — add field_reference.
// ---------------------------------------------------------------------------
$index_config = $config_factory->getEditable('search_api.index.ps_offer_search');
$fields = $index_config->get('field_settings');

if (!isset($fields['field_reference'])) {
  $fields['field_reference'] = [
    'label' => 'Reference',
    'datasource_id' => 'entity:node',
    'property_path' => 'field_reference',
    'type' => 'text',
    'boost' => '5',
  ];
  $index_config->set('field_settings', $fields)->save();
  echo "[ok] search_api.index — field_reference added\n";
}
else {
  echo "[skip] field_reference already in index\n";
}

// ---------------------------------------------------------------------------
// 3. Views config — use_ajax + multiple + location + reference filters.
// ---------------------------------------------------------------------------
$views_config = $config_factory->getEditable('views.view.ps_offer_search');

// Enable AJAX.
$views_config->set('display.default.display_options.use_ajax', TRUE);

// Multiple for property_type.
$views_config->set('display.default.display_options.filters.field_property_type.expose.multiple', TRUE);
$views_config->set('display.default.display_options.filters.field_property_type.group_info.multiple', TRUE);

// Multiple for transaction_type.
$views_config->set('display.default.display_options.filters.field_transaction_types.expose.multiple', TRUE);
$views_config->set('display.default.display_options.filters.field_transaction_types.group_info.multiple', TRUE);

// Add location filter (fulltext scoped to field_address).
$filters = $views_config->get('display.default.display_options.filters');

if (!isset($filters['location'])) {
  $filters['location'] = [
    'id' => 'location',
    'table' => 'search_api_index_ps_offer_search',
    'field' => 'search_api_fulltext',
    'relationship' => 'none',
    'group_type' => 'group',
    'admin_label' => '',
    'operator' => 'and',
    'value' => '',
    'group' => 1,
    'exposed' => TRUE,
    'expose' => [
      'operator_id' => 'location_op',
      'label' => 'Location(s)',
      'description' => '',
      'use_operator' => FALSE,
      'operator' => 'location_op',
      'identifier' => 'location',
      'required' => FALSE,
      'remember' => FALSE,
      'multiple' => FALSE,
      'remember_roles' => ['authenticated' => 'authenticated', 'anonymous' => '0'],
    ],
    'is_grouped' => FALSE,
    'group_info' => [
      'label' => '',
      'description' => '',
      'identifier' => '',
      'optional' => TRUE,
      'widget' => 'select',
      'multiple' => FALSE,
      'remember' => FALSE,
      'default_group' => 'All',
      'default_group_multiple' => [],
      'group_items' => [],
    ],
    'parse_mode' => 'terms',
    'min_length' => 0,
    'fields' => ['field_address'],
    'plugin_id' => 'search_api_fulltext',
  ];
  echo "[ok] views — location filter added\n";
}
else {
  echo "[skip] location filter already exists\n";
}

// Add field_reference filter.
if (!isset($filters['field_reference'])) {
  $filters['field_reference'] = [
    'id' => 'field_reference',
    'table' => 'search_api_index_ps_offer_search',
    'field' => 'field_reference',
    'relationship' => 'none',
    'group_type' => 'group',
    'admin_label' => '',
    'operator' => 'and',
    'value' => '',
    'group' => 1,
    'exposed' => TRUE,
    'expose' => [
      'operator_id' => 'field_reference_op',
      'label' => 'Ad reference',
      'description' => '',
      'use_operator' => FALSE,
      'operator' => 'field_reference_op',
      'identifier' => 'reference',
      'required' => FALSE,
      'remember' => FALSE,
      'multiple' => FALSE,
      'remember_roles' => ['authenticated' => 'authenticated', 'anonymous' => '0'],
    ],
    'is_grouped' => FALSE,
    'group_info' => [
      'label' => '',
      'description' => '',
      'identifier' => '',
      'optional' => TRUE,
      'widget' => 'select',
      'multiple' => FALSE,
      'remember' => FALSE,
      'default_group' => 'All',
      'default_group_multiple' => [],
      'group_items' => [],
    ],
    'parse_mode' => 'terms',
    'min_length' => 0,
    'fields' => ['field_reference'],
    'plugin_id' => 'search_api_fulltext',
  ];
  echo "[ok] views — field_reference filter added\n";
}
else {
  echo "[skip] field_reference filter already exists\n";
}

$views_config->set('display.default.display_options.filters', $filters)->save();
echo "[ok] views.view.ps_offer_search saved\n";

echo "\nDone. Run 'drush cr' to clear caches.\n";
