<?php

declare(strict_types=1);

namespace Drupal\ps_form\Hook;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\ps_form\Service\ContactNeedRouter;
use Drupal\ps_form\Service\ContactProjectFieldPresenter;
use Drupal\ps_form\Service\ContactWizardPresentationService;
use Drupal\webform\WebformSubmissionForm;
use Drupal\webform\WebformSubmissionInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Contact family webforms: hub routing and wizard presentation.
 */
final class ContactWebformHooks {

  use StringTranslationTrait;

  private const DIRECT_FIRST_PAGE = 'step_project';

  public function __construct(
    private readonly ContactNeedRouter $contactNeedRouter,
    private readonly ContactProjectFieldPresenter $contactProjectFieldPresenter,
    private readonly ContactWizardPresentationService $contactWizardPresentation,
    private readonly RequestStack $requestStack,
    private readonly ConfigFactoryInterface $configFactory,
    private readonly LanguageManagerInterface $languageManager,
  ) {}

  /**
   * Applies hub and direct contact webform presentation.
   */
  #[Hook('form_alter')]
  public function formAlter(array &$form, FormStateInterface $form_state, string $form_id): void {
    if (!str_starts_with($form_id, 'webform_submission_') || !str_ends_with($form_id, '_add_form')) {
      return;
    }

    $form_object = $form_state->getFormObject();
    if (!$form_object instanceof WebformSubmissionForm) {
      return;
    }

    $webformId = $form_object->getWebform()->id();

    if ($webformId === ContactNeedRouter::HUB_WEBFORM_ID) {
      $this->applyContactHubForm($form);
      $form['#after_build'][] = [$this, 'afterBuildContactHubForm'];
      return;
    }

    if (!$this->contactNeedRouter->isRoutableWebform($webformId)) {
      return;
    }

    $this->applyDirectFormPresentation(
      $form,
      $form_state,
      $this->contactNeedRouter->resolvePanelId($webformId),
    );

    $this->contactProjectFieldPresenter->applyToForm($form, $webformId);
    $this->contactWizardPresentation->applyToForm($form);
    $this->attachContactWizardAssets($form);
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
   * Prepends the hub target step when an enabled webform is opened from hub.
   *
   * @param array<string, mixed> $variables
   *   Theme variables for webform-progress-tracker.html.twig.
   */
  #[Hook('preprocess_webform_progress_tracker')]
  public function preprocessWebformProgressTracker(array &$variables): void {
    $webformId = $variables['webform']->id();
    if (!$this->contactNeedRouter->isContactFamilyWebform($webformId)) {
      return;
    }

    if ($webformId === ContactNeedRouter::HUB_WEBFORM_ID) {
      $this->appendHubTargetProgressSteps($variables);
    }
    elseif ($this->contactNeedRouter->isHubEnabledWebform($webformId) && $this->isFromHubProgress($variables)) {
      array_unshift($variables['progress'], [
        'name' => 'step_need',
        'title' => $this->contactNeedRouter->getHubNeedStepTitle(),
        'type' => 'page',
      ]);
      $variables['current_index']++;
    }

    $this->normalizeContactProgressSteps($variables);
  }

  /**
   * Appends target webform wizard steps to the hub progress tracker.
   *
   * @param array<string, mixed> $variables
   *   Theme variables for webform-progress-tracker.html.twig.
   */
  private function appendHubTargetProgressSteps(array &$variables): void {
    $targetWebformId = $this->resolveHubTargetWebformId();
    foreach ($this->contactNeedRouter->getWizardProgressPages($targetWebformId) as $step) {
      $variables['progress'][] = $step;
    }
  }

  /**
   * Normalizes contact-family progress labels (mockup: Details).
   *
   * @param array<string, mixed> $variables
   *   Theme variables for webform-progress-tracker.html.twig.
   */
  private function normalizeContactProgressSteps(array &$variables): void {
    if (!isset($variables['progress']) || !is_array($variables['progress'])) {
      return;
    }

    $detailsTitle = (string) $this->t('Details', [], ['context' => 'Contact wizard progress step']);
    foreach ($variables['progress'] as &$step) {
      if (!is_array($step) || ($step['name'] ?? '') !== 'step_contact') {
        continue;
      }
      $step['title'] = $detailsTitle;
    }
  }

  /**
   * Resolves the hub target webform for progress preview.
   */
  private function resolveHubTargetWebformId(): string {
    $request = $this->requestStack->getCurrentRequest();
    if ($request !== NULL) {
      $selected = $request->request->get('target_webform') ?? $request->query->get('target_webform');
      if (is_string($selected) && $selected !== '' && $this->contactNeedRouter->isHubEnabledWebform($selected)) {
        return $selected;
      }
    }

    return $this->contactNeedRouter->getDefaultHubWebformId() ?? 'find_property';
  }

  /**
   * Applies hub wizard CSS and dynamic target webform radios.
   *
   * @param array<string, mixed> $form
   *   The webform submission form array.
   */
  private function applyContactHubForm(array &$form): void {
    $form['#attributes']['class'][] = 'ps-contact-wizard';
    $form['#attributes']['class'][] = 'ps-contact-wizard--contact';
    $form['#attributes']['data-ps-contact-panel'] = 'contact-panel';
    $form['#cache']['tags'][] = 'config:ps_form.settings';

    if (isset($form['elements']['step_need']['need_title'])) {
      $form['elements']['step_need']['need_title']['#markup'] = '<p class="ps-form-section-intro h4">' . $this->t('To get started, what is your need?') . '</p>';
    }

    if (isset($form['elements']['step_need'])) {
      $form['elements']['step_need']['#title'] = $this->t('Need');
      $form['elements']['step_need']['#attributes']['class'][] = 'ps-form-radios--stacked';
    }

    $this->applyHubTargetOptions($form);
  }

  /**
   * Restores wizard_next on the hub need step.
   *
   * The contact hub was slimmed to step_need only; Webform then renders
   * #edit-submit (Soumettre + icon) instead of #edit-wizard-next (Continuer).
   * Hub continuation is handled client-side (contact-hub-need.js).
   *
   * @param array<string, mixed> $form
   *   The webform submission form array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   *
   * @return array<string, mixed>
   *   The form array.
   */
  public function afterBuildContactHubForm(array $form, FormStateInterface $form_state): array {
    $form_object = $form_state->getFormObject();
    if (!$form_object instanceof WebformSubmissionForm) {
      return $form;
    }

    $webform = $form_object->getWebform();
    $label = $webform->getSetting('wizard_next_button_label')
      ?: (string) $this->t('Continue');

    if (isset($form['actions'])) {
      $this->applyHubWizardNextButton($form['actions'], $label);
    }
    if (isset($form['ps_webform_sticky_footer']['actions'])) {
      $this->applyHubWizardNextButton($form['ps_webform_sticky_footer']['actions'], $label);
    }

    return $form;
  }

  /**
   * Hides submit and injects the native wizard_next button on the hub.
   *
   * @param array<string, mixed> $actions
   *   The form actions render array.
   * @param string $label
   *   The Continue button label.
   */
  private function applyHubWizardNextButton(array &$actions, string $label): void {
    if (isset($actions['submit'])) {
      $actions['submit']['#access'] = FALSE;
    }

    $actions['wizard_next'] = [
      '#type' => 'button',
      '#value' => $label,
      '#weight' => 10,
      '#attributes' => [
        'class' => ['webform-button--next', 'me-2'],
        'data-drupal-selector' => 'edit-wizard-next',
        'id' => 'edit-wizard-next',
      ],
    ];
  }

  /**
   * Builds hub target radios from enabled direct webforms (webform id keys).
   *
   * @param array<string, mixed> $form
   *   The webform submission form array.
   */
  private function applyHubTargetOptions(array &$form): void {
    if (!isset($form['elements']['step_need']['target_webform'])) {
      return;
    }

    $options = [];
    foreach ($this->contactNeedRouter->getEnabledHubWebformIds() as $webformId) {
      if (!$this->isWebformAvailable($webformId)) {
        continue;
      }

      if (!$this->contactNeedRouter->isRoutableWebform($webformId)) {
        continue;
      }

      $options[$webformId] = $this->contactNeedRouter->getPageTitle($webformId);
    }

    $form['elements']['step_need']['target_webform']['#options'] = $options;

    $default = $this->contactNeedRouter->getDefaultHubWebformId();
    if ($default !== NULL && isset($options[$default])) {
      $form['elements']['step_need']['target_webform']['#default_value'] = $default;
    }
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
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
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
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
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

    $request = $this->requestStack->getCurrentRequest();
    if ($request !== NULL) {
      $fromHub = $request->request->get(ContactNeedRouter::FROM_HUB_FIELD)
        ?? $request->query->get(ContactNeedRouter::FROM_HUB_FIELD);
      if ($fromHub === '1' || $fromHub === 1) {
        return TRUE;
      }
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
    $request = $this->requestStack->getCurrentRequest();
    return $request !== NULL && $request->query->get(ContactNeedRouter::FROM_HUB_QUERY) === '1';
  }

  /**
   * Attaches contact wizard JS settings (location suggest API).
   *
   * @param array<string, mixed> $form
   *   The webform submission form array.
   */
  private function attachContactWizardAssets(array &$form): void {
    if ($this->contactWizardPresentation->hasLocationField($form)) {
      $form['#attached']['library'][] = 'ps_form/contact_wizard';
    }

    $form['#attached']['drupalSettings']['psForm'] = [
      'locationSuggestUrl' => '/api/ps/location-suggest',
      'locationDataUrl' => '/api/ps/location-data',
      'contentLangcode' => $this->languageManager->getCurrentLanguage()->getId(),
    ];
  }

  /**
   * Checks whether a webform entity exists and is open.
   */
  private function isWebformAvailable(string $webformId): bool {
    $status = $this->configFactory->get('webform.webform.' . $webformId)->get('status');
    return $status === 'open' || $status === TRUE;
  }

}
