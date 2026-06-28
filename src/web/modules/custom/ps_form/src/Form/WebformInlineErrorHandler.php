<?php

declare(strict_types=1);

namespace Drupal\ps_form\Form;

use Drupal\Core\Form\FormElementHelper;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;
use Drupal\inline_form_errors\FormErrorHandler as InlineFormErrorHandler;

/**
 * Inline form errors without the top-level error summary (webforms in panels).
 */
final class WebformInlineErrorHandler extends InlineFormErrorHandler {

  /**
   * {@inheritdoc}
   */
  protected function displayErrorMessages(array $form, FormStateInterface $form_state): void {
    if (!empty($form['#disable_inline_form_errors'])) {
      parent::displayErrorMessages($form, $form_state);
      return;
    }

    $errors = $form_state->getErrors();
    foreach ($errors as $name => $error) {
      $form_element = FormElementHelper::getElementByName($name, $form);
      $title = FormElementHelper::getElementTitle($form_element);
      $is_visible_element = Element::isVisibleElement($form_element);
      $has_title = !empty($title);
      $has_id = !empty($form_element['#id']);

      if (!empty($form_element['#error_no_message'])) {
        unset($errors[$name]);
      }
      elseif ($is_visible_element && $has_title && $has_id) {
        unset($errors[$name]);
      }
    }

    foreach ($errors as $error) {
      $this->messenger->addError($error);
    }
  }

}
