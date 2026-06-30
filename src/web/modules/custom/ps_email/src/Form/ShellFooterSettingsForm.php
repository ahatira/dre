<?php

declare(strict_types=1);

namespace Drupal\ps_email\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\TypedConfigManagerInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\ps_email\Form\Trait\EmailConfigFormTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Global email footer and legal settings.
 */
final class ShellFooterSettingsForm extends ConfigFormBase {

  use EmailConfigFormTrait;

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
    return 'ps_email_shell_footer_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return ['ps_email.shell'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $config = $this->config('ps_email.shell');

    $form['intro'] = [
      '#markup' => '<p>' . $this->t(
        'Footer contact details, services line and legal notice shown at the bottom of transactional emails.',
      ) . '</p>',
    ];

    $this->addTranslatableIntro($form, $this->languageManager);

    $form['reuse_site_footer'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Reuse site footer social block when fields below are empty'),
      '#default_value' => (bool) $config->get('reuse_site_footer'),
    ];

    $form['footer_address'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Footer address'),
      '#default_value' => (string) $config->get('footer_address'),
      '#rows' => 2,
    ];

    $form['footer_phone'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Footer phone (display)'),
      '#default_value' => (string) $config->get('footer_phone'),
      '#maxlength' => 64,
    ];

    $form['footer_phone_link'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Footer phone link (tel: URI)'),
      '#default_value' => (string) $config->get('footer_phone_link'),
      '#maxlength' => 64,
    ];

    $form['footer_offers_url'] = [
      '#type' => 'url',
      '#title' => $this->t('Footer offers URL'),
      '#default_value' => (string) $config->get('footer_offers_url'),
    ];

    $form['footer_offers_label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Footer offers label'),
      '#default_value' => (string) $config->get('footer_offers_label'),
      '#maxlength' => 255,
    ];

    $form['footer_services'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Footer services line'),
      '#default_value' => (string) $config->get('footer_services'),
      '#maxlength' => 255,
    ];

    $form['legal_markup'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Legal footer markup'),
      '#description' => $this->t('HTML allowed. Keep email-safe tags only (p, a, strong, em).'),
      '#default_value' => (string) $config->get('legal_markup'),
      '#rows' => 6,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $this->config('ps_email.shell')
      ->set('reuse_site_footer', (bool) $form_state->getValue('reuse_site_footer'))
      ->set('footer_address', trim((string) $form_state->getValue('footer_address')))
      ->set('footer_phone', trim((string) $form_state->getValue('footer_phone')))
      ->set('footer_phone_link', trim((string) $form_state->getValue('footer_phone_link')))
      ->set('footer_offers_url', trim((string) $form_state->getValue('footer_offers_url')))
      ->set('footer_offers_label', trim((string) $form_state->getValue('footer_offers_label')))
      ->set('footer_services', trim((string) $form_state->getValue('footer_services')))
      ->set('legal_markup', trim((string) $form_state->getValue('legal_markup')))
      ->save();

    parent::submitForm($form, $form_state);
    $this->setSubmitRedirect($form_state, 'ps_email.shell_footer');
  }

}
