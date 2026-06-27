<?php

declare(strict_types=1);

namespace Drupal\ps_form\Hook;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\ps_core\Service\SiteUrgencyContactBuilder;
use Drupal\webform\WebformSubmissionForm;

/**
 * Injects the site-wide urgency contact block into every webform.
 */
final class WebformUrgencyContactHooks {

  private const HELP_FOOTER_KEY = 'help_footer';

  private const STICKY_FOOTER_KEY = 'ps_webform_sticky_footer';

  private const ACTIONS_WEIGHT = 1000;

  public function __construct(
    private readonly SiteUrgencyContactBuilder $urgencyContactBuilder,
  ) {}

  /**
   * Adds sticky submit + urgency phone footer (bnppre.fr sidenav-footer).
   */
  #[Hook('form_alter')]
  public function formAlter(array &$form, FormStateInterface $form_state, string $form_id): void {
    if (!str_starts_with($form_id, 'webform_submission_') || !str_ends_with($form_id, '_form')) {
      return;
    }

    $form_object = $form_state->getFormObject();
    if (!$form_object instanceof WebformSubmissionForm) {
      return;
    }

    $this->hideLegacyHelpFooter($form);

    // PS form styling (progress breadcrumb, layout, sticky footer rules) on every webform.
    $form['#attached']['library'][] = 'ps_theme/form';

    if (!$this->urgencyContactBuilder->isEnabled()) {
      return;
    }

    $build = $this->urgencyContactBuilder->buildRenderArray();
    if ($build === []) {
      return;
    }

    $stickyFooter = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['ps-webform-sticky-footer'],
      ],
      '#weight' => self::ACTIONS_WEIGHT,
    ];

    if (isset($form['actions'])) {
      $stickyFooter['actions'] = $form['actions'];
      $stickyFooter['actions']['#weight'] = 0;
      unset($form['actions']);
    }

    $stickyFooter['urgency'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['ps-webform-urgency-help-wrapper'],
      ],
      'content' => $build,
      '#weight' => 1,
    ];

    $form[self::STICKY_FOOTER_KEY] = $stickyFooter;
    $form['#attributes']['class'][] = 'ps-webform--has-sticky-footer';
    $form['#attached']['library'][] = 'ps_theme/webform_sticky_footer';
  }

  /**
   * Hides legacy YAML help_footer elements.
   *
   * Replaced by post-actions injection.
   *
   * @param array<string, mixed> $form
   *   The webform submission form array.
   */
  private function hideLegacyHelpFooter(array &$form): void {
    if (isset($form['elements'][self::HELP_FOOTER_KEY])) {
      $form['elements'][self::HELP_FOOTER_KEY]['#access'] = FALSE;
    }
  }

}
