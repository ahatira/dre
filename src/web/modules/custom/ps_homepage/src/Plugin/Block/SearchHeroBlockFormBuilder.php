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
      '#description' => $this->t('Full-width blurred hero background. Leave empty for the theme default.'),
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
      '#description' => $this->t('Right column promotional panel. Leave empty to reuse the hero background.'),
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

    foreach (['en' => $this->t('English'), 'fr' => $this->t('French')] as $langcode => $langLabel) {
      $form['lang_' . $langcode] = [
        '#type' => 'details',
        '#title' => $this->t('Content (@language)', ['@language' => $langLabel]),
        '#open' => $langcode === 'en',
      ];

      $group = &$form['lang_' . $langcode];

      $group['hero_' . $langcode] = [
        '#type' => 'details',
        '#title' => $this->t('Hero title'),
        '#open' => TRUE,
      ];
      $group['hero_' . $langcode]['title_' . $langcode] = $this->textElement(
        ['title' => 'Main title', 'max_length' => 255],
        $config['title_' . $langcode] ?? '',
      );

      $group['form_' . $langcode] = [
        '#type' => 'details',
        '#title' => $this->t('Search form'),
        '#open' => TRUE,
      ];
      foreach ($this->searchFormFieldDefinitions() as $key => $definition) {
        $field = $key . '_' . $langcode;
        $group['form_' . $langcode][$field] = $this->textElement($definition, $config[$field] ?? '');
      }

      $group['delegate_' . $langcode] = [
        '#type' => 'details',
        '#title' => $this->t('Delegate bar'),
        '#open' => TRUE,
      ];
      foreach ($this->delegateFieldDefinitions() as $key => $definition) {
        $field = $key . '_' . $langcode;
        $group['delegate_' . $langcode][$field] = $this->textElement($definition, $config[$field] ?? '');
      }
      $group['delegate_' . $langcode]['delegate_url_' . $langcode] = [
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
      foreach ($this->promoTextFieldDefinitions() as $key => $definition) {
        $field = $key . '_' . $langcode;
        $group['promo_' . $langcode][$field] = $this->textElement($definition, $config[$field] ?? '');
      }

      $group['promo_' . $langcode]['promo_offers_template_' . $langcode] = [
        '#type' => 'textfield',
        '#title' => $this->t('Offers line template (@count placeholder)'),
        '#default_value' => $config['promo_offers_template_' . $langcode] ?? '',
        '#description' => $this->t('Used when dynamic count is enabled. Example: @count offers currently available'),
        '#maxlength' => 255,
        '#states' => [
          'visible' => [
            ':input[name="settings[promo_offers][promo_offers_use_dynamic]"]' => ['checked' => TRUE],
          ],
        ],
      ];

      $group['promo_' . $langcode]['promo_offers_line_' . $langcode] = [
        '#type' => 'textfield',
        '#title' => $this->t('Offers line (manual)'),
        '#default_value' => $config['promo_offers_line_' . $langcode] ?? '',
        '#maxlength' => 255,
        '#states' => [
          'visible' => [
            ':input[name="settings[promo_offers][promo_offers_use_dynamic]"]' => ['checked' => FALSE],
          ],
        ],
      ];

      $group['promo_' . $langcode]['promo_description_' . $langcode] = [
        '#type' => 'text_format',
        '#title' => $this->t('Promo panel — description'),
        '#format' => 'basic_html',
        '#default_value' => $this->textFormatDefault($config['promo_description_' . $langcode] ?? ''),
      ];

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
    $configuration['promo_offers_use_dynamic'] = (bool) $form_state->getValue([
      'promo_offers',
      'promo_offers_use_dynamic',
    ]);

    foreach (['en', 'fr'] as $langcode) {
      $configuration['title_' . $langcode] = trim((string) $form_state->getValue([
        'lang_' . $langcode,
        'hero_' . $langcode,
        'title_' . $langcode,
      ]));

      foreach (array_keys($this->searchFormFieldDefinitions()) as $key) {
        $field = $key . '_' . $langcode;
        $configuration[$field] = trim((string) $form_state->getValue([
          'lang_' . $langcode,
          'form_' . $langcode,
          $field,
        ]));
      }

      foreach (array_keys($this->delegateFieldDefinitions()) as $key) {
        $field = $key . '_' . $langcode;
        $configuration[$field] = trim((string) $form_state->getValue([
          'lang_' . $langcode,
          'delegate_' . $langcode,
          $field,
        ]));
      }

      $configuration['delegate_url_' . $langcode] = trim((string) $form_state->getValue([
        'lang_' . $langcode,
        'delegate_' . $langcode,
        'delegate_url_' . $langcode,
      ]));

      foreach (array_keys($this->promoTextFieldDefinitions()) as $key) {
        $field = $key . '_' . $langcode;
        $configuration[$field] = trim((string) $form_state->getValue([
          'lang_' . $langcode,
          'promo_' . $langcode,
          $field,
        ]));
      }

      $configuration['promo_offers_template_' . $langcode] = trim((string) $form_state->getValue([
        'lang_' . $langcode,
        'promo_' . $langcode,
        'promo_offers_template_' . $langcode,
      ]));

      $configuration['promo_offers_line_' . $langcode] = trim((string) $form_state->getValue([
        'lang_' . $langcode,
        'promo_' . $langcode,
        'promo_offers_line_' . $langcode,
      ]));

      $description = $form_state->getValue([
        'lang_' . $langcode,
        'promo_' . $langcode,
        'promo_description_' . $langcode,
      ]);
      $configuration['promo_description_' . $langcode] = is_array($description)
        ? trim((string) ($description['value'] ?? ''))
        : trim((string) $description);

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
