<?php

/**
 * @file
 * Extracts EN/FR menu titles from ps_demo content exports.
 */

$dir = dirname(__DIR__, 2) . '/web/modules/custom/ps_demo/content/menu_link_content';
foreach (glob($dir . '/*.yml') as $file) {
  $parsed = \Drupal\Component\Serialization\Yaml::decode((string) file_get_contents($file));
  if (!is_array($parsed)) {
    continue;
  }
  $uuid = $parsed['_meta']['uuid'] ?? '';
  $en = $parsed['default']['title'][0]['value'] ?? '';
  $fr = $parsed['translations']['fr']['title'][0]['value'] ?? '';
  echo "{$uuid}|{$en}|{$fr}\n";
}
