<?php

declare(strict_types=1);

namespace Drupal\ui_suite_bnppre\Element;

use Drupal\Core\Form\FormStateInterface;
use Drupal\ui_suite_bnppre\Hook\ElementInfoAlter;

/**
 * Element Process methods for radios.
 */
class ElementProcessRadios {

  /**
   * Processes a radios form element.
   */
  public static function processRadios(array &$element, FormStateInterface $form_state, array &$complete_form): array {
    if (\is_array($element['#options']) && \count($element['#options']) > 0) {
      foreach ($element['#options'] as $key => $choice) {
        foreach (ElementInfoAlter::RADIOS_PROPERTIES as $property => $property_default_value) {
          if (isset($element["#{$property}"])) {
            // @phpstan-ignore-next-line
            $element[$key] += [
              "#{$property}" => $element["#{$property}"],
            ];
          }
        }
      }
    }
    return $element;
  }

}
