<?php

declare(strict_types=1);

namespace Drupal\ui_suite_bnppre\Hook;

use Drupal\Core\Hook\Attribute\Hook;

/**
 * Ensure views mini pager structure fits into links prop structure.
 */
class PreprocessViewsMiniPager extends PreprocessPager {

  /**
   * Implements hook_preprocess_HOOK().
   */
  #[Hook('preprocess_views_mini_pager')]
  public function preprocess(array &$variables): void {
    if (!isset($variables['items']) || !\is_array($variables['items'])) {
      return;
    }
    $this->setLinksAriaLabel($variables['items']);

    $variables['preprocessed_items'] = \array_filter([
      $variables['items']['previous'] ?? [],
      [
        'title' => $variables['items']['current'],
      ],
      $variables['items']['next'] ?? [],
    ]);
  }

}
