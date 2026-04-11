<?php

declare(strict_types=1);

namespace Drupal\ps\Hook;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Url;

/**
 * Hook implementations for PropertySearch toolbar integration.
 */
class ToolbarHooks {

  /**
   * Implements hook_toolbar().
   *
   * Adds the PropertySearch tab to the Drupal admin toolbar.
   */
  #[Hook('toolbar')]
  public function toolbar(): array {
    $items = [];

    // Add PropertySearch toolbar item.
    $items['ps_admin'] = [
      '#type' => 'toolbar_item',
      'tab' => [
        '#type' => 'link',
        '#title' => t('PropertySearch'),
        '#url' => Url::fromRoute('ps.admin'),
        '#options' => [
          'attributes' => [
            'title' => t('PropertySearch administration'),
            'class' => ['toolbar-icon', 'toolbar-icon-ps-admin'],
          ],
        ],
      ],
      '#weight' => -5,
      '#attached' => [
        'library' => [
          'ps/admin',
        ],
      ],
    ];

    return $items;
  }

}
