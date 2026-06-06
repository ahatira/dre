<?php

declare(strict_types=1);

namespace Drupal\ps_search\Hook;

use Drupal\Core\Hook\Attribute\Hook;

/**
 * Legacy theme registrations for ps_search.
 */
final class SearchThemeHooks {

  /**
   * Implements hook_theme().
   *
   * @return array<string, mixed>
   *   Empty — themes moved to ps_search_filters.
   */
  #[Hook('theme')]
  public function theme(): array {
    return [];
  }

}
