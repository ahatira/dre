<?php

declare(strict_types=1);

namespace Drupal\ps_form\Hook;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\ps_form\Service\ContactNeedRouter;
use Drupal\webform\WebformSubmissionForm;
use Drupal\webform\WebformSubmissionInterface;

/**
 * Contact family webforms: hub need routing and wizard presentation.
 */
final class ContactWebformHooks {

  private const DIRECT_FIRST_PAGE = 'step_project';

  public function __construct(
    private readonly ContactNeedRouter $contactNeedRouter,
  ) {}

  /**
   * Applies hub need radios and wizard CSS on the contact hub webform.
   */
  #[Hook('form_webform_submission_contact_add_form_alter')]
  public function contactFormAlter(array &$form, FormStateInterface $form_state): void {
    $form['#attributes']['class'][] = 'ps-contact-wizard';
    $form['#attributes']['class'][] = 'ps-contact-wizard--contact';
    $form['#attributes']['data-ps-contact-panel'] = 'contact-panel';

    $this->applyHubNeedOptions($form);
  }

  /**
   * Wizard presentation for entrust search direct webform.
   */
  #[Hook('form_webform_submission_entrust_search_add_form_alter')]
  public function entrustSearchFormAlter(array &$form, FormStateInterface $form_state): void {
    $this->applyDirectFormPresentation($form, $form_state, 'entrust-search-panel');
  }

  /**
   * Wizard presentation for get advice direct webform.
   */
  #[Hook('form_webform_submission_get_advice_add_form_alter')]
  public function getAdviceFormAlter(array &$form, FormStateInterface $form_state): void {
    $this->applyDirectFormPresentation($form, $form_state, 'get-advice-panel');
  }

  /**
   * Wizard presentation for entrust property direct webform.
   */
  #[Hook('form_webform_submission_entrust_property_add_form_alter')]
  public function entrustPropertyFormAlter(array &$form, FormStateInterface $form_state): void {
    $this->applyDirectFormPresentation($form, $form_state, 'entrust-property-panel');
  }

  /**
   * Wizard presentation for invest or sell direct webform.
   */
  #[Hook('form_webform_submission_invest_sell_add_form_alter')]
  public function investSellFormAlter(array &$form, FormStateInterface $form_state): void {
    $this->applyDirectFormPresentation($form, $form_state, 'invest-sell-panel');
  }

  /**
   * Wizard presentation for other request direct webform.
   */
  #[Hook('form_webform_submission_other_request_add_form_alter')]
  public function otherRequestFormAlter(array &$form, FormStateInterface $form_state): void {
    $this->applyDirectFormPresentation($form, $form_state, 'other-request-panel');
  }

  /**
   * Hides numeric wizard status for contact-family webforms.
   *
   * @param array<string, mixed> $variables
   *   Theme variables for webform-progress.html.twig.
   */
  #[Hook('preprocess_webform_progress')]
  public function preprocessWebformProgress(array &$variables): void {
    if (!$this->contactNeedRouter->isContactFamilyWebform($variables['webform']->id())) {
      return;
    }

    unset($variables['summary']);
  }

  /**
   * Prepends the hub "Need" step when a direct webform is opened from hub.
   *
   * @param array<string, mixed> $variables
   *   Theme variables for webform-progress-tracker.html.twig.
   */
  #[Hook('preprocess_webform_progress_tracker')]
  public function preprocessWebformProgressTracker(array &$variables): void {
    $webformId = $variables['webform']->id();
    if (!in_array($webformId, $this->contactNeedRouter->getDirectWebformIds(), TRUE)) {
      return;
    }

    if (!$this->isFromHubProgress($variables)) {
      return;
    }

    array_unshift($variables['progress'], [
      'name' => 'step_need',
      'title' => $this->contactNeedRouter->getHubNeedStepTitle(),
      'type' => 'page',
    ]);
    $variables['current_index']++;
  }

  /**
   * Removes hub need options whose direct webform is unavailable.
   *
   * @param array<string, mixed> $form
   *   The webform submission form array.
   */
  private function applyHubNeedOptions(array &$form): void {
    if (!isset($form['elements']['step_need']['need']['#options'])) {
      return;
    }

    $options = $form['elements']['step_need']['need']['#options'];
    foreach ($this->contactNeedRouter->getDirectDefinitions() as $need => $definition) {
      if (!$this->isWebformAvailable($definition['webform'])) {
        unset($options[$need]);
      }
    }

    $form['elements']['step_need']['need']['#options'] = $options;
  }

  /**
   * Adds shared CSS hooks for direct contact webforms.
   */
  private function applyDirectFormPresentation(array &$form, FormStateInterface $form_state, string $panelId): void {
    $form['#attributes']['class'][] = 'ps-contact-wizard';
    $form['#attributes']['class'][] = 'ps-contact-wizard--direct';
    $form['#attributes']['data-ps-contact-panel'] = $panelId;

    if (!$this->isFromHubForm($form, $form_state)) {
      return;
    }

    $form['#attributes']['class'][] = 'ps-contact-wizard--from-hub';
    $form['elements'][ContactNeedRouter::FROM_HUB_FIELD] = [
      '#type' => 'hidden',
      '#value' => '1',
    ];

    $form['#after_build'][] = [$this, 'afterBuildHubBackButton'];
  }

  /**
   * Adds the hub back button once wizard actions are available.
   *
   * @param array<string, mixed> $form
   *   The webform submission form array.
   *
   * @return array<string, mixed>
   *   The altered form array.
   */
  public function afterBuildHubBackButton(array $form, FormStateInterface $form_state): array {
    if (($form_state->get('current_page') ?? self::DIRECT_FIRST_PAGE) !== self::DIRECT_FIRST_PAGE) {
      return $form;
    }

    if (!isset($form['actions']) && !isset($form['ps_webform_sticky_footer']['actions'])) {
      return $form;
    }

    $form_object = $form_state->getFormObject();
    if (!$form_object instanceof WebformSubmissionForm) {
      return $form;
    }

    $label = $form_object->getWebform()->getSetting('wizard_prev_button_label') ?: 'Back';

    $button = [
      '#type' => 'button',
      '#value' => $label,
      '#attributes' => [
        'class' => [
          'webform-button--previous',
          'button',
          'js-form-submit',
          'form-submit',
          'ps-contact-hub-back',
        ],
        'type' => 'button',
      ],
      '#weight' => 0,
    ];

    if (isset($form['actions'])) {
      $form['actions']['ps_hub_back'] = $button;
    }
    else {
      $form['ps_webform_sticky_footer']['actions']['ps_hub_back'] = $button;
    }

    return $form;
  }

  /**
   * Detects whether the current form build continues a hub-initiated flow.
   *
   * @param array<string, mixed> $form
   *   The webform submission form array.
   */
  private function isFromHubForm(array $form, FormStateInterface $form_state): bool {
    if ($this->isFromHubQuery()) {
      return TRUE;
    }

    $value = $form_state->getValue(ContactNeedRouter::FROM_HUB_FIELD);
    if ($value === '1' || $value === 1) {
      return TRUE;
    }

    $input = $form_state->getUserInput();
    if (($input[ContactNeedRouter::FROM_HUB_FIELD] ?? '') === '1') {
      return TRUE;
    }

    if (($form['elements'][ContactNeedRouter::FROM_HUB_FIELD]['#value'] ?? '') === '1') {
      return TRUE;
    }

    return FALSE;
  }

  /**
   * Detects hub context during progress rendering.
   *
   * @param array<string, mixed> $variables
   *   Theme variables for webform progress templates.
   */
  private function isFromHubProgress(array $variables): bool {
    if ($this->isFromHubQuery()) {
      return TRUE;
    }

    $submission = $variables['webform_submission'] ?? NULL;
    if ($submission instanceof WebformSubmissionInterface) {
      $data = $submission->getData();
      if (($data[ContactNeedRouter::FROM_HUB_FIELD] ?? '') === '1') {
        return TRUE;
      }
    }

    return FALSE;
  }

  /**
   * Checks whether the current request carries the hub entry query flag.
   */
  private function isFromHubQuery(): bool {
    $request = \Drupal::requestStack()->getCurrentRequest();
    return $request !== NULL && $request->query->get(ContactNeedRouter::FROM_HUB_QUERY) === '1';
  }

  /**
   * Checks whether a webform entity exists and is open.
   */
  private function isWebformAvailable(string $webformId): bool {
    $status = \Drupal::config('webform.webform.' . $webformId)->get('status');
    return $status === 'open' || $status === TRUE;
  }

}
