<?php

declare(strict_types=1);

namespace Drupal\ui_suite_bnppre\Element;

use Drupal\Core\Form\FormStateInterface;
use Drupal\ui_suite_bnppre\Hook\ElementInfoAlter;

/**
 * Element Process methods for checkboxes.
 */
class ElementProcessCheckboxes {

  /**
   * Processes a checkboxes form element.
   */
  public static function processCheckboxes(array &$element, FormStateInterface $form_state, array &$complete_form): array {
    if (\is_array($element['#options']) && \count($element['#options']) > 0) {
      foreach ($element['#options'] as $key => $choice) {
        // Integer 0 is not a valid #return_value, so use '0' instead.
        // @see \Drupal\Core\Render\Element\Checkboxes::processCheckboxes().
        if ($key === 0) {
          $key = '0';
        }
        foreach (ElementInfoAlter::CHECKBOX_PROPERTIES as $property => $property_default_value) {
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
