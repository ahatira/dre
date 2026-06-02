<?php

declare(strict_types=1);

namespace Drupal\ui_suite_bnp_form_test\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Demonstration form.
 */
class Layout extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ui_suite_bnp_form_test_layout';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form['description'] = [
      '#type' => 'item',
      '#markup' => $this->t('Give your forms some structure from inline to horizontal to custom grid implementations with our form layout options.'),
    ];

    $form['documentation_link'] = [
      '#type' => 'component',
      '#component' => 'ui_suite_bnp:button',
      '#props' => [
        'variant' => 'primary__sm',
        'url' => 'https://getbootstrap.com/docs/5.3/forms/layout/',
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

    $form['utilities'] = [
      '#type' => 'item',
      '#markup' => '<h2>' . $this->t('Utilities: mb-3 classes added automatically') . '</h2>',
    ];

    $form['form_grid'] = [
      '#type' => 'item',
      '#markup' => '<h2>' . $this->t('Form grid') . '</h2>',
    ];

    $form['form_grid_wrapper'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => [
          'row',
          'g-3',
        ],
      ],
    ];

    $form['form_grid_wrapper']['first_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('First name'),
      '#title_display' => 'hidden',
      '#placeholder' => $this->t('First name'),
      '#wrapper_attributes' => [
        'class' => [
          'col',
        ],
      ],
    ];

    $form['form_grid_wrapper']['last_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Last name'),
      '#title_display' => 'hidden',
      '#placeholder' => $this->t('Last name'),
      '#wrapper_attributes' => [
        'class' => [
          'col',
        ],
      ],
    ];

    $form['gutters'] = [
      '#type' => 'item',
      '#markup' => '<h2>' . $this->t('Gutters') . '</h2>',
    ];

    $form['gutters_wrapper'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => [
          'row',
          'g-3',
        ],
      ],
    ];

    $form['gutters_wrapper']['layout_gutters_email'] = [
      '#type' => 'email',
      '#title' => $this->t('Email'),
      '#attributes' => [
        'autocomplete' => 'new-password',
      ],
      '#wrapper_attributes' => [
        'class' => [
          'col-md-6',
        ],
      ],
    ];

    $form['gutters_wrapper']['layout_gutters_password'] = [
      '#type' => 'password',
      '#title' => $this->t('Password'),
      '#attributes' => [
        'autocomplete' => 'new-password',
      ],
      '#wrapper_attributes' => [
        'class' => [
          'col-md-6',
        ],
      ],
    ];

    $form['gutters_wrapper']['layout_gutters_address_1'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Address'),
      '#attributes' => [
        'placeholder' => $this->t('1234 Main St'),
      ],
      '#wrapper_attributes' => [
        'class' => [
          'col-12',
        ],
      ],
    ];

    $form['gutters_wrapper']['layout_gutters_address_2'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Address 2'),
      '#attributes' => [
        'placeholder' => $this->t('Apartment, studio, or floor'),
      ],
      '#wrapper_attributes' => [
        'class' => [
          'col-12',
        ],
      ],
    ];

    $form['gutters_wrapper']['layout_gutters_city'] = [
      '#type' => 'textfield',
      '#title' => $this->t('City'),
      '#wrapper_attributes' => [
        'class' => [
          'col-md-6',
        ],
      ],
    ];

    $form['gutters_wrapper']['layout_gutters_state'] = [
      '#type' => 'select',
      '#title' => $this->t('State'),
      '#empty_option' => $this->t('Choose...'),
      '#options' => [
        'choice_1' => $this->t('Choice 1'),
        'choice_2' => $this->t('Choice 2'),
        'choice_3' => $this->t('Choice 3'),
      ],
      '#wrapper_attributes' => [
        'class' => [
          'col-md-4',
        ],
      ],
    ];

    $form['gutters_wrapper']['layout_gutters_zip'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Zip'),
      '#wrapper_attributes' => [
        'class' => [
          'col-md-2',
        ],
      ],
    ];

    $form['gutters_wrapper']['layout_gutters_checkbox_wrapper'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => [
          'col-12',
        ],
      ],
    ];

    $form['gutters_wrapper']['layout_gutters_checkbox_wrapper']['layout_gutters_checkbox'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Check me out'),
    ];

    $form['gutters_wrapper']['layout_gutters_submit_wrapper'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => [
          'col-12',
        ],
      ],
    ];

    $form['gutters_wrapper']['layout_gutters_submit_wrapper']['layout_gutters_submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Sign in'),
      '#attributes' => [
        'class' => [
          'btn-primary',
        ],
      ],
    ];

    $form['horizontal'] = [
      '#type' => 'item',
      '#markup' => '<h2>' . $this->t('Horizontal form') . '</h2>',
    ];

    $form['layout_horizontal_email'] = [
      '#type' => 'email',
      '#title' => $this->t('Email'),
      '#title_display' => 'inline',
      '#label_attributes' => [
        'class' => [
          'col-sm-2',
        ],
      ],
      '#inner_wrapper_attributes' => [
        'class' => [
          'col-sm-10',
        ],
      ],
    ];

    $form['layout_horizontal_password'] = [
      '#type' => 'password',
      '#title' => $this->t('Password'),
      '#title_display' => 'inline',
      '#label_attributes' => [
        'class' => [
          'col-sm-2',
        ],
      ],
      '#inner_wrapper_attributes' => [
        'class' => [
          'col-sm-10',
        ],
      ],
    ];

    $form['layout_horizontal_radios'] = [
      '#type' => 'radios',
      '#title' => $this->t('Radios'),
      '#title_display' => 'inline',
      '#options' => [
        'choice_1' => $this->t('First radio'),
        'choice_2' => $this->t('Second radio'),
        'choice_3' => $this->t('Third disabled radio'),
      ],
      '#label_attributes' => [
        'class' => [
          'col-sm-2',
          'pt-0',
        ],
      ],
      '#inner_wrapper_attributes' => [
        'class' => [
          'col-sm-10',
        ],
      ],
      'choice_3' => [
        '#disabled' => TRUE,
      ],
    ];

    $form['layout_horizontal_checkbox_wrapper_row'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => [
          'row',
          'mb-3',
        ],
      ],
    ];

    $form['layout_horizontal_checkbox_wrapper_row']['layout_horizontal_checkbox_wrapper_col'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => [
          'col-sm-10',
          'offset-sm-2',
        ],
      ],
    ];

    $form['layout_horizontal_checkbox_wrapper_row']['layout_horizontal_checkbox_wrapper_col']['layout_horizontal_checkbox'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Example checkbox'),
    ];

    $form['layout_horizontal_submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Sign in'),
      '#attributes' => [
        'class' => [
          'btn-primary',
        ],
      ],
    ];

    $form['layout_horizontal_description'] = [
      '#type' => 'email',
      '#title' => $this->t('Email (with description)'),
      '#title_display' => 'inline',
      '#description' => $this->t('Description'),
      '#label_attributes' => [
        'class' => [
          'col-sm-2',
        ],
      ],
      '#inner_wrapper_attributes' => [
        'class' => [
          'col-sm-10',
        ],
      ],
    ];

    $form['layout_horizontal_description_before'] = [
      '#type' => 'email',
      '#title' => $this->t('Email (with description)'),
      '#title_display' => 'inline',
      '#description' => $this->t('Description'),
      '#description_display' => 'before',
      '#label_attributes' => [
        'class' => [
          'col-sm-2',
        ],
      ],
      '#inner_wrapper_attributes' => [
        'class' => [
          'col-sm-10',
        ],
      ],
    ];

    $form['layout_horizontal_email_sizing_sm'] = [
      '#type' => 'email',
      '#title' => $this->t('Email'),
      '#title_display' => 'inline',
      '#attributes' => [
        'placeholder' => 'col-form-label-sm',
        'class' => [
          'form-control-sm',
        ],
      ],
      '#label_attributes' => [
        'class' => [
          'col-sm-2',
          'col-form-label-sm',
        ],
      ],
      '#inner_wrapper_attributes' => [
        'class' => [
          'col-sm-10',
        ],
      ],
    ];

    $form['layout_horizontal_email_sizing_normal'] = [
      '#type' => 'email',
      '#title' => $this->t('Email'),
      '#title_display' => 'inline',
      '#attributes' => [
        'placeholder' => 'col-form-label',
      ],
      '#label_attributes' => [
        'class' => [
          'col-sm-2',
        ],
      ],
      '#inner_wrapper_attributes' => [
        'class' => [
          'col-sm-10',
        ],
      ],
    ];

    $form['layout_horizontal_email_sizing_lg'] = [
      '#type' => 'email',
      '#title' => $this->t('Email'),
      '#title_display' => 'inline',
      '#attributes' => [
        'placeholder' => 'col-form-label-lg',
        'class' => [
          'form-control-lg',
        ],
      ],
      '#label_attributes' => [
        'class' => [
          'col-sm-2',
          'col-form-label-lg',
        ],
      ],
      '#inner_wrapper_attributes' => [
        'class' => [
          'col-sm-10',
        ],
      ],
    ];

    $form['column_sizing'] = [
      '#type' => 'item',
      '#markup' => '<h2>' . $this->t('Column sizing') . '</h2>',
    ];

    $form['column_sizing_wrapper_row'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => [
          'row',
          'mb-3',
        ],
      ],
    ];

    $form['column_sizing_wrapper_row']['city'] = [
      '#type' => 'textfield',
      '#title' => $this->t('City'),
      '#title_display' => 'hidden',
      '#placeholder' => $this->t('City'),
      '#wrapper_attributes' => [
        'class' => [
          'col-sm-7',
        ],
      ],
    ];

    $form['column_sizing_wrapper_row']['state'] = [
      '#type' => 'textfield',
      '#title' => $this->t('State'),
      '#title_display' => 'hidden',
      '#placeholder' => $this->t('State'),
      '#wrapper_attributes' => [
        'class' => [
          'col-sm',
        ],
      ],
    ];

    $form['column_sizing_wrapper_row']['zip'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Zip'),
      '#title_display' => 'hidden',
      '#placeholder' => $this->t('Zip'),
      '#wrapper_attributes' => [
        'class' => [
          'col-sm',
        ],
      ],
    ];

    $form['auto_sizing'] = [
      '#type' => 'item',
      '#markup' => '<h2>' . $this->t('Auto-sizing') . '</h2>',
    ];

    $form['auto_sizing_wrapper_row_1'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => [
          'row',
          'mb-3',
          'gy-2',
          'gx-2',
          'align-items-center',
        ],
      ],
    ];

    $form['auto_sizing_wrapper_row_1']['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Name'),
      '#title_display' => 'hidden',
      '#placeholder' => $this->t('Jane Doe'),
      '#size' => (int) 20,
      '#wrapper_attributes' => [
        'class' => [
          'col-auto',
        ],
      ],
    ];

    $form['auto_sizing_wrapper_row_1']['username'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Username'),
      '#title_display' => 'hidden',
      '#placeholder' => $this->t('username'),
      '#size' => (int) 20,
      '#wrapper_attributes' => [
        'class' => [
          'col-auto',
        ],
      ],
      '#field_prefix' => '@',
    ];

    $form['auto_sizing_wrapper_row_1']['preference'] = [
      '#type' => 'select',
      '#title' => $this->t('Preference'),
      '#title_display' => 'hidden',
      '#empty_option' => $this->t('Choose...'),
      '#options' => [
        'choice_1' => $this->t('Choice 1'),
        'choice_2' => $this->t('Choice 2'),
        'choice_3' => $this->t('Choice 3'),
      ],
      '#wrapper_attributes' => [
        'class' => [
          'col-auto',
        ],
      ],
    ];

    $form['auto_sizing_wrapper_row_1']['remember_me'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Remember me'),
      '#wrapper_attributes' => [
        'class' => [
          'col-auto',
        ],
      ],
    ];

    $form['auto_sizing_wrapper_row_1']['submit_wrapper'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => [
          'col-auto',
        ],
      ],
    ];

    $form['auto_sizing_wrapper_row_2'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => [
          'row',
          'mb-3',
          'gy-2',
          'gx-3',
          'align-items-center',
        ],
      ],
    ];

    $form['auto_sizing_wrapper_row_2']['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Name'),
      '#title_display' => 'hidden',
      '#placeholder' => $this->t('Jane Doe'),
      '#size' => (int) 20,
      '#wrapper_attributes' => [
        'class' => [
          'col-sm-3',
        ],
      ],
    ];

    $form['auto_sizing_wrapper_row_2']['username'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Username'),
      '#title_display' => 'hidden',
      '#placeholder' => $this->t('username'),
      '#size' => (int) 20,
      '#wrapper_attributes' => [
        'class' => [
          'col-sm-3',
        ],
      ],
      '#field_prefix' => '@',
    ];

    $form['auto_sizing_wrapper_row_2']['preference'] = [
      '#type' => 'select',
      '#title' => $this->t('Preference'),
      '#title_display' => 'hidden',
      '#empty_option' => $this->t('Choose...'),
      '#options' => [
        'choice_1' => $this->t('Choice 1'),
        'choice_2' => $this->t('Choice 2'),
        'choice_3' => $this->t('Choice 3'),
      ],
      '#wrapper_attributes' => [
        'class' => [
          'col-sm-3',
        ],
      ],
    ];

    $form['auto_sizing_wrapper_row_2']['remember_me'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Remember me'),
      '#wrapper_attributes' => [
        'class' => [
          'col-auto',
        ],
      ],
    ];

    $form['auto_sizing_wrapper_row_2']['submit_wrapper'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => [
          'col-auto',
        ],
      ],
    ];

    $form['auto_sizing_wrapper_row_2']['submit_wrapper']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    $form['inline_forms'] = [
      '#type' => 'item',
      '#markup' => '<h2>' . $this->t('Inline forms') . '</h2>',
    ];

    $form['inline_forms_wrapper_row'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => [
          'row',
          'row-cols-lg-auto',
          'g-3',
          'align-items-center',
          'mb-3',
        ],
      ],
    ];

    $form['inline_forms_wrapper_row']['username'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Username'),
      '#title_display' => 'hidden',
      '#placeholder' => $this->t('username'),
      '#size' => (int) 20,
      '#wrapper_attributes' => [
        'class' => [
          'col-12',
        ],
      ],
      '#field_prefix' => '@',
    ];

    $form['inline_forms_wrapper_row']['preference'] = [
      '#type' => 'select',
      '#title' => $this->t('Preference'),
      '#title_display' => 'hidden',
      '#empty_option' => $this->t('Choose...'),
      '#options' => [
        'choice_1' => $this->t('Choice 1'),
        'choice_2' => $this->t('Choice 2'),
        'choice_3' => $this->t('Choice 3'),
      ],
      '#wrapper_attributes' => [
        'class' => [
          'col-12',
        ],
      ],
    ];

    $form['inline_forms_wrapper_row']['remember_me'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Remember me'),
      '#wrapper_attributes' => [
        'class' => [
          'col-12',
        ],
      ],
    ];

    $form['inline_forms_wrapper_row']['submit_wrapper'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => [
          'col-12',
        ],
      ],
    ];

    $form['inline_forms_wrapper_row']['submit_wrapper']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {}

}
