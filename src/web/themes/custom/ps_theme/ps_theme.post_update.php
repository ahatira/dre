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
 * Migrate footer block layout to site-footer slots (business / about / contact).
 */
function ps_theme_post_update_9011_site_footer_slots(): void {
  if (!\Drupal::moduleHandler()->moduleExists('ps_block')) {
    \Drupal::service('module_installer')->install(['ps_block'], TRUE);
  }

  $legacy = \Drupal::entityTypeManager()->getStorage('block')->load('ps_theme_footer');
  if ($legacy) {
    $legacy->delete();
  }
}

/**
 * Move demo footer links from ps_footer_main to business / about menus.
 */
function ps_theme_post_update_9012_footer_menu_links(): void {
  $storage = \Drupal::entityTypeManager()->getStorage('menu_link_content');

  $business_heading = 'a1000003-0000-4000-8000-000000000303';
  $about_heading = 'a1000003-0000-4000-8000-000000000301';
  $business_children = [
    'a1000003-0000-4000-8000-000000000331',
    'a1000003-0000-4000-8000-000000000321',
    'a1000003-0000-4000-8000-000000000322',
    'a1000003-0000-4000-8000-000000000323',
    'a1000003-0000-4000-8000-000000000324',
    'a1000003-0000-4000-8000-000000000325',
  ];
  $about_children = [
    'a1000003-0000-4000-8000-000000000332',
    'a1000003-0000-4000-8000-000000000333',
    'a1000003-0000-4000-8000-000000000313',
  ];
  $disable = [
    'a1000003-0000-4000-8000-000000000302',
    'a1000003-0000-4000-8000-000000000311',
    'a1000003-0000-4000-8000-000000000312',
  ];

  $update_heading = static function (string $uuid, string $menu, array $titles) use ($storage): void {
    $entities = $storage->loadByProperties(['uuid' => $uuid]);
    foreach ($entities as $entity) {
      $entity->set('menu_name', $menu);
      $entity->set('enabled', TRUE);
      foreach ($titles as $langcode => $title) {
        if ($entity->hasTranslation($langcode)) {
          $entity->getTranslation($langcode)->set('title', $title);
        }
      }
      $entity->save();
    }
  };

  $move_child = static function (string $uuid, string $menu, string $parent_uuid) use ($storage): void {
    $entities = $storage->loadByProperties(['uuid' => $uuid]);
    foreach ($entities as $entity) {
      $entity->set('menu_name', $menu);
      $entity->set('parent', 'menu_link_content:' . $parent_uuid);
      $entity->set('enabled', TRUE);
      $entity->save();
    }
  };

  $update_heading($business_heading, 'ps_footer_business', [
    'en' => 'Business websites',
    'fr' => 'Sites métier',
  ]);
  $update_heading($about_heading, 'ps_footer_about', [
    'en' => 'About BNP Paribas Real Estate',
    'fr' => 'À propos de BNP Paribas Real Estate',
  ]);

  foreach ($business_children as $uuid) {
    $move_child($uuid, 'ps_footer_business', $business_heading);
  }
  foreach ($about_children as $uuid) {
    $move_child($uuid, 'ps_footer_about', $about_heading);
  }

  foreach ($disable as $uuid) {
    $entities = $storage->loadByProperties(['uuid' => $uuid]);
    foreach ($entities as $entity) {
      $entity->set('enabled', FALSE);
      $entity->save();
    }
  }

  \Drupal::service('plugin.manager.menu.link')->rebuild();
}

/**
 * Align footer legal menu labels with Stellar bottom bar mockup.
 */
function ps_theme_post_update_9013_footer_legal_labels(): void {
  $storage = \Drupal::entityTypeManager()->getStorage('menu_link_content');
  $updates = [
    'a1000005-0000-4000-8000-000000000501' => ['en' => 'Data protection', 'fr' => 'Données personnelles'],
    'a1000005-0000-4000-8000-000000000502' => ['en' => 'Cookie policy', 'fr' => 'Politique cookies'],
    'a1000005-0000-4000-8000-000000000503' => ['en' => 'Disclaimer', 'fr' => 'Avertissement'],
    'a1000005-0000-4000-8000-000000000504' => ['en' => 'Suppliers: BNP Paribas is committed to its partners and suppliers', 'fr' => 'Fournisseurs : BNP Paribas s\'engage envers ses partenaires et fournisseurs'],
  ];

  foreach ($updates as $uuid => $titles) {
    $entities = $storage->loadByProperties(['uuid' => $uuid]);
    foreach ($entities as $entity) {
      foreach ($titles as $langcode => $title) {
        if ($entity->hasTranslation($langcode)) {
          $entity->getTranslation($langcode)->set('title', $title);
        }
        else {
          $entity->set('title', $title);
        }
      }
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
