<?php

declare(strict_types=1);

namespace Drupal\ps_search\Hook;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\DependencyInjection\DependencySerializationTrait;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\ps_search\Service\SearchAlertCriteriaSerializer;
use Drupal\ps_search\Service\SearchAlertCriteriaSummaryBuilder;
use Drupal\ps_search\Service\SearchAlertRepository;
use Drupal\webform\WebformSubmissionForm;
use Drupal\webform\WebformSubmissionInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Webform integration for search alert creation.
 */
final class SearchAlertWebformHooks {

  use DependencySerializationTrait;
  use StringTranslationTrait;

  public function __construct(
    private readonly SearchAlertCriteriaSerializer $criteriaSerializer,
    private readonly SearchAlertCriteriaSummaryBuilder $summaryBuilder,
    private readonly SearchAlertRepository $alertRepository,
    private readonly RequestStack $requestStack,
    private readonly MessengerInterface $messenger,
    private readonly ConfigFactoryInterface $configFactory,
    private readonly AccountProxyInterface $currentUser,
    private readonly RendererInterface $renderer,
  ) {}

  /**
   * Pre-fills alert webform values from the current search state.
   */
  #[Hook('form_webform_submission_search_alert_add_form_alter')]
  public function formAlter(array &$form, FormStateInterface $form_state): void {
    $form_object = $form_state->getFormObject();
    if (!$form_object instanceof WebformSubmissionForm) {
      return;
    }

    $request = $this->requestStack->getCurrentRequest();
    if ($request === NULL) {
      return;
    }

    $criteria = $this->criteriaSerializer->fromRequest($request);
    $display = $this->summaryBuilder->buildStructured($criteria);

    $form['#attributes']['class'][] = 'ps-search-alert-form';

    if (isset($form['elements']['criteria_display'])) {
      $criteriaDisplay = [
        '#theme' => 'ps_search_alert_criteria_display',
        '#zones' => $display['zones'],
        '#criteria' => $display['criteria'],
      ];
      $form['elements']['criteria_display']['#markup'] = (string) $this->renderer->renderPlain($criteriaDisplay);
    }
    if (isset($form['elements']['prof_email_address']) && $this->currentUser->isAuthenticated()) {
      $form['elements']['prof_email_address']['#default_value'] = $this->currentUser->getEmail();
    }
    if (isset($form['elements']['alert_name'])) {
      $form['elements']['alert_name']['#default_value'] = $display['default_title'];
    }
    if (isset($form['elements']['frequence'])) {
      $form['elements']['frequence']['#default_value'] = $this->configFactory
        ->get('ps_search.alert_settings')
        ->get('default_frequence') ?? 'weekly';
      $form['elements']['frequence']['#attributes']['class'][] = 'ps-search-alert-form__frequency';
    }
    if (isset($form['elements']['optout_group'])) {
      $form['elements']['optout_group']['#attributes']['class'][] = 'ps-search-alert-form__optout';
    }
    if (isset($form['elements']['criteria_json'])) {
      $form['elements']['criteria_json']['#default_value'] = $this->criteriaSerializer->toJson($criteria);
    }
    if (isset($form['elements']['search_url'])) {
      $searchUrl = trim((string) $request->query->get('search_url', ''));
      $form['elements']['search_url']['#default_value'] = $searchUrl !== ''
        ? $searchUrl
        : (string) ($criteria['search_url'] ?? $request->getUri());
    }
    if (isset($form['elements']['search_path'])) {
      $searchPath = trim((string) $request->query->get('search_path', ''));
      $form['elements']['search_path']['#default_value'] = $searchPath !== ''
        ? $searchPath
        : (string) ($criteria['search_path'] ?? $request->getPathInfo());
    }

    if (isset($form['actions']['submit'])) {
      $form['actions']['submit']['#value'] = $this->t('Continue');
    }

    $form['#validate'][] = [$this, 'validateDuplicateAlert'];
  }

  /**
   * Validates duplicate active alerts for the same email and criteria.
   */
  public function validateDuplicateAlert(array &$form, FormStateInterface $form_state): void {
    $email = strtolower(trim((string) $form_state->getValue('prof_email_address')));
    $criteriaJson = (string) $form_state->getValue('criteria_json');
    $criteria = json_decode($criteriaJson, TRUE);
    if (!is_array($criteria)) {
      return;
    }

    $hash = $this->criteriaSerializer->hash($this->criteriaSerializer->normalizeCriteria($criteria));
    if ($email !== '' && $this->alertRepository->findDuplicate($email, $hash) !== NULL) {
      $form_state->setErrorByName('prof_email_address', $this->t('An active alert already exists for this email and search criteria.'));
    }
  }

  /**
   * Creates a search alert entity when the webform is submitted.
   */
  #[Hook('webform_submission_insert')]
  public function onSubmissionInsert(WebformSubmissionInterface $webform_submission): void {
    if ($webform_submission->getWebform()->id() !== 'search_alert') {
      return;
    }

    $entity = $this->alertRepository->createFromSubmission($webform_submission->getData());
    if ($entity === NULL) {
      $this->messenger->addWarning($this->t('This alert could not be saved. It may already exist for this email and search.'));
    }
  }

}
