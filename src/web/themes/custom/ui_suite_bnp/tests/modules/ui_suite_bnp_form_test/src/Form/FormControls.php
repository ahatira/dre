<?php

declare(strict_types=1);

namespace Drupal\ui_suite_bnp_form_test\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Demonstration form.
 */
class FormControls extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ui_suite_bnp_form_test_form_controls';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form['description'] = [
      '#type' => 'item',
      '#markup' => $this->t('Form controls give textual form controls like input and textarea an upgrade with custom styles, sizing, focus states, and more.'),
    ];

    $form['documentation_link'] = [
      '#type' => 'component',
      '#component' => 'ui_suite_bnp:button',
      '#props' => [
        'variant' => 'primary__sm',
        'url' => 'https://getbootstrap.com/docs/5.3/forms/form-control/',
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

    $form['form_controls_email'] = [
      '#type' => 'email',
      '#title' => $this->t('Email address'),
      '#placeholder' => $this->t('name@example.com'),
    ];

    $form['form_controls_textarea'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Example textarea'),
      '#rows' => (int) 3,
    ];

    $form['sizing'] = [
      '#type' => 'item',
      '#markup' => '<h2>' . $this->t('Sizing') . '</h2>',
    ];

    $form['form_controls_large'] = [
      '#type' => 'textfield',
      '#title' => $this->t('.form-control-lg example'),
      '#title_display' => 'hidden',
      '#placeholder' => '.form-control-lg',
      '#attributes' => [
        'aria-label' => $this->t('.form-control-lg example'),
        'class' => [
          'form-control-lg',
        ],
      ],
    ];

    $form['form_controls_default'] = [
      '#type' => 'textfield',
      '#title' => $this->t('default input example'),
      '#title_display' => 'hidden',
      '#placeholder' => $this->t('Default input'),
      '#attributes' => [
        'aria-label' => $this->t('default input example'),
      ],
    ];

    $form['form_controls_small'] = [
      '#type' => 'textfield',
      '#title' => $this->t('.form-control-sm example'),
      '#title_display' => 'hidden',
      '#placeholder' => '.form-control-sm',
      '#attributes' => [
        'aria-label' => $this->t('.form-control-sm example'),
        'class' => [
          'form-control-sm',
        ],
      ],
    ];

    $form['form_text'] = [
      '#type' => 'item',
      '#markup' => '<h2>' . $this->t('Form text (description)') . '</h2>',
    ];

    $form['form_text_password'] = [
      '#type' => 'password',
      '#title' => $this->t('password'),
      '#description' => $this->t('Your password must be 8-20 characters long, contain letters and numbers, and must not contain spaces, special characters, or emoji.'),
      '#attributes' => [
        'autocomplete' => 'new-password',
      ],
    ];

    $form['form_text_password_inline'] = [
      '#type' => 'password',
      '#title' => $this->t('password'),
      '#title_display' => 'inline',
      '#description' => $this->t('Must be 8-20 characters long. (Compared with Bootstrap documentation example, the description is not in its own column.)'),
      '#label_attributes' => [
        'class' => [
          'col-auto',
        ],
      ],
      '#inner_wrapper_attributes' => [
        'class' => [
          'col-auto',
        ],
      ],
      '#wrapper_attributes' => [
        'autocomplete' => 'new-password',
        'class' => [
          'align-items-center',
        ],
      ],
    ];

    $form['disabled'] = [
      '#type' => 'item',
      '#markup' => '<h2>' . $this->t('Disabled') . '</h2>',
    ];

    $form['disabled_text'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Disabled input'),
      '#title_display' => 'hidden',
      '#disabled' => TRUE,
      '#placeholder' => $this->t('Disabled input'),
      '#attributes' => [
        'aria-label' => $this->t('Disabled input example'),
      ],
    ];

    $form['disabled_text_readonly'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Disabled readonly input'),
      '#title_display' => 'hidden',
      '#disabled' => TRUE,
      '#attributes' => [
        'aria-label' => $this->t('Disabled input example'),
        'readonly' => TRUE,
      ],
      '#value' => $this->t('Disabled readonly input'),
    ];

    $form['readonly'] = [
      '#type' => 'item',
      '#markup' => '<h2>' . $this->t('Readonly') . '</h2>',
    ];

    $form['readonly_text'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Readonly input example'),
      '#title_display' => 'hidden',
      '#attributes' => [
        'aria-label' => $this->t('Readonly input example'),
        'readonly' => TRUE,
      ],
      '#value' => $this->t('Readonly input here...'),
    ];

    $form['readonly_plaintext'] = [
      '#type' => 'item',
      '#markup' => '<h2>' . $this->t('Readonly plain text') . '</h2>',
    ];

    $form['readonly_plaintext_text'] = [
      '#type' => 'email',
      '#title' => $this->t('Email'),
      '#attributes' => [
        'class' => [
          'form-control-plaintext',
        ],
        'readonly' => TRUE,
      ],
      '#value' => $this->t('email@example.com'),
    ];

    $form['file_input'] = [
      '#type' => 'item',
      '#markup' => '<h2>' . $this->t('File input') . '</h2>',
    ];

    $form['file_default'] = [
      '#type' => 'file',
      '#title' => $this->t('Default file input example'),
    ];

    $form['file_multiple'] = [
      '#type' => 'file',
      '#title' => $this->t('Multiple files input example'),
      '#multiple' => TRUE,
    ];

    $form['file_disabled'] = [
      '#type' => 'file',
      '#title' => $this->t('Disabled file input example'),
      '#disabled' => TRUE,
    ];

    $form['file_small'] = [
      '#type' => 'file',
      '#title' => $this->t('Small file input example'),
      '#attributes' => [
        'class' => [
          'form-control-sm',
        ],
      ],
    ];

    $form['file_large'] = [
      '#type' => 'file',
      '#title' => $this->t('Large file input example'),
      '#attributes' => [
        'class' => [
          'form-control-lg',
        ],
      ],
    ];

    $form['color'] = [
      '#type' => 'item',
      '#markup' => '<h2>' . $this->t('Color') . '</h2>',
    ];

    $form['color_example'] = [
      '#type' => 'color',
      '#title' => $this->t('Color picker'),
      '#default_value' => '#563d7c',
    ];

    $form['datalists'] = [
      '#type' => 'item',
      '#markup' => '<h2>' . $this->t('Datalists') . '</h2>',
    ];

    $form['datalist'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Datalist example'),
      '#placeholder' => $this->t('Type to search...'),
      '#attributes' => [
        'list' => 'datalistOptions',
      ],
    ];
    $form['my_datalist'] = [
      '#type' => 'html_tag',
      '#tag' => 'datalist',
      '#attributes' => [
        'id' => 'datalistOptions',
      ],
      'options' => [],
    ];

    $datalist_options = [
      'San Francisco',
      'New York',
      'Seattle',
      'Los Angeles',
      'Chicago',
    ];
    foreach ($datalist_options as $datalist_option) {
      $form['my_datalist']['options'][] = [
        '#type' => 'html_tag',
        '#tag' => 'option',
        '#attributes' => [
          'value' => $datalist_option,
        ],
      ];
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {}

}
