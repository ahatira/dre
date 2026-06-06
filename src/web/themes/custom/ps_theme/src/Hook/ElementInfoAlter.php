<?php

declare(strict_types=1);

namespace Drupal\ps_theme\Hook;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\ui_suite_bnp\Element\ElementPreRenderContextualLinksPlaceholder;

/**
 * Alter render elements — keep Drupal contextual links off Bootstrap dropdown.
 */
final class ElementInfoAlter {

  /**
   * Implements hook_element_info_alter().
   */
  #[Hook('element_info_alter')]
  public function alter(array &$info): void {
    if (!isset($info['contextual_links_placeholder']['#pre_render'])) {
      return;
    }

    foreach ($info['contextual_links_placeholder']['#pre_render'] as $key => $callback) {
      if (($callback[0] ?? NULL) === ElementPreRenderContextualLinksPlaceholder::class) {
        unset($info['contextual_links_placeholder']['#pre_render'][$key]);
      }
    }
  }

}
