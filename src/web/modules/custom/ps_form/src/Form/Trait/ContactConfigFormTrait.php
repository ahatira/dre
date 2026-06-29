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
  protected function addTranslatableIntro(array &$form, LanguageManagerInterface $languageManager, string $scope = 'settings'): void {
    if (count($languageManager->getLanguages()) <= 1) {
      return;
    }

    $defaultLangcode = $languageManager
      ->getDefaultLanguage(LanguageInterface::TYPE_CONTENT)
      ->getId();

    $message = match ($scope) {
      'email' => $this->t(
        'Email wording is translatable. Edit default language (@lang) here; use the Translate tab for other languages.',
        ['@lang' => $defaultLangcode],
      ),
      default => $this->t(
        'Urgency contact wording is translatable. Edit default language (@lang) here; use the Translate tab for other languages.',
        ['@lang' => $defaultLangcode],
      ),
    };

    $form['intro_multilingual'] = [
      '#markup' => '<p><em>' . $message . '</em></p>',
      '#weight' => -20,
    ];
  }

  /**
   * Returns the contact email confirmation config array.
   *
   * @return array<string, mixed>
   *   Email confirmation settings.
   */
  protected function getEmailConfig(): array {
    $emailConfig = $this->config('ps_form.settings')->get('contact_email_confirmation');
    return is_array($emailConfig) ? $emailConfig : [];
  }

  /**
   * Merges partial email confirmation settings into ps_form.settings.
   *
   * @param array<string, mixed> $partial
   *   Settings to merge over the existing contact_email_confirmation array.
   */
  protected function saveEmailConfigPartial(array $partial): void {
    $this->configFactory->getEditable('ps_form.settings')
      ->set('contact_email_confirmation', array_merge($this->getEmailConfig(), $partial))
      ->save();
  }

  /**
   * Redirects to a route after successful submit unless #ajax is used.
   */
  protected function setSubmitRedirect(FormStateInterface $form_state, string $route): void {
    $form_state->setRedirect($route);
  }

}
