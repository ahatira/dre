<?php

declare(strict_types=1);

namespace Drupal\ps_news\Hook;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\views\ViewExecutable;

/**
 * Views hooks for news displays.
 */
final class NewsViewsHooks {

  /**
   * Implements hook_views_pre_render().
   */
  #[Hook('views_pre_render')]
  public function viewsPreRender(ViewExecutable $view): void {
    if ($view->id() !== 'ps_news') {
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
