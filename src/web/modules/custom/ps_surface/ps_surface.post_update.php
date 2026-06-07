<?php

/**
 * @file
 * Post update hooks for ps_surface.
 */

declare(strict_types=1);

use Drupal\Core\Config\FileStorage;

/**
 * Imports the surface division table row display.
 */
function ps_surface_post_update_import_table_row_display(): void {
  $path = \Drupal::service('extension.list.module')->getPath('ps_surface') . '/config/install';
  $storage = new FileStorage($path);
  foreach ([
    'core.entity_view_mode.ps_surface_division.table_row',
    'core.entity_view_display.ps_surface_division.ps_surface_division.table_row',
  ] as $config_name) {
    $data = $storage->read($config_name);
    if ($data !== FALSE) {
      \Drupal::configFactory()->getEditable($config_name)->setData($data)->save(TRUE);
    }
  }
}
