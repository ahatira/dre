<?php

declare(strict_types=1);

namespace Drupal\ps_homepage\Hook;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\views\ViewExecutable;

/**
 * Views hooks for homepage news displays.
 */
final class HomepageViewsHooks {

  /**
   * Implements hook_views_pre_render().
   */
  #[Hook('views_pre_render')]
  public function viewsPreRender(ViewExecutable $view): void {
    if ($view->id() !== 'ps_homepage_news') {
      return;
    }

    if (!in_array($view->current_display, ['homepage_news_teaser', 'news_list'], TRUE)) {
      return;
    }

    $view->element['#attributes']['class'][] = 'ps-homepage-news__view';
    if ($view->current_display === 'news_list') {
      $view->element['#attributes']['class'][] = 'ps-homepage-news--cols-3';
    }
  }

}
