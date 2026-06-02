<?php

declare(strict_types=1);

namespace Drupal\ui_suite_bnp_form_test\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Demonstration form.
 */
class Details extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ui_suite_bnp_form_test_details';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form['description'] = [
      '#type' => 'item',
      '#markup' => $this->t('In UI Suite BNP, details are rendered as accordion by default. You can obtain a <code>details</code> element by setting <code>#bootstrap_accordion</code> property to FALSE in the form element structure.'),
    ];

    $form['accordion_details'] = [
      '#type' => 'details',
      '#title' => $this->t('Accordion details'),
      '#open' => TRUE,
    ];
    $form['accordion_details']['title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Title'),
      '#required' => TRUE,
    ];

    $form['details'] = [
      '#type' => 'details',
      '#title' => $this->t('Details'),
      '#open' => TRUE,
      '#bootstrap_accordion' => FALSE,
    ];
    $form['details']['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Name'),
      '#required' => TRUE,
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
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $title = $form_state->getValue('title');
    $this->messenger()->addMessage($this->t('You specified a title of %title.', ['%title' => $title]));

    $name = $form_state->getValue('name');
    $this->messenger()->addMessage($this->t('You specified a name of %name.', ['%name' => $name]));
  }

}
