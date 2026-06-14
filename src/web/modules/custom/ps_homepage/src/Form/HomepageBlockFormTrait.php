<?php

declare(strict_types=1);

namespace Drupal\ps_homepage\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\file\Entity\File;
use Drupal\ps_core\Form\IconAutocompleteHelperTrait;
use Drupal\ps_core\Utility\IconIdUtility;
use Drupal\ps_search\Service\SearchPresetOptionsProvider;

/**
 * Shared form helpers for homepage LB blocks with repeatable items.
 */
trait HomepageBlockFormTrait {

  use StringTranslationTrait;
  use IconAutocompleteHelperTrait;

  /**
   * @return array<string, mixed>
   */
  protected function buildLanguageTabs(array $config, callable $fieldsCallback): array {
    $form = [];
    foreach (['en' => $this->t('English'), 'fr' => $this->t('French')] as $langcode => $langLabel) {
      $form['lang_' . $langcode] = [
        '#type' => 'details',
        '#title' => $this->t('Content (@language)', ['@language' => $langLabel]),
        '#open' => $langcode === 'en',
      ];
      $form['lang_' . $langcode] += $fieldsCallback($langcode, $config);
    }
    return $form;
  }

  /**
   * @return array<string, mixed>
   */
  protected function buildHeadingFields(string $langcode, array $config, bool $includeSubtitle = TRUE): array {
    $fields = [
      'title_' . $langcode => [
        '#type' => 'textfield',
        '#title' => $this->t('Section title'),
        '#default_value' => $config['title_' . $langcode] ?? '',
        '#maxlength' => 255,
        '#required' => $langcode === 'en',
      ],
    ];

    if ($includeSubtitle) {
      $fields['subtitle_' . $langcode] = [
        '#type' => 'textfield',
        '#title' => $this->t('Section subtitle'),
        '#default_value' => $config['subtitle_' . $langcode] ?? '',
        '#maxlength' => 512,
      ];
    }

    return $fields;
  }

  /**
   * @return array<string, mixed>
   */
  protected function buildFooterCtaFields(string $langcode, array $config): array {
    return [
      'see_more_label_' . $langcode => [
        '#type' => 'textfield',
        '#title' => $this->t('Footer CTA label'),
        '#default_value' => $config['see_more_label_' . $langcode] ?? '',
        '#maxlength' => 255,
      ],
      'see_more_url_' . $langcode => [
        '#type' => 'textfield',
        '#title' => $this->t('Footer CTA URL'),
        '#default_value' => $config['see_more_url_' . $langcode] ?? '',
        '#maxlength' => 512,
      ],
    ];
  }

  /**
   * @return array<string, mixed>
   */
  protected function buildLinkTypeElement(string $fieldName, mixed $defaultValue, array $options): array {
    return [
      '#type' => 'select',
      '#title' => $this->t('Link type'),
      '#options' => $options,
      '#default_value' => $defaultValue ?: 'url',
    ];
  }

  /**
   * @return array<string, string>
   */
  protected function serviceLinkTypeOptions(): array {
    return [
      'url' => (string) $this->t('URL'),
      'modal' => (string) $this->t('Modal'),
      'offcanvas' => (string) $this->t('Contact offcanvas'),
      'search_preset' => (string) $this->t('Search preset'),
    ];
  }

  /**
   * @return array<string, string>
   */
  protected function shortcutLinkTypeOptions(): array {
    return [
      'search_preset' => (string) $this->t('Search preset'),
      'custom_url' => (string) $this->t('Custom URL'),
    ];
  }

  /**
   * @return array<string, string>
   */
  protected function expertCtaLinkTypeOptions(): array {
    return [
      'url' => (string) $this->t('URL'),
      'modal' => (string) $this->t('Modal'),
      'offcanvas' => (string) $this->t('Contact offcanvas'),
    ];
  }

  /**
   * @return array<string, mixed>
   */
  protected function buildManagedFileElement(
    string $title,
    mixed $defaultFid,
    string $uploadLocation,
    bool $required = FALSE,
  ): array {
    return [
      '#type' => 'managed_file',
      '#title' => $title,
      '#upload_location' => $uploadLocation,
      '#upload_validators' => [
        'FileExtension' => ['extensions' => 'png jpg jpeg webp'],
      ],
      '#default_value' => !empty($defaultFid) ? [(int) $defaultFid] : [],
      '#required' => $required,
    ];
  }

  /**
   * Persists a managed file upload and returns the file ID.
   */
  protected function persistManagedFile(mixed $value): ?int {
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

  /**
   * @return array<string, mixed>
   */
  protected function buildPresetFields(
    array $parents,
    array $item,
    SearchPresetOptionsProvider $optionsProvider,
  ): array {
    $selector = $this->buildStateSelector($parents, 'link_type');

    return [
      'preset_operation' => [
        '#type' => 'select',
        '#title' => $this->t('Operation'),
        '#options' => $optionsProvider->getOperationOptions(),
        '#default_value' => $item['preset_operation'] ?? '',
        '#states' => [
          'visible' => [
            $selector => ['value' => 'search_preset'],
          ],
        ],
      ],
      'preset_asset' => [
        '#type' => 'select',
        '#title' => $this->t('Asset type'),
        '#options' => $optionsProvider->getAssetOptions(),
        '#default_value' => $item['preset_asset'] ?? '',
        '#states' => [
          'visible' => [
            $selector => ['value' => 'search_preset'],
          ],
        ],
      ],
      'preset_locality' => [
        '#type' => 'textfield',
        '#title' => $this->t('Location (optional)'),
        '#description' => $this->t('City, postal code or department token as used in search (e.g. Paris, 75008, 75).'),
        '#default_value' => $item['preset_locality'] ?? '',
        '#maxlength' => 255,
        '#states' => [
          'visible' => [
            $selector => ['value' => 'search_preset'],
          ],
        ],
      ],
    ];
  }

  /**
   * @param list<string> $parents
   */
  protected function buildStateSelector(array $parents, string $field): string {
    $name = 'settings';
    foreach ($parents as $parent) {
      $name .= '[' . $parent . ']';
    }
    return ':input[name="' . $name . '[' . $field . ']"]';
  }

  /**
   * @param array<int, array<string, mixed>> $items
   *
   * @return array<int, array<string, mixed>>
   */
  protected function sortItemsByWeight(array $items): array {
    usort($items, static function (array $a, array $b): int {
      return ((int) ($a['weight'] ?? 0)) <=> ((int) ($b['weight'] ?? 0));
    });
    return array_values($items);
  }

  /**
   * Extracts icon value from form state for a table row.
   */
  protected function extractIconValue(FormStateInterface $form_state, array $parents, string $field, string $fallback = ''): string {
    $value = $form_state->getValue($parents);
    if (!is_array($value)) {
      return $fallback;
    }
    return IconIdUtility::extractFromSubmission($value[$field] ?? NULL, $fallback);
  }

}
