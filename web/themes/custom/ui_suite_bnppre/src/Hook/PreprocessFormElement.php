<?php

declare(strict_types=1);

namespace Drupal\ui_suite_bnppre\Hook;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Template\Attribute;
use Drupal\ui_suite_bnppre\Utility\Element;
use Drupal\ui_suite_bnppre\Utility\Variables;

/**
 * Pre-processes variables for the "form_element" theme hook.
 */
class PreprocessFormElement {

  /**
   * The Variables object.
   *
   * @var \Drupal\ui_suite_bnppre\Utility\Variables
   */
  protected Variables $variables;

  /**
   * An element object provided in the variables array, may not be set.
   *
   * @var \Drupal\ui_suite_bnppre\Utility\Element|false
   */
  protected $element;

  /**
   * Implements hook_preprocess_HOOK().
   */
  #[Hook('preprocess_form_element')]
  public function formElement(array &$variables): void {
    if (!isset($variables['element'])) {
      return;
    }

    $this->variables = Variables::create($variables);
    $this->element = $this->variables->element;
    if (!$this->element) {
      return;
    }
    // @phpstan-ignore-next-line
    $label = Element::create($variables['label']);

    // See https://getbootstrap.com/docs/5.3/forms/checks-radios
    if ($this->element->isType('checkbox') || $this->element->isType('radio')) {
      $label->addClass('form-check-label');
      $this->variables->addClass('form-check');

      if ($this->element->hasProperty('is_inline') && $this->element->getProperty('is_inline')) {
        $this->variables->addClass('form-check-inline');
      }
      if ($this->element->hasProperty('is_reverse') && $this->element->getProperty('is_reverse')) {
        $this->variables->addClass('form-check-reverse');
      }
      // Even if only for checkbox.
      if ($this->element->hasProperty('is_switch') && $this->element->getProperty('is_switch')) {
        $this->variables->addClass('form-switch');
      }
    }
    // For all other form elements add 'form-label' class.
    else {
      $label->addClass('form-label');
      // If something has already put a mb class to control the margin, do not
      // put our default margin.
      if (!\str_contains(\implode(' ', $this->variables->getClasses()), 'mb-')) {
        // If the element is a sub-element of a text_format, do not add the
        // class.
        if (!$this->element->hasProperty('is_text_format')) {
          $this->variables->addClass('mb-3');
        }
      }
    }

    // Input group.
    // Create variables for input_group flags.
    $this->variables->offsetSet(
      'input_group',
      $this->element->getProperty('input_group_after')
      || $this->element->getProperty('input_group_before')
    );
    // Get input group attributes.
    /** @var array $input_group_attributes */
    $input_group_attributes = $this->element->getProperty('input_group_attributes', []);
    // Validation.
    if ($this->element->getProperty('errors')) {
      $input_group_attributes = new Attribute($input_group_attributes);
      $input_group_attributes->addClass('has-validation');
    }
    // Cannot use map directly because of the attributes' management.
    $this->variables->offsetSet('input_group_attributes', $input_group_attributes);

    // Map the element properties.
    $this->variables->map([
      'input_group_after' => 'input_group_after',
      'input_group_before' => 'input_group_before',
    ]);

    // Floating label.
    // Override title_display if using #floating_label.
    if ($this->element->hasProperty('floating_label') && $this->element->getProperty('floating_label')) {
      $this->element->setProperty('title_display', 'floating');
      $this->variables->map([
        'title_display' => 'title_display',
      ]);
      $this->variables->map([
        'title_display' => 'label_display',
      ]);
    }
    $this->variables->offsetSet('floating_label_attributes', new Attribute([
      'class' => [
        'form-floating',
        // Validation.
        ($this->variables->offsetGet('input_group') && $this->element->getProperty('errors')) ? 'is-invalid' : '',
      ],
    ]));

    // Layout: horizontal form.
    // _title_display is created by Webform.
    if ($this->element->getProperty('title_display') == 'inline' || $this->element->getProperty('_title_display') == 'inline') {
      $this->variables->addClass('row');
      $this->variables->offsetSet('inner_wrapper', TRUE);
      $label->addClass('col-form-label');
      /** @var array $inner_wrapper_attributes */
      $inner_wrapper_attributes = $this->element->getProperty('inner_wrapper_attributes', []);
      $inner_wrapper_attributes = new Attribute($inner_wrapper_attributes);
      // Cannot use map directly because of the attributes' management.
      $this->variables->offsetSet('inner_wrapper_attributes', $inner_wrapper_attributes);
    }

    $this->validation();
  }

  /**
   * Implements hook_preprocess_HOOK().
   */
  #[Hook('preprocess_details__accordion')]
  public function detailsAccordion(array &$variables): void {
    if (!isset($variables['element'])) {
      return;
    }

    $this->variables = Variables::create($variables);
    $this->element = $this->variables->element;
    if (!$this->element) {
      return;
    }

    /** @var array $accordion_attributes */
    $accordion_attributes = $this->element->getProperty('accordion_attributes', []);
    $accordion_attributes = new Attribute($accordion_attributes);
    if ($this->element->getProperty('isOffcanvas')) {
      $accordion_attributes->addClass('accordion-flush');
      $style = $accordion_attributes->offsetGet('style');
      if ($style == NULL) {
        // @todo should the array be keyed?
        $accordion_attributes->setAttribute('style', ['--bs-accordion-body-padding-x: 0;']);
      }
      elseif (\is_array($style)) {
        $style[] = '--bs-accordion-body-padding-x: 0;';
        $accordion_attributes->setAttribute('style', $style);
      }
      elseif (\is_string($style)) {
        $accordion_attributes->setAttribute('style', $style . '--bs-accordion-body-padding-x: 0;');
      }
    }
    else {
      $accordion_attributes->addClass('mb-3');
    }

    // Remove Core library for details HTML tag.
    /** @var array $attached */
    $attached = $this->element->getProperty('attached', []);
    if (isset($attached['library']) && \is_array($attached['library'])) {
      $key = \array_search('core/drupal.collapse', $attached['library'], TRUE);
      if ($key !== FALSE) {
        unset($attached['library'][$key]);
      }
    }
    $this->element->setProperty('attached', $attached);

    // Cannot use map directly because of the attributes' management.
    $this->variables->offsetSet('accordion_attributes', $accordion_attributes);

    $this->validation();
  }

  /**
   * Implements hook_preprocess_HOOK().
   */
  #[Hook('preprocess_fieldset')]
  public function fieldset(array &$variables): void {
    if (!isset($variables['element'])) {
      return;
    }

    $this->variables = Variables::create($variables);
    $this->element = $this->variables->element;
    if (!$this->element) {
      return;
    }

    if ($this->element->getProperty('isDisplayBuilder')) {
      return;
    }

    /** @var array $wrapper_attributes */
    $wrapper_attributes = $this->element->getProperty('wrapper_attributes', []);
    $wrapper_attributes = new Attribute($wrapper_attributes);
    $wrapper_attributes->addClass('mb-3');

    /** @var array $label_attributes */
    $label_attributes = $this->element->getProperty('label_attributes', []);
    $label_attributes = new Attribute($label_attributes);

    /** @var array $inner_wrapper_attributes */
    $inner_wrapper_attributes = $this->element->getProperty('inner_wrapper_attributes', []);
    $inner_wrapper_attributes = new Attribute($inner_wrapper_attributes);

    // Layout: horizontal form.
    // _title_display is created by Webform.
    if ($this->element->getProperty('title_display') == 'inline' || $this->element->getProperty('_title_display') == 'inline') {
      $wrapper_attributes->addClass([
        'row',
      ]);
      $label_attributes->addClass('col-form-label');
    }
    // In Layout Builder/Offcanvas, ensure the fieldset legend has normal size
    // for checkboxes and radios.
    elseif ($this->element->isType(['checkboxes', 'radios']) && $this->element->getProperty('isOffcanvas')) {
      $label_attributes->addClass('fs-6');
    }
    // Display fieldset as card by default.
    else {
      $this->variables->addAttachments([
        'library' => ['core/components.ui_suite_bnppre--card'],
      ]);
      $wrapper_attributes->addClass([
        'card',
      ]);
      $label_attributes->addClass('card-header');
      $inner_wrapper_attributes->addClass('card-body');
    }

    // Merge wrapper attributes.
    $this->variables->setAttributes($wrapper_attributes->toArray());
    if ($variables['legend']['attributes'] instanceof Attribute) {
      $variables['legend']['attributes']->merge($label_attributes);
    }
    // Cannot use map directly because of the attributes' management.
    $this->variables->offsetSet('inner_wrapper_attributes', $inner_wrapper_attributes);

    $this->validation();
  }

  /**
   * Set validation class.
   */
  protected function validation(): void {
    if (!$this->element) {
      return;
    }

    /** @var string $errors_display */
    $errors_display = $this->element->getProperty('errors_display', 'feedback');
    $this->variables->offsetSet('errors_attributes', new Attribute([
      'class' => [
        'form-item--error-message',
        'invalid-' . $errors_display,
      ],
    ]));
    if ($errors_display == 'tooltip') {
      $this->variables->addClass('position-relative');
    }
  }

}
