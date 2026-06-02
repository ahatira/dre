<?php
use Drupal\Component\Serialization\Yaml;

$path = '/var/www/html/web/modules/custom/ps_migrate/config/install/migrate_plus.migration.ps_offer_translations_from_xml.yml';
$data = Yaml::decode(file_get_contents($path));
\Drupal::service('config.factory')->getEditable('migrate_plus.migration.ps_offer_translations_from_xml')->setData($data)->save();
print "updated\n";
