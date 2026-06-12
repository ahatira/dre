<?php

declare(strict_types=1);

namespace Drupal\ps_compare\Hook;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\ps_compare\Service\CompareEmailSender;
use Drupal\ps_compare\Service\CompareManagerInterface;
use Drupal\ps_compare\Service\ComparePathResolver;
use Drupal\ps_core\Service\SiteUrgencyContactBuilder;
use Drupal\webform\WebformSubmissionForm;
use Drupal\webform\WebformSubmissionInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Webform integration for comparison share by email.
 */
final class CompareWebformHooks {

  use StringTranslationTrait;

  public function __construct(
    private readonly CompareManagerInterface $compareManager,
    private readonly CompareEmailSender $emailSender,
    private readonly ComparePathResolver $comparePathResolver,
    private readonly RequestStack $requestStack,
    private readonly MessengerInterface $messenger,
    private readonly AccountProxyInterface $currentUser,
    private readonly SiteUrgencyContactBuilder $urgencyContactBuilder,
  ) {}

  /**
   * Pre-fills compare share webform values.
   */
  #[Hook('form_webform_submission_compare_share_add_form_alter')]
  public function formAlter(array &$form, FormStateInterface $form_state): void {
    $form_object = $form_state->getFormObject();
    if (!$form_object instanceof WebformSubmissionForm) {
      return;
    }

    $request = $this->requestStack->getCurrentRequest();
    $form['#attributes']['class'][] = 'ps-compare-share-form';

    if (isset($form['elements']['prof_email_address']) && $this->currentUser->isAuthenticated()) {
      $form['elements']['prof_email_address']['#default_value'] = $this->currentUser->getEmail();
    }

    if (isset($form['elements']['optout_group'])) {
      $form['elements']['optout_group']['#attributes']['class'][] = 'ps-compare-share-form__optout';
    }

    if (isset($form['elements']['compare_url'])) {
      $compareUrl = trim((string) $request?->query->get('compare_url', ''));
      $form['elements']['compare_url']['#default_value'] = $compareUrl !== ''
        ? $compareUrl
        : $this->comparePathResolver->getPublicPath();
    }

    if (isset($form['elements']['help_footer'])) {
      $form['elements']['help_footer']['#markup'] = $this->urgencyContactBuilder->buildMarkup();
      if ($form['elements']['help_footer']['#markup'] === '') {
        $form['elements']['help_footer']['#access'] = FALSE;
      }
    }

    if (isset($form['actions']['submit'])) {
      $form['actions']['submit']['#value'] = $this->t('Receive my comparison');
    }

    $form['#validate'][] = [$this, 'validateCompareList'];
  }

  /**
   * Ensures enough properties remain in the comparison list.
   */
  public function validateCompareList(array &$form, FormStateInterface $form_state): void {
    if (!$this->compareManager->canOpenComparisonPage()) {
      $form_state->setErrorByName(
        'prof_email_address',
        $this->t('Select at least @min properties to share a comparison.', [
          '@min' => $this->compareManager->getMinItems(),
        ]),
      );
    }
  }

  /**
   * Sends the comparison email when the webform is submitted.
   */
  #[Hook('webform_submission_insert')]
  public function onSubmissionInsert(WebformSubmissionInterface $webform_submission): void {
    if ($webform_submission->getWebform()->id() !== 'compare_share') {
      return;
    }

    $data = $webform_submission->getData();
    $email = trim((string) ($data['prof_email_address'] ?? ''));
    if ($email === '') {
      return;
    }

    if (!$this->emailSender->sendFromForm($email)) {
      $this->messenger->addWarning($this->t('Unable to send the comparison. Please try again.'));
    }
  }

}
