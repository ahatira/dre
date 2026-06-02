<?php
use Drupal\Component\Serialization\Yaml;

$paths = [
  '/var/www/html/web/modules/custom/ps_migrate/config/install/migrate_plus.migration.ps_agent_avatar_file_from_xml.yml',
  '/var/www/html/web/modules/custom/ps_migrate/config/install/migrate_plus.migration.ps_agent_from_xml.yml',
];

foreach ($paths as $path) {
  $data = Yaml::decode(file_get_contents($path));
  $id = $data['id'] ?? NULL;
  if (!$id) {
    continue;
  }
  \Drupal::service('config.factory')->getEditable('migrate_plus.migration.' . $id)->setData($data)->save();
  print "updated $id\n";
}
