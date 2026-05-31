<?php

declare(strict_types=1);

use Symfony\Component\Yaml\Yaml;

$config_ids = [
  'migrate_plus.migration.ps_dictionary_asset_type_from_xml',
  'migrate_plus.migration.ps_dictionary_operation_type_from_xml',
  'migrate_plus.migration.ps_feature_groups_from_xml',
  'migrate_plus.migration.ps_feature_definitions_from_xml',
  'migrate_plus.migration.ps_agent_avatar_file_from_xml',
  'migrate_plus.migration.ps_agent_from_xml',
  'migrate_plus.migration.ps_file_from_xml',
  'migrate_plus.migration.ps_media_from_xml',
  'migrate_plus.migration.ps_media_virtual_tour_from_xml',
  'migrate_plus.migration.ps_surface_division_from_xml',
  'migrate_plus.migration.ps_offer_from_xml',
  'migrate_plus.migration.ps_offer_translations_from_xml',
  'migrate_plus.migration.ps_offer_translations_from_xml_nl',
  'migrate_plus.migration.ps_offer_translations_from_xml_es',
  'migrate_plus.migration.ps_offer_translations_from_xml_it',
  'migrate_plus.migration.ps_offer_translations_from_xml_de',
  'migrate_plus.migration.ps_offer_translations_from_xml_pl',
  'migrate_plus.migration.ps_offer_translations_from_xml_lb',
];

$config_factory = \Drupal::service('config.factory');

foreach ($config_ids as $config_id) {
  $path = DRUPAL_ROOT . "/modules/custom/ps_migrate/config/install/{$config_id}.yml";
  if (!is_file($path)) {
    continue;
  }

  $data = Yaml::parseFile($path);
  if (!is_array($data)) {
    continue;
  }

  $config_factory->getEditable($config_id)->setData($data)->save(TRUE);
}

$obsolete_config_ids = [
  'migrate_plus.migration.ps_dictionary_entry_from_csv',
];

foreach ($obsolete_config_ids as $config_id) {
  $config = $config_factory->getEditable($config_id);
  if ($config->getRawData() !== []) {
    $config->delete();
  }
}

print "ps_migrate migration configs ensured." . PHP_EOL;
