<?php

declare(strict_types=1);

namespace Drupal\ps_homepage\Plugin\Block;

use Drupal\Core\Form\FormStateInterface;
use Drupal\ps_homepage\Form\HomepageBlockFormTrait;

/**
 * Block form builder for the homepage search hero editorial fields.
 */
final class SearchHeroBlockFormBuilder {

  use HomepageBlockFormTrait;

  /**
   * @param array<string, mixed> $config
   *
   * @return array<string, mixed>
   */
  public function buildForm(array $config): array {
    $form = [
      'editing_language' => $this->buildEditingLanguageNotice(),
    ];

    $form['media'] = [
      '#type' => 'details',
      '#title' => $this->t('Background images'),
      '#open' => TRUE,
    ];

    $form['media']['background_image'] = $this->buildMediaLibraryElement(
      $this->t('Hero background image'),
      $config['background_image'] ?? NULL,
    );
    $form['media']['background_image']['#description'] = $this->t('Full-width blurred hero background. Leave empty for the theme default.');

    $form['media']['promo_background_image'] = $this->buildMediaLibraryElement(
      $this->t('Promo panel background image'),
      $config['promo_background_image'] ?? NULL,
    );
    $form['media']['promo_background_image']['#description'] = $this->t('Right column promotional panel. Leave empty to reuse the hero background.');

    $form['promo_offers'] = [
      '#type' => 'details',
      '#title' => $this->t('Promo offers line'),
      '#open' => FALSE,
    ];

    $form['promo_offers']['promo_offers_use_dynamic'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Use dynamic offer count from Solr'),
      '#default_value' => $config['promo_offers_use_dynamic'] ?? TRUE,
    ];

    $form['hero'] = [
      '#type' => 'details',
      '#title' => $this->t('Hero title'),
      '#open' => TRUE,
    ];
    $form['hero']['title'] = $this->textElement(
      ['title' => 'Main title', 'max_length' => 255],
      $config['title'] ?? '',
    );

    $form['search_form'] = [
      '#type' => 'details',
      '#title' => $this->t('Search form'),
      '#open' => TRUE,
    ];
    foreach ($this->searchFormFieldDefinitions() as $key => $definition) {
      $form['search_form'][$key] = $this->textElement($definition, $config[$key] ?? '');
    }

    $form['delegate'] = [
      '#type' => 'details',
      '#title' => $this->t('Delegate bar'),
      '#open' => TRUE,
    ];
    foreach ($this->delegateFieldDefinitions() as $key => $definition) {
      $form['delegate'][$key] = $this->textElement($definition, $config[$key] ?? '');
    }
    $form['delegate']['delegate_url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Delegate my search — URL'),
      '#default_value' => $config['delegate_url'] ?? '',
      '#maxlength' => 512,
    ];

    $form['promo'] = [
      '#type' => 'details',
      '#title' => $this->t('Promotional panel'),
      '#open' => TRUE,
    ];
    foreach ($this->promoTextFieldDefinitions() as $key => $definition) {
      $form['promo'][$key] = $this->textElement($definition, $config[$key] ?? '');
    }

    $form['promo']['promo_offers_template'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Offers line template (@count placeholder)'),
      '#default_value' => $config['promo_offers_template'] ?? '',
      '#description' => $this->t('Used when dynamic count is enabled. Example: @count offers currently available'),
      '#maxlength' => 255,
      '#states' => [
        'visible' => [
          ':input[name="settings[promo_offers][promo_offers_use_dynamic]"]' => ['checked' => TRUE],
        ],
      ],
    ];

    $form['promo']['promo_offers_line'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Offers line (manual)'),
      '#default_value' => $config['promo_offers_line'] ?? '',
      '#maxlength' => 255,
      '#states' => [
        'visible' => [
          ':input[name="settings[promo_offers][promo_offers_use_dynamic]"]' => ['checked' => FALSE],
        ],
      ],
    ];

    $form['promo']['promo_description'] = [
      '#type' => 'text_format',
      '#title' => $this->t('Promo panel — description'),
      '#format' => 'basic_html',
      '#default_value' => $this->textFormatDefault($config['promo_description'] ?? ''),
    ];

    $form['promo']['promo_cta_url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Discover our service offers — URL'),
      '#default_value' => $config['promo_cta_url'] ?? '',
      '#maxlength' => 512,
    ];

    return $form;
  }

  /**
   * @param array<string, mixed> $configuration
   */
  public function submitForm(array &$configuration, FormStateInterface $form_state): void {
    $configuration['promo_offers_use_dynamic'] = (bool) $form_state->getValue([
      'promo_offers',
      'promo_offers_use_dynamic',
    ]);

    $configuration['title'] = trim((string) $form_state->getValue(['hero', 'title']));

    foreach (array_keys($this->searchFormFieldDefinitions()) as $key) {
      $configuration[$key] = trim((string) $form_state->getValue(['search_form', $key]));
    }

    foreach (array_keys($this->delegateFieldDefinitions()) as $key) {
      $configuration[$key] = trim((string) $form_state->getValue(['delegate', $key]));
    }

    $configuration['delegate_url'] = trim((string) $form_state->getValue(['delegate', 'delegate_url']));

    foreach (array_keys($this->promoTextFieldDefinitions()) as $key) {
      $configuration[$key] = trim((string) $form_state->getValue(['promo', $key]));
    }

    $configuration['promo_offers_template'] = trim((string) $form_state->getValue(['promo', 'promo_offers_template']));
    $configuration['promo_offers_line'] = trim((string) $form_state->getValue(['promo', 'promo_offers_line']));

    $description = $form_state->getValue(['promo', 'promo_description']);
    $configuration['promo_description'] = is_array($description)
      ? trim((string) ($description['value'] ?? ''))
      : trim((string) $description);

    $configuration['promo_cta_url'] = trim((string) $form_state->getValue(['promo', 'promo_cta_url']));

    $configuration['background_image'] = $this->persistMediaReference($form_state->getValue(['media', 'background_image']));
    $configuration['promo_background_image'] = $this->persistMediaReference($form_state->getValue(['media', 'promo_background_image']));
  }

  /**
   * @return array<string, array{title: string, type?: string, max_length?: int}>
   */
  private function searchFormFieldDefinitions(): array {
    return [
      'transaction_type_label' => ['title' => 'Transaction type — section label', 'max_length' => 128],
      'op_buy_label' => ['title' => 'Transaction — Buy button', 'max_length' => 64],
      'op_rent_label' => ['title' => 'Transaction — Rent button', 'max_length' => 64],
      'op_flexible_label' => ['title' => "Transaction — I'm flexible button", 'max_length' => 64],
      'location_label' => ['title' => 'Location(s) — field label', 'max_length' => 128],
      'location_placeholder' => ['title' => 'Location(s) — placeholder', 'max_length' => 255],
      'property_type_label' => ['title' => 'Property type — field label', 'max_length' => 128],
      'property_type_placeholder' => ['title' => 'Property type — placeholder', 'max_length' => 255],
      'surface_min_label' => ['title' => 'Minimum surface (m²) — field label', 'max_length' => 128],
      'surface_min_placeholder' => ['title' => 'Minimum surface (m²) — placeholder', 'max_length' => 64],
      'price_max_label' => ['title' => 'Maximum price — field label', 'max_length' => 128],
      'price_max_placeholder' => ['title' => 'Maximum price — placeholder', 'max_length' => 64],
      'optional_label' => ['title' => 'Optional hint label', 'max_length' => 64],
      'search_button_label' => ['title' => 'Search button label', 'max_length' => 64],
    ];
  }

  /**
   * @return array<string, array{title: string, type?: string, max_length?: int}>
   */
  private function delegateFieldDefinitions(): array {
    return [
      'delegate_prompt' => ['title' => 'Delegate search — prompt text', 'max_length' => 512],
      'delegate_tooltip' => ['title' => 'Delegate search — info tooltip text', 'type' => 'textarea', 'max_length' => 1024],
      'delegate_button_label' => ['title' => 'Delegate my search — button label', 'max_length' => 128],
    ];
  }

  /**
   * @return array<string, array{title: string, type?: string, max_length?: int}>
   */
  private function promoTextFieldDefinitions(): array {
    return [
      'promo_title' => ['title' => 'Promo panel — title', 'max_length' => 512],
      'promo_cta_label' => ['title' => 'Promo panel — CTA button label', 'max_length' => 128],
    ];
  }

  /**
   * @param array{title: string, type?: string, max_length?: int} $definition
   *
   * @return array<string, mixed>
   */
  private function textElement(array $definition, mixed $default): array {
    $type = $definition['type'] ?? 'textfield';
    $element = [
      '#type' => $type,
      '#title' => $this->t($definition['title']),
      '#default_value' => $default,
    ];

    if ($type === 'textfield') {
      $element['#maxlength'] = $definition['max_length'] ?? 255;
    }
    else {
      $element['#rows'] = 4;
    }

    return $element;
  }

  private function textFormatDefault(mixed $value): string {
    if (is_array($value)) {
      return (string) ($value['value'] ?? '');
    }

    return (string) $value;
  }

}
