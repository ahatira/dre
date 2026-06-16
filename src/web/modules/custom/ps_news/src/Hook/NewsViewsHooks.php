<?php

declare(strict_types=1);

namespace Drupal\ps_news\Hook;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Language\LanguageInterface;
use Drupal\views\Plugin\views\query\QueryPluginBase;
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

    if ($view->current_display !== 'news_list') {
      return;
    }

    $view->element['#attributes']['class'][] = 'ps-homepage-news__view';
    $view->element['#attributes']['class'][] = 'ps-homepage-news--cols-3';
  }

  /**
   * Restricts news displays to the current content language.
   */
  #[Hook('views_query_alter')]
  public function viewsQueryAlter(ViewExecutable $view, QueryPluginBase $query): void {
    if ($view->id() !== 'ps_news') {
      return;
    }

    if (!in_array($view->current_display, ['homepage_news_teaser', 'news_list'], TRUE)) {
      return;
    }

    $langcode = \Drupal::languageManager()
      ->getCurrentLanguage(LanguageInterface::TYPE_CONTENT)
      ->getId();
    $query->addWhere('ps_news_lang', 'node_field_data.langcode', $langcode, '=');
  }

}
