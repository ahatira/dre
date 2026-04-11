<?php

declare(strict_types=1);

namespace Drupal\ps_features\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\ps_dictionary\Service\DictionaryManagerInterface;
use Drupal\ps_features\Service\FeatureManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form handler for Feature add/edit forms.
 */
class FeatureForm extends EntityForm {

  /**
   * The feature manager service.
   *
   * @var \Drupal\ps_features\Service\FeatureManagerInterface|null
   */
  private ?FeatureManagerInterface $featureManager = NULL;

  /**
   * The dictionary manager service.
   *
   * @var \Drupal\ps_dictionary\Service\DictionaryManagerInterface|null
   */
  private ?DictionaryManagerInterface $dictionaryManager = NULL;

  /**
   * Constructs a FeatureForm.
   *
   * @param \Drupal\ps_features\Service\FeatureManagerInterface|null $feature_manager
   *   The feature manager service.
   * @param \Drupal\ps_dictionary\Service\DictionaryManagerInterface|null $dictionary_manager
   *   The dictionary manager service.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(
    ?FeatureManagerInterface $feature_manager = NULL,
    ?DictionaryManagerInterface $dictionary_manager = NULL,
    ?EntityTypeManagerInterface $entity_type_manager = NULL,
  ) {
    $this->featureManager = $feature_manager;
    $this->dictionaryManager = $dictionary_manager;
    if ($entity_type_manager) {
      $this->entityTypeManager = $entity_type_manager;
    }
  }

  /**
   * Gets the feature manager service.
   *
   * @return \Drupal\ps_features\Service\FeatureManagerInterface
   *   The feature manager service.
   */
  protected function getFeatureManager(): FeatureManagerInterface {
    return $this->featureManager;
  }

  /**
   * Gets the dictionary manager service.
   *
   * @return \Drupal\ps_dictionary\Service\DictionaryManagerInterface
   *   The dictionary manager service.
   */
  protected function getDictionaryManager(): DictionaryManagerInterface {
    return $this->dictionaryManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    return new static(
      $container->get('ps_features.manager'),
      $container->get('ps_dictionary.manager'),
      $container->get('entity_type.manager'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state): array {
    $form = parent::form($form, $form_state);

    /** @var \Drupal\ps_features\Entity\FeatureInterface $feature */
    $feature = $this->entity;

    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $feature->label(),
      '#description' => $this->t('The human-readable name of this feature.'),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $feature->id(),
      '#machine_name' => [
        'exists' => [$this, 'exist'],
      ],
      '#disabled' => !$feature->isNew(),
    ];

    $form['description'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Description'),
      '#default_value' => $feature->getDescription(),
      '#description' => $this->t('A description of what this feature represents.'),
    ];

    $form['value_type'] = [
      '#type' => 'select',
      '#title' => $this->t('Value Type'),
      '#default_value' => $feature->getValueType(),
      '#options' => $this->getFeatureManager()->getValueTypes(),
      '#required' => TRUE,
      '#ajax' => [
        'callback' => '::valueTypeCallback',
        'wrapper' => 'value-type-dependent',
      ],
    ];

    $form['type_dependent'] = [
      '#type' => 'container',
      '#attributes' => ['id' => 'value-type-dependent'],
    ];

    $valueType = $form_state->getValue('value_type') ?? $feature->getValueType();

    if ($valueType === 'dictionary') {
      $form['type_dependent']['dictionary_type'] = [
        '#type' => 'select',
        '#title' => $this->t('Dictionary Type'),
        '#default_value' => $feature->getDictionaryType(),
        '#options' => $this->getDictionaryOptions(),
        '#required' => TRUE,
      ];
    }

    if (in_array($valueType, ['numeric', 'range'], TRUE)) {
      $form['type_dependent']['unit'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Unit'),
        '#default_value' => $feature->getUnit(),
        '#maxlength' => 50,
        '#description' => $this->t('Optional unit of measurement (e.g., m, m², %).'),
      ];

      $rules = $feature->getValidationRules();

      $form['type_dependent']['min'] = [
        '#type' => 'number',
        '#title' => $this->t('Minimum Value'),
        '#default_value' => $rules['min'] ?? NULL,
        '#step' => 0.01,
      ];

      $form['type_dependent']['max'] = [
        '#type' => 'number',
        '#title' => $this->t('Maximum Value'),
        '#default_value' => $rules['max'] ?? NULL,
        '#step' => 0.01,
      ];
    }

    $form['is_required'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Required'),
      '#default_value' => $feature->isRequired(),
      '#description' => $this->t('Whether this feature is required.'),
    ];

    $form['is_facetable'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Facetable'),
      '#default_value' => $feature->isFacetable(),
      '#description' => $this->t('Expose this feature as a search facet.'),
    ];

    // Pre-select group from URL parameter if creating new feature.
    $defaultGroup = $feature->getGroup();
    if ($feature->isNew()) {
      $requestGroup = $this->getRequest()->query->get('group');
      $groupOptions = $this->getGroupOptions();
      if ($requestGroup && array_key_exists($requestGroup, $groupOptions)) {
        $defaultGroup = $requestGroup;
      }
    }

    $form['group'] = [
      '#type' => 'select',
      '#title' => $this->t('Group'),
      '#default_value' => $defaultGroup,
      '#options' => $this->getGroupOptions(),
      '#empty_option' => $this->t('- None -'),
      '#description' => $this->t('Logical group for organizing features.'),
    ];

    // Hide weight field - managed via drag-and-drop in grouped list builder.
    $form['weight'] = [
      '#type' => 'value',
      '#value' => $feature->getWeight(),
    ];

    $form['metadata'] = [
      '#type' => 'details',
      '#title' => $this->t('Metadata'),
      '#open' => FALSE,
    ];

    $metadata = $feature->getMetadata();

    $form['metadata']['icon'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Icon'),
      '#default_value' => $metadata['icon'] ?? '',
      '#description' => $this->t('Icon name or class.'),
    ];

    return $form;
  }

  /**
   * Ajax callback for value type selection.
   */
  public function valueTypeCallback(array &$form, FormStateInterface $form_state): array {
    return $form['type_dependent'];
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state): int {
    /** @var \Drupal\ps_features\Entity\FeatureInterface $feature */
    $feature = $this->entity;

    // Build validation rules from form values.
    $rules = [];
    $min = $form_state->getValue('min');
    $max = $form_state->getValue('max');

    if ($min !== NULL && $min !== '') {
      $rules['min'] = (float) $min;
    }
    if ($max !== NULL && $max !== '') {
      $rules['max'] = (float) $max;
    }

    $feature->set('validation_rules', $rules);

    // Save group and weight as direct properties.
    $feature->set('group', $form_state->getValue('group'));
    $feature->set('weight', (int) ($form_state->getValue('weight') ?? 0));
    $feature->set('is_facetable', (bool) $form_state->getValue('is_facetable'));

    // Build metadata from form values (without group/weight).
    $metadata = [
      'icon' => $form_state->getValue('icon') ?? '',
    ];

    $feature->set('metadata', $metadata);

    $result = parent::save($form, $form_state);

    $messageArgs = ['%label' => $feature->label()];

    if ($result === SAVED_NEW) {
      $this->messenger()->addStatus($this->t('Created feature %label.', $messageArgs));
    }
    else {
      $this->messenger()->addStatus($this->t('Updated feature %label.', $messageArgs));
    }

    $form_state->setRedirectUrl($feature->toUrl('collection'));

    return $result;
  }

  /**
   * Checks if a feature with the given ID already exists.
   *
   * @param string $id
   *   The feature ID.
   *
   * @return bool
   *   TRUE if exists, FALSE otherwise.
   */
  public function exist(string $id): bool {
    return $this->getFeatureManager()->featureExists($id);
  }

  /**
   * Gets dictionary options for select element.
   *
   * Loads all dictionary types dynamically from entity storage.
   *
   * @return array<string, string>
   *   Dictionary options keyed by type ID with label values.
   *
   * @see docs/specs/04-ps-features.md#dictionary-integration
   */
  private function getDictionaryOptions(): array {
    $options = [];

    try {
      // Load all dictionary types from entity storage.
      $types = $this->entityTypeManager
        ->getStorage('ps_dictionary_type')
        ->loadMultiple();

      foreach ($types as $type) {
        $options[$type->id()] = $type->label();
      }

      // Sort alphabetically by label.
      asort($options);
    }
    catch (\Exception $e) {
      // Fallback to empty array if dictionary module not available.
      $this->logger('ps_features')->error('Failed to load dictionary types: @message', [
        '@message' => $e->getMessage(),
      ]);
    }

    return $options;
  }

  /**
   * Gets feature group options from dictionary.
   *
   * @return array<string, string>
   *   Group options keyed by (lowercase) group code with label values.
   */
  private function getGroupOptions(): array {
    $options = ['' => $this->t('- None -')];

    // Load groups from feature_group dictionary.
    if ($this->dictionaryManager) {
      try {
        $dictOptions = $this->dictionaryManager->getOptions('feature_group');
        if (!empty($dictOptions)) {
          foreach ($dictOptions as $code => $label) {
            // Preserve existing lowercase storage for backwards compatibility.
            $options[strtolower($code)] = $label;
          }
        }
      }
      catch (\Throwable $e) {
        // Dictionary not available.
      }
    }

    return $options;
  }

}
