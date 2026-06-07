<?php

/**
 * @file
 * Post update hooks for ps_offer.
 */

declare(strict_types=1);

use Drupal\Core\Config\FileStorage;

/**
 * Renames the offer agent formatter and Layout Builder CSS class.
 */
function ps_offer_post_update_rename_agent_sidebar_card_formatter(): void {
  $config_name = 'core.entity_view_display.node.offer.full';
  $config = \Drupal::configFactory()->getEditable($config_name);
  if ($config->isNew()) {
    return;
  }

  $data = $config->getRawData();
  $updated = FALSE;

  if (isset($data['content']['layout_builder__layout']['sections']) && is_array($data['content']['layout_builder__layout']['sections'])) {
    foreach ($data['content']['layout_builder__layout']['sections'] as &$section) {
      if (!isset($section['components']) || !is_array($section['components'])) {
        continue;
      }
      foreach ($section['components'] as &$component) {
        $formatter_type = $component['configuration']['formatter']['type'] ?? NULL;
        if ($formatter_type === 'ps_offer_agent_sidebar_card') {
          $component['configuration']['formatter']['type'] = 'ps_offer_agent_card';
          $updated = TRUE;
        }

        $css_classes = $component['additional']['css_classes'] ?? NULL;
        if (is_array($css_classes)) {
          foreach ($css_classes as $index => $class) {
            if ($class === 'ps-offer-sidebar__agent') {
              $component['additional']['css_classes'][$index] = 'ps-offer-agent-panel';
              $updated = TRUE;
            }
          }
        }
      }
    }
    unset($section, $component);
  }

  if ($updated) {
    $config->setData($data)->save(TRUE);
    return;
  }

  $path = \Drupal::service('extension.list.module')->getPath('ps_offer') . '/config/install';
  $storage = new FileStorage($path);
  $install_data = $storage->read($config_name);
  if ($install_data !== FALSE) {
    \Drupal::configFactory()->getEditable($config_name)->setData($install_data)->save(TRUE);
  }
}
