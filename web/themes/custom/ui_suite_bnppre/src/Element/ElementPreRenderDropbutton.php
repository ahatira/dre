<?php

declare(strict_types=1);

namespace Drupal\ui_suite_bnppre\Element;

use Drupal\Core\Security\TrustedCallbackInterface;

/**
 * Element Prerender methods for dropbutton.
 */
class ElementPreRenderDropbutton implements TrustedCallbackInterface {

  /**
   * Prerender a dropbutton form element.
   */
  public static function preRenderDropbutton(array $element): array {
    if (!empty($element['#dropbutton_type'])) {
      $element['#attributes']['dropbutton_type'] = $element['#dropbutton_type'];
    }

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public static function trustedCallbacks(): array {
    return ['preRenderDropbutton'];
  }

}
