<?php

declare(strict_types=1);

namespace Drupal\ui_suite_bnp_form_test\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Demonstration form.
 */
class ChecksRadios extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ui_suite_bnp_form_test_checks_radios';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form['description'] = [
      '#type' => 'item',
      '#markup' => $this->t('Create consistent cross-browser and cross-device checkboxes and radios with our completely rewritten checks component.'),
    ];

    $form['documentation_link'] = [
      '#type' => 'component',
      '#component' => 'ui_suite_bnp:button',
      '#props' => [
        'variant' => 'primary__sm',
        'url' => 'https://getbootstrap.com/docs/5.3/forms/checks-radios/',
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

    $form['checkboxes'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Checks'),
      '#options' => [
        '1' => $this->t('Default checkbox'),
        '2' => $this->t('Checked checkbox'),
      ],
      '#default_value' => [
        '2',
      ],
    ];

    $form['indeterminate'] = [
      '#type' => 'item',
      '#markup' => '<h2>' . $this->t('Indeterminate (not supported out-of-the-box)') . '</h2>',
    ];

    $form['disabled'] = [
      '#type' => 'item',
      '#markup' => '<h2>' . $this->t('Disabled') . '</h2>',
    ];

    $form['disabled_checkboxes'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Disabled checkboxes'),
      '#options' => [
        '1' => $this->t('Disabled checkbox'),
        '2' => $this->t('Disabled checked checkbox'),
      ],
      '#default_value' => [
        '2',
      ],
      '#disabled' => TRUE,
    ];

    $form['radios'] = [
      '#type' => 'item',
      '#markup' => '<h2>' . $this->t('Radios') . '</h2>',
    ];

    $form['radios_example'] = [
      '#type' => 'radios',
      '#title' => $this->t('Radios'),
      '#options' => [
        '1' => $this->t('Default radio'),
        '2' => $this->t('Default checked radio'),
      ],
      '#default_value' => [
        '2',
      ],
    ];

    $form['disabled_radios'] = [
      '#type' => 'radios',
      '#title' => $this->t('Disabled radios'),
      '#options' => [
        '1' => $this->t('Disabled radio'),
        '2' => $this->t('Disabled checked radio'),
      ],
      '#default_value' => [
        '2',
      ],
      '#disabled' => TRUE,
    ];

    $form['switches'] = [
      '#type' => 'item',
      '#markup' => '<h2>' . $this->t('Switches') . '</h2>',
    ];

    $form['checkbox_switch'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Switch'),
      '#is_switch' => TRUE,
    ];

    $form['checkboxes_individual_switch'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Individual switch'),
      '#options' => [
        'option_1' => $this->t('Option 1 is a checkbox'),
        'option_2' => $this->t('Option 2 is a switch'),
      ],
      'option_2' => [
        '#is_switch' => TRUE,
      ],
    ];

    $form['checkboxes_switch'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Switches'),
      '#options' => [
        '1' => $this->t('Default switch checkbox input'),
        '2' => $this->t('Checked switch checkbox input'),
        '3' => $this->t('Disabled switch checkbox input'),
        '4' => $this->t('Disabled checked switch checkbox input'),
      ],
      '#default_value' => [
        '2',
        '4',
      ],
      '#is_switch' => TRUE,
      '3' => [
        '#disabled' => TRUE,
      ],
      '4' => [
        '#disabled' => TRUE,
      ],
    ];

    $form['native_switch'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Native switch haptics'),
      '#attributes' => [
        'switch' => TRUE,
      ],
      '#is_switch' => TRUE,
    ];

    $form['inline'] = [
      '#type' => 'item',
      '#markup' => '<h2>' . $this->t('Inline') . '</h2>',
    ];

    $form['radios_inline'] = [
      '#type' => 'radios',
      '#title' => $this->t('Inline radios'),
      '#options' => [
        '1' => $this->t('1'),
        '2' => $this->t('2'),
        '3' => $this->t('3 (disabled)'),
      ],
      '3' => [
        '#disabled' => TRUE,
      ],
      '#is_inline' => TRUE,
    ];

    $form['checkboxes_inline'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Inline checkboxes'),
      '#options' => [
        '1' => $this->t('1'),
        '2' => $this->t('2'),
        '3' => $this->t('3 (disabled)'),
      ],
      '3' => [
        '#disabled' => TRUE,
      ],
      '#is_inline' => TRUE,
    ];

    $form['reverse'] = [
      '#type' => 'item',
      '#markup' => '<h2>' . $this->t('Reverse') . '</h2>',
    ];

    $form['radios_reverse'] = [
      '#type' => 'radios',
      '#title' => $this->t('Reverse radios'),
      '#options' => [
        '1' => $this->t('Reverse radio'),
        '2' => $this->t('Disabled reverse radio'),
      ],
      '2' => [
        '#disabled' => TRUE,
      ],
      '#is_reverse' => TRUE,
    ];

    $form['checkboxes_reverse'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Reverse checkboxes'),
      '#options' => [
        '1' => $this->t('Reverse checkbox'),
        '2' => $this->t('Disabled reverse checkbox'),
        '3' => $this->t('Reverse switch checkbox input'),
      ],
      '2' => [
        '#disabled' => TRUE,
      ],
      '3' => [
        '#is_switch' => TRUE,
      ],
      '#is_reverse' => TRUE,
    ];

    $form['without_labels'] = [
      '#type' => 'item',
      '#markup' => '<h2>' . $this->t('Without labels: no example') . '</h2>',
    ];

    $form['toggle_buttons'] = [
      '#type' => 'item',
      '#markup' => '<h2>' . $this->t('Toggle buttons (not supported out-of-the-box)') . '</h2>',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {}

}
