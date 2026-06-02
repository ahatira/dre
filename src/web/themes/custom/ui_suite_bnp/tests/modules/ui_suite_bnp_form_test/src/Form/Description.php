<?php

declare(strict_types=1);

namespace Drupal\ui_suite_bnp_form_test\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Demonstration form.
 */
class Description extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ui_suite_bnp_form_test_description';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form['description'] = [
      '#type' => 'item',
      '#markup' => $this->t('Test description position.'),
    ];

    $descriptionPositions = [
      'after' => 'after',
      'before' => 'before',
      'invisible' => 'invisible',
    ];

    $testCases = [
      'default' => [],
      'input_group' => [
        '#input_group_before' => [
          '$',
          '0.00',
        ],
        '#input_group_after' => [
          '$',
          '0.00',
        ],
      ],
      'floating_label' => [
        '#title_display' => 'floating',
      ],
      'input_group_floating_label' => [
        '#title_display' => 'floating',
        '#input_group_before' => [
          '$',
          '0.00',
        ],
        '#input_group_after' => [
          '$',
          '0.00',
        ],
      ],
    ];

    $elements = [
      'textfield' => [
        '#type' => 'textfield',
      ],
      'textarea' => [
        '#type' => 'textarea',
      ],
      'select' => [
        '#type' => 'select',
        '#options' => [
          '1' => '1',
          '2' => '2',
          '3' => '3',
        ],
      ],
      'checkboxes' => [
        '#type' => 'checkboxes',
        '#options' => [
          '1' => '1',
          '2' => '2',
          '3' => '3',
        ],
      ],
      'radios' => [
        '#type' => 'radios',
        '#options' => [
          '1' => '1',
          '2' => '2',
          '3' => '3',
        ],
      ],
    ];

    foreach ($elements as $elementInfos) {
      foreach ($testCases as $testCase => $testCaseValues) {
        foreach ($descriptionPositions as $descriptionPosition => $descriptionPositionValue) {
          $form[$elementInfos['#type'] . '_' . $descriptionPosition . '_' . $testCase] = \array_merge($elementInfos, $testCaseValues, [
            '#title' => $this->t('Test case: @testCase, description position: @descriptionPosition', [
              '@testCase' => $testCase,
              '@descriptionPosition' => $descriptionPosition,
            ]),
            '#description' => $this->t('Description'),
            '#description_display' => $descriptionPositionValue,
          ]);
        }
      }
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {}

}
