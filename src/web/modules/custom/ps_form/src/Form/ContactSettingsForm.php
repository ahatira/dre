<?php

declare(strict_types=1);

namespace Drupal\ps_form\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\TypedConfigManagerInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\ps_form\Service\ContactDisplayModeManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Site-wide contact hub and webform display settings.
 */
final class ContactSettingsForm extends ConfigFormBase {

  public function __construct(
    ConfigFactoryInterface $config_factory,
    TypedConfigManagerInterface $typed_config_manager,
    private readonly ContactDisplayModeManager $displayModeManager,
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
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'ps_form_contact_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return ['ps_form.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $config = $this->config('ps_form.settings');

    $form['intro'] = [
      '#markup' => '<p>' . $this->t(
        'Choose how the contact hub and contact-family webforms open across the site (header, homepage cards, deep links, hub wizard). Changes apply immediately without redeploy.',
      ) . '</p>',
    ];

    $form['contact_display_mode'] = [
      '#type' => 'radios',
      '#title' => $this->t('Contact display mode'),
      '#options' => $this->displayModeManager->getModeLabels(),
      '#default_value' => $this->displayModeManager->getMode(),
      '#required' => TRUE,
    ];

    $form['contact_modal_dialog_options'] = [
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
      ->save();

    parent::submitForm($form, $form_state);
  }

}
