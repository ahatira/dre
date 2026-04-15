<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Form;

use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\ps_offer\Entity\OfferReferenceSegmentInterface;
use Drupal\ps_offer\Service\OfferReferenceBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form handler for Offer Reference Segment add/edit.
 */
final class OfferReferenceSegmentForm extends EntityForm {

  /**
   * Constructs the form.
   */
  public function __construct(
    protected EntityFieldManagerInterface $entityFieldManager,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    return new static(
      $container->get('entity_field.manager'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state): array {
    $form = parent::form($form, $form_state);

    /** @var \Drupal\ps_offer\Entity\OfferReferenceSegmentInterface $segment */
    $segment = $this->entity;

    $source_options = $this->getSourceFieldOptions(FALSE);
    $date_source_options = $this->getSourceFieldOptions(TRUE);

    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#default_value' => $segment->label(),
      '#maxlength' => 255,
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $segment->id(),
      '#machine_name' => [
        'exists' => '\\Drupal\\ps_offer\\Entity\\OfferReferenceSegment::load',
      ],
      '#disabled' => !$segment->isNew(),
    ];

    $form['enabled'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enabled'),
      '#default_value' => $segment->isEnabled(),
    ];

    $form['weight'] = [
      '#type' => 'number',
      '#title' => $this->t('Weight'),
      '#default_value' => $segment->getWeight(),
      '#description' => $this->t('Lower values are processed first.'),
      // Weight is managed via drag-and-drop on the listing page.
      '#access' => FALSE,
    ];

    $form['segment_type'] = [
      '#type' => 'select',
      '#title' => $this->t('Mapping type'),
      '#options' => [
        'auto' => $this->t('auto (increment)'),
        'custom' => $this->t('custom (mapping table)'),
        'start' => $this->t('start (substring from position)'),
        'date' => $this->t('date (preconfigured formats)'),
        'static' => $this->t('static (fixed value)'),
      ],
      '#default_value' => $segment->getSegmentType(),
      '#required' => TRUE,
    ];

    $form['source_field'] = [
      '#type' => 'select',
      '#title' => $this->t('Source Offer field'),
      '#options' => $source_options,
      '#empty_option' => $this->t('- None -'),
      '#default_value' => $segment->getSourceField(),
      '#ajax' => [
        'callback' => '::ajaxRefreshCustomMap',
        'wrapper' => 'custom-map-wrapper',
        'event' => 'change',
      ],
      '#states' => [
        'visible' => [
          [':input[name="segment_type"]' => ['value' => 'custom']],
          'or',
          [':input[name="segment_type"]' => ['value' => 'start']],
        ],
      ],
    ];

    $form['length'] = [
      '#type' => 'number',
      '#title' => $this->t('Segment length'),
      '#default_value' => $segment->getLength(),
      '#min' => 1,
      '#max' => 32,
      '#required' => TRUE,
    ];

    $form['static_value'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Static value'),
      '#default_value' => $segment->getStaticValue(),
      '#maxlength' => 64,
      '#states' => [
        'visible' => [
          ':input[name="segment_type"]' => ['value' => 'static'],
        ],
      ],
    ];

    // Resolve the source field: form state value (AJAX rebuild) or entity
    // saved value (initial page load).
    $source_field_value = (string) ($form_state->getValue('source_field') ?? $segment->getSourceField());
    $allowed_values = $this->getFieldAllowedValues($source_field_value);

    // Pre-populate table defaults from the entity's saved map. On AJAX
    // rebuilds Drupal automatically restores submitted values for matching row
    // keys via form_state user input; this default applies for new keys.
    $existing_map = [];
    try {
      $existing_map = $this->parseCustomMapText($segment->getCustomMapText());
    }
    catch (\InvalidArgumentException) {}

    $form['custom_map_wrapper'] = [
      '#type' => 'container',
      '#attributes' => ['id' => 'custom-map-wrapper'],
      '#states' => [
        'visible' => [
          ':input[name="segment_type"]' => ['value' => 'custom'],
        ],
      ],
    ];

    if ($allowed_values !== []) {
      // Field exposes known allowed values (list_* or ps_dictionary): render a
      // table so each source key can be mapped to a target code without syntax.
      $form['custom_map_wrapper']['custom_map_table'] = [
        '#type' => 'table',
        '#caption' => $this->t('Map each source value to a target code. Leave empty to fall back to the first character.'),
        '#header' => [
          $this->t('Source key'),
          $this->t('Source label'),
          $this->t('Target code'),
        ],
        '#attributes' => ['class' => ['custom-map-table']],
      ];

      foreach ($allowed_values as $raw_key => $label) {
        $key = strtoupper((string) $raw_key);
        $form['custom_map_wrapper']['custom_map_table'][$key] = [
          // Render-only cells: arrays with #plain_text are valid in FormBuilder
          // table context (no non-# keys, so no recursion error).
          'source_key' => ['#plain_text' => $key],
          'source_label' => ['#plain_text' => (string) $label],
          'value' => [
            '#type' => 'textfield',
            '#title' => $this->t('Target code for @key', ['@key' => $key]),
            '#title_display' => 'invisible',
            '#default_value' => $existing_map[$key] ?? '',
            '#maxlength' => 64,
            '#size' => 10,
            '#attributes' => ['placeholder' => $this->t('fallback: 1st char')],
          ],
        ];
      }
    }
    else {
      // Field has no introspectable allowed values: keep the textarea fallback.
      $form['custom_map_wrapper']['custom_map_text'] = [
        '#type' => 'textarea',
        '#title' => $this->t('Custom map (one KEY=VALUE per line)'),
        '#default_value' => $segment->getCustomMapText(),
        '#description' => $this->t('If a key is missing, fallback uses the first character.'),
        '#rows' => 8,
      ];
    }

    $form['start_index'] = [
      '#type' => 'number',
      '#title' => $this->t('Start position (1-based)'),
      '#default_value' => $segment->getStartIndex(),
      '#min' => 1,
      '#states' => [
        'visible' => [
          ':input[name="segment_type"]' => ['value' => 'start'],
        ],
      ],
    ];

    $form['date_source_field'] = [
      '#type' => 'select',
      '#title' => $this->t('Date source field'),
      '#options' => $date_source_options,
      '#default_value' => $segment->getDateSourceField(),
      '#states' => [
        'visible' => [
          ':input[name="segment_type"]' => ['value' => 'date'],
        ],
      ],
    ];

    $form['date_format'] = [
      '#type' => 'select',
      '#title' => $this->t('Date format'),
      '#options' => [
        'YY' => 'YY',
        'YYYY' => 'YYYY',
        'MM' => 'MM',
        'YYMM' => 'YYMM',
        'YYMMDD' => 'YYMMDD',
      ],
      '#default_value' => $segment->getDateFormat(),
      '#states' => [
        'visible' => [
          ':input[name="segment_type"]' => ['value' => 'date'],
        ],
      ],
    ];

    $form['auto_start'] = [
      '#type' => 'number',
      '#title' => $this->t('Auto start value'),
      '#default_value' => $segment->getAutoStart(),
      '#min' => 1,
      '#states' => [
        'visible' => [
          ':input[name="segment_type"]' => ['value' => 'auto'],
        ],
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state): void {
    parent::validateForm($form, $form_state);

    $type = (string) $form_state->getValue('segment_type');
    $source_field = trim((string) $form_state->getValue('source_field'));
    $start_index = (int) $form_state->getValue('start_index');
    $date_format = (string) $form_state->getValue('date_format');

    if (in_array($type, ['custom', 'start'], TRUE) && $source_field === '') {
      $form_state->setErrorByName('source_field', $this->t('A source field is required for this mapping type.'));
    }

    if ($type === 'start' && $start_index < 1) {
      $form_state->setErrorByName('start_index', $this->t('Start position must be >= 1.'));
    }

    if ($type === 'date' && !isset(OfferReferenceBuilder::DATE_FORMATS[$date_format])) {
      $form_state->setErrorByName('date_format', $this->t('Invalid date format.'));
    }

    if ($type === 'custom') {
      // In table mode the field supplies known allowed values; individual cell
      // constraints already apply, so skip textarea syntax validation.
      if ($form_state->getValue('custom_map_table') === NULL) {
        try {
          $this->parseCustomMapText((string) $form_state->getValue('custom_map_text'));
        }
        catch (\InvalidArgumentException $e) {
          $form_state->setErrorByName('custom_map_text', $this->t('Custom map is invalid: @message', ['@message' => $e->getMessage()]));
        }
      }
    }
  }

  /**
   * Serializes the custom map table back to KEY=VALUE text before entity save.
   *
   * When the source field exposes known allowed values (list_* or ps_dictionary),
   * the form renders a table instead of a textarea. This method converts the
   * submitted table row values back to the KEY=VALUE string format expected by
   * the entity, then delegates to parent which calls copyFormValuesToEntity().
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $table = $form_state->getValue('custom_map_table');
    if (is_array($table)) {
      $lines = [];
      foreach ($table as $key => $row_values) {
        $mapped = strtoupper(trim((string) ($row_values['value'] ?? '')));
        if ($mapped !== '') {
          $lines[] = strtoupper((string) $key) . '=' . $mapped;
        }
      }
      $form_state->setValue('custom_map_text', implode("\n", $lines));
      // Remove table values so copyFormValuesToEntity() does not attempt to
      // assign an unknown property on the config entity.
      $form_state->unsetValue('custom_map_table');
    }
    parent::submitForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state): int {
    /** @var \Drupal\ps_offer\Entity\OfferReferenceSegmentInterface $segment */
    $segment = $this->entity;

    $status = $segment->save();

    // SAVED_NEW (= 1) is defined in core/includes/common.inc at global scope.
    // IDE may flag it as unknown since .inc files are not indexed; it works at runtime.
    if ($status === SAVED_NEW) {
      $this->messenger()->addStatus($this->t('Created segment %label.', ['%label' => $segment->label()]));
    }
    else {
      $this->messenger()->addStatus($this->t('Saved segment %label.', ['%label' => $segment->label()]));
    }

    $form_state->setRedirect('entity.ps_offer_reference_segment.collection');
    return $status;
  }

  /**
   * Returns available node offer source fields.
   *
   * @param bool $date_only
   *   TRUE to return only date-capable fields.
   *
   * @return array<string, string>
   *   Field options.
   */
  protected function getSourceFieldOptions(bool $date_only): array {
    $definitions = $this->entityFieldManager->getFieldDefinitions('node', 'offer');
    $options = [];

    $options['publish_on'] = (string) $this->t('Published at (Scheduler: publish_on)');
    $options['created'] = (string) $this->t('Created date (base field)');

    foreach ($definitions as $field_name => $definition) {
      $type = $definition->getType();
      $is_date = in_array($type, ['datetime', 'timestamp', 'created', 'changed'], TRUE);
      if ($date_only && !$is_date) {
        continue;
      }

      if (!$date_only && in_array($field_name, ['external_id'], TRUE)) {
        continue;
      }

      $label = (string) $definition->getLabel();
      if ($label === '') {
        $label = $field_name;
      }

      $options[$field_name] = $label . ' (' . $field_name . ')';
    }

    ksort($options);
    return $options;
  }

  /**
   * Returns allowed values for a field when available.
   *
   * @return array<string, string>
   *   Allowed values keyed by storage value.
   */
  protected function getFieldAllowedValues(string $field_name): array {
    if ($field_name === '') {
      return [];
    }

    $definitions = $this->entityFieldManager->getFieldDefinitions('node', 'offer');
    if (!isset($definitions[$field_name])) {
      return [];
    }

    $storage = $definitions[$field_name]->getFieldStorageDefinition();
    if (!$storage instanceof FieldStorageDefinitionInterface) {
      return [];
    }

    $settings = $storage->getSettings();
    if (isset($settings['allowed_values']) && is_array($settings['allowed_values']) && $settings['allowed_values'] !== []) {
      return $settings['allowed_values'];
    }

    $callback = $settings['allowed_values_function'] ?? NULL;
    if (is_string($callback) && $callback !== '' && function_exists($callback)) {
      $cacheable = TRUE;
      $values = $callback($storage, NULL, $cacheable);
      return is_array($values) ? $values : [];
    }

    if (function_exists('options_allowed_values')) {
      $values = options_allowed_values($storage);
      return is_array($values) ? $values : [];
    }

    return [];
  }

  /**
   * Parses custom mapping textarea into a map array.
   *
   * @return array<string, string>
   *   Mapping array.
   */
  protected function parseCustomMapText(string $text): array {
    $map = [];
    $lines = preg_split('/\r\n|\r|\n/', $text) ?: [];

    foreach ($lines as $line) {
      $line = trim($line);
      if ($line === '') {
        continue;
      }

      if (!str_contains($line, '=')) {
        throw new \InvalidArgumentException('Expected KEY=VALUE format.');
      }

      [$key, $value] = explode('=', $line, 2);
      $key = strtoupper(trim($key));
      $value = strtoupper(trim($value));

      if ($key === '' || $value === '') {
        throw new \InvalidArgumentException('KEY and VALUE must both be non-empty.');
      }

      $map[$key] = $value;
    }

    return $map;
  }

  /**
   * AJAX callback: refreshes the custom map widget when source_field changes.
   *
   * Returns the #custom-map-wrapper container so JS replaces it in-place.
   * The rebuilt container contains either a table (list_* or ps_dictionary
   * fields with introspectable allowed values) or the textarea fallback.
   *
   * @param array $form
   *   The current form structure.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current form state.
   *
   * @return array
   *   The render array for the custom map wrapper.
   */
  public function ajaxRefreshCustomMap(array &$form, FormStateInterface $form_state): array {
    return $form['custom_map_wrapper'];
  }

}
