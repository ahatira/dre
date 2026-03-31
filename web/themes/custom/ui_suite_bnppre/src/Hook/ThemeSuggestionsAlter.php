<?php

declare(strict_types=1);

namespace Drupal\ui_suite_bnppre\Hook;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\ui_suite_bnppre\Utility\Variables;

/**
 * Add theme suggestions.
 */
class ThemeSuggestionsAlter {

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
   * Implements hook_theme_suggestions_HOOK_alter().
   */
  #[Hook('theme_suggestions_details_alter')]
  public function details(array &$suggestions, array $variables): void {
    $this->variables = Variables::create($variables);
    $this->element = $this->variables->element;

    if (!$this->element) {
      return;
    }

    if ($this->element->getProperty('isDisplayBuilder', FALSE)) {
      return;
    }

    if ($this->element->getProperty('bootstrap_accordion', TRUE)) {
      $suggestions[] = 'details__accordion';
    }
  }

  /**
   * Implements hook_theme_suggestions_HOOK_alter().
   */
  #[Hook('theme_suggestions_input_alter')]
  public function input(array &$suggestions, array $variables): void {
    $this->variables = Variables::create($variables);
    $this->element = $this->variables->element;

    if ($this->element && $this->element->isButton()) {
      $hook = 'input__button';
      $suggestions[] = $hook;
    }
  }

  /**
   * Implements hook_theme_suggestions_HOOK_alter().
   */
  #[Hook('theme_suggestions_links_alter')]
  public function links(array &$suggestions, array $variables): void {
    if (isset($variables['context']['usb_suggestion'])) {
      $suggestions[] = $variables['context']['usb_suggestion'];
    }
  }

}
