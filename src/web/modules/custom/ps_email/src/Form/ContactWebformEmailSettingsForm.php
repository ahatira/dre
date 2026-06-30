<?php

declare(strict_types=1);

namespace Drupal\ps_email\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\TypedConfigManagerInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ps_email\Form\Trait\EmailConfigFormTrait;
use Drupal\ps_email\Service\ContactWebformEmailSettings;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Contact confirmation email copy for one hub webform.
 */
final class ContactWebformEmailSettingsForm extends ConfigFormBase {

  use EmailConfigFormTrait;

  /**
   * Human-readable labels for hub webforms.
   *
   * @var array<string, string>
   */
  private const WEBFORM_LABELS = [
    'find_property' => 'Find a property',
    'entrust_search' => 'Entrust a search',
    'get_advice' => 'Get advice',
    'entrust_property' => 'Entrust a property',
    'invest_sell' => 'Invest or sell',
    'other_request' => 'Other request',
  ];

  private string $webformId = '';

  public function __construct(
    ConfigFactoryInterface $config_factory,
    TypedConfigManagerInterface $typed_config_manager,
    private readonly LanguageManagerInterface $languageManager,
    private readonly ContactWebformEmailSettings $contactWebformEmailSettings,
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
      $container->get('ps_email.contact_webform_email_settings'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'ps_email_contact_webform_email_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return ['ps_email.contact'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ?string $webform_id = NULL): array {
    $webformId = trim((string) $webform_id);
    if ($webformId === '' || !$this->contactWebformEmailSettings->isHubConfirmationWebform($webformId)) {
      throw new NotFoundHttpException();
    }

    $this->webformId = $webformId;
    $copy = $this->contactWebformEmailSettings->getWebformCopy($webformId);

    $form['intro'] = [
      '#markup' => '<p>' . $this->t(
        'Copy shown in visitor confirmation emails for the @webform webform. The email subject line configured on the webform handler stays separate from the display title below.',
        ['@webform' => self::WEBFORM_LABELS[$webformId] ?? $webformId],
      ) . '</p>',
    ];

    $this->addTranslatableIntro($form, $this->languageManager);

    $form['display_title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Display title'),
      '#description' => $this->t('Shown as the main heading in the email body.'),
      '#default_value' => (string) ($copy['display_title'] ?? 'Your request has been sent'),
      '#required' => TRUE,
      '#maxlength' => 255,
    ];

    $form['greeting_prefix'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Greeting prefix'),
      '#description' => $this->t('Rendered as “@prefix First name,” using the submitted first name.', ['@prefix' => 'Hello']),
      '#default_value' => (string) ($copy['greeting_prefix'] ?? 'Hello'),
      '#required' => TRUE,
      '#maxlength' => 64,
    ];

    $form['intro_text'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Intro text'),
      '#default_value' => (string) ($copy['intro_text'] ?? ''),
      '#rows' => 3,
    ];

    $form['recap_intro'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Recap intro'),
      '#default_value' => (string) ($copy['recap_intro'] ?? ''),
      '#maxlength' => 255,
    ];

    $form['closing_text'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Closing text'),
      '#default_value' => (string) ($copy['closing_text'] ?? ''),
      '#rows' => 2,
    ];

    $form['signoff_text'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Sign-off text'),
      '#default_value' => (string) ($copy['signoff_text'] ?? ''),
      '#rows' => 2,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $webformId = $this->webformId;
    $webforms = $this->config('ps_email.contact')->get('webforms');
    if (!is_array($webforms)) {
      $webforms = [];
    }

    $webforms[$webformId] = [
      'display_title' => trim((string) $form_state->getValue('display_title')),
      'greeting_prefix' => trim((string) $form_state->getValue('greeting_prefix')),
      'intro_text' => trim((string) $form_state->getValue('intro_text')),
      'recap_intro' => trim((string) $form_state->getValue('recap_intro')),
      'closing_text' => trim((string) $form_state->getValue('closing_text')),
      'signoff_text' => trim((string) $form_state->getValue('signoff_text')),
    ];

    $this->config('ps_email.contact')
      ->set('webforms', $webforms)
      ->save();

    parent::submitForm($form, $form_state);
    $this->setSubmitRedirect($form_state, 'ps_email.contact_webform', ['webform_id' => $webformId]);
  }

  /**
   * Returns the page title for one webform copy form.
   */
  public function title(string $webform_id): TranslatableMarkup {
    $label = self::WEBFORM_LABELS[$webform_id] ?? $webform_id;
    return $this->t('Contact email: @webform', ['@webform' => $label]);
  }

}
