<?php

declare(strict_types=1);

namespace Drupal\ps_form\Hook;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\ps_form\Service\ContactDisplayModeManager;

/**
 * Applies contact display mode to menu links.
 */
final class ContactMenuHooks {

  public function __construct(
    private readonly ContactDisplayModeManager $displayModeManager,
  ) {}

  /**
   * Applies display-mode attributes to header contact links.
   *
   * @param array<string, mixed> $variables
   *   Theme variables for menu--ps-header-actions.html.twig.
   */
  #[Hook('preprocess_menu__ps_header_actions')]
  public function preprocessHeaderActionsMenu(array &$variables): void {
    if (empty($variables['items']) || !is_array($variables['items'])) {
      return;
    }

    foreach ($variables['items'] as &$item) {
      if (!is_array($item)) {
        continue;
      }
      $this->displayModeManager->applyToMenuLink($item);
    }
  }

}
