<?php

declare(strict_types=1);

namespace Drupal\ui_suite_bnppre\Hook;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\ui_suite_bnppre\Utility\Variables;

/**
 * Pre-processes variables for the "input" theme hook.
 */
class PreprocessInput {

  /**
   * The types of elements to not receive the "form-control" class.
   *
   * @var array
   */
  protected array $ignoreFormControlTypes = [
    'checkbox',
    'hidden',
    'radio',
    'range',
  ];

  /**
   * The Variables object.
   *
   * @var \Drupal\ui_suite_bnppre\Utility\Variables
   */
  protected $variables;

  /**
   * An element object provided in the variables array, may not be set.
   *
   * @var \Drupal\ui_suite_bnppre\Utility\Element|false
   */
  protected $element;

  /**
   * Implements hook_preprocess_HOOK().
   */
  #[Hook('preprocess_input')]
  public function input(array &$variables): void {
    if (!isset($variables['element'])) {
      return;
    }

    $this->variables = Variables::create($variables);
    $this->element = $this->variables->element;
    if (!$this->element) {
      return;
    }

    // Form control.
    // @see https://getbootstrap.com/docs/5.3/forms/form-control
    if (!$this->element->isButton()
      && !$this->element->isType($this->ignoreFormControlTypes)
    ) {
      if (!$this->element->hasClass('form-control-plaintext')) {
        $this->element->addClass('form-control');

        if ($this->element->isType('color')) {
          $this->element->addClass('form-control-color');
        }
      }
    }

    // Checks and radios.
    if ($this->element->isType('checkbox') || $this->element->isType('radio')) {
      $this->element->addClass('form-check-input');
    }
    // Switch checkbox.
    if ($this->element->hasProperty('is_switch') && $this->element->getProperty('is_switch')) {
      $this->element->setAttribute('role', 'switch');
    }

    // Range.
    if ($this->element->isType('range')) {
      $this->element->addClass('form-range');
    }

    // Button.
    if ($this->element->isButton()) {
      $this->element->colorize();
      if ($this->element->isPropertyEmpty('icon')) {
        $this->element->setIcon();
      }
      $this->variables->offsetSet('label', $this->element->getProperty('value'));
    }

    $this->floatingLabel();
    $this->validation();

    // Map the element properties.
    $this->variables->map([
      'attributes' => 'attributes',
      'icon' => 'icon',
      'icon_position' => 'icon_position',
    ]);
  }

  /**
   * Implements hook_preprocess_HOOK().
   */
  #[Hook('preprocess_select')]
  public function select(array &$variables): void {
    if (!isset($variables['element'])) {
      return;
    }

    $this->variables = Variables::create($variables);
    $this->element = $this->variables->element;
    if (!$this->element) {
      return;
    }

    $this->validation();

    // Map the element properties.
    $this->variables->map([
      'attributes' => 'attributes',
    ]);
  }

  /**
   * Implements hook_preprocess_HOOK().
   */
  #[Hook('preprocess_textarea')]
  public function textarea(array &$variables): void {
    if (!isset($variables['element'])) {
      return;
    }

    $this->variables = Variables::create($variables);
    $this->element = $this->variables->element;
    if (!$this->element) {
      return;
    }

    $this->floatingLabel();
    $this->validation();

    // Map the element properties.
    $this->variables->map([
      'attributes' => 'attributes',
    ]);
  }

  /**
   * Ensure the element has a placeholder. Otherwise, fallback to the label.
   */
  protected function floatingLabel(): void {
    if (!$this->element) {
      return;
    }

    if (
      (
        (
          $this->element->hasProperty('floating_label')
          && $this->element->getProperty('floating_label')
        )
        || $this->element->getProperty('title_display') == 'floating'
      )
      && !$this->element->hasAttribute('placeholder')
    ) {
      // @phpstan-ignore-next-line
      $this->element->setAttribute('placeholder', $this->element->getProperty('title'));
    }
  }

  /**
   * Set validation class.
   */
  protected function validation(): void {
    if (!$this->element) {
      return;
    }

    // This is the same test as in
    // \Drupal\Core\Render\Element\RenderElement::setAttributes().
    if ($this->element->getProperty('parents') && $this->element->getProperty('validated')) {
      if ($this->element->getProperty('errors')) {
        $this->element->addClass('is-invalid');
      }
      // Per \Drupal\Core\Form\FormBuilder::processForm(), GET forms are always
      // processed and validated, so we exclude the valid feedback for GET
      // forms.
      elseif (!($this->element->hasProperty('form_method')) || $this->element->getProperty('form_method') != 'get') {
        $this->element->addClass('is-valid');
      }
    }
  }

}
