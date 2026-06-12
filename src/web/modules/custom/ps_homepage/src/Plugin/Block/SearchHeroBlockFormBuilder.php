<?php

declare(strict_types=1);

namespace Drupal\ps_homepage\Plugin\Block;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\file\Entity\File;
use Drupal\ps_homepage\Utility\HomepageSearchHeroEditorial;

/**
 * Block form builder for the homepage search hero editorial fields.
 */
final class SearchHeroBlockFormBuilder {

  use StringTranslationTrait;

  /**
   * @param array<string, mixed> $config
   *
   * @return array<string, mixed>
   */
  public function buildForm(array $config): array {
    $form = [];

    $form['media'] = [
      '#type' => 'details',
      '#title' => $this->t('Background images'),
      '#open' => TRUE,
    ];

    $form['media']['background_image'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('Hero background image'),
      '#description' => $this->t('Full-width blurred hero background. Leave empty for the demo default.'),
      '#upload_location' => 'public://homepage/hero/',
      '#upload_validators' => [
        'FileExtension' => ['extensions' => 'png jpg jpeg webp'],
      ],
      '#default_value' => !empty($config['background_image']) ? [(int) $config['background_image']] : [],
    ];

    $form['media']['background_alt'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Hero background alternative text'),
      '#default_value' => $config['background_alt'] ?? '',
      '#maxlength' => 255,
    ];

    $form['media']['promo_background_image'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('Promo panel background image'),
      '#description' => $this->t('Right column promotional panel. Leave empty for the demo default.'),
      '#upload_location' => 'public://homepage/hero/',
      '#upload_validators' => [
        'FileExtension' => ['extensions' => 'png jpg jpeg webp'],
      ],
      '#default_value' => !empty($config['promo_background_image']) ? [(int) $config['promo_background_image']] : [],
    ];

    $form['media']['promo_background_alt'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Promo background alternative text'),
      '#default_value' => $config['promo_background_alt'] ?? '',
      '#maxlength' => 255,
    ];

    foreach (['en' => $this->t('English'), 'fr' => $this->t('French')] as $langcode => $langLabel) {
      $form['lang_' . $langcode] = [
        '#type' => 'details',
        '#title' => $this->t('Content (@language)', ['@language' => $langLabel]),
        '#open' => $langcode === 'en',
      ];

      $group = &$form['lang_' . $langcode];

      $group['form_' . $langcode] = [
        '#type' => 'details',
        '#title' => $this->t('Search form'),
        '#open' => TRUE,
      ];

      foreach ($this->textFieldDefinitions('form') as $key => $definition) {
        $field = $key . '_' . $langcode;
        $group['form_' . $langcode][$field] = $this->textElement($definition, $config[$field] ?? '');
      }

      $group['form_' . $langcode]['delegate_url_' . $langcode] = [
        '#type' => 'textfield',
        '#title' => $this->t('Delegate my search — URL'),
        '#default_value' => $config['delegate_url_' . $langcode] ?? '',
        '#maxlength' => 512,
      ];

      $group['promo_' . $langcode] = [
        '#type' => 'details',
        '#title' => $this->t('Promotional panel'),
        '#open' => TRUE,
      ];

      foreach ($this->textFieldDefinitions('promo') as $key => $definition) {
        $field = $key . '_' . $langcode;
        $group['promo_' . $langcode][$field] = $this->textElement($definition, $config[$field] ?? '');
      }

      $group['promo_' . $langcode]['promo_cta_url_' . $langcode] = [
        '#type' => 'textfield',
        '#title' => $this->t('Discover our service offers — URL'),
        '#default_value' => $config['promo_cta_url_' . $langcode] ?? '',
        '#maxlength' => 512,
      ];
    }

    return $form;
  }

  /**
   * @param array<string, mixed> $configuration
   */
  public function submitForm(array &$configuration, FormStateInterface $form_state): void {
    $formKeys = [
      'form' => [
        'title',
        'transaction_type_label',
        'op_buy_label',
        'op_rent_label',
        'op_flexible_label',
        'location_label',
        'location_placeholder',
        'property_type_label',
        'property_type_placeholder',
        'surface_min_label',
        'surface_min_placeholder',
        'price_max_label',
        'price_max_placeholder',
        'optional_label',
        'search_button_label',
        'delegate_prompt',
        'delegate_button_label',
      ],
      'promo' => [
        'promo_title',
        'promo_offers_line',
        'promo_description',
        'promo_cta_label',
      ],
    ];

    foreach (['en', 'fr'] as $langcode) {
      foreach ($formKeys['form'] as $key) {
        $field = $key . '_' . $langcode;
        $configuration[$field] = trim((string) $form_state->getValue([
          'lang_' . $langcode,
          'form_' . $langcode,
          $field,
        ]));
      }
      foreach ($formKeys['promo'] as $key) {
        $field = $key . '_' . $langcode;
        $configuration[$field] = trim((string) $form_state->getValue([
          'lang_' . $langcode,
          'promo_' . $langcode,
          $field,
        ]));
      }
      $configuration['delegate_url_' . $langcode] = trim((string) $form_state->getValue([
        'lang_' . $langcode,
        'form_' . $langcode,
        'delegate_url_' . $langcode,
      ]));
      $configuration['promo_cta_url_' . $langcode] = trim((string) $form_state->getValue([
        'lang_' . $langcode,
        'promo_' . $langcode,
        'promo_cta_url_' . $langcode,
      ]));
    }

    $configuration['background_alt'] = trim((string) $form_state->getValue(['media', 'background_alt']));
    $configuration['promo_background_alt'] = trim((string) $form_state->getValue(['media', 'promo_background_alt']));
    $configuration['background_image'] = $this->persistManagedFile($form_state->getValue(['media', 'background_image']));
    $configuration['promo_background_image'] = $this->persistManagedFile($form_state->getValue(['media', 'promo_background_image']));
  }

  /**
   * @return array<string, array{title: string, type?: string, max_length?: int}>
   */
  private function textFieldDefinitions(string $section): array {
    $form = [
      'title' => ['title' => 'Main title', 'max_length' => 255],
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
      'delegate_prompt' => ['title' => 'Delegate search — prompt text', 'max_length' => 512],
      'delegate_button_label' => ['title' => 'Delegate my search — button label', 'max_length' => 128],
    ];

    $promo = [
      'promo_title' => ['title' => 'Promo panel — title', 'max_length' => 512],
      'promo_offers_line' => ['title' => 'Promo panel — offers line', 'max_length' => 255],
      'promo_description' => ['title' => 'Promo panel — description', 'type' => 'textarea', 'max_length' => 2048],
      'promo_cta_label' => ['title' => 'Promo panel — CTA button label', 'max_length' => 128],
    ];

    return $section === 'promo' ? $promo : $form;
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

  /**
   * @param mixed $value
   */
  private function persistManagedFile(mixed $value): ?int {
    if (!is_array($value) || empty($value[0])) {
      return NULL;
    }

    $fid = (int) $value[0];
    $file = File::load($fid);
    if ($file !== NULL) {
      $file->setPermanent();
      $file->save();
    }

    return $fid;
  }

}
