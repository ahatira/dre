<?php

declare(strict_types=1);

namespace Drupal\ui_suite_bnp_form_test\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Demonstration form.
 */
class Select extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ui_suite_bnp_form_test_select';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form['description'] = [
      '#type' => 'item',
      '#markup' => $this->t('Customize the native select with custom CSS that changes the element’s initial appearance.'),
    ];

    $form['documentation_link'] = [
      '#type' => 'component',
      '#component' => 'ui_suite_bnp:button',
      '#props' => [
        'variant' => 'primary__sm',
        'url' => 'https://getbootstrap.com/docs/5.3/forms/select/',
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

    $options = [
      '1' => $this->t('One'),
      '2' => $this->t('Two'),
      '3' => $this->t('Three'),
    ];

    $form['default'] = [
      '#type' => 'select',
      '#title' => $this->t('Default select example'),
      '#options' => $options,
      '#empty_option' => $this->t('Open this select menu'),
    ];

    $form['sizing'] = [
      '#type' => 'item',
      '#markup' => '<h2>' . $this->t('Sizing') . '</h2>',
    ];

    $form['large'] = [
      '#type' => 'select',
      '#title' => $this->t('Large select example'),
      '#options' => $options,
      '#empty_option' => $this->t('Open this select menu'),
      '#attributes' => [
        'class' => [
          'form-select-lg',
        ],
      ],
    ];

    $form['small'] = [
      '#type' => 'select',
      '#title' => $this->t('Small select example'),
      '#options' => $options,
      '#empty_option' => $this->t('Open this select menu'),
      '#attributes' => [
        'class' => [
          'form-select-sm',
        ],
      ],
    ];

    $form['multiple'] = [
      '#type' => 'select',
      '#title' => $this->t('Multiple select example'),
      '#options' => $options,
      '#empty_option' => $this->t('Open this select menu'),
      '#multiple' => TRUE,
    ];

    $form['size'] = [
      '#type' => 'select',
      '#title' => $this->t('Size 3 select example'),
      '#options' => $options,
      '#empty_option' => $this->t('Open this select menu'),
      '#size' => (int) 3,
    ];

    $form['disabled'] = [
      '#type' => 'item',
      '#markup' => '<h2>' . $this->t('Disabled') . '</h2>',
    ];

    $form['disabled_example'] = [
      '#type' => 'select',
      '#title' => $this->t('Disabled select example'),
      '#options' => $options,
      '#empty_option' => $this->t('Open this select menu'),
      '#disabled' => TRUE,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {}

}
