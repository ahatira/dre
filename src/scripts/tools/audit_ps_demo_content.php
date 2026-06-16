<?php

/**
 * @file
 * Verifies ps_demo/content exports are present on the current site.
 *
 * Usage: drush php:script modules/custom/ps_demo/scripts/audit-content.php
 */

declare(strict_types=1);

use Drupal\Component\Serialization\Yaml;

$path = DRUPAL_ROOT . '/' . \Drupal::service('extension.list.module')->getPath('ps_demo') . '/content';
$missing = [];
$ok = 0;

foreach (glob($path . '/*/*.yml') ?: [] as $file) {
  $data = Yaml::decode((string) file_get_contents($file));
  $type = (string) ($data['_meta']['entity_type'] ?? '');
  $uuid = (string) ($data['_meta']['uuid'] ?? '');
  if ($type === '' || $uuid === '') {
    continue;
  }
  try {
    $entity = \Drupal::service('entity.repository')->loadEntityByUuid($type, $uuid);
  }
  catch (\Exception) {
    $entity = NULL;
  }
  if ($entity === NULL) {
    $missing[] = "{$type}:{$uuid}";
  }
  else {
    $ok++;
  }
}

echo "Exported entities present: {$ok}\n";
echo "Missing: " . count($missing) . "\n";
foreach ($missing as $line) {
  echo "  {$line}\n";
}

$faqUuids = [
  'b2000004-0000-4000-8000-000000000001',
  'b2000004-0000-4000-8000-000000000002',
  'b2000004-0000-4000-8000-000000000003',
  'b2000004-0000-4000-8000-000000000004',
];
echo "\nFAQ nodes:\n";
foreach ($faqUuids as $uuid) {
  try {
    $node = \Drupal::service('entity.repository')->loadEntityByUuid('node', $uuid);
  }
  catch (\Exception) {
    $node = NULL;
  }
  if (!$node) {
    echo "  MISSING faq_item {$uuid}\n";
    continue;
  }
  $langs = implode(',', array_keys($node->getTranslationLanguages()));
  echo "  {$uuid} nid={$node->id()} langs={$langs} title={$node->label()}\n";
}

$heroUuid = 'c1000002-0000-4000-8000-000000000001';
try {
  $media = \Drupal::service('entity.repository')->loadEntityByUuid('media', $heroUuid);
}
catch (\Exception) {
  $media = NULL;
}
echo "\nHero media: " . ($media ? "present mid={$media->id()}" : 'MISSING') . "\n";
