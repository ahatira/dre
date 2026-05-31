<?php

namespace Drupal\ps_feature\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\ps_feature\Service\FeatureTypeManager;

/**
 * Form handler for the Feature Definition add and edit forms.
 */
class FeatureDefinitionForm extends EntityForm {

  /**
   * The feature type manager.
   *
   * @var \Drupal\ps_feature\Service\FeatureTypeManager
   */
  protected FeatureTypeManager $featureTypeManager;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a FeatureDefinitionForm.
   *
   * @param \Drupal\ps_feature\Service\FeatureTypeManager $feature_type_manager
   *   The feature type manager.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(FeatureTypeManager $feature_type_manager, EntityTypeManagerInterface $entity_type_manager) {
    $this->featureTypeManager = $feature_type_manager;
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    return new static(
      $container->get('ps_feature.type_manager'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state): array {
    $form = parent::form($form, $form_state);

    /** @var \Drupal\ps_feature\Entity\FeatureDefinition $feature_definition */
    $feature_definition = $this->entity;

    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Feature name'),
      '#maxlength' => 255,
      '#default_value' => $feature_definition->label(),
      '#description' => $this->t('The name displayed for this feature (e.g., Total surface, Parking included).'),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $feature_definition->id(),
      '#machine_name' => [
        'exists' => '\Drupal\ps_feature\Entity\FeatureDefinition::load',
      ],
      '#disabled' => !$feature_definition->isNew(),
    ];

    $form['code'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Code'),
      '#maxlength' => 64,
      '#default_value' => $feature_definition->getCode(),
      '#description' => $this->t('Unique code for this feature within the group (for import/export). Example: total_area, parking_included'),
      '#required' => FALSE,
    ];

    $form['description'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Description'),
      '#default_value' => $feature_definition->getDescription(),
      '#description' => $this->t('Optional description documenting this feature usage.'),
      '#rows' => 3,
    ];

    $form['group'] = [
      '#type' => 'select',
      '#title' => $this->t('Feature group'),
      '#description' => $this->t('Select the group this feature belongs to.'),
      '#options' => $this->getFeatureGroupOptions(),
      '#default_value' => $feature_definition->getGroup(),
      '#required' => TRUE,
    ];

    $form['type_driver'] = [
      '#type' => 'select',
      '#title' => $this->t('Data type'),
      '#description' => $this->t('The value type this feature can store (numeric, text, yes/no, etc.).'),
      '#options' => $this->featureTypeManager->getAllTypes(),
      '#default_value' => $feature_definition->getTypeDriver(),
      '#required' => TRUE,
      '#ajax' => [
        'callback' => '::updatePayloadDefaultsFields',
        'wrapper' => 'payload-defaults-wrapper',
        'event' => 'change',
      ],
    ];

    $form['required_asset_types'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Required asset types'),
      '#description' => $this->t('Select asset types where this feature is required. Leave empty to keep it optional for all.'),
      '#options' => $this->getAssetTypeOptions(),
      '#default_value' => $feature_definition->getRequiredAssetTypes(),
    ];

    $form['weight'] = [
      '#type' => 'number',
      '#title' => $this->t('Weight'),
      '#description' => $this->t('Display order of the feature. Features with lower weight are shown first.'),
      '#default_value' => $feature_definition->getWeight(),
    ];

    $form['status'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Active'),
      '#description' => $this->t('Inactive features are not available in forms.'),
      '#default_value' => $feature_definition->status(),
    ];

    $form['expose_as_filter'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Expose as search filter'),
      '#description' => $this->t('When checked, this feature will be available as a filter in the search page.'),
      '#default_value' => $feature_definition->isExposeAsFilter(),
    ];

    // Dynamic payload defaults fields based on type.
    $form['payload_defaults_container'] = [
      '#type' => 'container',
      '#prefix' => '<div id="payload-defaults-wrapper">',
      '#suffix' => '</div>',
      '#tree' => TRUE,
    ];

    $selected_type = $form_state->getValue('type_driver') ?? $feature_definition->getTypeDriver();
    $this->buildPayloadDefaultsFields($form['payload_defaults_container'], $selected_type, $feature_definition->getPayloadDefaults());

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state): void {
    parent::validateForm($form, $form_state);

    // Validate code uniqueness within the group.
    $code = trim($form_state->getValue('code') ?? '');
    if (!empty($code)) {
      $group = $form_state->getValue('group');
      /** @var \Drupal\ps_feature\Entity\FeatureDefinition $entity */
      $entity = $this->entity;
      
      // Check if another feature in the same group has this code.
      $storage = \Drupal::entityTypeManager()->getStorage('fb_feature_definition');
      $query = $storage->getQuery()
        ->condition('group', $group)
        ->condition('code', $code)
        ->accessCheck(FALSE);
      
      // Exclude current entity if editing.
      if (!$entity->isNew()) {
        $query->condition('id', $entity->id(), '<>');
      }
      
      $existing = $query->execute();
      if (!empty($existing)) {
        $form_state->setErrorByName('code', 
          $this->t('Code "@code" is already used by another feature in this group.', ['@code' => $code]));
      }
    }

    // Filter out unchecked asset types.
    $asset_types = array_filter($form_state->getValue('required_asset_types'));
    $form_state->setValue('required_asset_types', array_values($asset_types));

    // Build payload_defaults from individual fields.
    $type = $form_state->getValue('type_driver');
    $container_values = $form_state->getValue('payload_defaults_container') ?? [];
    $payload_defaults = [];

    switch ($type) {
      case 'numeric':
      case 'range':
        if (!empty($container_values['unit'])) {
          $payload_defaults['unit'] = $container_values['unit'];
        }
        if (isset($container_values['min']) && $container_values['min'] !== '') {
          $payload_defaults['min'] = (float) $container_values['min'];
        }
        if (isset($container_values['max']) && $container_values['max'] !== '') {
          $payload_defaults['max'] = (float) $container_values['max'];
        }
        if ($type === 'numeric' && isset($container_values['decimals'])) {
          $payload_defaults['decimals'] = (int) $container_values['decimals'];
        }
        break;

      case 'text':
        if (isset($container_values['max_length'])) {
          $payload_defaults['max_length'] = (int) $container_values['max_length'];
        }
        if (isset($container_values['rows'])) {
          $payload_defaults['rows'] = (int) $container_values['rows'];
        }
        break;

      case 'dictionary':
        if (!empty($container_values['dictionary_id'])) {
          $payload_defaults['dictionary_id'] = $container_values['dictionary_id'];
        }
        else {
          $form_state->setErrorByName('payload_defaults_container][dictionary_id', 
            $this->t('Dictionary identifier is required for this data type.'));
        }
        if (!empty($container_values['allow_custom'])) {
          $payload_defaults['allow_custom'] = TRUE;
        }
        break;

      case 'taxonomy':
        if (!empty($container_values['vocabulary_id'])) {
          $payload_defaults['vocabulary_id'] = $container_values['vocabulary_id'];

          if ($this->entityTypeManager->hasDefinition('taxonomy_vocabulary')) {
            $vocabulary = $this->entityTypeManager
              ->getStorage('taxonomy_vocabulary')
              ->load($payload_defaults['vocabulary_id']);
            if (!$vocabulary) {
              $form_state->setErrorByName('payload_defaults_container][vocabulary_id',
                $this->t('Vocabulary "@vocabulary" does not exist.', ['@vocabulary' => $payload_defaults['vocabulary_id']]));
            }
          }
          else {
            $form_state->setErrorByName('payload_defaults_container][vocabulary_id',
              $this->t('Taxonomy vocabularies are unavailable because the Taxonomy module is not enabled.'));
          }
        }
        else {
          $form_state->setErrorByName('payload_defaults_container][vocabulary_id', 
            $this->t('Vocabulary is required for this data type.'));
        }
        $payload_defaults['multiple'] = !empty($container_values['multiple']);
        break;

      case 'list':
        if (!empty($container_values['options'])) {
          $payload_defaults['options'] = $this->parseListOptions($container_values['options']);
          if (empty($payload_defaults['options'])) {
            $form_state->setErrorByName('payload_defaults_container][options', 
              $this->t('Please provide at least one option.'));
          }
        }
        $payload_defaults['multiple'] = !empty($container_values['multiple']);
        break;

      case 'date':
        $payload_defaults['format'] = $container_values['format'] ?? 'Y-m-d';
        $payload_defaults['include_time'] = !empty($container_values['include_time']);
        if ($payload_defaults['include_time']) {
          $payload_defaults['time_format'] = $container_values['time_format'] ?? 'H:i';
        }
        break;
    }

    $form_state->setValue('payload_defaults', $payload_defaults);
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state): int {
    /** @var \Drupal\ps_feature\Entity\FeatureDefinition $feature_definition */
    $feature_definition = $this->entity;
    
    $result = parent::save($form, $form_state);

    $message_args = ['%label' => $feature_definition->label()];
    $message = $result == SAVED_NEW
      ? $this->t('Feature definition %label has been created.', $message_args)
      : $this->t('Feature definition %label has been updated.', $message_args);
    $this->messenger()->addStatus($message);

    $form_state->setRedirectUrl($feature_definition->toUrl('collection'));
    return $result;
  }

  /**
   * AJAX callback for type_driver field.
   */
  public function updatePayloadDefaultsFields(array &$form, FormStateInterface $form_state): array {
    return $form['payload_defaults_container'];
  }

  /**
   * Builds payload defaults fields based on the selected type.
   *
   * @param array &$container
   *   The container form element.
   * @param string|null $type
   *   The selected type driver.
   * @param array $defaults
   *   Existing default values.
   */
  protected function buildPayloadDefaultsFields(array &$container, ?string $type, array $defaults): void {
    if (empty($type)) {
      $container['empty'] = [
        '#markup' => '<p>' . $this->t('Select a data type to configure default values.') . '</p>',
      ];
      return;
    }

    switch ($type) {
      case 'numeric':
        $container['unit'] = [
          '#type' => 'textfield',
          '#title' => $this->t('Unit'),
          '#description' => $this->t('Measurement unit (e.g., m2, EUR, kg).'),
          '#default_value' => $defaults['unit'] ?? '',
          '#size' => 20,
        ];
        $container['min'] = [
          '#type' => 'number',
          '#title' => $this->t('Minimum value'),
          '#description' => $this->t('Allowed minimum value (optional).'),
          '#default_value' => $defaults['min'] ?? '',
          '#step' => 'any',
        ];
        $container['max'] = [
          '#type' => 'number',
          '#title' => $this->t('Maximum value'),
          '#description' => $this->t('Allowed maximum value (optional).'),
          '#default_value' => $defaults['max'] ?? '',
          '#step' => 'any',
        ];
        $container['decimals'] = [
          '#type' => 'number',
          '#title' => $this->t('Decimal places'),
          '#description' => $this->t('Number of decimal places to display (default: 2).'),
          '#default_value' => $defaults['decimals'] ?? 2,
          '#min' => 0,
          '#max' => 10,
        ];
        break;

      case 'range':
        $container['unit'] = [
          '#type' => 'textfield',
          '#title' => $this->t('Unit'),
          '#description' => $this->t('Measurement unit (e.g., EUR, m2).'),
          '#default_value' => $defaults['unit'] ?? '',
          '#size' => 20,
        ];
        $container['min'] = [
          '#type' => 'number',
          '#title' => $this->t('Suggested minimum value'),
          '#description' => $this->t('Suggested minimum value (optional).'),
          '#default_value' => $defaults['min'] ?? '',
          '#step' => 'any',
        ];
        $container['max'] = [
          '#type' => 'number',
          '#title' => $this->t('Suggested maximum value'),
          '#description' => $this->t('Suggested maximum value (optional).'),
          '#default_value' => $defaults['max'] ?? '',
          '#step' => 'any',
        ];
        break;

      case 'text':
        $container['max_length'] = [
          '#type' => 'number',
          '#title' => $this->t('Maximum length'),
          '#description' => $this->t('Maximum number of allowed characters.'),
          '#default_value' => $defaults['max_length'] ?? 255,
          '#min' => 1,
          '#max' => 65535,
        ];
        $container['rows'] = [
          '#type' => 'number',
          '#title' => $this->t('Number of rows'),
          '#description' => $this->t('Input field height (1 = single-line field, >1 = text area).'),
          '#default_value' => $defaults['rows'] ?? 1,
          '#min' => 1,
          '#max' => 20,
        ];
        break;

      case 'dictionary':
        $dictionary_options = $this->getDictionaryOptions();
        if (empty($dictionary_options)) {
          $container['no_dictionaries'] = [
            '#markup' => '<p class="messages messages--warning">' . $this->t('No dictionary available. <a href="@url">Create a dictionary</a>.', ['@url' => '/admin/ps/structure/dictionary']) . '</p>',
          ];
        }
        else {
          $container['dictionary_id'] = [
            '#type' => 'select',
            '#title' => $this->t('Dictionary'),
            '#description' => $this->t('Select the dictionary to use for this feature.'),
            '#options' => $dictionary_options,
            '#default_value' => $defaults['dictionary_id'] ?? '',
            '#required' => TRUE,
            '#empty_option' => $this->t('- Select -'),
          ];
          $container['allow_custom'] = [
            '#type' => 'checkbox',
            '#title' => $this->t('Allow custom values'),
            '#description' => $this->t('Allows entering a value not present in the dictionary.'),
            '#default_value' => $defaults['allow_custom'] ?? FALSE,
          ];
        }
        break;

      case 'taxonomy':
        $vocabulary_options = $this->getVocabularyOptions();
        if (empty($vocabulary_options)) {
          $container['no_vocabularies'] = [
            '#markup' => '<p class="messages messages--warning">' . $this->t('No taxonomy vocabulary available. <a href="@url">Create a vocabulary</a>.', ['@url' => '/admin/structure/taxonomy']) . '</p>',
          ];
        }
        else {
          $container['vocabulary_id'] = [
            '#type' => 'select',
            '#title' => $this->t('Vocabulary'),
            '#description' => $this->t('Select the taxonomy vocabulary to use.'),
            '#options' => $vocabulary_options,
            '#default_value' => $defaults['vocabulary_id'] ?? '',
            '#required' => TRUE,
            '#empty_option' => $this->t('- Select -'),
          ];
          $container['multiple'] = [
            '#type' => 'checkbox',
            '#title' => $this->t('Multiple selection'),
            '#description' => $this->t('Allows selecting multiple terms.'),
            '#default_value' => $defaults['multiple'] ?? FALSE,
          ];
        }
        break;

      case 'list':
        $container['options'] = [
          '#type' => 'textarea',
          '#title' => $this->t('Available options'),
          '#description' => $this->t('One option per line. Format: key|Label (e.g., wifi|Wi-Fi).'),
          '#default_value' => $this->formatListOptions($defaults['options'] ?? []),
          '#rows' => 5,
        ];
        $container['multiple'] = [
          '#type' => 'checkbox',
          '#title' => $this->t('Multiple selection'),
          '#description' => $this->t('Allows selecting multiple values.'),
          '#default_value' => $defaults['multiple'] ?? TRUE,
        ];
        break;

      case 'date':
        $container['format'] = [
          '#type' => 'select',
          '#title' => $this->t('Date format'),
          '#description' => $this->t('Date display format.'),
          '#options' => [
            'Y-m-d' => $this->t('YYYY-MM-DD (2026-05-17)'),
            'd/m/Y' => $this->t('DD/MM/YYYY (17/05/2026)'),
            'm/d/Y' => $this->t('MM/DD/YYYY (05/17/2026)'),
          ],
          '#default_value' => $defaults['format'] ?? 'Y-m-d',
        ];
        $container['include_time'] = [
          '#type' => 'checkbox',
          '#title' => $this->t('Include time'),
          '#description' => $this->t('Also display time with the date.'),
          '#default_value' => $defaults['include_time'] ?? FALSE,
        ];
        $container['time_format'] = [
          '#type' => 'select',
          '#title' => $this->t('Time format'),
          '#description' => $this->t('Time display format.'),
          '#options' => [
            'H:i' => $this->t('24h (14:30)'),
            'h:i A' => $this->t('12h (02:30 PM)'),
          ],
          '#default_value' => $defaults['time_format'] ?? 'H:i',
          '#states' => [
            'visible' => [
              ':input[name="payload_defaults_container[include_time]"]' => ['checked' => TRUE],
            ],
          ],
        ];
        break;

      case 'flag':
      case 'yes_no':
        $container['info'] = [
          '#markup' => '<p><em>' . $this->t('This data type does not require additional configuration.') . '</em></p>',
        ];
        break;

      default:
        $container['empty'] = [
          '#markup' => '<p>' . $this->t('No specific configuration for this data type.') . '</p>',
        ];
        break;
    }
  }

  /**
   * Formats list options for display in textarea.
   *
   * @param array $options
   *   Array of options.
   *
   * @return string
   *   Formatted string.
   */
  protected function formatListOptions(array $options): string {
    $lines = [];
    foreach ($options as $key => $label) {
      $lines[] = "$key|$label";
    }
    return implode("\n", $lines);
  }

  /**
   * Parses list options from textarea.
   *
   * @param string $text
   *   Textarea content.
   *
   * @return array
   *   Array of options.
   */
  protected function parseListOptions(string $text): array {
    $options = [];
    $lines = explode("\n", $text);
    foreach ($lines as $line) {
      $line = trim($line);
      if (empty($line)) {
        continue;
      }
      if (strpos($line, '|') !== FALSE) {
        [$key, $label] = explode('|', $line, 2);
        $options[trim($key)] = trim($label);
      }
      else {
        $options[$line] = $line;
      }
    }
    return $options;
  }

  /**
   * Gets feature group options.
   *
   * @return array
   *   Array of feature group options.
   */
  protected function getFeatureGroupOptions(): array {
    $options = [];
    $groups = \Drupal::entityTypeManager()
      ->getStorage('fb_feature_group')
      ->loadMultiple();
    
    foreach ($groups as $group) {
      $options[$group->id()] = $group->label();
    }
    
    return $options;
  }

  /**
   * Gets asset type options.
   *
   * @return array
   *   Array of asset type options.
   */
  protected function getAssetTypeOptions(): array {
    // TODO: Get from ps_dictionary when available.
    return [
      'BUR' => $this->t('Office'),
      'COW' => $this->t('Coworking'),
      'ENT' => $this->t('Warehouse/Logistics'),
      'ACT' => $this->t('Activity unit'),
      'COM' => $this->t('Retail unit'),
      'TER' => $this->t('Land'),
    ];
  }

  /**
   * Gets dictionary type options.
   *
   * @return array
   *   Array of dictionary options keyed by ID.
   */
  protected function getDictionaryOptions(): array {
    $options = [];
    
    // Load all dictionary types from ps_dictionary.
    try {
      $storage = \Drupal::entityTypeManager()->getStorage('ps_dictionary_type');
      $dictionary_types = $storage->loadMultiple();
      
      foreach ($dictionary_types as $type) {
        $options[$type->id()] = $type->label();
      }
      
      ksort($options);
    }
    catch (\Exception $e) {
      // ps_dictionary module may not be enabled.
      \Drupal::logger('ps_feature')->warning('Unable to load dictionary types: @message', ['@message' => $e->getMessage()]);
    }
    
    return $options;
  }

  /**
   * Gets taxonomy vocabulary options.
   *
   * @return array
   *   Array of vocabulary options keyed by machine name.
   */
  protected function getVocabularyOptions(): array {
    $options = [];
    
    try {
      $vocabularies = \Drupal::entityTypeManager()
        ->getStorage('taxonomy_vocabulary')
        ->loadMultiple();
      
      foreach ($vocabularies as $vocabulary) {
        $options[$vocabulary->id()] = $vocabulary->label();
      }
      
      ksort($options);
    }
    catch (\Exception $e) {
      \Drupal::logger('ps_feature')->warning('Unable to load taxonomies: @message', ['@message' => $e->getMessage()]);
    }
    
    return $options;
  }

}
