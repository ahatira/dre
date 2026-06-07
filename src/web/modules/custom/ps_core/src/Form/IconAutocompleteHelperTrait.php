<?php

declare(strict_types=1);

namespace Drupal\ps_core\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\ps_core\Utility\IconIdUtility;

/**
 * Reusable helpers for UI Icons picker form elements (modal gallery).
 */
trait IconAutocompleteHelperTrait {

  /**
   * Builds an icon picker form element (modal gallery + search).
   *
   * @param \Stringable|string $title
   *   Field title.
   * @param string $defaultValue
   *   Default pack:id value.
   * @param array<string, mixed> $options
   *   Optional element overrides: description, required, allowed_icon_packs,
   *   placeholder, button_label.
   *
   * @return array<string, mixed>
   *   Form API element.
   */
  protected function buildIconPickerElement(\Stringable|string $title, string $defaultValue, array $options = []): array {
    $allowedPacks = $options['allowed_icon_packs'] ?? [IconIdUtility::DEFAULT_BNP_PACK];

    return [
      '#type' => 'icon_picker',
      '#title' => (string) $title,
      '#default_value' => $defaultValue,
      '#allowed_icon_pack' => $allowedPacks,
      '#return_id' => TRUE,
      '#required' => (bool) ($options['required'] ?? FALSE),
      '#description' => $options['description'] ?? NULL,
      '#placeholder' => $options['placeholder'] ?? 'Select icon',
      '#wrapper_attributes' => ['class' => ['ps-icon-picker']],
      '#attached' => ['library' => ['ps_core/icon_picker']],
    ];
  }

  /**
   * @deprecated in ps_core:0.0.0 and is removed from ps_core:0.0.0. Use
   *   ::buildIconPickerElement() instead.
   */
  protected function buildIconAutocompleteElement(\Stringable|string $title, string $defaultValue, array $options = []): array {
    return $this->buildIconPickerElement($title, $defaultValue, $options);
  }

  /**
   * Returns a stored icon id or the configured fallback.
   */
  protected function getIconDefault(mixed $value, string $fallback): string {
    return IconIdUtility::normalizeStoredIcon($value, $fallback);
  }

  /**
   * Extracts a pack:id icon value from an icon_autocomplete submission.
   */
  protected function extractIconId(mixed $value, string $fallback = ''): string {
    return IconIdUtility::extractFromSubmission($value, $fallback);
  }

  /**
   * Reads a submitted icon_autocomplete value from a nested form tree.
   */
  protected function getSubmittedIconValue(FormStateInterface $form_state, string $key, ?string $parentsKey = NULL): mixed {
    if ($parentsKey !== NULL) {
      $parentsValue = $form_state->getValue($parentsKey);
      if (is_array($parentsValue) && array_key_exists($key, $parentsValue)) {
        return $parentsValue[$key];
      }
    }

    return $form_state->getValue($key);
  }

}
