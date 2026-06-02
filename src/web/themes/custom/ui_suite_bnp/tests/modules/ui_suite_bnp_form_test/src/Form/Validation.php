<?php

declare(strict_types=1);

namespace Drupal\ui_suite_bnp_form_test\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Demonstration form.
 */
class Validation extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ui_suite_bnp_form_test_validation';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form['description'] = [
      '#type' => 'item',
      '#markup' => $this->t('Provide valuable, actionable feedback to your users with HTML5 form validation, via browser default behaviors or custom styles and JavaScript.'),
    ];

    $form['documentation_link'] = [
      '#type' => 'component',
      '#component' => 'ui_suite_bnp:button',
      '#props' => [
        'variant' => 'primary__sm',
        'url' => 'https://getbootstrap.com/docs/5.3/forms/validation/',
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

    $form['custom_styles'] = [
      '#type' => 'item',
      '#markup' => '<h2>' . $this->t('Custom styles (not supported out-of-the-box)') . '</h2>',
    ];

    $form['browser_defaults'] = [
      '#type' => 'item',
      '#markup' => '<h2>' . $this->t('Browser defaults: no example') . '</h2>',
    ];

    $form['server_site'] = [
      '#type' => 'item',
      '#markup' => '<h2>' . $this->t('Server side') . '</h2>',
    ];

    $form['validation_textfield'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Textfield'),
      '#description' => $this->t('Must be at least 5 characters in length.'),
    ];

    $form['validation_textfield_input_group'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Textfield with input group'),
      '#description' => $this->t('Must be at least 5 characters in length.'),
      '#field_prefix' => '@',
      '#field_suffix' => '.com',
    ];

    $form['validation_textfield_floating_label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Textfield with floating label'),
      '#title_display' => 'floating',
      '#description' => $this->t('Must be at least 5 characters in length.'),
    ];

    $form['validation_textfield_floating_label_input_group'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Textfield with floating label and input group'),
      '#title_display' => 'floating',
      '#description' => $this->t('Must be at least 5 characters in length.'),
      '#field_prefix' => '@',
      '#field_suffix' => '.com',
    ];

    $form['validation_select'] = [
      '#type' => 'select',
      '#title' => $this->t('Select'),
      '#description' => $this->t('Must be choice 1.'),
      '#empty_option' => $this->t('Choose'),
      '#options' => [
        'choice_1' => $this->t('Choice 1'),
        'choice_2' => $this->t('Choice 2'),
        'choice_3' => $this->t('Choice 3'),
      ],
    ];

    $form['validation_select_with_input_group'] = [
      '#type' => 'select',
      '#title' => $this->t('Select with input group'),
      '#description' => $this->t('Must be choice 1.'),
      '#empty_option' => $this->t('Choose'),
      '#options' => [
        'choice_1' => $this->t('Choice 1'),
        'choice_2' => $this->t('Choice 2'),
        'choice_3' => $this->t('Choice 3'),
      ],
      '#field_prefix' => $this->t('I choose'),
      '#field_suffix' => $this->t('wisely.'),
    ];

    $form['validation_textarea'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Textarea'),
      '#description' => $this->t('Must be at least 5 characters in length.'),
    ];

    $form['validation_textarea_with_input_group'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Textarea with input group'),
      '#description' => $this->t('Must be at least 5 characters in length.'),
      '#field_prefix' => $this->t('With textarea'),
    ];

    $form['validation_checkbox'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Checkbox'),
      '#description' => $this->t('Must be checked.'),
    ];

    $form['validation_checkboxes'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Checkboxes'),
      '#description' => $this->t('Must be choice 1.'),
      '#options' => [
        'choice_1' => $this->t('Choice 1'),
        'choice_2' => $this->t('Choice 2'),
        'choice_3' => $this->t('Choice 3'),
      ],
    ];

    $form['validation_radios'] = [
      '#type' => 'radios',
      '#title' => $this->t('Radios'),
      '#description' => $this->t('Must be checked.'),
      '#options' => [
        'choice_1' => $this->t('Choice 1'),
        'choice_2' => $this->t('Choice 2'),
        'choice_3' => $this->t('Choice 3'),
      ],
    ];

    $form['tooltips'] = [
      '#type' => 'item',
      '#markup' => '<h2>' . $this->t('Tooltips') . '</h2>',
    ];

    $form['validation_textfield_tooltip'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Textfield with tooltip'),
      '#errors_display' => 'tooltip',
    ];

    $form['actions'] = [
      '#type' => 'actions',
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state): void {
    $minimal_length = [
      'validation_textfield',
      'validation_textfield_tooltip',
      'validation_textfield_input_group',
      'validation_textarea',
      'validation_textarea_with_input_group',
      'validation_textfield_floating_label',
      'validation_textfield_floating_label_input_group',
    ];
    foreach ($minimal_length as $minimal_length_field) {
      /** @var string $value */
      $value = $form_state->getValue($minimal_length_field) ?? '';
      if (\strlen($value) < 5) {
        $form_state->setErrorByName($minimal_length_field, $this->t('Must be at least 5 characters long.'));
      }
    }

    $choice_1 = [
      'validation_select',
      'validation_select_with_input_group',
      'validation_radios',
    ];
    foreach ($choice_1 as $choice_1_field) {
      if ($form_state->hasValue($choice_1_field) && $form_state->getValue($choice_1_field) != 'choice_1') {
        $form_state->setErrorByName($choice_1_field, $this->t('Choose choice 1.'));
      }
    }
    if ($form_state->hasValue('validation_checkboxes') && $form_state->getValue('validation_checkboxes')['choice_1'] != 'choice_1') {
      $form_state->setErrorByName('validation_checkboxes', $this->t('The validation_checkboxes must be choice 1.'));
    }

    if ($form_state->hasValue('validation_checkbox') && !$form_state->getValue('validation_checkbox')) {
      $form_state->setErrorByName('validation_checkbox', $this->t('The validation_checkbox must be checked.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {}

}
