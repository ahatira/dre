<?php

declare(strict_types=1);

namespace Drupal\ps_price\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\TypedConfigManagerInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\ps_dictionary\Service\DictionaryManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form handler for Price module settings.
 */
class PriceSettingsForm extends ConfigFormBase {

  /**
   * Constructs a PriceSettingsForm object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   The config factory.
   * @param \Drupal\Core\Config\TypedConfigManagerInterface $typedConfigManager
   *   The typed config manager.
   * @param \Drupal\ps_dictionary\Service\DictionaryManagerInterface $dictionaryManager
   *   The dictionary manager service.
   */
  public function __construct(
    ConfigFactoryInterface $configFactory,
    TypedConfigManagerInterface $typedConfigManager,
    protected readonly DictionaryManagerInterface $dictionaryManager,
  ) {
    parent::__construct($configFactory, $typedConfigManager);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    return new static(
      $container->get('config.factory'),
      $container->get('config.typed'),
      $container->get('ps_dictionary.manager'),
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return ['ps_price.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'ps_price_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $config = $this->config('ps_price.settings');

    $form['description'] = [
      '#markup' => $this->t('Configure default settings for price fields.'),
    ];

    $form['default_currency'] = [
      '#type' => 'select',
      '#title' => $this->t('Default Currency'),
      '#default_value' => $config->get('default_currency') ?? 'EUR',
      '#options' => $this->dictionaryManager->getOptions('currency'),
      '#required' => TRUE,
      '#description' => $this->t('Default currency used for new price fields.'),
    ];

    $form['normalization'] = [
      '#type' => 'details',
      '#title' => $this->t('Normalization Settings'),
      '#open' => TRUE,
    ];

    $form['normalization']['normalize_on_zero_surface'] = [
      '#type' => 'select',
      '#title' => $this->t('Behavior when surface is zero'),
      '#default_value' => $config->get('normalize_on_zero_surface') ?? 'null',
      '#options' => [
        'null' => $this->t('Return NULL (recommended)'),
        'zero' => $this->t('Return 0'),
        'original' => $this->t('Return original amount'),
      ],
      '#description' => $this->t('Defines how normalized price behaves when dividing by zero surface. NULL prevents invalid comparisons.'),
    ];

    $form['normalization']['reference_period'] = [
      '#type' => 'select',
      '#title' => $this->t('Reference Period'),
      '#default_value' => $config->get('reference_period') ?? 'ANN',
      '#options' => $this->dictionaryManager->getOptions('price_period'),
      '#required' => TRUE,
      '#description' => $this->t('Period used for price normalization. All prices will be converted to this period (e.g., ANN = yearly, MEN = monthly).'),
    ];

    $form['normalization']['info'] = [
      '#type' => 'item',
      '#markup' => $this->t('<strong>Normalization formula:</strong> [Default Currency] / m² / [Reference Period]<br><em>Example: If EUR + ANN → prices normalized to EUR/m²/year</em>'),
    ];

    $form['display'] = [
      '#type' => 'details',
      '#title' => $this->t('Display Settings'),
      '#open' => TRUE,
    ];

    $form['display']['display_codes_as_labels'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Display codes as labels'),
      '#default_value' => $config->get('display_codes_as_labels') ?? FALSE,
      '#description' => $this->t('When enabled, formatters will display "Monthly" instead of "MEN", using dictionary labels.'),
    ];

    $form['display']['offer_display'] = [
      '#type' => 'details',
      '#title' => $this->t('Offer display'),
      '#open' => TRUE,
      '#description' => $this->t('Settings used by the "Offer" price formatter.'),
    ];

    $form['display']['offer_display']['price_flag_ht_label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label for VAT excluded flag'),
      '#default_value' => $config->get('price_flag_ht_label') ?? 'HT',
      '#maxlength' => 20,
      '#size' => 20,
      '#required' => TRUE,
    ];

    $form['display']['offer_display']['price_flag_hc_label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label for charges excluded flag'),
      '#default_value' => $config->get('price_flag_hc_label') ?? 'HC',
      '#maxlength' => 20,
      '#size' => 20,
      '#required' => TRUE,
    ];

    $form['display']['offer_display']['price_tooltip_ht_text'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Tooltip text for VAT excluded flag'),
      '#default_value' => $config->get('price_tooltip_ht_text') ?? 'HT : Hors taxes',
      '#maxlength' => 255,
      '#size' => 80,
    ];

    $form['display']['offer_display']['price_tooltip_hc_text'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Tooltip text for charges excluded flag'),
      '#default_value' => $config->get('price_tooltip_hc_text') ?? 'HC : Hors charges locatives',
      '#maxlength' => 255,
      '#size' => 80,
    ];

    $form['display']['offer_display']['price_tooltip_extra_text'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Additional tooltip lines'),
      '#description' => $this->t('Displayed only when at least one price flag is active. One line per row.'),
      '#default_value' => $config->get('price_tooltip_extra_text') ?? "Honoraires à la charge de l'acquéreur :\nNous consulter, à la signature du bail",
      '#rows' => 4,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $this->config('ps_price.settings')
      ->set('default_currency', $form_state->getValue('default_currency'))
      ->set('normalize_on_zero_surface', $form_state->getValue('normalize_on_zero_surface'))
      ->set('reference_period', $form_state->getValue('reference_period'))
      ->set('display_codes_as_labels', $form_state->getValue('display_codes_as_labels'))
      ->set('price_flag_ht_label', trim((string) $form_state->getValue('price_flag_ht_label')))
      ->set('price_flag_hc_label', trim((string) $form_state->getValue('price_flag_hc_label')))
      ->set('price_tooltip_ht_text', trim((string) $form_state->getValue('price_tooltip_ht_text')))
      ->set('price_tooltip_hc_text', trim((string) $form_state->getValue('price_tooltip_hc_text')))
      ->set('price_tooltip_extra_text', trim((string) $form_state->getValue('price_tooltip_extra_text')))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
