<?php

declare(strict_types=1);

namespace Drupal\ps_form\Form\Trait;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Language\LanguageManagerInterface;

/**
 * Shared helpers for ps_form.settings config forms.
 */
trait ContactConfigFormTrait {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return ['ps_form.settings'];
  }

  /**
   * Adds a multilingual hint when more than one content language is enabled.
   */
  protected function addTranslatableIntro(array &$form, LanguageManagerInterface $languageManager): void {
    if (count($languageManager->getLanguages()) <= 1) {
      return;
    }

    $defaultLangcode = $languageManager
      ->getDefaultLanguage(LanguageInterface::TYPE_CONTENT)
      ->getId();

    $message = $this->t(
      'Urgency contact wording is translatable. Edit default language (@lang) here; use the Translate tab for other languages.',
      ['@lang' => $defaultLangcode],
    );

    $form['intro_multilingual'] = [
      '#markup' => '<p><em>' . $message . '</em></p>',
      '#weight' => -20,
    ];
  }

  /**
   * Redirects to a route after successful submit unless #ajax is used.
   */
  protected function setSubmitRedirect(FormStateInterface $form_state, string $route): void {
    $form_state->setRedirect($route);
  }

}
