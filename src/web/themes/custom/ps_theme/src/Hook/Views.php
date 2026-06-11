<?php

declare(strict_types=1);

namespace Drupal\ps_theme\Hook;

use Drupal\Core\Extension\ThemeExtensionList;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Url;
use Drupal\views\ViewExecutable;

/**
 * Views preprocess hooks for Property Search pages.
 */
final class Views {

  public function __construct(
    private readonly ThemeExtensionList $themeExtensionList,
  ) {}

  /**
   * Implements hook_preprocess_HOOK().
   */
  #[Hook('preprocess_views_view')]
  public function preprocessViewsView(array &$variables): void {
    $view = $variables['view'] ?? NULL;
    if (!$view instanceof ViewExecutable) {
      return;
    }

    if ($view->id() !== 'ps_search_offers' || ($variables['display_id'] ?? '') !== 'page_list') {
      return;
    }

    unset($variables['title']);

    $illustrationPath = $this->themeExtensionList->getPath('ps_theme') . '/assets/images/offer-placeholder.svg';
    $variables['ps_search_empty_illustration'] = Url::fromUri('base:' . $illustrationPath)->toString();
  }

}
