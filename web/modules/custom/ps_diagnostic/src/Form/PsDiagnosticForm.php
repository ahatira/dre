<?php

declare(strict_types=1);

namespace Drupal\ps_diagnostic\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity\EntityStorageException;
use Drupal\ps_diagnostic\Service\DiagnosticClassSuggesterInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form handler for diagnostic type add/edit forms.
 *
 * Simple form using standard Drupal patterns with AJAX and smart suggestions.
 */
class PsDiagnosticForm extends EntityForm {

  /**
   * The diagnostic class suggester service.
   *
   * @var \Drupal\ps_diagnostic\Service\DiagnosticClassSuggesterInterface
   */
  protected DiagnosticClassSuggesterInterface $classSuggester;

  /**
   * Constructs a PsDiagnosticForm object.
   *
   * @param \Drupal\ps_diagnostic\Service\DiagnosticClassSuggesterInterface $classSuggester
   *   The class suggester service.
   */
  public function __construct(DiagnosticClassSuggesterInterface $classSuggester) {
    $this->classSuggester = $classSuggester;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    return new static(
      $container->get('ps_diagnostic.class_suggester')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state): array {
    $form = parent::form($form, $form_state);

    /** @var \Drupal\ps_diagnostic\Entity\PsDiagnosticInterface $diagnostic */
    $diagnostic = $this->entity;

    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $diagnostic->label(),
      '#description' => $this->t('Name of the diagnostic type (e.g., DPE, GES).'),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $diagnostic->id(),
      '#machine_name' => [
        'exists' => '\Drupal\ps_diagnostic\Entity\PsDiagnostic::load',
      ],
      '#disabled' => !$diagnostic->isNew(),
    ];

    $form['unit'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Unit'),
      '#maxlength' => 50,
      '#default_value' => $diagnostic->getUnit(),
      '#description' => $this->t('Unit of measurement (e.g., kWh/m²/an).'),
    ];

    $form['icon'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Icon'),
      '#maxlength' => 255,
      '#default_value' => $diagnostic->getIcon(),
      '#description' => $this->t('Icon class or path.'),
    ];

    // Classes table.
    $classes = $diagnostic->getClasses();

    // Initialize with default classes for new entities.
    if ($diagnostic->isNew() && empty($classes)) {
      $classes = $this->getDefaultClasses();
    }

    // Get number of classes from form state (for add more).
    $num_classes = $form_state->get('num_classes');
    if ($num_classes === NULL) {
      $num_classes = max(count($classes), 1);
      $form_state->set('num_classes', $num_classes);
    }

    // Check if we have stored form values from AJAX rebuild.
    $stored_form_values = $form_state->get('classes_form_values');
    if (!empty($stored_form_values)) {
      // Use the stored form values.
      $classes = $this->buildClassesFromFormValues($stored_form_values);
    }

    $form['classes'] = [
      '#type' => 'container',
      '#attributes' => ['id' => 'classes-wrapper'],
      '#tree' => TRUE,
    ];

    $form['classes']['fieldset'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Energy classes'),
      '#description' => $this->t('Define the classification scale. Only the last class should have an empty max value.'),
    ];

    $form['classes']['fieldset']['table'] = [
      '#type' => 'table',
      '#header' => [
        $this->t('Label'),
        $this->t('Color'),
        $this->t('Range max'),
      ],
      '#empty' => $this->t('No classes defined.'),
    ];

    // Rebuild classes array indexed by delta.
    $classes_indexed = array_values($classes);

    // If adding a new class, get suggestion from service.
    if ($num_classes > count($classes_indexed)) {
      $suggestion = $this->classSuggester->suggestNextClass($classes);
      $classes_indexed[] = $suggestion;
    }

    for ($i = 0; $i < $num_classes; $i++) {
      $class = $classes_indexed[$i] ?? ['label' => '', 'color' => '#000000', 'range_max' => NULL];

      $form['classes']['fieldset']['table'][$i]['label'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Label'),
        '#title_display' => 'invisible',
        '#size' => 20,
        '#default_value' => $class['label'] ?? '',
        '#required' => TRUE,
        '#placeholder' => $this->t('e.g., A, B, C'),
      ];

      $form['classes']['fieldset']['table'][$i]['color'] = [
        '#type' => 'color',
        '#title' => $this->t('Color'),
        '#title_display' => 'invisible',
        '#default_value' => $class['color'] ?? '#000000',
        '#required' => TRUE,
      ];

      $form['classes']['fieldset']['table'][$i]['range_max'] = [
        '#type' => 'number',
        '#title' => $this->t('Range max'),
        '#title_display' => 'invisible',
        '#min' => 0,
        '#step' => 1,
        '#default_value' => $class['range_max'] ?? NULL,
        '#description' => $i === ($num_classes - 1) ? $this->t('Leave empty for the last class') : '',
      ];
    }

    $form['classes']['fieldset']['actions'] = [
      '#type' => 'actions',
    ];

    $form['classes']['fieldset']['actions']['add'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add class'),
      '#submit' => ['::addClassSubmit'],
      '#ajax' => [
        'callback' => '::ajaxCallback',
        'wrapper' => 'classes-wrapper',
        'effect' => 'fade',
      ],
      '#limit_validation_errors' => [],
    ];

    if ($num_classes > 1) {
      $form['classes']['fieldset']['actions']['remove'] = [
        '#type' => 'submit',
        '#value' => $this->t('Remove last class'),
        '#submit' => ['::removeClassSubmit'],
        '#ajax' => [
          'callback' => '::ajaxCallback',
          'wrapper' => 'classes-wrapper',
          'effect' => 'fade',
        ],
        '#limit_validation_errors' => [],
      ];
    }

    return $form;
  }

  /**
   * Submit handler to add a class.
   */
  public function addClassSubmit(array &$form, FormStateInterface $form_state): void {
    // Save current form values BEFORE incrementing.
    // For AJAX, we need getUserInput() not getValue().
    $user_input = $form_state->getUserInput();
    $form_values = $user_input['classes']['fieldset']['table'] ?? [];

    if (!empty($form_values)) {
      $form_state->set('classes_form_values', $form_values);
    }

    $num_classes = $form_state->get('num_classes');
    $form_state->set('num_classes', $num_classes + 1);
    $form_state->setRebuild();
  }

  /**
   * Submit handler to remove the last class.
   */
  public function removeClassSubmit(array &$form, FormStateInterface $form_state): void {
    // Save current form values BEFORE decrementing.
    // For AJAX, we need getUserInput() not getValue().
    $user_input = $form_state->getUserInput();
    $form_values = $user_input['classes']['fieldset']['table'] ?? [];

    if (!empty($form_values)) {
      $form_state->set('classes_form_values', $form_values);
    }

    $num_classes = $form_state->get('num_classes');
    if ($num_classes > 1) {
      $form_state->set('num_classes', $num_classes - 1);
    }
    $form_state->setRebuild();
  }

  /**
   * AJAX callback for add/remove class operations.
   *
   * @param array $form
   *   The form array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current form state.
   *
   * @return array
   *   The classes wrapper element to replace via AJAX.
   */
  public function ajaxCallback(array &$form, FormStateInterface $form_state): array {
    // Store current values in form_state for next rebuild.
    $form_values = $form_state->getValue(['classes', 'fieldset', 'table']);
    if (!empty($form_values)) {
      $form_state->set('classes_form_values', $form_values);
    }
    return $form['classes'];
  }

  /**
   * Returns default classes for new diagnostics (A-G energy scale).
   */
  protected function getDefaultClasses(): array {
    return [
      'a' => ['label' => 'A', 'color' => '#00A651', 'range_max' => 70],
      'b' => ['label' => 'B', 'color' => '#8DC63F', 'range_max' => 110],
      'c' => ['label' => 'C', 'color' => '#FFF200', 'range_max' => 180],
      'd' => ['label' => 'D', 'color' => '#F7941D', 'range_max' => 250],
      'e' => ['label' => 'E', 'color' => '#ED1C24', 'range_max' => 330],
      'f' => ['label' => 'F', 'color' => '#C1272D', 'range_max' => 420],
      'g' => ['label' => 'G', 'color' => '#A10D0D', 'range_max' => NULL],
    ];
  }

  /**
   * Builds classes array from form values.
   *
   * @param array $formValues
   *   Form values from table.
   *
   * @return array
   *   Classes array keyed by code.
   */
  protected function buildClassesFromFormValues(array $formValues): array {
    $classes = [];

    foreach ($formValues as $row) {
      $label = trim($row['label'] ?? '');

      if ($label === '') {
        continue;
      }

      // Auto-generate code from label (lowercase).
      $code = trim(strtolower($label));

      $range_max = $row['range_max'] ?? NULL;
      if ($range_max === '' || $range_max === NULL) {
        $range_max = NULL;
      }
      else {
        $range_max = (int) $range_max;
      }

      $classes[$code] = [
        'label' => $label,
        'color' => strtoupper($row['color'] ?? '#000000'),
        'range_max' => $range_max,
      ];
    }

    return $classes;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state): void {
    parent::validateForm($form, $form_state);

    $classes_data = $form_state->getValue(['classes', 'fieldset', 'table']) ?? [];

    if (empty($classes_data)) {
      $form_state->setErrorByName('classes', $this->t('At least one class must be defined.'));
      return;
    }

    // Build classes array and validate.
    $classes = [];
    $last_index = NULL;

    foreach ($classes_data as $i => $row) {
      $label = trim($row['label'] ?? '');

      if ($label === '') {
        continue;
      }

      // Validate allowed characters: A-Z, +, -.
      if (!preg_match('/^[A-Z+-]+$/i', $label)) {
        $form_state->setErrorByName("classes][fieldset][table][$i][label",
          $this->t('Label can only contain letters A-Z and characters "+" or "-".'));
        continue;
      }

      // Auto-generate code from label (lowercase).
      $code = trim(strtolower($label));

      $range_max = $row['range_max'] ?? NULL;
      if ($range_max === '' || $range_max === NULL) {
        $range_max = NULL;
      }
      else {
        $range_max = (int) $range_max;
        if ($range_max < 0) {
          $form_state->setErrorByName("classes][fieldset][table][$i][range_max",
            $this->t('Range max must be a positive number.'));
        }
      }

      $classes[$code] = [
        'label' => $label,
        'color' => strtoupper($row['color']),
        'range_max' => $range_max,
      ];

      $last_index = $code;
    }

    if (empty($classes)) {
      $form_state->setErrorByName('classes', $this->t('At least one valid class must be defined.'));
      return;
    }

    // Validate that only the last class has empty range_max.
    $empty_count = 0;
    foreach ($classes as $code => $config) {
      if ($config['range_max'] === NULL) {
        $empty_count++;
        if ($code !== $last_index) {
          $form_state->setErrorByName('classes',
            $this->t('Only the last class can have an empty range max value.'));
          return;
        }
      }
    }

    if ($empty_count === 0) {
      $form_state->setErrorByName('classes',
        $this->t('The last class must have an empty range max value.'));
    }

    // Store validated classes in form state.
    $form_state->set('validated_classes', $classes);
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state): int {
    try {
      /** @var \Drupal\ps_diagnostic\Entity\PsDiagnosticInterface $diagnostic */
      $diagnostic = $this->entity;

      // Get validated classes from form state.
      $classes = $form_state->get('validated_classes') ?? [];
      $diagnostic->setClasses($classes);

      $result = parent::save($form, $form_state);

      $message_args = ['%label' => $diagnostic->label()];
      $message = $result === SAVED_NEW
        ? $this->t('Created diagnostic type %label.', $message_args)
        : $this->t('Updated diagnostic type %label.', $message_args);

      $this->messenger()->addStatus($message);
      $form_state->setRedirectUrl($diagnostic->toUrl('collection'));

      return $result;
    }
    catch (EntityStorageException $e) {
      $this->messenger()->addError(
        $this->t('Error saving diagnostic type: @error', ['@error' => $e->getMessage()])
      );
      $form_state->setRebuild();
      return SAVED_UPDATED;
    }
  }

}
