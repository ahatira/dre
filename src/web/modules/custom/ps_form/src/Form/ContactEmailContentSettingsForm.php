<?php

declare(strict_types=1);

namespace Drupal\ps_form\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\TypedConfigManagerInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\ps_form\Form\Trait\ContactConfigFormTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Visitor confirmation email copy settings.
 */
final class ContactEmailContentSettingsForm extends ConfigFormBase {

  use ContactConfigFormTrait;

  public function __construct(
    ConfigFactoryInterface $config_factory,
    TypedConfigManagerInterface $typed_config_manager,
    private readonly LanguageManagerInterface $languageManager,
  ) {
    parent::__construct($config_factory, $typed_config_manager);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    return new static(
      $container->get('config.factory'),
      $container->get('config.typed'),
      $container->get('language_manager'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'ps_form_contact_email_content_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $emailConfig = $this->getEmailConfig();

    $form['intro'] = [
      '#markup' => '<p>' . $this->t(
        'Copy shown in visitor confirmation emails. The email subject line configured on each webform handler stays separate from the display title below.',
      ) . '</p>',
    ];

    $this->addTranslatableIntro($form, $this->languageManager, 'email');

    $form['contact_email_display_title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Display title'),
      '#description' => $this->t('Shown as the main heading in the email body.'),
      '#default_value' => (string) ($emailConfig['display_title'] ?? 'Your request has been sent'),
      '#required' => TRUE,
      '#maxlength' => 255,
    ];

    $form['contact_email_greeting_prefix'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Greeting prefix'),
      '#description' => $this->t('Rendered as “@prefix First name,” using the submitted first name.', ['@prefix' => 'Hello']),
      '#default_value' => (string) ($emailConfig['greeting_prefix'] ?? 'Hello'),
      '#required' => TRUE,
      '#maxlength' => 64,
    ];

    $form['contact_email_intro_text'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Intro text'),
      '#default_value' => (string) ($emailConfig['intro_text'] ?? ''),
      '#rows' => 3,
    ];

    $form['contact_email_recap_intro'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Recap intro'),
      '#default_value' => (string) ($emailConfig['recap_intro'] ?? ''),
      '#maxlength' => 255,
    ];

    $form['contact_email_closing_text'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Closing text'),
      '#default_value' => (string) ($emailConfig['closing_text'] ?? ''),
      '#rows' => 2,
    ];

    $form['contact_email_signoff_text'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Sign-off text'),
      '#default_value' => (string) ($emailConfig['signoff_text'] ?? ''),
      '#rows' => 2,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $this->saveEmailConfigPartial([
      'display_title' => trim((string) $form_state->getValue('contact_email_display_title')),
      'greeting_prefix' => trim((string) $form_state->getValue('contact_email_greeting_prefix')),
      'intro_text' => trim((string) $form_state->getValue('contact_email_intro_text')),
      'recap_intro' => trim((string) $form_state->getValue('contact_email_recap_intro')),
      'closing_text' => trim((string) $form_state->getValue('contact_email_closing_text')),
      'signoff_text' => trim((string) $form_state->getValue('contact_email_signoff_text')),
    ]);

    parent::submitForm($form, $form_state);
    $this->setSubmitRedirect($form_state, 'ps_form.email_content');
  }

}
