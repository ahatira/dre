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
 * Classic email footer settings — three WYSIWYG zones.
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
        'Configure the dark footer (two columns) and the legal notice for transactional emails.',
      ) . '</p>',
    ];

    $this->addTranslatableIntro($form, $this->languageManager);

    $form['footer'] = [
      '#type' => 'details',
      '#title' => $this->t('Footer (dark zone)'),
      '#open' => TRUE,
    ];

    $form['footer']['footer_left'] = $this->textFormatElement(
      $this->t('Left column — address, phone, email'),
      $config->get('footer_left'),
      $this->t('Contact block shown in the left footer column.'),
    );

    $form['footer']['footer_right'] = $this->textFormatElement(
      $this->t('Right column — email footer menu'),
      $config->get('footer_right'),
      $this->t('Links and services menu shown in the right footer column.'),
    );

    $form['legal'] = [
      '#type' => 'details',
      '#title' => $this->t('Legal notice'),
      '#open' => TRUE,
    ];

    $form['legal']['legal'] = $this->textFormatElement(
      $this->t('Legal content'),
      $config->get('legal'),
      $this->t('Legal disclaimer below the dark footer.'),
    );

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $editable = $this->config('ps_email.footer');
    foreach (['footer_left', 'footer_right', 'legal'] as $field) {
      $value = $form_state->getValue($field);
      $editable->set($field, is_array($value) ? $value : ['value' => '', 'format' => 'email_html']);
    }
    $editable->save();

    parent::submitForm($form, $form_state);
    $this->setSubmitRedirect($form_state, 'ps_email.footer');
  }

  /**
   * Builds a text_format form element for one footer zone.
   *
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
