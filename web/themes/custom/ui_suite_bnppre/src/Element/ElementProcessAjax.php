<?php

declare(strict_types=1);

namespace Drupal\ui_suite_bnppre\Element;

use Drupal\Core\Form\FormStateInterface;
use Drupal\ui_suite_bnppre\Utility\Bootstrap;
use Drupal\ui_suite_bnppre\Utility\Element;

/**
 * Element Process methods for input group feature.
 */
class ElementProcessAjax {

  /**
   * Processes element supporting input group and having ajax.
   */
  public static function processAjax(array &$element, FormStateInterface $form_state, array &$complete_form): array {
    // @phpstan-ignore-next-line
    $element_object = Element::create($element, $form_state);

    // Process AJAX.
    if (($element_object->getProperty('ajax') && !$element_object->isButton()) || $element_object->getProperty('autocomplete_route_name')) {
      static::processElementAjax($element_object, $form_state, $complete_form);
    }

    // @phpstan-ignore-next-line
    return $element;
  }

  /**
   * Processes elements with AJAX properties.
   *
   * @param \Drupal\ui_suite_bnppre\Utility\Element $element
   *   The element object.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   * @param array $complete_form
   *   The complete form structure.
   */
  public static function processElementAjax(Element $element, FormStateInterface $form_state, array &$complete_form): void {
    /** @var array $ajax */
    $ajax = $element->getProperty('ajax', []);

    // Show throbber AJAX requests in an input button group.
    $ignore_types = ['checkbox', 'checkboxes', 'hidden', 'radio', 'radios'];
    if ((!isset($ajax['progress']['type']) || $ajax['progress']['type'] === 'throbber') && !$element->isType($ignore_types)) {
      $icon = Bootstrap::icon('arrow-repeat');
      $processedIcon = [
        '#type' => 'html_tag',
        '#tag' => 'span',
        '#attributes' => [
          'class' => [
            'input-group-text',
            'ajax-progress',
            'ajax-progress-throbber',
          ],
        ],
        '#value' => Element::create($icon)->renderPlain(),
      ];
      $inputGroupAfter = $element->getProperty('input_group_after', []);
      $fieldSuffix = $element->getProperty('field_suffix', '');

      if (!empty($inputGroupAfter)) {
        $element->appendProperty('input_group_after', Element::create($processedIcon));
      }
      elseif (!empty($fieldSuffix)) {
        $processedSuffix = [
          '#type' => 'html_tag',
          '#tag' => 'span',
          '#attributes' => [
            'class' => [
              'input-group-text',
            ],
          ],
          // @phpstan-ignore-next-line
          '#value' => Element::create($fieldSuffix)->renderPlain(),
        ];
        $element->appendProperty('input_group_after', Element::create($processedSuffix));
        $element->appendProperty('input_group_after', Element::create($processedIcon));
        $element->unsetProperty('field_suffix');
      }
      else {
        $element->appendProperty('input_group_after', Element::create($processedIcon));
      }
    }
  }

}
