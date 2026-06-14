<?php

declare(strict_types=1);

namespace Drupal\ps_content\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\node\NodeInterface;
use Drupal\ps_core\Form\IconAutocompleteHelperTrait;
use Drupal\ps_core\Form\SectionBlockFormTrait;
use Drupal\ps_core\Utility\IconIdUtility;
use Drupal\ps_search\Service\SearchPresetOptionsProvider;

/**
 * Shared form helpers for ps_content LB blocks.
 */
trait ContentBlockFormTrait {

  use SectionBlockFormTrait;
  use IconAutocompleteHelperTrait;

  /**
   * @return array<string, mixed>
   */
  protected function buildContentEditingLanguageNotice(): array {
    return $this->buildEditingLanguageNotice([
      'ps_homepage/homepage_block_form',
      'media_library/widget',
      'media_library/ui',
      'core/drupal.dialog.ajax',
    ]);
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

  protected function extractIconValue(FormStateInterface $form_state, array $parents, string $field, string $fallback = ''): string {
    $value = $form_state->getValue($parents);
    if (!is_array($value)) {
      return $fallback;
    }
    return IconIdUtility::extractFromSubmission($value[$field] ?? NULL, $fallback);
  }

}
