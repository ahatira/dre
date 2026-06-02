<?php
$storage = \Drupal::service('config.storage');
$editable = \Drupal::service('config.factory')->getEditable('migrate_plus.migration.ps_offer_from_xml');

$editable->set('process.title', [
  'plugin' => 'offer_composed_title',
  'source' => ['operation_code', 'type_code', 'address_city', 'business_id', 'address_country'],
  'mode' => 'title',
]);

$editable->set('process.field_commercial_title', [
  'plugin' => 'offer_composed_title',
  'source' => ['operation_code', 'type_code', 'all_surface_values', 'business_id', 'address_country'],
  'mode' => 'commercial',
]);

$editable->save();

$config = \Drupal::config('migrate_plus.migration.ps_offer_from_xml');
print "title mapping:\n";
print_r($config->get('process.title'));
print "commercial mapping:\n";
print_r($config->get('process.field_commercial_title'));
