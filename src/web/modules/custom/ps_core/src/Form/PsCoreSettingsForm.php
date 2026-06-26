<?php

declare(strict_types=1);

namespace Drupal\ps_core\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\TypedConfigManagerInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Global Property Search settings (site contact).
 */
final class PsCoreSettingsForm extends ConfigFormBase {

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
    return 'ps_core_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return ['ps_core.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $config = $this->config('ps_core.settings');

    $form['intro'] = [
      '#type' => 'container',
      'lead' => [
        '#markup' => '<p>' . $this->t(
          'Configure site-wide contact details shown in offcanvas forms.',
        ) . '</p>',
      ],
    ];

    if (count($this->languageManager->getLanguages()) > 1) {
      $default_langcode = $this->languageManager
        ->getDefaultLanguage(LanguageInterface::TYPE_CONTENT)
        ->getId();
      $form['intro']['multilingual'] = [
        '#markup' => '<p><em>' . $this->t(
          'This site is multilingual. Edit translatable wording here in the default language (@lang). Use the Translate tab for other enabled languages.',
          ['@lang' => $default_langcode],
        ) . '</em></p>',
      ];
    }

    $form['site_contact'] = [
      '#type' => 'details',
      '#title' => $this->t('Site contact'),
      '#description' => $this->t('Phone and opening hours shown in offcanvas forms (search alert, contact, share, etc.).'),
      '#open' => TRUE,
    ];

    $form['site_contact']['urgency_help_enabled'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Display urgency help block'),
      '#default_value' => (bool) ($config->get('urgency_help_enabled') ?? TRUE),
    ];

    $urgency_states = [
      'visible' => [
        ':input[name="urgency_help_enabled"]' => ['checked' => TRUE],
      ],
    ];

    $form['site_contact']['urgency_help_lead'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Lead text'),
      '#description' => $this->t('Text before the phone number, e.g. “In a hurry? Call us at”.'),
      '#default_value' => (string) ($config->get('urgency_help_lead') ?? 'In a hurry? Call us at'),
      '#maxlength' => 255,
      '#states' => $urgency_states,
    ];

    $form['site_contact']['urgency_help_phone'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Phone number (display)'),
      '#default_value' => (string) ($config->get('urgency_help_phone') ?? ''),
      '#maxlength' => 64,
      '#states' => $urgency_states + [
        'required' => [
          ':input[name="urgency_help_enabled"]' => ['checked' => TRUE],
        ],
      ],
    ];

    $form['site_contact']['urgency_help_phone_link'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Phone link (tel: URI)'),
      '#description' => $this->t('Optional. Leave empty to derive from the display number (FR numbers starting with 0).'),
      '#default_value' => (string) ($config->get('urgency_help_phone_link') ?? ''),
      '#maxlength' => 64,
      '#states' => $urgency_states,
    ];

    $form['site_contact']['urgency_help_hours'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Opening hours'),
      '#default_value' => (string) ($config->get('urgency_help_hours') ?? ''),
      '#maxlength' => 255,
      '#states' => $urgency_states,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $this->configFactory->getEditable('ps_core.settings')
      ->set('urgency_help_enabled', (bool) $form_state->getValue('urgency_help_enabled'))
      ->set('urgency_help_lead', trim((string) $form_state->getValue('urgency_help_lead')))
      ->set('urgency_help_phone', trim((string) $form_state->getValue('urgency_help_phone')))
      ->set('urgency_help_phone_link', trim((string) $form_state->getValue('urgency_help_phone_link')))
      ->set('urgency_help_hours', trim((string) $form_state->getValue('urgency_help_hours')))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
