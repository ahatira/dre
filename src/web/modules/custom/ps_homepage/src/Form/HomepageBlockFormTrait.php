<?php

declare(strict_types=1);

namespace Drupal\ps_homepage\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\node\NodeInterface;
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
  protected function buildEditingLanguageNotice(): array {
    $langcode = $this->editingLangcode();
    $language = \Drupal::languageManager()->getLanguage($langcode);
    $label = $language ? $language->getName() : strtoupper($langcode);

    return [
      '#type' => 'item',
      '#markup' => (string) $this->t(
        'You are editing content in <strong>@language</strong>. Switch the page translation to edit another language.',
        ['@language' => $label],
      ),
      '#wrapper_attributes' => ['class' => ['messages', 'messages--status', 'ps-homepage-block-lang-notice']],
      '#attached' => [
        'library' => [
          'ps_homepage/homepage_block_form',
          'media_library/widget',
          'media_library/ui',
          'core/drupal.dialog.ajax',
        ],
      ],
    ];
  }

  /**
   * @return array<string, mixed>
   */
  protected function buildSectionHeaderFields(array $config, bool $includeSubtitle = TRUE): array {
    $fields = [
      '#type' => 'details',
      '#title' => $this->t('Section header'),
      '#open' => TRUE,
      'title' => [
        '#type' => 'textfield',
        '#title' => $this->t('Section title'),
        '#default_value' => $config['title'] ?? '',
        '#maxlength' => 255,
        '#required' => TRUE,
      ],
    ];

    if ($includeSubtitle) {
      $fields['subtitle'] = [
        '#type' => 'textarea',
        '#title' => $this->t('Section subtitle'),
        '#default_value' => $config['subtitle'] ?? '',
        '#rows' => 3,
        '#maxlength' => 512,
      ];
    }

    return $fields;
  }

  /**
   * @return array<string, mixed>
   */
  protected function buildSectionFooterFields(array $config, bool $includeUrl = TRUE): array {
    $fields = [
      '#type' => 'details',
      '#title' => $this->t('Section footer'),
      '#open' => FALSE,
      'see_more_label' => [
        '#type' => 'textfield',
        '#title' => $this->t('Footer CTA label'),
        '#default_value' => $config['see_more_label'] ?? '',
        '#maxlength' => 255,
      ],
    ];

    if ($includeUrl) {
      $fields['see_more_url'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Footer CTA URL'),
        '#default_value' => $config['see_more_url'] ?? '',
        '#maxlength' => 512,
      ];
    }

    return $fields;
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
  protected function buildMediaLibraryElement(
    \Stringable|string $title,
    mixed $defaultMid,
    bool $required = FALSE,
    ?string $description = NULL,
  ): array {
    $element = \Drupal::service('ps_homepage.media_library_form_element_builder')->build(
      $title,
      $defaultMid,
      $required,
    );

    if ($description !== NULL) {
      $element['#description'] = $description;
    }

    return $element;
  }

  /**
   * Persists a media library selection and returns the media ID.
   */
  protected function persistMediaReference(mixed $value): ?int {
    if (!is_array($value) || $value === []) {
      return NULL;
    }

    if (isset($value['target_id']) && is_numeric($value['target_id']) && (int) $value['target_id'] > 0) {
      return (int) $value['target_id'];
    }

    if (isset($value['selection']) && is_array($value['selection'])) {
      $value = $value['selection'];
    }

    foreach ($value as $key => $item) {
      if (in_array($key, ['add_button', 'open_button', 'media_library_selection', 'media_library_update_widget', 'preview', 'remove_button', 'meta'], TRUE)) {
        continue;
      }
      if (is_numeric($item) && (int) $item > 0) {
        return (int) $item;
      }
      if (is_array($item) && !empty($item['target_id'])) {
        return (int) $item['target_id'];
      }
    }

    return NULL;
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

  protected function editingLangcode(): string {
    $node = \Drupal::routeMatch()->getParameter('node');
    if ($node instanceof NodeInterface) {
      return $node->language()->getId();
    }

    return \Drupal::languageManager()->getCurrentLanguage()->getId();
  }

}
