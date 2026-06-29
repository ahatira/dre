<?php

declare(strict_types=1);

namespace Drupal\ps_form\Service;

use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Aligns contact webform labels with Stellar mockups (section titles + placeholders).
 */
final class ContactFormFieldLabelService {

  use StringTranslationTrait;

  /**
   * Range fieldset keys whose legend is hidden (mockup: placeholders only).
   *
   * @var list<string>
   */
  private const HIDDEN_RANGE_FIELDSETS = ['budget', 'rent', 'surface', 'post'];

  /**
   * Criteria / property inputs that use placeholders instead of visible labels.
   *
   * @var list<string>
   */
  private const CRITERIA_PLACEHOLDER_FIELDS = [
    'min_budget',
    'max_budget',
    'min_rent',
    'max_rent',
    'min_surface',
    'max_surface',
    'min_post',
    'max_post',
    'search_territory',
    'tf_assetpostalcode',
    'totale_surface',
  ];

  /**
   * Contact step fields using placeholders instead of visible labels.
   *
   * @var list<string>
   */
  private const CONTACT_PLACEHOLDER_FIELDS = [
    'firstname',
    'lastname',
    'company_name',
    'prof_phone',
    'prof_email_address',
  ];

  /**
   * Applies mockup-aligned label and placeholder rules to a contact webform.
   *
   * @param array<string, mixed> $form
   *   The webform submission form array.
   * @param string|null $webformId
   *   The webform machine name.
   */
  public function applyToForm(array &$form, ?string $webformId = NULL): void {
    if (isset($form['elements']['step_project']) && is_array($form['elements']['step_project'])) {
      $this->applyProjectStep($form['elements']['step_project'], $webformId);
    }

    if (isset($form['elements']['step_contact']) && is_array($form['elements']['step_contact'])) {
      $this->applyContactStep($form['elements']['step_contact']);
    }

    if (isset($form['elements']['step_message']) && is_array($form['elements']['step_message'])) {
      $this->applyMessageStep($form['elements']['step_message'], $webformId);
    }
  }

  /**
   * Project step: hide redundant legends, convert criteria inputs to placeholders.
   *
   * @param array<string, mixed> $step
   *   The step_project element.
   * @param string|null $webformId
   *   The webform machine name.
   */
  private function applyProjectStep(array &$step, ?string $webformId): void {
    if ($webformId === 'entrust_property') {
      $this->applyEntrustPropertyCharacteristics($step);
    }

    if (!isset($step['project']) || !is_array($step['project'])) {
      return;
    }

    $project = &$step['project'];
    $this->hideFieldsetTitle($project);

    if (isset($project['search_criteria']) && is_array($project['search_criteria'])) {
      $this->applySearchCriteriaLabels($project['search_criteria']);
    }

    foreach (self::CRITERIA_PLACEHOLDER_FIELDS as $key) {
      if (!isset($project[$key]) || !is_array($project[$key])) {
        continue;
      }
      $this->applyPlaceholderField($project[$key], $key);
    }
  }

  /**
   * Entrust property: characteristics intro + placeholder fields at project root.
   *
   * @param array<string, mixed> $step
   *   The step_project element.
   */
  private function applyEntrustPropertyCharacteristics(array &$step): void {
    if (!isset($step['project']) || !is_array($step['project'])) {
      return;
    }

    $project = &$step['project'];

    if (!isset($project['characteristics_intro'])) {
      $project['characteristics_intro'] = [
        '#type' => 'webform_markup',
        '#markup' => '<p class="ps-form-section-intro h4">' . $this->t('What are the characteristics of your property?', [], ['context' => 'Contact wizard section title']) . '</p>',
        '#weight' => -10,
      ];
    }

    foreach (['tf_assetpostalcode', 'totale_surface'] as $key) {
      if (!isset($project[$key]) || !is_array($project[$key])) {
        continue;
      }
      $project[$key]['#wrapper_attributes']['class'][] = 'ps-form-property-characteristics__field';
      $this->applyPlaceholderField($project[$key], $key);
    }

    if (isset($project['search_territory']) && is_array($project['search_territory'])) {
      $this->applyPlaceholderField($project['search_territory'], 'search_territory');
    }
  }

  /**
   * Hides nested range fieldset legends; converts leaf fields to placeholders.
   *
   * @param array<string, mixed> $criteria
   *   The search_criteria fieldset.
   */
  private function applySearchCriteriaLabels(array &$criteria): void {
    foreach (self::HIDDEN_RANGE_FIELDSETS as $key) {
      if (!isset($criteria[$key]) || !is_array($criteria[$key])) {
        continue;
      }
      $this->hideFieldsetTitle($criteria[$key]);
      $this->applyPlaceholderToChildren($criteria[$key]);
    }

    if (isset($criteria['search_territory']) && is_array($criteria['search_territory'])) {
      $this->applyPlaceholderField($criteria['search_territory'], 'search_territory');
    }
  }

  /**
   * Applies placeholder presentation to direct children of a fieldset.
   *
   * @param array<string, mixed> $fieldset
   *   A fieldset element.
   */
  private function applyPlaceholderToChildren(array &$fieldset): void {
    foreach ($fieldset as $key => &$child) {
      if (!is_string($key) || str_starts_with($key, '#') || !is_array($child)) {
        continue;
      }
      if (!isset($child['#type']) || !in_array($child['#type'], ['number', 'textfield', 'tel', 'email'], TRUE)) {
        continue;
      }
      $this->applyPlaceholderField($child, $key);
    }
  }

  /**
   * Contact step: placeholders for identity fields, hide job title label.
   *
   * @param array<string, mixed> $step
   *   The step_contact wizard page.
   */
  private function applyContactStep(array &$step): void {
    foreach (self::CONTACT_PLACEHOLDER_FIELDS as $key) {
      if (!isset($step[$key]) || !is_array($step[$key])) {
        continue;
      }
      $this->applyPlaceholderField($step[$key], $key, TRUE);
    }

    if (isset($step['job_title']) && is_array($step['job_title'])) {
      $step['job_title']['#title_display'] = 'invisible';
      $step['job_title']['#wrapper_attributes']['class'][] = 'ps-form-field--placeholder';
    }

    if (!isset($step['required_fields_note'])) {
      $step['required_fields_note'] = [
        '#type' => 'webform_markup',
        '#markup' => '<p class="ps-form-required-note">' . $this->t('* Required information', [], ['context' => 'Contact wizard required fields note']) . '</p>',
        '#weight' => 90,
      ];
    }
  }

  /**
   * Message step: section intro + visible Message label per mockup.
   *
   * @param array<string, mixed> $step
   *   The step_message wizard page.
   * @param string|null $webformId
   *   The webform machine name.
   */
  private function applyMessageStep(array &$step, ?string $webformId): void {
    if (!isset($step['message_intro'])) {
      $intro = $webformId === 'other_request'
        ? $this->t('Would you like to clarify your request?', [], ['context' => 'Contact wizard section title'])
        : $this->t('Would you like to clarify your project?', [], ['context' => 'Contact wizard section title']);
      $step['message_intro'] = [
        '#type' => 'webform_markup',
        '#markup' => '<p class="ps-form-section-intro h4">' . $intro . '</p>',
        '#weight' => -100,
      ];
    }

    if (isset($step['qualification_comment']) && is_array($step['qualification_comment'])) {
      $step['qualification_comment']['#title_display'] = 'before';
      $step['qualification_comment']['#wrapper_attributes']['class'][] = 'ps-form-message-field';
    }
  }

  /**
   * Hides a fieldset legend (mockup uses section intros instead).
   *
   * @param array<string, mixed> $element
   *   A fieldset element.
   */
  private function hideFieldsetTitle(array &$element): void {
    if (!isset($element['#type']) || $element['#type'] !== 'fieldset') {
      return;
    }
    $element['#title_display'] = 'none';
    $element['#attributes']['class'][] = 'ps-form-fieldset--no-legend';
  }

  /**
   * Moves the element title into #placeholder and hides the visible label.
   *
   * @param array<string, mixed> $element
   *   A form element.
   * @param string $key
   *   The element key (for rent/surface placeholder variants).
   * @param bool $appendRequiredMarker
   *   When TRUE, appends "*" for required fields (contact step mockup).
   */
  private function applyPlaceholderField(array &$element, string $key, bool $appendRequiredMarker = FALSE): void {
    $title = $this->extractPlainTitle($element['#title'] ?? '');
    $required = !empty($element['#required']);

    if ($title !== '' && empty($element['#placeholder'])) {
      $placeholder = $title;
      if ($appendRequiredMarker && $required) {
        $placeholder .= '*';
      }
      elseif (in_array($key, ['tf_assetpostalcode', 'totale_surface'], TRUE) && $required) {
        $placeholder .= '*';
      }
      $element['#placeholder'] = $placeholder;
    }

    $element['#title_display'] = 'invisible';
    $element['#wrapper_attributes']['class'][] = 'ps-form-field--placeholder';
  }

  /**
   * Extracts plain text from a form element title.
   */
  private function extractPlainTitle(mixed $title): string {
    if (is_string($title)) {
      return trim($title);
    }

    if (is_array($title) && isset($title['#markup']) && is_string($title['#markup'])) {
      return trim(strip_tags($title['#markup']));
    }

    return '';
  }

}
