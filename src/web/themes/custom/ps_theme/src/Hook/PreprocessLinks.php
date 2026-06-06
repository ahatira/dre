<?php

declare(strict_types=1);

namespace Drupal\ps_theme\Hook;

use Drupal\Core\Hook\Attribute\Hook;

/**
 * Links preprocess — keep contextual links on core markup.
 */
final class PreprocessLinks {

  /**
   * Implements hook_preprocess_HOOK().
   */
  #[Hook('preprocess_links__contextual')]
  public function preprocessContextual(array &$variables): void {
    unset($variables['preprocessed_items']);
  }

}
