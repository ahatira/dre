<?php

declare(strict_types=1);

namespace Drupal\ui_suite_bnp_form_test\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Demonstration form.
 */
class InputGroup extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ui_suite_bnp_form_test_input_group';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form['description'] = [
      '#type' => 'item',
      '#markup' => $this->t('Easily extend form controls by adding text, buttons, or button groups on either side of textual inputs, custom selects, and custom file inputs.'),
    ];

    $form['documentation_link'] = [
      '#type' => 'component',
      '#component' => 'ui_suite_bnp:button',
      '#props' => [
        'variant' => 'primary__sm',
        'url' => 'https://getbootstrap.com/docs/5.3/forms/input-group/',
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

    $form['input_group_example_1'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Example'),
      '#title_display' => 'hidden',
      '#attributes' => [
        'placeholder' => $this->t('Username'),
      ],
      '#field_prefix' => '@',
    ];

    $form['input_group_example_2'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Example'),
      '#title_display' => 'hidden',
      '#attributes' => [
        'placeholder' => $this->t("Recipient's username"),
      ],
      '#field_suffix' => '@example.com',
    ];

    $form['input_group_example_3'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Your vanity URL'),
      '#field_prefix' => 'https://example.com/users/',
    ];

    $form['input_group_example_4'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Example'),
      '#title_display' => 'hidden',
      '#field_prefix' => '$',
      '#field_suffix' => '.00',
    ];

    $form['input_group_example_5'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Example'),
      '#title_display' => 'hidden',
      '#field_prefix' => $this->t('With textarea'),
    ];

    $form['wrapping'] = [
      '#type' => 'item',
      '#markup' => '<h2>' . $this->t('Wrapping') . '</h2>',
    ];

    $form['wrapping_example'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Example'),
      '#title_display' => 'hidden',
      '#field_prefix' => '@',
      '#input_group_attributes' => [
        'class' => [
          'flex-no-wrap',
        ],
      ],
    ];

    $form['border_radius'] = [
      '#type' => 'item',
      '#markup' => '<h2>' . $this->t('Border radius: no example') . '</h2>',
    ];

    $form['sizing'] = [
      '#type' => 'item',
      '#markup' => '<h2>' . $this->t('Sizing') . '</h2>',
    ];

    $form['input_group_sizing_sm'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Example'),
      '#title_display' => 'hidden',
      '#input_group_attributes' => [
        'class' => [
          'input-group-sm',
        ],
      ],
      '#field_prefix' => $this->t('Small'),
    ];

    $form['input_group_sizing'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Example'),
      '#title_display' => 'hidden',
      '#field_prefix' => $this->t('Default'),
    ];

    $form['input_group_sizing_lg'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Example'),
      '#title_display' => 'hidden',
      '#input_group_attributes' => [
        'class' => [
          'input-group-lg',
        ],
      ],
      '#field_prefix' => $this->t('Large'),
    ];

    $form['checkboxes_radios'] = [
      '#type' => 'item',
      '#markup' => '<h2>' . $this->t('Checkboxes and radios (not supported out-of-the-box)') . '</h2>',
    ];

    $form['multiple_inputs'] = [
      '#type' => 'item',
      '#markup' => '<h2>' . $this->t('Multiple inputs (not supported out-of-the-box)') . '</h2>',
    ];

    $form['multiple_addons'] = [
      '#type' => 'item',
      '#markup' => '<h2>' . $this->t('Multiple addons') . '</h2>',
    ];

    $form['input_group_multiple_addons_prefix'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Example'),
      '#title_display' => 'hidden',
      '#input_group_before' => [
        '$',
        '0.00',
      ],
    ];

    $form['input_group_multiple_addons_suffix'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Example'),
      '#title_display' => 'hidden',
      '#input_group_after' => [
        '$',
        '0.00',
      ],
    ];

    $form['button_addons'] = [
      '#type' => 'item',
      '#markup' => '<h2>' . $this->t('Button addons') . '</h2>',
    ];

    $form['input_group_button_addons_1'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Example'),
      '#title_display' => 'hidden',
      '#input_group_before' => [
        [
          '#type' => 'submit',
          '#value' => $this->t('Button'),
        ],
      ],
    ];

    $form['input_group_button_addons_2'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Example'),
      '#title_display' => 'hidden',
      '#placeholder' => $this->t("Recipient's username"),
      '#input_group_after' => [
        [
          '#type' => 'submit',
          '#value' => $this->t('Button'),
        ],
      ],
    ];

    $form['input_group_button_addons_3'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Example'),
      '#title_display' => 'hidden',
      '#input_group_before' => [
        [
          '#type' => 'submit',
          '#value' => $this->t('Button'),
        ],
        [
          '#type' => 'submit',
          '#value' => $this->t('Submit'),
          '#attributes' => [
            'class' => [
              'btn-outline-secondary',
            ],
          ],
        ],
      ],
    ];

    $form['input_group_button_addons_4'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Example'),
      '#title_display' => 'hidden',
      '#placeholder' => $this->t("Recipient's username"),
      '#input_group_after' => [
        [
          '#type' => 'submit',
          '#value' => $this->t('Button'),
        ],
        [
          '#type' => 'submit',
          '#value' => $this->t('Submit'),
          '#attributes' => [
            'class' => [
              'btn-outline-secondary',
            ],
          ],
        ],
      ],
    ];

    $form['example_automatic_input_group_button'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Example with automatic detection'),
      '#input_group_before' => [
        'Test',
        'Test 2',
      ],
      '#input_group_button' => TRUE,
    ];

    $form['example_automatic_input_group_button_submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    $form['buttons_with_dropdowns'] = [
      '#type' => 'item',
      '#markup' => '<h2>' . $this->t('Buttons with dropdowns (not supported out-of-the-box)') . '</h2>',
    ];

    $form['segmented_buttons'] = [
      '#type' => 'item',
      '#markup' => '<h2>' . $this->t('Segmented buttons (not supported out-of-the-box)') . '</h2>',
    ];

    $form['custom_form'] = [
      '#type' => 'item',
      '#markup' => '<h2>' . $this->t('Custom forms') . '</h2>',
    ];

    $form['custom_select_1'] = [
      '#type' => 'select',
      '#title' => $this->t('Example'),
      '#title_display' => 'hidden',
      '#empty_option' => $this->t('Choose...'),
      '#options' => [
        '1' => $this->t('One'),
        '2' => $this->t('Two'),
        '3' => $this->t('Three'),
      ],
      '#field_prefix' => $this->t('Options'),
    ];

    $form['custom_select_2'] = [
      '#type' => 'select',
      '#title' => $this->t('Example'),
      '#title_display' => 'hidden',
      '#empty_option' => $this->t('Choose...'),
      '#options' => [
        '1' => $this->t('One'),
        '2' => $this->t('Two'),
        '3' => $this->t('Three'),
      ],
      '#field_suffix' => $this->t('Options'),
    ];

    $form['custom_select_3'] = [
      '#type' => 'select',
      '#title' => $this->t('Example'),
      '#title_display' => 'hidden',
      '#empty_option' => $this->t('Choose...'),
      '#options' => [
        '1' => $this->t('One'),
        '2' => $this->t('Two'),
        '3' => $this->t('Three'),
      ],
      '#input_group_before' => [
        [
          '#type' => 'submit',
          '#value' => $this->t('Button'),
          '#attributes' => [
            'class' => [
              'btn-outline-secondary',
            ],
          ],
        ],
      ],
    ];

    $form['custom_select_4'] = [
      '#type' => 'select',
      '#title' => $this->t('Example'),
      '#title_display' => 'hidden',
      '#empty_option' => $this->t('Choose...'),
      '#options' => [
        '1' => $this->t('One'),
        '2' => $this->t('Two'),
        '3' => $this->t('Three'),
      ],
      '#input_group_after' => [
        [
          '#type' => 'submit',
          '#value' => $this->t('Button'),
          '#attributes' => [
            'class' => [
              'btn-outline-secondary',
            ],
          ],
        ],
      ],
    ];

    $form['custom_file_1'] = [
      '#type' => 'file',
      '#title' => $this->t('Example'),
      '#title_display' => 'hidden',
      '#field_prefix' => $this->t('Upload'),
    ];

    $form['custom_file_2'] = [
      '#type' => 'file',
      '#title' => $this->t('Example'),
      '#title_display' => 'hidden',
      '#field_suffix' => $this->t('Upload'),
    ];

    $form['custom_file_3'] = [
      '#type' => 'file',
      '#title' => $this->t('Example'),
      '#title_display' => 'hidden',
      '#input_group_before' => [
        [
          '#type' => 'submit',
          '#value' => $this->t('Button'),
          '#attributes' => [
            'class' => [
              'btn-outline-secondary',
            ],
          ],
        ],
      ],
    ];

    $form['custom_file_4'] = [
      '#type' => 'file',
      '#title' => $this->t('Example'),
      '#title_display' => 'hidden',
      '#input_group_after' => [
        [
          '#type' => 'submit',
          '#value' => $this->t('Button'),
          '#attributes' => [
            'class' => [
              'btn-outline-secondary',
            ],
          ],
        ],
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {}

}
