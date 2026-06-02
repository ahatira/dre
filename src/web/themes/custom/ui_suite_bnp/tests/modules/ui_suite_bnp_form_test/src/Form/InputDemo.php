<?php

declare(strict_types=1);

namespace Drupal\ui_suite_bnp_form_test\Form;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Demonstration form.
 *
 * @see https://git.drupalcode.org/project/examples/-/blob/4.0.x/modules/form_api_example/src/Form/InputDemo.php?ref_type=heads
 */
class InputDemo extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ui_suite_bnp_form_test_drupal_input';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['description'] = [
      '#type' => 'item',
      '#markup' => $this->t('This example shows the use of all input-types.'),
    ];

    // CheckBoxes.
    $form['tests_taken'] = [
      '#type' => 'checkboxes',
      '#options' => ['SAT' => $this->t('SAT'), 'ACT' => $this->t('ACT')],
      '#title' => $this->t('What standardized tests did you take?'),
      '#description' => $this->t('Checkboxes, #type = checkboxes'),
    ];

    // Color.
    $form['color'] = [
      '#type' => 'color',
      '#title' => $this->t('Color'),
      '#default_value' => '#ffffff',
      '#description' => $this->t('Color, #type = color'),
    ];

    // Date.
    $now = new DrupalDateTime();
    $form['expiration'] = [
      '#type' => 'date',
      '#title' => $this->t('Content expiration'),
      '#default_value' => $now->format('Y-m-d'),
      '#description' => $this->t('Date, #type = date'),
    ];

    // Date-time.
    $form['datetime'] = [
      '#type' => 'datetime',
      '#title' => 'Date Time',
      '#date_increment' => 1,
      '#default_value' => $now,
      '#description' => $this->t('Date time, #type = datetime'),
    ];

    // URL.
    $form['url'] = [
      '#type' => 'url',
      '#title' => $this->t('URL'),
      '#maxlength' => 255,
      '#size' => 30,
      '#description' => $this->t('URL, #type = url'),
    ];

    // Email.
    $form['email'] = [
      '#type' => 'email',
      '#title' => $this->t('Email'),
      '#description' => $this->t('Email, #type = email'),
    ];

    // Number.
    $form['quantity'] = [
      '#type' => 'number',
      '#title' => $this->t('Quantity'),
      '#description' => $this->t('Number, #type = number'),
    ];

    // Password.
    $form['password'] = [
      '#type' => 'password',
      '#title' => $this->t('Password'),
      '#description' => $this->t('Password, #type = password'),
    ];

    // Password Confirm.
    $form['password_confirm'] = [
      '#type' => 'password_confirm',
      '#title' => $this->t('New Password'),
      '#description' => $this->t('PasswordConfirm, #type = password_confirm'),
    ];

    // Radios.
    $form['settings']['active'] = [
      '#type' => 'radios',
      '#title' => $this->t('Poll status'),
      '#options' => [0 => $this->t('Closed'), 1 => $this->t('Active')],
      '#description' => $this->t('Radios, #type = radios'),
    ];

    // Search.
    $form['search'] = [
      '#type' => 'search',
      '#title' => $this->t('Search'),
      '#description' => $this->t('Search, #type = search'),
    ];

    // Select.
    $form['favorite'] = [
      '#type' => 'select',
      '#title' => $this->t('Favorite color'),
      '#options' => [
        'red' => $this->t('Red'),
        'blue' => $this->t('Blue'),
        'green' => $this->t('Green'),
      ],
      '#empty_option' => $this->t('-select-'),
      '#description' => $this->t('Select, #type = select'),
    ];

    // Multiple values option elements.
    $form['select_multiple'] = [
      '#type' => 'select',
      '#title' => $this->t('Select (multiple)'),
      '#multiple' => TRUE,
      '#options' => [
        'sat' => 'SAT',
        'act' => 'ACT',
        'none' => 'N/A',
      ],
      '#default_value' => ['sat'],
      '#description' => $this->t('Select Multiple'),
    ];

    $form['phone'] = [
      '#type' => 'tel',
      '#title' => $this->t('Phone'),
      '#description' => $this->t('Tel, #type = tel'),
    ];

    $form['details'] = [
      '#type' => 'details',
      '#title' => $this->t('Details'),
      '#description' => $this->t('Details, #type = details'),
    ];

    // TableSelect.
    $options = [
      1 => ['first_name' => 'Indy', 'last_name' => 'Jones'],
      2 => ['first_name' => 'Darth', 'last_name' => 'Vader'],
      3 => ['first_name' => 'Super', 'last_name' => 'Man'],
    ];

    $header = [
      'first_name' => $this->t('First Name'),
      'last_name' => $this->t('Last Name'),
    ];

    $form['table'] = [
      '#type' => 'tableselect',
      '#title' => $this->t('Users'),
      '#header' => $header,
      '#options' => $options,
      '#empty' => $this->t('No users found'),
    ];

    // Textarea.
    $form['text'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Text'),
      '#description' => $this->t('Textarea, #type = textarea'),
    ];

    // Text format.
    $form['text_format'] = [
      '#type' => 'text_format',
      '#title' => $this->t('Text format'),
      '#format' => 'plain_text',
      '#description' => $this->t('Text format, #type = text_format'),
    ];

    // Textfield.
    $form['subject'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Subject'),
      '#size' => 60,
      '#maxlength' => 128,
      '#description' => $this->t('Textfield, #type = textfield'),
    ];

    // Weight.
    $form['weight'] = [
      '#type' => 'weight',
      '#title' => $this->t('Weight'),
      '#delta' => 10,
      '#description' => $this->t('Weight, #type = weight'),
    ];

    // Group submit handlers in an actions element with a key of "actions" so
    // that it gets styled correctly, and so that other modules may add actions
    // to the form.
    $form['actions'] = [
      '#type' => 'actions',
    ];

    // File.
    $form['file'] = [
      '#type' => 'file',
      '#title' => $this->t('File'),
      '#description' => $this->t('File, #type = file'),
    ];

    // Manage file.
    $form['managed_file'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('Managed file'),
      '#description' => $this->t('Manage file, #type = managed_file'),
    ];

    // Button.
    $form['button'] = [
      '#type' => 'button',
      '#value' => $this->t('Button'),
      '#description' => $this->t('Button, #type = button'),
    ];

    // Add a submit button that handles the submission of the form.
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
      '#description' => $this->t('Submit, #type = submit'),
    ];

    // Add a reset button that handles the submission of the form.
    $form['actions']['reset'] = [
      '#type' => 'button',
      '#button_type' => 'reset',
      '#value' => $this->t('Reset'),
      '#description' => $this->t('Submit, #type = button, #button_type = reset, #attributes = this.form.reset();return false'),
      '#attributes' => [
        'onclick' => 'this.form.reset(); return false;',
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   *
   * @SuppressWarnings("PHPMD.DevelopmentCodeFragment")
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    // Find out what was submitted.
    $values = $form_state->getValues();
    foreach ($values as $key => $value) {
      $label = $form[$key]['#title'] ?? $key;

      // Many arrays return 0 for unselected values so lets filter that out.
      if (\is_array($value)) {
        $value = \array_filter($value);
      }

      // Only display for controls that have titles and values.
      if ($value && $label) {
        // @phpstan-ignore-next-line
        $display_value = \is_array($value) ? \preg_replace('/[\n\r\s]+/', ' ', \print_r($value, TRUE)) : $value;
        $message = $this->t('Value for %title: %value', ['%title' => $label, '%value' => $display_value]);
        $this->messenger()->addMessage($message);
      }
    }
  }

}
