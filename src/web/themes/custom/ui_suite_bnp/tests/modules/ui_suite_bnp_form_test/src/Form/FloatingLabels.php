<?php

declare(strict_types=1);

namespace Drupal\ui_suite_bnp_form_test\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Demonstration form.
 */
class FloatingLabels extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ui_suite_bnp_form_test_floating_labels';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form['description'] = [
      '#type' => 'item',
      '#markup' => $this->t('Create beautifully simple form labels that float over your input fields.'),
    ];

    $form['documentation_link'] = [
      '#type' => 'component',
      '#component' => 'ui_suite_bnp:button',
      '#props' => [
        'variant' => 'primary__sm',
        'url' => 'https://getbootstrap.com/docs/5.3/forms/floating-labels/',
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

    $form['floating_label_property'] = [
      '#type' => 'textfield',
      '#title' => $this->t('With #floating_label property'),
      '#floating_label' => TRUE,
    ];

    $form['floating_label_email'] = [
      '#type' => 'email',
      '#title' => $this->t('Email address'),
      '#title_display' => 'floating',
      '#attributes' => [
        'placeholder' => $this->t('name@example.com'),
        'autocomplete' => 'new-password',
      ],
    ];

    $form['floating_label_password'] = [
      '#type' => 'password',
      '#title' => $this->t('Password'),
      '#title_display' => 'floating',
      '#attributes' => [
        'placeholder' => $this->t('Password'),
        'autocomplete' => 'new-password',
      ],
    ];

    $form['floating_label_value'] = [
      '#type' => 'email',
      '#title' => $this->t('Input with value'),
      '#title_display' => 'floating',
      '#default_value' => 'test@example.com',
      '#attributes' => [
        'placeholder' => $this->t('name@example.com'),
      ],
    ];

    $form['textareas'] = [
      '#type' => 'item',
      '#markup' => '<h2>' . $this->t('Textareas') . '</h2>',
    ];

    $form['floating_label_textarea'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Comments'),
      '#title_display' => 'floating',
      '#attributes' => [
        'placeholder' => $this->t('Leave a comment here'),
      ],
    ];

    $form['floating_label_textarea_height'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Comments'),
      '#title_display' => 'floating',
      '#attributes' => [
        'placeholder' => $this->t('Leave a comment here'),
        'style' => 'height: 100px;',
      ],
    ];

    $form['selects'] = [
      '#type' => 'item',
      '#markup' => '<h2>' . $this->t('Selects') . '</h2>',
    ];

    $form['floating_label_select'] = [
      '#type' => 'select',
      '#title' => $this->t('Works with selects'),
      '#title_display' => 'floating',
      '#empty_option' => $this->t('Open this select menu'),
      '#options' => [
        '1' => $this->t('One'),
        '2' => $this->t('Two'),
        '3' => $this->t('Three'),
      ],
    ];

    $form['disabled'] = [
      '#type' => 'item',
      '#markup' => '<h2>' . $this->t('Disabled') . '</h2>',
    ];

    $form['floating_label_email_disabled'] = [
      '#type' => 'email',
      '#title' => $this->t('Email address'),
      '#title_display' => 'floating',
      '#attributes' => [
        'placeholder' => $this->t('name@example.com'),
        'autocomplete' => 'new-password',
      ],
      '#disabled' => TRUE,
    ];

    $form['floating_label_textarea_disabled'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Comments'),
      '#title_display' => 'floating',
      '#attributes' => [
        'placeholder' => $this->t('Leave a comment here'),
      ],
      '#disabled' => TRUE,
    ];

    $form['floating_label_textarea_height_disabled'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Comments'),
      '#title_display' => 'floating',
      '#default_value' => $this->t('Disabled textarea with some text inside'),
      '#attributes' => [
        'placeholder' => $this->t('Leave a comment here'),
        'style' => 'height: 100px;',
      ],
      '#disabled' => TRUE,
    ];

    $form['floating_label_select_disabled'] = [
      '#type' => 'select',
      '#title' => $this->t('Works with selects'),
      '#title_display' => 'floating',
      '#empty_option' => $this->t('Open this select menu'),
      '#options' => [
        '1' => $this->t('One'),
        '2' => $this->t('Two'),
        '3' => $this->t('Three'),
      ],
      '#disabled' => TRUE,
    ];

    $form['readonly'] = [
      '#type' => 'item',
      '#markup' => '<h2>' . $this->t('Readonly plain text') . '</h2>',
    ];

    $form['floating_label_readonly'] = [
      '#type' => 'email',
      '#title' => $this->t('Empty input'),
      '#title_display' => 'floating',
      '#attributes' => [
        'placeholder' => $this->t('name@example.com'),
        'class' => [
          'form-control-plaintext',
        ],
        'readonly' => TRUE,
      ],
    ];

    $form['floating_label_readonly_value'] = [
      '#type' => 'email',
      '#title' => $this->t('Input with value'),
      '#title_display' => 'floating',
      '#default_value' => 'name@example.com',
      '#attributes' => [
        'placeholder' => $this->t('name@example.com'),
        'class' => [
          'form-control-plaintext',
        ],
        'readonly' => TRUE,
      ],
    ];

    $form['input_groups'] = [
      '#type' => 'item',
      '#markup' => '<h2>' . $this->t('Input groups') . '</h2>',
    ];

    $form['floating_label_input_group'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Username'),
      '#title_display' => 'floating',
      '#attributes' => [
        'placeholder' => $this->t('Username'),
      ],
      '#field_prefix' => '@',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {}

}
