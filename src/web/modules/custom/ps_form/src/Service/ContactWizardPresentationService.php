<?php

declare(strict_types=1);

namespace Drupal\ps_form\Service;

use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Applies shared contact wizard layout hooks (project criteria, contact details).
 */
final class ContactWizardPresentationService {

  use StringTranslationTrait;

  /**
   * Fieldset keys rendered as min/max pairs in a two-column grid.
   *
   * @var list<string>
   */
  private const RANGE_PAIR_FIELDSETS = ['budget', 'rent', 'surface', 'post'];

  /**
   * Contact step fields laid out in a two-column grid.
   *
   * @var list<string>
   */
  private const CONTACT_GRID_FIELDS = [
    'firstname',
    'lastname',
    'company_name',
    'job_title',
    'prof_phone',
    'prof_email_address',
  ];

  /**
   * Opt-out checkbox keys displayed inline after the intro line.
   *
   * @var list<string>
   */
  private const OPTOUT_FIELDS = [
    'optout_email_transaction',
    'optout_sms_transaction',
    'optout_tel_transaction',
  ];

  public function __construct(
    private readonly ContactFormFieldLabelService $contactFormFieldLabel,
  ) {}

  /**
   * Applies wizard presentation hooks on a contact-family webform.
   *
   * @param array<string, mixed> $form
   *   The webform submission form array.
   * @param string|null $webformId
   *   The webform machine name.
   */
  public function applyToForm(array &$form, ?string $webformId = NULL): void {
    $this->contactFormFieldLabel->applyToForm($form, $webformId);

    if (isset($form['elements']['step_project']) && is_array($form['elements']['step_project'])) {
      $this->applyProjectStep($form['elements']['step_project']);
    }

    if (isset($form['elements']['step_contact']) && is_array($form['elements']['step_contact'])) {
      $this->applyContactStep($form['elements']['step_contact']);
    }

    if (isset($form['elements']['step_message']) && is_array($form['elements']['step_message'])) {
      $this->applyMessageStep($form['elements']['step_message']);
    }
  }

  /**
   * Adds project criteria grid, muted inputs, and location autocomplete hooks.
   *
   * @param array<string, mixed> $step
   *   The step_project element.
   */
  private function applyProjectStep(array &$step): void {
    $step['#attributes']['class'][] = 'ps-form-wizard-page--project';

    if (!isset($step['project']) || !is_array($step['project'])) {
      return;
    }

    $project = &$step['project'];

    if (isset($project['search_criteria']) && is_array($project['search_criteria'])) {
      $this->applySearchCriteriaPresentation($project['search_criteria']);
    }

    $this->applyLocationFields($project);
    $this->applyStackedChoiceFields($project);
  }

  /**
   * Applies stacked layout classes on project-step checkbox groups.
   *
   * @param array<string, mixed> $project
   *   The project fieldset.
   */
  private function applyStackedChoiceFields(array &$project): void {
    foreach (['consulting_type', 'other_need'] as $key) {
      if (!isset($project[$key]) || !is_array($project[$key])) {
        continue;
      }
      $project[$key]['#attributes']['class'][] = 'ps-form-checkboxes--stacked';
    }
  }

  /**
   * Styles nested search criteria fieldsets and range pairs.
   *
   * @param array<string, mixed> $criteria
   *   The search_criteria fieldset.
   */
  private function applySearchCriteriaPresentation(array &$criteria): void {
    $criteria['#attributes']['class'][] = 'ps-form-search-criteria';

    foreach (self::RANGE_PAIR_FIELDSETS as $key) {
      if (!isset($criteria[$key]) || !is_array($criteria[$key])) {
        continue;
      }
      $criteria[$key]['#attributes']['class'][] = 'ps-form-range-pair';
    }

    if (isset($criteria['search_territory']) && is_array($criteria['search_territory'])) {
      $this->applyLocationField($criteria['search_territory']);
    }
  }

  /**
   * Applies location autocomplete hooks on territory fields at project level.
   *
   * @param array<string, mixed> $project
   *   The project fieldset.
   */
  private function applyLocationFields(array &$project): void {
    if (isset($project['search_territory']) && is_array($project['search_territory'])) {
      $this->applyLocationField($project['search_territory']);
    }
  }

  /**
   * Wraps a location textfield for the shared search location editor.
   *
   * Uses ui_suite_bnp input_group so only the input sits inside the editor
   * shell (not the whole form-item — Webform #prefix wraps the full element).
   *
   * @param array<string, mixed> $element
   *   The search_territory textfield.
   */
  private function applyLocationField(array &$element): void {
    $element['#attributes']['class'][] = 'js-ps-locality-input';
    $element['#attributes']['class'][] = 'js-ps-contact-location-input';
    $element['#attributes']['autocomplete'] = 'off';
    $element['#attributes']['aria-autocomplete'] = 'list';
    $element['#attributes']['role'] = 'combobox';
    $element['#wrapper_attributes']['class'][] = 'ps-form-location-field';
    $element['#wrapper_attributes']['class'][] = 'ps-form-location';

    $element['#input_group'] = TRUE;
    $element['#input_group_attributes'] = [
      'class' => ['js-ps-location-editor', 'ps-form-location__editor'],
    ];
    $element['#input_group_before'] = [
      [
        '#markup' => '<div class="js-ps-location-chips ps-form-location__chips"></div>',
      ],
    ];
    $element['#input_group_after'] = [
      [
        '#markup' => '<div class="js-ps-location-suggest ps-form-location__suggest" role="listbox" hidden></div>',
      ],
    ];
  }

  /**
   * Whether the form includes a search_territory field.
   *
   * @param array<string, mixed> $form
   *   The webform submission form array.
   */
  public function hasLocationField(array $form): bool {
    if (!isset($form['elements']['step_project']['project']) || !is_array($form['elements']['step_project']['project'])) {
      return FALSE;
    }

    $project = $form['elements']['step_project']['project'];
    if (isset($project['search_territory'])) {
      return TRUE;
    }

    return isset($project['search_criteria']['search_territory']);
  }

  /**
   * Adds contact details section intro, grid, opt-out row, and legal classes.
   *
   * @param array<string, mixed> $step
   *   The step_contact wizard page.
   */
  private function applyContactStep(array &$step): void {
    $step['#attributes']['class'][] = 'ps-form-contact-details';

    if (!isset($step['contact_details_intro'])) {
      $step['contact_details_intro'] = [
        '#type' => 'webform_markup',
        '#markup' => '<p class="ps-form-section-intro h4">' . $this->t('What are your contact details?', [], ['context' => 'Contact wizard section title']) . '</p>',
        '#weight' => -100,
      ];
    }

    foreach (self::CONTACT_GRID_FIELDS as $key) {
      if (!isset($step[$key]) || !is_array($step[$key])) {
        continue;
      }
      $step[$key]['#wrapper_attributes']['class'][] = 'ps-form-grid-item';
    }

    if (isset($step['optout_intro']) && is_array($step['optout_intro'])) {
      $step['optout_intro']['#wrapper_attributes']['class'][] = 'ps-form-optout-intro';
    }

    foreach (self::OPTOUT_FIELDS as $key) {
      if (!isset($step[$key]) || !is_array($step[$key])) {
        continue;
      }
      $step[$key]['#wrapper_attributes']['class'][] = 'ps-form-optout-item';
    }

    if (isset($step['legal_notice']) && is_array($step['legal_notice'])) {
      $this->applyLegalNoticePresentation($step['legal_notice']);
    }
  }

  /**
   * Adds message step layout; intro and label rules live in ContactFormFieldLabelService.
   *
   * @param array<string, mixed> $step
   *   The step_message wizard page.
   */
  private function applyMessageStep(array &$step): void {
    $step['#attributes']['class'][] = 'ps-form-message-step';

    if (isset($step['qualification_comment']) && is_array($step['qualification_comment'])) {
      $step['qualification_comment']['#wrapper_attributes']['class'][] = 'ps-form-message-field';
    }
  }

  /**
   * Normalizes legal notice wrapper and markup classes.
   *
   * @param array<string, mixed> $element
   *   The legal_notice markup element.
   */
  private function applyLegalNoticePresentation(array &$element): void {
    $element['#wrapper_attributes']['class'][] = 'ps-form-legal-notice';
    $element['#wrapper_attributes']['class'][] = 'ps-contact-form__legal';

    if (isset($element['#markup']) && is_string($element['#markup'])) {
      $element['#markup'] = str_replace(
        'ps-contact-form__legal-notice',
        'ps-form-legal-notice',
        $element['#markup'],
      );
    }
  }

}
