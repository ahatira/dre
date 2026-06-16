<?php

/**
 * @file
 * Purges entities listed in ps_demo/content/ (dev reset before re-import).
 *
 * Usage: drush php:script scripts/tools/purge_ps_demo_content.php
 */

declare(strict_types=1);

use Drupal\Component\Serialization\Yaml;

$module_path = DRUPAL_ROOT . '/' . \Drupal::service('extension.list.module')->getPath('ps_demo') . '/content';
$files = glob($module_path . '/*/*.yml') ?: [];
$deleted = 0;
$uuids_by_type = [];

foreach ($files as $file) {
  $data = Yaml::decode((string) file_get_contents($file));
  if (empty($data['_meta']['uuid']) || empty($data['_meta']['entity_type'])) {
    continue;
  }

  $type = (string) $data['_meta']['entity_type'];
  $uuid = (string) $data['_meta']['uuid'];
  $uuids_by_type[$type][] = $uuid;

  try {
    $entity = \Drupal::service('entity.repository')->loadEntityByUuid($type, $uuid);
  }
  catch (\Exception) {
    $entity = NULL;
  }

  if ($entity) {
    $entity->delete();
    $deleted++;
  }
}

// Remove corrupted rows left by partial imports (entity API cannot load them).
if (!empty($uuids_by_type['menu_link_content'])) {
  $orphan_ids = \Drupal::database()->select('menu_link_content', 'mlc')
    ->fields('mlc', ['id'])
    ->condition('uuid', $uuids_by_type['menu_link_content'], 'IN')
    ->execute()
    ->fetchCol();

  foreach ($orphan_ids as $id) {
    if (\Drupal::entityTypeManager()->getStorage('menu_link_content')->load($id)) {
      continue;
    }
    foreach ([
      'menu_link_content_data',
      'menu_link_content_field_revision',
      'menu_link_content_revision',
      'menu_link_content',
    ] as $table) {
      \Drupal::database()->delete($table)->condition('id', $id)->execute();
    }
    print "Purged orphan menu link $id\n";
  }
}

if (!empty($uuids_by_type['node'])) {
  $orphan_nids = \Drupal::database()->select('node', 'n')
    ->fields('n', ['nid'])
    ->condition('uuid', $uuids_by_type['node'], 'IN')
    ->execute()
    ->fetchCol();

  foreach ($orphan_nids as $nid) {
    if (\Drupal::entityTypeManager()->getStorage('node')->load($nid)) {
      continue;
    }
    foreach (['node_field_data', 'node_field_revision', 'node_revision', 'node'] as $table) {
      \Drupal::database()->delete($table)->condition('nid', $nid)->execute();
    }
    print "Purged orphan node $nid\n";
  }
}

\Drupal::service('plugin.manager.menu.link')->rebuild();
print "Deleted $deleted exported entities.\n";
