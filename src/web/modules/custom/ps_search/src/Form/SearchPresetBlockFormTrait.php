<?php

declare(strict_types=1);

namespace Drupal\ps_search\Form;

use Drupal\ps_core\Form\IconAutocompleteHelperTrait;
use Drupal\ps_core\Form\SectionBlockFormTrait;
use Drupal\ps_core\Utility\IconIdUtility;
use Drupal\ps_search\Service\SearchPresetOptionsProvider;

/**
 * Form helpers for search shortcut / preset link blocks.
 */
trait SearchPresetBlockFormTrait {

  use SectionBlockFormTrait;
  use IconAutocompleteHelperTrait;

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
   * Extracts icon value from form state for a table row.
   */
  protected function extractShortcutIconValue(mixed $row, string $fallback = 'bnp_custom:offices'): string {
    if (!is_array($row)) {
      return $fallback;
    }
    return IconIdUtility::extractFromSubmission($row['icon'] ?? NULL, $fallback);
  }

}
