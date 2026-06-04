<?php

declare(strict_types=1);

namespace Drupal\ps_theme\Hook;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\ps_theme\Utility\TransactionToggleBuilder;
use Drupal\views\ViewExecutable;

/**
 * Views preprocess hooks for Property Search pages.
 */
final class Views {

  /**
   * Implements hook_preprocess_HOOK().
   */
  #[Hook('preprocess_views_view')]
  public function preprocessViewsView(array &$variables): void {
    $view = $variables['view'] ?? NULL;
    if (!$view instanceof ViewExecutable || $view->id() !== 'ps_search_offers') {
      return;
    }

    if (($variables['display_id'] ?? '') !== 'page_list') {
      return;
    }

    $variables['transaction_toggle'] = TransactionToggleBuilder::build();
    unset($variables['title']);
  }

}
