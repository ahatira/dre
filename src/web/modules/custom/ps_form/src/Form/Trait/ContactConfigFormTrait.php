<?php

declare(strict_types=1);

namespace Drupal\ps_form\Form\Trait;

use Drupal\Component\Utility\NestedArray;
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

  /**
   * Resolves a managed_file fid after AJAX upload rebuilds.
   *
   * @param list<string> $parents
   *   Form value parents (e.g. ['heroes', 'find_property', 'hero_file', 'upload']).
   */
  protected function resolveNestedManagedFileFid(array $parents, int $configuredFid, FormStateInterface $form_state): int {
    $input = $form_state->getUserInput();
    if (is_array($input)) {
      $inputValue = NestedArray::getValue($input, $parents);
      if (is_array($inputValue) && array_key_exists('fids', $inputValue)) {
        return $this->extractManagedFileFid($inputValue);
      }
    }

    $value = NestedArray::getValue($form_state->getValues(), $parents);
    $extracted = $this->extractManagedFileFid($value);
    if ($extracted > 0) {
      return $extracted;
    }

    if (is_array($value)) {
      return 0;
    }

    return $configuredFid;
  }

  /**
   * Extracts a file id from a managed_file form value.
   */
  protected function extractManagedFileFid(mixed $value): int {
    if (!is_array($value)) {
      return 0;
    }

    if (isset($value[0]) && (int) $value[0] > 0) {
      return (int) $value[0];
    }

    if (array_key_exists('fids', $value)) {
      $fids = trim((string) $value['fids']);
      if ($fids === '') {
        return 0;
      }

      return (int) strtok($fids, ' ');
    }

    return 0;
  }

}
