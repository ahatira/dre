<?php

declare(strict_types=1);

namespace Drupal\ps_compare\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Ajax\InvokeCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Url;
use Drupal\ps_compare\Service\CompareEmailSender;
use Drupal\ps_compare\Service\CompareManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Share comparison by email — modal form (Form API, no webform).
 */
final class CompareShareForm extends FormBase {

  public function __construct(
    private readonly CompareEmailSender $emailSender,
    private readonly CompareManagerInterface $compareManager,
    private readonly RendererInterface $renderer,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    return new static(
      $container->get('ps_compare.email_sender'),
      $container->get('ps_compare.manager'),
      $container->get('renderer'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'ps_compare_share_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form['#attributes']['class'][] = 'ps-compare-share-form';
    $form['#action'] = Url::fromRoute('ps_compare.share_modal')->toString();

    if (!$this->compareManager->canOpenComparisonPage()) {
      $form['notice'] = [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $this->t('Select at least @min properties to share a comparison.', [
          '@min' => $this->compareManager->getMinItems(),
        ]),
        '#attributes' => ['class' => ['alert', 'alert-warning', 'mb-0']],
      ];
      return $form;
    }

    $form['email'] = [
      '#type' => 'email',
      '#title' => $this->t('Recipient email'),
      '#required' => TRUE,
      '#attributes' => [
        'class' => ['form-control'],
        'placeholder' => 'name@company.com',
      ],
    ];

    $form['message'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Optional message'),
      '#rows' => 3,
      '#attributes' => [
        'class' => ['form-control'],
        'placeholder' => (string) $this->t('Add a short note for the recipient (optional).'),
      ],
    ];

    $form['legal'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('I have read and accept the terms of the Personal Data Protection Policy.'),
      '#required' => TRUE,
    ];

    $form['feedback'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['ps-compare-share-form__feedback'],
        'data-ps-compare-share-feedback' => '',
        'aria-live' => 'polite',
      ],
    ];

    $form['actions'] = [
      '#type' => 'actions',
      'submit' => [
        '#type' => 'submit',
        '#value' => $this->t('Send comparison'),
        '#button_type' => 'primary',
        '#ajax' => [
          'callback' => '::ajaxSubmit',
          'wrapper' => 'ps-compare-share-form-wrapper',
          'effect' => 'none',
          'progress' => [
            'type' => 'throbber',
            'message' => NULL,
          ],
        ],
      ],
    ];

    $form['#prefix'] = '<div id="ps-compare-share-form-wrapper">';
    $form['#suffix'] = '</div>';
    $form['#attached']['library'][] = 'core/drupal.ajax';
    $form['#attached']['library'][] = 'ps_theme/form';

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state): void {
    if (!$this->compareManager->canOpenComparisonPage()) {
      $form_state->setErrorByName('email', $this->t('Not enough properties in the comparison list.'));
      return;
    }

    $email = trim((string) $form_state->getValue('email'));
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $form_state->setErrorByName('email', $this->t('Please enter a valid email address.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $email = trim((string) $form_state->getValue('email'));
    $message = trim((string) $form_state->getValue('message'));

    if (!$this->emailSender->sendFromForm($email, $message)) {
      $form_state->setErrorByName('email', $this->t('Unable to send the comparison. Please try again.'));
      return;
    }

    $form_state->set('share_success', TRUE);
    $form_state->set('share_success_message', $this->t('Comparison sent to @email.', ['@email' => $email]));
  }

  /**
   * AJAX callback after submit.
   */
  public function ajaxSubmit(array &$form, FormStateInterface $form_state): AjaxResponse {
    $response = new AjaxResponse();

    if ($form_state->get('share_success')) {
      $message = $form_state->get('share_success_message');
      $text = $message instanceof TranslatableMarkup
        ? (string) $message
        : (string) $message;

      $success = [
        '#theme' => 'ps_compare_share_success',
        '#message' => $text,
      ];

      $response->addCommand(new HtmlCommand(
        '[data-ps-compare-share-modal-body]',
        (string) $this->renderer->renderRoot($success),
      ));
      $response->addCommand(new InvokeCommand(
        '[data-ps-compare-share-modal]',
        'addClass',
        ['ps-compare-share-modal--sent'],
      ));

      return $response;
    }

    $response->addCommand(new InvokeCommand(
      '[data-ps-compare-share-modal]',
      'removeClass',
      ['ps-compare-share-modal--sent'],
    ));
    $response->addCommand(new HtmlCommand(
      '#ps-compare-share-form-wrapper',
      (string) $this->renderer->renderRoot($form),
    ));

    return $response;
  }

}
