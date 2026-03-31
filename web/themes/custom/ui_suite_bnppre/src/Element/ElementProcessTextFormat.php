<?php

declare(strict_types=1);

namespace Drupal\ui_suite_bnppre\Element;

use Drupal\Core\Form\FormStateInterface;
use Drupal\ui_suite_bnppre\Utility\Bootstrap;
use Drupal\ui_suite_bnppre\Utility\DrupalAttributes;
use Drupal\ui_suite_bnppre\Utility\Element;

/**
 * Element Process methods for text format.
 */
class ElementProcessTextFormat {

  /**
   * Ensure the format select list is placed before the about link.
   */
  public const int FORMAT_WEIGHT = -10;

  /**
   * Processes a text format form element.
   */
  public static function processTextFormat(array &$element, FormStateInterface $form_state, array &$complete_form): array {
    // @phpstan-ignore-next-line
    $element_object = Element::create($element);
    if (isset($element_object->value) && $element_object->value instanceof Element) {
      $element_object->value->setProperty('is_text_format', TRUE);
    }

    if (isset($element_object->format) && $element_object->format instanceof Element) {
      $element_object->format->addClass([
        'border',
        'border-top-0',
        'p-3',
        'mb-3',
        'd-flex',
        'align-items-center',
      ]);

      // Guidelines (removed).
      if (isset($element_object->format->guidelines) && $element_object->format->guidelines instanceof Element) {
        $element_object->format->guidelines->setProperty('access', FALSE);
      }

      // Format (select).
      if (isset($element_object->format->format) && $element_object->format->format instanceof Element) {
        $element_object->format->format->setProperty('is_text_format', TRUE);
        $element_object->format->format->setProperty('title_display', 'invisible');
        $element_object->format->format->addClass([
          'me-auto',
        ], DrupalAttributes::WRAPPER);
        $element_object->format->format->setProperty('weight', static::FORMAT_WEIGHT);
        // Allow to detect that this select list is from a text format element.
        // Value textarea already have this property.
        $element_object->format->format->setProperty('format', $element_object->getProperty('format', []));
      }

      // Help (link).
      if (isset($element_object->format->help) && $element_object->format->help instanceof Element) {
        $element_object->format->help->addClass([
          'ms-auto',
        ]);
        if (isset($element_object->format->help->about) && $element_object->format->help->about instanceof Element) {
          $element_object->format->help->about
            ->setAttribute('title', \t('Opens in new window'))
            ->setProperty('icon', Bootstrap::icon('question-circle-fill'));
        }
      }
    }

    // @phpstan-ignore-next-line
    return $element;
  }

}
