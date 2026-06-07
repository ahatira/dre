<?php

/**
 * @file
 * Post update hooks for ps_agent.
 */

declare(strict_types=1);

use Drupal\Core\Config\FileStorage;

/**
 * Imports the agent sidebar view mode and display.
 */
function ps_agent_post_update_import_sidebar_display(): void {
  $path = \Drupal::service('extension.list.module')->getPath('ps_agent') . '/config/install';
  $storage = new FileStorage($path);
  foreach ([
    'core.entity_view_mode.ps_agent.sidebar',
    'core.entity_view_display.ps_agent.default.sidebar',
  ] as $config_name) {
    $data = $storage->read($config_name);
    if ($data !== FALSE) {
      \Drupal::configFactory()->getEditable($config_name)->setData($data)->save(TRUE);
    }
  }
}

/**
 * Aligns the card display with the offer sidebar field set.
 */
function ps_agent_post_update_align_card_display_for_offer_sidebar(): void {
  $path = \Drupal::service('extension.list.module')->getPath('ps_agent') . '/config/install';
  $storage = new FileStorage($path);
  $config_name = 'core.entity_view_display.ps_agent.default.card';
  $data = $storage->read($config_name);
  if ($data !== FALSE) {
    \Drupal::configFactory()->getEditable($config_name)->setData($data)->save(TRUE);
  }
}

/**
 * Switches agent phone displays to telephone_formatter (libphonenumber).
 */
function ps_agent_post_update_apply_telephone_formatter_displays(): void {
  $path = \Drupal::service('extension.list.module')->getPath('ps_agent') . '/config/install';
  $storage = new FileStorage($path);
  foreach ([
    'core.entity_view_display.ps_agent.default.card',
    'core.entity_view_display.ps_agent.default.default',
    'core.entity_view_display.ps_agent.default.full',
  ] as $config_name) {
    $data = $storage->read($config_name);
    if ($data !== FALSE) {
      \Drupal::configFactory()->getEditable($config_name)->setData($data)->save(TRUE);
    }
  }
}

/**
 * Removes the deprecated sidebar view mode and its display configuration.
 */
function ps_agent_post_update_remove_sidebar_view_mode(): void {
  $config_factory = \Drupal::configFactory();
  foreach ([
    'core.entity_view_display.ps_agent.default.sidebar',
    'core.entity_view_mode.ps_agent.sidebar',
  ] as $config_name) {
    $config_factory->getEditable($config_name)->delete();
  }

  $path = \Drupal::service('extension.list.module')->getPath('ps_agent') . '/config/install';
  $storage = new FileStorage($path);
  $config_name = 'core.entity_view_display.ps_agent.default.card';
  $data = $storage->read($config_name);
  if ($data !== FALSE) {
    $config_factory->getEditable($config_name)->setData($data)->save(TRUE);
  }
}
