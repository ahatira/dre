<?php

declare(strict_types=1);

namespace Drupal\ui_suite_bnppre\Hook;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\ui_patterns\Plugin\UiPatterns\PropType\LinksPropType;

/**
 * Ensure links structure fits into list group structure.
 */
class PreprocessLinks {

  /**
   * Implements hook_preprocess_HOOK().
   */
  #[Hook('preprocess_links__contextual')]
  #[Hook('preprocess_links__layout_builder_links')]
  #[Hook('preprocess_links__media_library_menu')]
  public function preprocess(array &$variables): void {
    if (empty($variables['links']) || !\is_array($variables['links'])) {
      return;
    }

    $variables['preprocessed_items'] = LinksPropType::normalize(\array_filter(
      $variables['links'],
    ));
  }

}
