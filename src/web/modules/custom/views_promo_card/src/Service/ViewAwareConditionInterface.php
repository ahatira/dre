<?php

declare(strict_types=1);

namespace Drupal\views_promo_card\Service;

use Drupal\views\ViewExecutable;

/**
 * Allows condition plugins to access the current view.
 */
interface ViewAwareConditionInterface {

  /**
   * Sets the view being evaluated.
   */
  public function setView(ViewExecutable $view): void;

}
