<?php

/**
 * @file
 * Post update functions for PS Demo.
 */

declare(strict_types=1);

/**
 * Import homepage copy config (Config-First migration from theme YAML).
 */
function ps_demo_post_update_9001_homepage_config(): void {
  $path = \Drupal::service('extension.list.module')->getPath('ps_demo') . '/config/install/ps_demo.homepage.yml';
  if (!is_readable($path)) {
    return;
  }
  $data = \Drupal\Component\Serialization\Yaml::decode((string) file_get_contents($path));
  \Drupal::configFactory()->getEditable('ps_demo.homepage')->setData($data)->save(TRUE);

  \Drupal::configFactory()->getEditable('ps_demo.settings')
    ->set('front_page', '/node/1')
    ->save(TRUE);
}
