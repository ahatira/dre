<?php

declare(strict_types=1);

namespace Drupal\ps_form\Service;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;
use Drupal\ps_dictionary\Service\DictionaryEntryIconResolver;
use Drupal\ps_dictionary\Service\DictionaryResolver;

/**
 * Enriches contact hub project fields with dictionary labels and asset icons.
 */
final class ContactProjectFieldPresenter {

  private const OPERATION_DICTIONARY = 'operation_type';

  private const ASSET_DICTIONARY = 'asset_type';

  /**
   * Webform option keys that are not dictionary-backed.
   *
   * @var list<string>
   */
  private const OPERATION_FALLBACK_CODES = ['EITHER'];

  public function __construct(
    private readonly DictionaryResolver $dictionaryResolver,
    private readonly DictionaryEntryIconResolver $iconResolver,
  ) {}

  /**
   * Webforms where LOC/VEN labels differ from the operation_type dictionary.
   *
   * @var list<string>
   */
  private const OPERATION_LABEL_FALLBACK_WEBFORMS = ['invest_sell'];

  /**
   * Legacy transaction field keys kept for sites not yet re-imported from install.
   *
   * @var list<string>
   */
  private const TRANSACTION_FIELD_KEYS = ['transaction_type', 'transaction_type_sell'];

  /**
   * Applies dictionary labels and presentation hooks on step_project fields.
   *
   * @param array<string, mixed> $form
   *   The contact webform submission form array.
   * @param string|null $webformId
   *   The webform machine name (controls operation label strategy).
   */
  public function applyToForm(array &$form, ?string $webformId = NULL): void {
    if (!isset($form['elements']['step_project']['project']) || !is_array($form['elements']['step_project']['project'])) {
      return;
    }

    $project = &$form['elements']['step_project']['project'];
    $useDictionaryOperationLabels = !in_array($webformId, self::OPERATION_LABEL_FALLBACK_WEBFORMS, TRUE);

    foreach (self::TRANSACTION_FIELD_KEYS as $fieldKey) {
      if (!isset($project[$fieldKey]) || !is_array($project[$fieldKey])) {
        continue;
      }
      $this->applyOperationType($project[$fieldKey], $useDictionaryOperationLabels);
      break;
    }

    if (isset($project['search_type']) && is_array($project['search_type'])) {
      $this->applyAssetType($project['search_type']);
    }
  }

  /**
   * After-build callback (static for form cache serialization).
   *
   * @param array<string, mixed> $element
   *   The search_type radios element.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   *
   * @return array<string, mixed>
   *   The altered element.
   */
  public static function afterBuildAssetTypeRadios(array $element, FormStateInterface $form_state): array {
    /** @var self $presenter */
    $presenter = \Drupal::service('ps_form.contact_project_field_presenter');
    return $presenter->buildAssetTypeRadioTitles($element);
  }

  /**
   * Injects dictionary icons into asset type radio labels.
   *
   * @param array<string, mixed> $element
   *   The search_type radios element.
   *
   * @return array<string, mixed>
   *   The altered element.
   */
  private function buildAssetTypeRadioTitles(array $element): array {
    foreach (Element::children($element) as $key) {
      if (!isset($element[$key]['#type']) || $element[$key]['#type'] !== 'radio') {
        continue;
      }

      $code = strtoupper((string) ($element[$key]['#return_value'] ?? $key));
      $label = $this->extractPlainTitle($element[$key]['#title'] ?? '');
      if ($label === '') {
        continue;
      }

      $element[$key]['#title'] = $this->buildAssetRadioTitle($code, $label);
    }

    return $element;
  }

  /**
   * Applies dictionary labels and after-build to operation type radios.
   *
   * @param array<string, mixed> $element
   *   The transaction_type radios element.
   * @param bool $useDictionaryLabels
   *   When FALSE, keeps webform fallback labels (e.g. invest_sell Sell/Buy).
   */
  private function applyOperationType(array &$element, bool $useDictionaryLabels = TRUE): void {
    $options = $element['#options'] ?? [];
    if (!is_array($options)) {
      return;
    }

    if ($useDictionaryLabels) {
      $element['#options'] = $this->resolveOperationOptions($options);
    }
    $element['#attributes']['class'][] = 'ps-webform-op-choices';
  }

  /**
   * Applies dictionary labels, order, and icon after-build to asset type radios.
   *
   * @param array<string, mixed> $element
   *   The search_type radios element.
   */
  private function applyAssetType(array &$element): void {
    $options = $element['#options'] ?? [];
    if (!is_array($options)) {
      return;
    }

    $element['#options'] = $this->resolveAssetOptions($options);
    $element['#attributes']['class'][] = 'ps-webform-asset-grid';
    $element['#after_build'][] = [self::class, 'afterBuildAssetTypeRadios'];
  }

  /**
   * Resolves operation type option labels from the dictionary.
   *
   * @param array<string, string> $options
   *   Webform option keys and fallback labels.
   *
   * @return array<string, string>
   *   Ordered options with dictionary labels when available.
   */
  private function resolveOperationOptions(array $options): array {
    $resolved = [];
    foreach ($options as $code => $fallbackLabel) {
      $normalized = strtoupper((string) $code);
      $dictionaryLabel = $this->dictionaryResolver->resolveLabel(self::OPERATION_DICTIONARY, $normalized);
      $resolved[$code] = $dictionaryLabel ?? (string) $fallbackLabel;
    }

    return $this->orderOptionsByDictionary($resolved, self::OPERATION_DICTIONARY, self::OPERATION_FALLBACK_CODES);
  }

  /**
   * Resolves asset type option labels from the dictionary.
   *
   * @param array<string, string> $options
   *   Webform option keys and fallback labels.
   *
   * @return array<string, string>
   *   Ordered options with dictionary labels when available.
   */
  private function resolveAssetOptions(array $options): array {
    $resolved = [];
    foreach ($options as $code => $fallbackLabel) {
      $normalized = strtoupper((string) $code);
      $dictionaryLabel = $this->dictionaryResolver->resolveLabel(self::ASSET_DICTIONARY, $normalized);
      $resolved[$code] = $dictionaryLabel ?? (string) $fallbackLabel;
    }

    return $this->orderOptionsByDictionary($resolved, self::ASSET_DICTIONARY);
  }

  /**
   * Orders webform options to match dictionary weight, keeping unknown keys last.
   *
   * @param array<string, string> $options
   *   Resolved option labels keyed by webform value.
   * @param string $dictionaryType
   *   Dictionary type machine name.
   * @param list<string> $trailingCodes
   *   Codes appended after dictionary order (e.g. EITHER).
   *
   * @return array<string, string>
   *   Reordered options.
   */
  private function orderOptionsByDictionary(array $options, string $dictionaryType, array $trailingCodes = []): array {
    $ordered = [];
    $knownCodes = [];

    foreach ($this->dictionaryResolver->all($dictionaryType) as $entry) {
      $code = strtoupper((string) $entry['code']);
      $knownCodes[$code] = TRUE;
      foreach ($options as $optionKey => $label) {
        if (strtoupper((string) $optionKey) === $code) {
          $ordered[$optionKey] = $label;
        }
      }
    }

    foreach ($trailingCodes as $trailingCode) {
      foreach ($options as $optionKey => $label) {
        if (strtoupper((string) $optionKey) === strtoupper($trailingCode)) {
          $ordered[$optionKey] = $label;
        }
      }
    }

    foreach ($options as $optionKey => $label) {
      if (isset($ordered[$optionKey])) {
        continue;
      }
      if (isset($knownCodes[strtoupper((string) $optionKey)])) {
        continue;
      }
      $ordered[$optionKey] = $label;
    }

    return $ordered;
  }

  /**
   * Builds a render array label for an asset type radio option.
   *
   * @return array<string, mixed>
   *   Render array used as the radio #title.
   */
  private function buildAssetRadioTitle(string $code, string $label): array {
    return [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['ps-webform-asset-card__link-inner'],
      ],
      'icon' => [
        '#type' => 'container',
        '#attributes' => [
          'class' => ['ps-webform-asset-card__icon'],
        ],
        'content' => $this->iconResolver->buildRenderable(
          NULL,
          ['size' => '24px'],
          ['type' => self::ASSET_DICTIONARY, 'code' => $code],
        ),
      ],
      'text' => [
        '#type' => 'html_tag',
        '#tag' => 'span',
        '#value' => $label,
        '#attributes' => [
          'class' => ['ps-webform-asset-card__label'],
        ],
      ],
    ];
  }

  /**
   * Extracts plain text from a form element title.
   */
  private function extractPlainTitle(mixed $title): string {
    if (is_string($title)) {
      return $title;
    }

    if (is_array($title) && isset($title['#markup']) && is_string($title['#markup'])) {
      return strip_tags($title['#markup']);
    }

    return '';
  }

}
