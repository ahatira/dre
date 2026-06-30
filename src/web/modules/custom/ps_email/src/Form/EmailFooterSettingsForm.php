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
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Email footer settings — GDPR legal + corporate identifiers textarea.
 */
final class EmailFooterSettingsForm extends ConfigFormBase {

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
    return 'ps_email_footer_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return ['ps_email.footer'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $config = $this->config('ps_email.footer');

    $form['intro'] = [
      '#markup' => '<p>' . $this->t(
        'Configure the GDPR notice and corporate identifiers shown below the green accent line in transactional emails.',
      ) . '</p>',
    ];

    $this->addTranslatableIntro($form, $this->languageManager);

    $form['legal'] = [
      '#type' => 'details',
      '#title' => $this->t('GDPR / data protection notice'),
      '#open' => TRUE,
    ];

    $form['legal']['legal'] = $this->textFormatElement(
      $this->t('Legal content'),
      $config->get('legal'),
      $this->t('Displayed in the legal zone below the 5px green separator.'),
    );

    $form['corporate'] = [
      '#type' => 'details',
      '#title' => $this->t('Corporate identifiers'),
      '#open' => TRUE,
    ];

    $form['corporate']['corporate'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Corporate identifiers line'),
      '#description' => $this->t('Single line or short block: company name, RCS, VAT, registered office, etc.'),
      '#default_value' => (string) ($config->get('corporate') ?? ''),
      '#rows' => 4,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $editable = $this->config('ps_email.footer');

    $legal = $form_state->getValue('legal');
    $editable->set('legal', is_array($legal) ? $legal : ['value' => '', 'format' => 'email_html']);
    $editable->set('corporate', trim((string) $form_state->getValue('corporate')));

    $editable->save();

    parent::submitForm($form, $form_state);
    $this->setSubmitRedirect($form_state, 'ps_email.footer');
  }

  /**
   * @param array<string, mixed>|null $stored
   *   Stored config value.
   *
   * @return array<string, mixed>
   *   Form element render array.
   */
  private function textFormatElement(string|TranslatableMarkup $title, mixed $stored, string|TranslatableMarkup $description): array {
    $body = is_array($stored) ? $stored : ['value' => '', 'format' => 'email_html'];

    return [
      '#type' => 'text_format',
      '#title' => $title,
      '#description' => $description,
      '#format' => $body['format'] ?? 'email_html',
      '#default_value' => $body['value'] ?? '',
      '#allowed_formats' => ['email_html'],
      '#rows' => 8,
    ];
  }

}
