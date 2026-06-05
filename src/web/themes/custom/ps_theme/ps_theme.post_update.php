<?php

/**
 * @file
 * Post update hooks for the Property Search theme.
 *
 * Legacy migrations for existing databases. Fresh installs use config/install/
 * (theme) + export/content-structural/ (ps_demo) + make demo.
 */

declare(strict_types=1);

use Drupal\ps_theme\Service\HomepageInstaller;
use Drupal\ps_theme\Utility\DemoContent;

/**
 * Fix homepage view mode id (node.page.full → node.full).
 */
function ps_theme_post_update_9003_homepage_view_mode(): void {
  if (!DemoContent::isManagedByPsDemo()) {
    HomepageInstaller::create(\Drupal::getContainer())->install();
  }
}

/**
 * Enable Language Icons for the Stellar header language switcher.
 */
function ps_theme_post_update_9006_languageicons(): void {
  if (!\Drupal::moduleHandler()->moduleExists('languageicons')) {
    \Drupal::service('module_installer')->install(['languageicons'], TRUE);
  }
}

/**
 * Move header search CTA to the right of the actions menu.
 */
function ps_theme_post_update_9009_header_search_action_order(): void {
  $storage = \Drupal::entityTypeManager()->getStorage('menu_link_content');
  $entities = $storage->loadByProperties(['uuid' => 'a2000001-0000-4000-8000-000000000004']);
  foreach ($entities as $entity) {
    $entity->set('weight', 40);
    $entity->save();
  }
  \Drupal::service('plugin.manager.menu.link')->rebuild();
}

/**
 * Add ps-header-actions__btn class to the header search outline CTA.
 */
function ps_theme_post_update_9010_header_search_btn_classes(): void {
  $storage = \Drupal::entityTypeManager()->getStorage('menu_link_content');
  $entities = $storage->loadByProperties(['uuid' => 'a2000001-0000-4000-8000-000000000004']);
  foreach ($entities as $entity) {
    $link = $entity->link->first();
    if ($link === NULL) {
      continue;
    }
    $options = $link->get('options')->getValue() ?? [];
    $classes = $options['attributes']['class'] ?? [];
    if (!in_array('ps-header-actions__btn', $classes, TRUE)) {
      $classes[] = 'ps-header-actions__btn';
      $options['attributes']['class'] = $classes;
      $link->set('options', $options);
      $entity->save();
    }
  }
  \Drupal::service('plugin.manager.menu.link')->rebuild();
}

/**
 * Deprecated stellar menu imports — use make demo instead.
 */
function ps_theme_post_update_9001_stellar_menus(): void {
  // No-op: content moved to ps_demo export + make demo.
}

/**
 * Deprecated corporate header menu import.
 */
function ps_theme_post_update_9004_corporate_header_menu(): void {
  // No-op: content moved to ps_demo export + make demo.
}

/**
 * Deprecated mega-menu header import.
 */
function ps_theme_post_update_9005_mega_menu_header(): void {
  // No-op: content moved to ps_demo export + make demo.
}

/**
 * Deprecated mega-menu columns import.
 */
function ps_theme_post_update_9007_mega_menu_columns(): void {
  // No-op: content moved to ps_demo export + make demo.
}

/**
 * Deprecated demo config reimport — use make demo (drush cim config/demo).
 */
function ps_theme_post_update_9008_mega_menu_reimport(): void {
  // No-op: run `make demo` for mega-menu CMI and full demo content.
}
