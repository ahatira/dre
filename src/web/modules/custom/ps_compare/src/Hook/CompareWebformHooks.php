<?php

declare(strict_types=1);

namespace Drupal\ps_compare\Hook;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\ps_compare\Service\ComparePathResolver;
use Drupal\webform\WebformSubmissionForm;
use Drupal\webform\WebformSubmissionInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Webform integration for comparison share by email.
 */
final class CompareWebformHooks {

  use StringTranslationTrait;

  public function __construct(
    private readonly ComparePathResolver $comparePathResolver,
    private readonly RequestStack $requestStack,
    private readonly AccountProxyInterface $currentUser,
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

    if (isset($form['actions']['submit'])) {
      $form['actions']['submit']['#value'] = $this->t('Receive my comparison');
      if (isset($form['actions']['submit']['#ajax'])) {
        $form['actions']['submit']['#ajax']['disable-refocus'] = TRUE;
      }
    }

    $form['#attributes']['class'][] = 'js-ps-compare-share-webform';

    $form['#validate'][] = [self::class, 'validateCompareList'];
  }

  /**
   * Ensures enough properties remain in the comparison list.
   *
   * Static callback avoids serializing hook services during Ajax rebuilds.
   */
  public static function validateCompareList(array &$form, FormStateInterface $form_state): void {
    /** @var \Drupal\ps_compare\Service\CompareManagerInterface $compareManager */
    $compareManager = \Drupal::service('ps_compare.manager');
    if (!$compareManager->canOpenComparisonPage()) {
      $form_state->setErrorByName(
        'prof_email_address',
        \Drupal::translation()->translate('Select at least @min properties to share a comparison.', [
          '@min' => $compareManager->getMinItems(),
        ]),
      );
    }
  }

  /**
   * Simplifies compare share inline confirmation in the offcanvas.
   */
  #[Hook('preprocess_webform_confirmation')]
  public function preprocessWebformConfirmation(array &$variables): void {
    $webform = $variables['webform'] ?? NULL;
    if ($webform === NULL || $webform->id() !== 'compare_share') {
      return;
    }

    $variables['attributes']['class'][] = 'ps-compare-share-confirmation';
    $variables['back'] = FALSE;
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

    /** @var \Drupal\ps_compare\Service\CompareEmailSender $sender */
    $sender = \Drupal::service('ps_compare.email_sender');
    if (!$sender->sendFromForm($email)) {
      \Drupal::logger('ps_compare')->warning(
        'Failed to send comparison email for webform submission @id.',
        ['@id' => $webform_submission->id()],
      );
    }
  }

}
