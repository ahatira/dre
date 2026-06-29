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
 * Visitor confirmation email footer settings.
 */
final class ContactEmailFooterSettingsForm extends ConfigFormBase {

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
    return 'ps_form_contact_email_footer_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $emailConfig = $this->getEmailConfig();

    $form['intro'] = [
      '#markup' => '<p>' . $this->t(
        'Footer contact details, services line and legal notice shown at the bottom of visitor confirmation emails.',
      ) . '</p>',
    ];

    $this->addTranslatableIntro($form, $this->languageManager, 'email');

    $form['contact_email_reuse_site_footer'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Reuse site footer social block when fields below are empty'),
      '#default_value' => (bool) ($emailConfig['reuse_site_footer'] ?? TRUE),
    ];

    $form['contact_email_footer_address'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Footer address'),
      '#default_value' => (string) ($emailConfig['footer_address'] ?? ''),
      '#rows' => 2,
    ];

    $form['contact_email_footer_phone'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Footer phone (display)'),
      '#default_value' => (string) ($emailConfig['footer_phone'] ?? ''),
      '#maxlength' => 64,
    ];

    $form['contact_email_footer_phone_link'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Footer phone link (tel: URI)'),
      '#default_value' => (string) ($emailConfig['footer_phone_link'] ?? ''),
      '#maxlength' => 64,
    ];

    $form['contact_email_footer_offers_url'] = [
      '#type' => 'url',
      '#title' => $this->t('Footer offers URL'),
      '#default_value' => (string) ($emailConfig['footer_offers_url'] ?? ''),
    ];

    $form['contact_email_footer_offers_label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Footer offers label'),
      '#default_value' => (string) ($emailConfig['footer_offers_label'] ?? ''),
      '#maxlength' => 255,
    ];

    $form['contact_email_footer_services'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Footer services line'),
      '#default_value' => (string) ($emailConfig['footer_services'] ?? ''),
      '#maxlength' => 255,
    ];

    $form['contact_email_legal_markup'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Legal footer markup'),
      '#description' => $this->t('HTML allowed. Keep email-safe tags only (p, a, strong, em).'),
      '#default_value' => (string) ($emailConfig['legal_markup'] ?? ''),
      '#rows' => 6,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $this->saveEmailConfigPartial([
      'reuse_site_footer' => (bool) $form_state->getValue('contact_email_reuse_site_footer'),
      'footer_address' => trim((string) $form_state->getValue('contact_email_footer_address')),
      'footer_phone' => trim((string) $form_state->getValue('contact_email_footer_phone')),
      'footer_phone_link' => trim((string) $form_state->getValue('contact_email_footer_phone_link')),
      'footer_offers_url' => trim((string) $form_state->getValue('contact_email_footer_offers_url')),
      'footer_offers_label' => trim((string) $form_state->getValue('contact_email_footer_offers_label')),
      'footer_services' => trim((string) $form_state->getValue('contact_email_footer_services')),
      'legal_markup' => trim((string) $form_state->getValue('contact_email_legal_markup')),
    ]);

    parent::submitForm($form, $form_state);
    $this->setSubmitRedirect($form_state, 'ps_form.email_footer');
  }

}
