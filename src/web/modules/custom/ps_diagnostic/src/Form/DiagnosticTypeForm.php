<?php

declare(strict_types=1);

namespace Drupal\ps_diagnostic\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

final class DiagnosticTypeForm extends EntityForm {

  private const CLASSES_WRAPPER_ID = 'ps-diagnostic-classes-wrapper';

  public function form(array $form, FormStateInterface $form_state): array {
    $form = parent::form($form, $form_state);
    $form['#attached']['library'][] = 'ps_diagnostic/diagnostic_admin';

    /** @var \Drupal\ps_diagnostic\Entity\DiagnosticType $entity */
    $entity = $this->entity;

    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#required' => TRUE,
      '#default_value' => $entity->label(),
      '#maxlength' => 255,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $entity->id(),
      '#machine_name' => ['exists' => '\\Drupal\\ps_diagnostic\\Entity\\DiagnosticType::load'],
      '#disabled' => !$entity->isNew(),
    ];

    $form['description'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Description'),
      '#default_value' => $entity->get('description'),
    ];

    $form['unit'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Unit'),
      '#default_value' => (string) $entity->get('unit'),
      '#maxlength' => 32,
      '#description' => $this->t('Example: kWh/m2/year, kgCO2/m2/year.'),
    ];

    $form['icon'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Icon'),
      '#default_value' => (string) $entity->get('icon'),
      '#maxlength' => 64,
      '#description' => $this->t('Simple semantic key used by the formatter (example: energy, co2).'),
    ];

    $existing_classes = is_array($entity->get('classes')) ? $entity->get('classes') : [];
    if (!$form_state->has('classes_rows_state')) {
      $default_rows = $this->buildDefaultRows($existing_classes);
      $form_state->set('classes_rows_state', $default_rows);
      $form_state->set('classes_count', max(1, count($default_rows)));
    }

    $rows_state = (array) $form_state->get('classes_rows_state');
    $row_count = max(1, (int) $form_state->get('classes_count', count($rows_state)));
    if ($rows_state === []) {
      $rows_state = $this->buildDefaultRows($existing_classes);
    }

    $rows_state = $this->normalizeRowsForForm($rows_state, $row_count);
    $last_configured_row = $this->findLastConfiguredRow($rows_state);

    $form['classes_wrapper'] = [
      '#type' => 'container',
      '#attributes' => [
        'id' => self::CLASSES_WRAPPER_ID,
      ],
    ];

    $form['classes_wrapper']['classes'] = [
      '#type' => 'table',
      '#title' => $this->t('Energy classes'),
      '#header' => [
        $this->t('Move'),
        $this->t('Label'),
        $this->t('Color'),
        $this->t('Range max'),
        $this->t('Remove'),
      ],
      '#empty' => $this->t('No classes configured yet.'),
      '#tree' => TRUE,
      '#description' => $this->t('Define classes in ascending order. The last configured class is open-ended and must keep an empty max range.'),
      '#tabledrag' => [
        [
          'action' => 'order',
          'relationship' => 'sibling',
          'group' => 'ps-diagnostic-class-weight',
          'hidden' => FALSE,
        ],
      ],
    ];

    for ($i = 0; $i < $row_count; $i++) {
      $row = $rows_state[$i] ?? [];
      $form['classes_wrapper']['classes'][$i]['#attributes']['class'][] = 'draggable';

      $form['classes_wrapper']['classes'][$i]['weight'] = [
        '#type' => 'weight',
        '#title' => $this->t('Order'),
        '#title_display' => 'invisible',
        '#default_value' => (int) ($row['weight'] ?? $i),
        '#delta' => max(10, $row_count + 5),
        '#attributes' => [
          'class' => ['ps-diagnostic-class-weight'],
        ],
      ];

      $form['classes_wrapper']['classes'][$i]['label'] = [
        '#type' => 'textfield',
        '#default_value' => (string) ($row['label'] ?? ''),
        '#maxlength' => 16,
        '#placeholder' => $this->t('A'),
      ];

      $default_color = strtoupper(trim((string) ($row['color'] ?? '')));
      if (!preg_match('/^#[0-9A-F]{6}$/', $default_color)) {
        $default_color = '#5DBB63';
      }
      $form['classes_wrapper']['classes'][$i]['color'] = [
        '#type' => 'color',
        '#default_value' => $default_color,
      ];

      $range_max_default = '';
      if ($i !== $last_configured_row && ($row['range_max'] ?? '') !== '') {
        $range_max_default = (int) $row['range_max'];
      }
      $form['classes_wrapper']['classes'][$i]['range_max'] = [
        '#type' => 'number',
        '#default_value' => $range_max_default,
        '#min' => 0,
        '#step' => 1,
      ];

      $form['classes_wrapper']['classes'][$i]['remove'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('Remove'),
        '#title_display' => 'invisible',
        '#default_value' => !empty($row['remove']),
      ];
    }

    $form['classes_wrapper']['actions'] = [
      '#type' => 'actions',
    ];
    $form['classes_wrapper']['actions']['add_class'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add class'),
      '#submit' => ['::addClassSubmit'],
      '#limit_validation_errors' => [],
      '#ajax' => [
        'callback' => '::classesTableAjax',
        'wrapper' => self::CLASSES_WRAPPER_ID,
      ],
    ];
    $form['classes_wrapper']['actions']['remove_selected'] = [
      '#type' => 'submit',
      '#value' => $this->t('Remove selected'),
      '#submit' => ['::removeSelectedClassesSubmit'],
      '#limit_validation_errors' => [],
      '#ajax' => [
        'callback' => '::classesTableAjax',
        'wrapper' => self::CLASSES_WRAPPER_ID,
      ],
    ];

    $form['enabled'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enabled'),
      '#default_value' => (bool) $entity->get('enabled'),
    ];

    $form['weight'] = [
      '#type' => 'number',
      '#title' => $this->t('Weight'),
      '#default_value' => (int) ($entity->get('weight') ?: 0),
      '#access' => FALSE,
    ];

    return $form;
  }

  public function classesTableAjax(array &$form, FormStateInterface $form_state): array {
    return $form['classes_wrapper'];
  }

  public function addClassSubmit(array &$form, FormStateInterface $form_state): void {
    $rows = $this->extractRowsFromFormState($form_state);
    $rows[] = [
      'label' => '',
      'color' => '#5DBB63',
      'range_max' => '',
      'weight' => count($rows),
      'remove' => 0,
    ];

    $rows = $this->sortRowsByWeight($rows);
    $this->reindexRowWeights($rows);

    $form_state->set('classes_rows_state', $rows);
    $form_state->set('classes_count', count($rows));
    $form_state->setRebuild(TRUE);
  }

  public function removeSelectedClassesSubmit(array &$form, FormStateInterface $form_state): void {
    $rows = $this->extractRowsFromFormState($form_state);
    $rows = $this->sortRowsByWeight($rows);

    $filtered = [];
    foreach ($rows as $row) {
      if (!empty($row['remove'])) {
        continue;
      }
      $row['remove'] = 0;
      $filtered[] = $row;
    }

    if ($filtered === []) {
      $filtered[] = [
        'label' => '',
        'color' => '#5DBB63',
        'range_max' => '',
        'weight' => 0,
        'remove' => 0,
      ];
    }

    $this->reindexRowWeights($filtered);

    $form_state->set('classes_rows_state', $filtered);
    $form_state->set('classes_count', count($filtered));
    $form_state->setRebuild(TRUE);
  }

  public function actions(array $form, FormStateInterface $form_state): array {
    $actions = parent::actions($form, $form_state);
    $actions['cancel'] = [
      '#type' => 'link',
      '#title' => $this->t('Back to types'),
      '#url' => Url::fromRoute('entity.ps_diagnostic_type.collection'),
      '#attributes' => ['class' => ['button', 'button--secondary']],
    ];
    return $actions;
  }

  public function validateForm(array &$form, FormStateInterface $form_state): void {
    parent::validateForm($form, $form_state);

    $class_rows = $this->sortRowsByWeight($this->extractRowsFromFormState($form_state));

    $configured = [];
    foreach ($class_rows as $class_row) {
      $label = trim((string) ($class_row['label'] ?? ''));
      $color = trim((string) ($class_row['color'] ?? ''));
      $range_max_raw = $class_row['range_max'] ?? '';

      if ($label === '' && $color === '' && $range_max_raw === '') {
        continue;
      }

      $configured[] = [
        'label' => $label,
        'color' => $color,
        'range_max' => $range_max_raw,
      ];
    }

    $last_max = -1;
    $configured_total = count($configured);
    foreach ($configured as $index => $class_row) {
      $is_last = $index === ($configured_total - 1);
      $label = $class_row['label'];
      $color = $class_row['color'];
      $range_max_raw = $class_row['range_max'];

      if ($label === '') {
        $form_state->setErrorByName('classes][' . $index . '][label', $this->t('Class label is required when a class row is used.'));
      }

      if ($color === '' || !preg_match('/^#[0-9A-Fa-f]{6}$/', $color)) {
        $form_state->setErrorByName('classes][' . $index . '][color', $this->t('Color must be a valid hex value (example: #8BC34A).'));
      }

      if ($is_last) {
        if ($range_max_raw !== '' && $range_max_raw !== NULL) {
          $form_state->setErrorByName('classes][' . $index . '][range_max', $this->t('The last configured class must keep an empty range max.'));
        }
        continue;
      }

      if ($range_max_raw === '' || !is_numeric((string) $range_max_raw)) {
        $form_state->setErrorByName('classes][' . $index . '][range_max', $this->t('Range max is required for configured classes except the last one.'));
        continue;
      }

      $range_max = (int) $range_max_raw;
      if ($range_max <= $last_max) {
        $form_state->setErrorByName('classes][' . $index . '][range_max', $this->t('Class ranges must be strictly increasing.'));
      }
      $last_max = $range_max;
    }
  }

  public function save(array $form, FormStateInterface $form_state): int {
    $this->entity->set('unit', trim((string) $form_state->getValue('unit', '')));
    $this->entity->set('icon', trim((string) $form_state->getValue('icon', '')));

    $class_rows = $this->sortRowsByWeight($this->extractRowsFromFormState($form_state));
    $clean_classes = [];
    foreach ($class_rows as $class_row) {
      $label = trim((string) ($class_row['label'] ?? ''));
      $color = trim((string) ($class_row['color'] ?? ''));
      $range_max_raw = $class_row['range_max'] ?? '';
      if ($label === '' && $color === '' && $range_max_raw === '') {
        continue;
      }
      $clean_classes[] = [
        'label' => $label,
        'color' => $color,
        'range_max' => $range_max_raw,
      ];
    }

    $last_index = count($clean_classes) - 1;
    foreach ($clean_classes as $index => &$class_row) {
      $class_row['range_max'] = $index === $last_index ? 0 : (int) $class_row['range_max'];
    }
    unset($class_row);

    $this->entity->set('classes', $clean_classes);
    $form_state->set('classes_rows_state', $clean_classes);
    $form_state->set('classes_count', max(1, count($clean_classes)));

    $status = parent::save($form, $form_state);
    $this->messenger()->addStatus($this->t('Diagnostic type %label has been saved.', ['%label' => $this->entity->label()]));
    $form_state->setRedirectUrl($this->entity->toUrl('collection'));
    return $status;
  }

  /**
   * @param array<int, array<string, mixed>> $rows
   *
   * @return array<int, array<string, mixed>>
   */
  private function buildDefaultRows(array $rows): array {
    $normalized = [];
    foreach (array_values($rows) as $index => $row) {
      $range_max = $row['range_max'] ?? '';
      if ((int) $range_max === 0) {
        $range_max = '';
      }
      $normalized[] = [
        'label' => trim((string) ($row['label'] ?? '')),
        'color' => trim((string) ($row['color'] ?? '#5DBB63')),
        'range_max' => $range_max,
        'weight' => (int) ($row['weight'] ?? $index),
        'remove' => 0,
      ];
    }

    if ($normalized === []) {
      $normalized[] = [
        'label' => '',
        'color' => '#5DBB63',
        'range_max' => '',
        'weight' => 0,
        'remove' => 0,
      ];
    }

    $normalized = $this->sortRowsByWeight($normalized);
    $this->reindexRowWeights($normalized);

    return $normalized;
  }

  /**
   * @param array<int, array<string, mixed>> $rows
   *
   * @return array<int, array<string, mixed>>
   */
  private function normalizeRowsForForm(array $rows, int $row_count): array {
    $rows = $this->sortRowsByWeight($rows);
    $this->reindexRowWeights($rows);

    for ($i = count($rows); $i < $row_count; $i++) {
      $rows[] = [
        'label' => '',
        'color' => '#5DBB63',
        'range_max' => '',
        'weight' => $i,
        'remove' => 0,
      ];
    }

    return $rows;
  }

  /**
   * @return array<int, array<string, mixed>>
   */
  private function extractRowsFromFormState(FormStateInterface $form_state): array {
    $rows = (array) $form_state->getValue('classes', []);
    if ($rows === []) {
      $rows = (array) $form_state->get('classes_rows_state', []);
    }

    $normalized = [];
    foreach ($rows as $index => $row) {
      $normalized[] = [
        'label' => trim((string) ($row['label'] ?? '')),
        'color' => trim((string) ($row['color'] ?? '#5DBB63')),
        'range_max' => isset($row['range_max']) ? trim((string) $row['range_max']) : '',
        'weight' => isset($row['weight']) ? (int) $row['weight'] : (int) $index,
        'remove' => !empty($row['remove']) ? 1 : 0,
      ];
    }

    if ($normalized === []) {
      $normalized[] = [
        'label' => '',
        'color' => '#5DBB63',
        'range_max' => '',
        'weight' => 0,
        'remove' => 0,
      ];
    }

    $normalized = $this->sortRowsByWeight($normalized);
    $this->reindexRowWeights($normalized);
    $form_state->set('classes_rows_state', $normalized);
    $form_state->set('classes_count', max(1, count($normalized)));

    return $normalized;
  }

  /**
   * @param array<int, array<string, mixed>> $rows
   */
  private function reindexRowWeights(array &$rows): void {
    foreach (array_values($rows) as $index => $row) {
      $rows[$index] = $row;
      $rows[$index]['weight'] = $index;
    }
  }

  /**
   * @param array<int, array<string, mixed>> $rows
   *
   * @return array<int, array<string, mixed>>
   */
  private function sortRowsByWeight(array $rows): array {
    usort($rows, static function (array $a, array $b): int {
      return ((int) ($a['weight'] ?? 0)) <=> ((int) ($b['weight'] ?? 0));
    });

    return array_values($rows);
  }

  /**
   * @param array<int, array<string, mixed>> $rows
   */
  private function findLastConfiguredRow(array $rows): int {
    $last = -1;
    foreach ($rows as $index => $row) {
      $label = trim((string) ($row['label'] ?? ''));
      $color = trim((string) ($row['color'] ?? ''));
      $range_max = trim((string) ($row['range_max'] ?? ''));
      if ($label !== '' || $color !== '' || $range_max !== '') {
        $last = $index;
      }
    }

    return $last;
  }

}
