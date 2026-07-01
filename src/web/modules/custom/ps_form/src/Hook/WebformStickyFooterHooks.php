<?php

declare(strict_types=1);

namespace Drupal\ps_form\Hook;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\ps_form\Service\FormUrgencyContactBuilder;
use Drupal\webform\WebformSubmissionForm;

/**
 * Sticky footer + urgency block for webform submissions (config-driven).
 */
final class WebformStickyFooterHooks {

  private const STICKY_FOOTER_KEY = 'ps_webform_sticky_footer';

  private const ACTIONS_WEIGHT = 1000;

  public function __construct(
    private readonly FormUrgencyContactBuilder $urgencyContactBuilder,
  ) {}

  /**
   * Adds sticky submit + urgency phone footer from ps_form.settings.
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
    $form['#attached']['library'][] = 'ps_theme/form';

    if (!isset($form['actions'])) {
      return;
    }

    $stickyFooter = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['ps-webform-sticky-footer'],
      ],
      '#weight' => self::ACTIONS_WEIGHT,
    ];

    $stickyFooter['actions'] = $form['actions'];
    $stickyFooter['actions']['#weight'] = 0;
    unset($form['actions']);

    $build = $this->urgencyContactBuilder->buildRenderArray();
    if ($build !== []) {
      $stickyFooter['urgency'] = [
        '#type' => 'container',
        '#attributes' => [
          'class' => ['ps-webform-urgency-help-wrapper'],
        ],
        'content' => $build,
        '#weight' => 1,
      ];
    }

    $form[self::STICKY_FOOTER_KEY] = $stickyFooter;
    $form['#attributes']['class'][] = 'ps-webform--has-sticky-footer';
    $form['#attached']['library'][] = 'ps_theme/webform_sticky_footer';
  }

  /**
   * Hides legacy empty help_footer YAML elements superseded by urgency config.
   *
   * @param array<string, mixed> $form
   *   The webform submission form array.
   */
  private function hideLegacyHelpFooter(array &$form): void {
    if (isset($form['elements']['help_footer'])) {
      $form['elements']['help_footer']['#access'] = FALSE;
    }
  }

}
