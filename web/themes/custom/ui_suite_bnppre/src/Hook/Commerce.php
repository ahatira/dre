<?php

declare(strict_types=1);

namespace Drupal\ui_suite_bnppre\Hook;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Url;

/**
 * Hooks for Commerce support.
 */
class Commerce {

  /**
   * Implements hook_form_FORM_ID_alter().
   *
   * Use Bootstrap grid and some classes on login step.
   */
  #[Hook('form_commerce_checkout_flow_multistep_default_alter')]
  public function checkoutFlowMultistepDefaultAlter(array &$form, FormStateInterface $formState, string $form_id): void {
    if ($form['#step_id'] != 'login') {
      return;
    }

    if (isset($form['login']['returning_customer']['forgot_password']['#url'])
      && $form['login']['returning_customer']['forgot_password']['#url'] instanceof Url
    ) {
      $form['login']['returning_customer']['forgot_password']['#url']->mergeOptions([
        'attributes' => [
          'class' => [
            'btn',
            'btn-link',
          ],
        ],
      ]);
    }

    if (isset($form['login'])) {
      $form['login']['#attributes']['class'][] = 'row';
    }

    $wrapper = [
      'container' => [
        '#attributes' => [
          'class' => [
            'col-md-6',
          ],
        ],
      ],
    ];
    if (isset($form['login']['returning_customer'])) {
      if (isset($form['login']['returning_customer']['#theme_wrappers'])) {
        $form['login']['returning_customer']['#theme_wrappers'] = NestedArray::mergeDeepArray([
          $form['login']['returning_customer']['#theme_wrappers'],
          $wrapper,
        ]);
      }
      else {
        $form['login']['returning_customer']['#theme_wrappers'] = NestedArray::mergeDeepArray([
          ['fieldset'],
          $wrapper,
        ]);
      }
    }
    if (isset($form['login']['guest'])) {
      if (isset($form['login']['guest']['#theme_wrappers'])) {
        $form['login']['guest']['#theme_wrappers'] = NestedArray::mergeDeepArray([
          $form['login']['guest']['#theme_wrappers'],
          $wrapper,
        ]);
      }
      else {
        $form['login']['guest']['#theme_wrappers'] = NestedArray::mergeDeepArray([
          ['fieldset'],
          $wrapper,
        ]);
      }
    }
    if (isset($form['login']['register'])) {
      if (isset($form['login']['register']['#theme_wrappers'])) {
        $form['login']['register']['#theme_wrappers'] = NestedArray::mergeDeepArray([
          $form['login']['register']['#theme_wrappers'],
          $wrapper,
        ]);
      }
      else {
        $form['login']['register']['#theme_wrappers'] = NestedArray::mergeDeepArray([
          ['fieldset'],
          $wrapper,
        ]);
      }

      // If there is the anonymous checkout, stack register form below.
      if (isset($form['login']['guest']['#access']) && $form['login']['guest']['#access']) {
        $form['login']['register']['#theme_wrappers']['container']['#attributes']['class'][] = 'offset-md-6';
      }
    }
  }

}
