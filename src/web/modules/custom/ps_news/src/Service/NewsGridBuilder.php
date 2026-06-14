<?php

declare(strict_types=1);

namespace Drupal\ps_news\Service;

use Drupal\views\Views;

/**
 * Builds the news grid view render array for homepage §7.
 */
final class NewsGridBuilder {

  private const VIEW_ID = 'ps_news';

  private const DISPLAY_ID = 'homepage_news_teaser';

  /**
   * @return array{
   *   body: array<string, mixed>,
   *   cache: array<string, mixed>,
   *   attached: array<string, mixed>,
   *   meta: array<string, mixed>
   * }
   */
  public function build(int $itemsCount): array {
    $itemsCount = $this->normalizeItemsCount($itemsCount);

    $view = Views::getView(self::VIEW_ID);
    if ($view === NULL) {
      return [
        'body' => ['#markup' => ''],
        'cache' => [],
        'attached' => [],
        'meta' => ['items_count' => $itemsCount],
      ];
    }

    $view->setDisplay(self::DISPLAY_ID);
    $view->setItemsPerPage($itemsCount);
    $view->preExecute();
    $view->execute();
    if ($view->result === []) {
      return [
        'body' => ['#markup' => ''],
        'cache' => [],
        'attached' => [],
        'meta' => ['items_count' => $itemsCount],
      ];
    }

    $viewRender = $view->buildRenderable(self::DISPLAY_ID);
    if ($viewRender === []) {
      return [
        'body' => ['#markup' => ''],
        'cache' => [],
        'attached' => [],
        'meta' => ['items_count' => $itemsCount],
      ];
    }

    $viewRender['#attributes']['class'][] = 'ps-homepage-news__view';

    return [
      'body' => ['view' => $viewRender],
      'cache' => [
        'contexts' => ['languages:language_interface'],
        'tags' => ['config:views.view.ps_news', 'node_list:article'],
      ],
      'attached' => [
        'library' => ['ps_news/news_grid'],
      ],
      'meta' => [
        'items_count' => $itemsCount,
        'section_class_suffix' => 'ps-homepage-news--cols-' . $itemsCount,
      ],
    ];
  }

  private function normalizeItemsCount(int $count): int {
    return match (TRUE) {
      $count >= 6 => 6,
      $count >= 4 => 4,
      default => 3,
    };
  }

}
