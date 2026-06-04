<?php

/**
 * @file
 * Post update hooks for the Property Search theme.
 */

declare(strict_types=1);

use Drupal\ps_theme\Service\HomepageInstaller;
use Drupal\ps_theme\Service\LegacyDemoContentInstaller;
use Drupal\ps_theme\Utility\DemoContent;

/**
 * Import Stellar default menus (FR + EN).
 *
 * @deprecated in ps_theme:1.0.0 and is removed from ps_theme:2.0.0. Use ps_demo
 *   content exports instead.
 */
function ps_theme_post_update_9001_stellar_menus(): void {
  LegacyDemoContentInstaller::create(\Drupal::getContainer())->install(menus: TRUE, homepage: FALSE);
}

/**
 * Fix homepage view mode id (node.page.full → node.full).
 */
function ps_theme_post_update_9003_homepage_view_mode(): void {
  if (!DemoContent::isManagedByPsDemo()) {
    HomepageInstaller::create(\Drupal::getContainer())->install();
  }
}

/**
 * Import Stellar corporate header menu (FR + EN) and disable legacy Contact link.
 */
function ps_theme_post_update_9004_corporate_header_menu(): void {
  LegacyDemoContentInstaller::create(\Drupal::getContainer())->install(menus: TRUE, homepage: FALSE);

  $storage = \Drupal::entityTypeManager()->getStorage('menu_link_content');
  $entities = $storage->loadByProperties(['uuid' => 'a1000001-0000-4000-8000-000000000105']);
  foreach ($entities as $entity) {
    $entity->set('enabled', FALSE);
    $entity->save();
  }

  \Drupal::service('plugin.manager.menu.link')->rebuild();
}

/**
 * Import Stellar mega-menu header structure (FR + EN).
 */
function ps_theme_post_update_9005_mega_menu_header(): void {
  LegacyDemoContentInstaller::create(\Drupal::getContainer())->install(menus: TRUE, homepage: FALSE);
  \Drupal::service('plugin.manager.menu.link')->rebuild();
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
 * Mega-menu: Rent/Buy/Coworking columns, About/Solutions/News panels, cleanup.
 */
function ps_theme_post_update_9007_mega_menu_columns(): void {
  ps_theme_update_9006();
}

/**
 * Re-apply mega-menu config after ps_demo import on existing sites.
 */
function ps_theme_post_update_9008_mega_menu_reimport(): void {
  ps_theme_reimport_demo_config();
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
