<?php

declare(strict_types=1);

namespace Drupal\ui_suite_bnp_form_test\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Demonstration form.
 */
class Range extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ui_suite_bnp_form_test_range';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form['description'] = [
      '#type' => 'item',
      '#markup' => $this->t('Use our custom range inputs for consistent cross-browser styling and built-in customization.'),
    ];

    $form['documentation_link'] = [
      '#type' => 'component',
      '#component' => 'ui_suite_bnp:button',
      '#props' => [
        'variant' => 'primary__sm',
        'url' => 'https://getbootstrap.com/docs/5.3/forms/range/',
        'attributes' => [
          'class' => [
            'mb-3',
          ],
        ],
      ],
      'label' => [
        '#markup' => $this->t('External documentation'),
      ],
    ];

    $form['range'] = [
      '#type' => 'range',
      '#title' => $this->t('Example range'),
    ];

    $form['disabled'] = [
      '#type' => 'item',
      '#markup' => '<h2>' . $this->t('Disabled') . '</h2>',
    ];

    $form['disabled_example'] = [
      '#type' => 'range',
      '#title' => $this->t('Disabled range'),
      '#disabled' => TRUE,
    ];

    $form['min_max'] = [
      '#type' => 'item',
      '#markup' => '<h2>' . $this->t('Min and max') . '</h2>',
    ];

    $form['min_max_example'] = [
      '#type' => 'range',
      '#title' => $this->t('Example range'),
      '#min' => 0,
      '#max' => 5,
    ];

    $form['steps'] = [
      '#type' => 'item',
      '#markup' => '<h2>' . $this->t('Steps') . '</h2>',
    ];

    $form['steps_example'] = [
      '#type' => 'range',
      '#title' => $this->t('Example range'),
      '#min' => 0,
      '#max' => 5,
      '#step' => 0.5,
    ];

    $form['output'] = [
      '#type' => 'item',
      '#markup' => '<h2>' . $this->t('Output value (not supported out-of-the-box)') . '</h2>',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {}

}
