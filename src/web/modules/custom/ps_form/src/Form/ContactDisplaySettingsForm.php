<?php

declare(strict_types=1);

namespace Drupal\ps_form\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\TypedConfigManagerInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\ps_form\Form\Trait\ContactConfigFormTrait;
use Drupal\ps_form\Service\ContactDisplayModeManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Contact display mode and urgency phone block settings.
 */
final class ContactDisplaySettingsForm extends ConfigFormBase {

  use ContactConfigFormTrait;

  public function __construct(
    ConfigFactoryInterface $config_factory,
    TypedConfigManagerInterface $typed_config_manager,
    private readonly ContactDisplayModeManager $displayModeManager,
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
      $container->get('ps_form.contact_display_mode'),
      $container->get('language_manager'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'ps_form_contact_display_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $config = $this->config('ps_form.settings');

    $form['intro'] = [
      '#markup' => '<p>' . $this->t(
        'Configure how contact webforms open on the site and the urgency phone block shown below webform actions.',
      ) . '</p>',
      '#weight' => -10,
    ];

    $this->addTranslatableIntro($form, $this->languageManager);

    $form['display'] = [
      '#type' => 'details',
      '#title' => $this->t('Display mode'),
      '#open' => TRUE,
    ];

    $form['display']['contact_display_mode'] = [
      '#type' => 'radios',
      '#title' => $this->t('Contact display mode'),
      '#options' => $this->displayModeManager->getModeLabels(),
      '#default_value' => $this->displayModeManager->getMode(),
      '#required' => TRUE,
    ];

    $form['display']['contact_modal_dialog_options'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Modal dialog options (JSON)'),
      '#description' => $this->t('Drupal AJAX modal options. Example keys: width, height, dialogClass.'),
      '#default_value' => (string) ($config->get('contact_modal_dialog_options') ?: $this->displayModeManager->getDefaultModalDialogOptionsJson()),
      '#rows' => 6,
      '#states' => [
        'visible' => [
          ':input[name="contact_display_mode"]' => ['value' => ContactDisplayModeManager::MODE_MODAL],
        ],
        'required' => [
          ':input[name="contact_display_mode"]' => ['value' => ContactDisplayModeManager::MODE_MODAL],
        ],
      ],
    ];

    $urgency_states = [
      'visible' => [
        ':input[name="urgency_help_enabled"]' => ['checked' => TRUE],
      ],
    ];

    $form['urgency'] = [
      '#type' => 'details',
      '#title' => $this->t('Urgency contact'),
      '#description' => $this->t('Phone and opening hours shown below webform actions (contact hub, search alert, share, etc.).'),
      '#open' => TRUE,
    ];

    $form['urgency']['urgency_help_enabled'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Display urgency help block'),
      '#default_value' => (bool) ($config->get('urgency_help_enabled') ?? TRUE),
    ];

    $form['urgency']['urgency_help_lead'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Lead text'),
      '#description' => $this->t('Text before the phone number, e.g. “In a hurry? Call us at”.'),
      '#default_value' => (string) ($config->get('urgency_help_lead') ?? 'In a hurry? Call us at'),
      '#maxlength' => 255,
      '#states' => $urgency_states,
    ];

    $form['urgency']['urgency_help_phone'] = [
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

    $form['urgency']['urgency_help_phone_link'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Phone link (tel: URI)'),
      '#description' => $this->t('Optional. Leave empty to derive from the display number (FR numbers starting with 0).'),
      '#default_value' => (string) ($config->get('urgency_help_phone_link') ?? ''),
      '#maxlength' => 64,
      '#states' => $urgency_states,
    ];

    $form['urgency']['urgency_help_hours'] = [
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
  public function validateForm(array &$form, FormStateInterface $form_state): void {
    parent::validateForm($form, $form_state);

    if ($form_state->getValue('contact_display_mode') !== ContactDisplayModeManager::MODE_MODAL) {
      return;
    }

    $raw = trim((string) $form_state->getValue('contact_modal_dialog_options'));
    if ($raw === '') {
      $form_state->setErrorByName('contact_modal_dialog_options', $this->t('Modal dialog options are required when modal mode is selected.'));
      return;
    }

    try {
      $decoded = json_decode($raw, TRUE, 512, JSON_THROW_ON_ERROR);
    }
    catch (\JsonException $exception) {
      $form_state->setErrorByName('contact_modal_dialog_options', $this->t('Invalid JSON: @message', [
        '@message' => $exception->getMessage(),
      ]));
      return;
    }

    if (!is_array($decoded)) {
      $form_state->setErrorByName('contact_modal_dialog_options', $this->t('Modal dialog options must be a JSON object.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $mode = (string) $form_state->getValue('contact_display_mode');
    $modalOptions = trim((string) $form_state->getValue('contact_modal_dialog_options'));

    $this->configFactory->getEditable('ps_form.settings')
      ->set('contact_display_mode', $mode)
      ->set('contact_modal_dialog_options', $mode === ContactDisplayModeManager::MODE_MODAL ? $modalOptions : '')
      ->set('urgency_help_enabled', (bool) $form_state->getValue('urgency_help_enabled'))
      ->set('urgency_help_lead', trim((string) $form_state->getValue('urgency_help_lead')))
      ->set('urgency_help_phone', trim((string) $form_state->getValue('urgency_help_phone')))
      ->set('urgency_help_phone_link', trim((string) $form_state->getValue('urgency_help_phone_link')))
      ->set('urgency_help_hours', trim((string) $form_state->getValue('urgency_help_hours')))
      ->save();

    parent::submitForm($form, $form_state);
    $this->setSubmitRedirect($form_state, 'ps_form.settings');
  }

}
