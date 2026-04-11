<?php

declare(strict_types=1);

namespace Drupal\ps_features;

use Drupal\Core\Config\Entity\DraggableListBuilder;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\ps_dictionary\Service\DictionaryManagerInterface;
use Drupal\ps_features\Entity\Feature;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a grouped listing of Features with drag & drop reordering.
 *
 * Features are organized by groups from the feature_group dictionary
 * exactly like blocks are organized by regions in Block Layout. All features
 * are displayed in a single table with group section rows intercalated.
 *
 * @see \Drupal\ps_features\Entity\Feature
 * @see \Drupal\block\BlockListBuilder
 * @see docs/specs/04-ps-features.md#feature-groups
 */
class FeatureGroupedListBuilder extends DraggableListBuilder {
  /**
   * The entity type manager.
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  /**
   * The dictionary manager (optional until full migration from FeatureGroup).
   *
   * @var \Drupal\ps_dictionary\Service\DictionaryManagerInterface|null
   */
  protected ?DictionaryManagerInterface $dictionaryManager = NULL;

  /**
   * Constructs the list builder.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type definition.
   * @param \Drupal\Core\Entity\EntityStorageInterface $storage
   *   The storage handler for the feature config entity.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
   * @param \Drupal\ps_dictionary\Service\DictionaryManagerInterface|null $dictionary_manager
   *   The dictionary manager (optional for transitional compatibility).
   */
  public function __construct(
    EntityTypeInterface $entity_type,
    EntityStorageInterface $storage,
    EntityTypeManagerInterface $entity_type_manager,
    ?DictionaryManagerInterface $dictionary_manager = NULL,
  ) {
    parent::__construct($entity_type, $storage);
    $this->entityTypeManager = $entity_type_manager;
    $this->dictionaryManager = $dictionary_manager;
    // Disable limit to show all features like Block Layout.
    $this->limit = FALSE;
  }

  /**
   * {@inheritdoc}
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   The service container.
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type definition.
   *
   * @return static
   *   A new instance.
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type): static {
    /** @var \Drupal\ps_dictionary\Service\DictionaryManagerInterface $dictionary_manager */
    $dictionary_manager = $container->get('ps_dictionary.manager');
    return new static(
          $entity_type,
          $container->get('entity_type.manager')->getStorage($entity_type->id()),
          $container->get('entity_type.manager'),
          $dictionary_manager,
      );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'ps_features_admin_display_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader(): array {
    return [
      'label' => $this->t('Feature'),
      'value_type' => $this->t('Value Type'),
      'group' => $this->t('Group'),
      'weight' => $this->t('Weight'),
      'operations' => $this->t('Operations'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity): array {
    // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
    /** @var \Drupal\ps_features\Entity\FeatureInterface $entity */
    // This method is not used directly in the form.
    // Features are built in buildForm() instead.
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function render(): array {
    return $this->formBuilder->getForm($this);
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form['#attached']['library'][] = 'core/drupal.tableheader';
    $form['#attached']['library'][] = 'ps_features/admin';
    $form['#attributes']['class'][] = 'clearfix';

    // Disable form caching to ensure fresh builds.
    $form_state->setCached(FALSE);

    // Get filter values from user input (works on both initial submit and rebuild).
    $user_input = $form_state->getUserInput();
    $filter_group = $user_input['group'] ?? '';
    $filter_type = $user_input['value_type'] ?? '';
    $filter_status = $user_input['status'] ?? '';

    // Add filters section at the top.
    $form['filters'] = [
      '#type' => 'details',
      '#title' => $this->t('Filter'),
      '#open' => TRUE,
      '#attributes' => ['class' => ['container-inline']],
    ];

    $groups = $this->getFeatureGroups();
    $form['filters']['group'] = [
      '#type' => 'select',
      '#title' => $this->t('Group'),
      '#options' => ['' => $this->t('- All -')] + $groups,
      '#default_value' => $filter_group,
    ];

    $form['filters']['value_type'] = [
      '#type' => 'select',
      '#title' => $this->t('Value Type'),
      '#options' => [
        '' => $this->t('- All -'),
        'boolean' => $this->t('Boolean'),
        'dictionary' => $this->t('Dictionary'),
        'string' => $this->t('String'),
        'numeric' => $this->t('Numeric'),
        'range' => $this->t('Range'),
      ],
      '#default_value' => $filter_type,
    ];

    $form['filters']['status'] = [
      '#type' => 'select',
      '#title' => $this->t('Status'),
      '#options' => [
        '' => $this->t('- All -'),
        'active' => $this->t('Active'),
        'inactive' => $this->t('Inactive'),
      ],
      '#default_value' => $filter_status,
    ];

    $form['filters']['actions'] = [
      '#type' => 'actions',
    ];
    $form['filters']['actions']['apply'] = [
      '#type' => 'submit',
      '#value' => $this->t('Apply'),
      '#submit' => ['::applyFilters'],
      '#limit_validation_errors' => [],
    ];
    $form['filters']['actions']['reset'] = [
      '#type' => 'submit',
      '#value' => $this->t('Reset'),
      '#submit' => ['::resetFilters'],
      '#limit_validation_errors' => [],
    ];

    // Build the main features table.
    $form['features'] = $this->buildFeaturesForm($form_state, $filter_group, $filter_type, $filter_status);

    $form['actions'] = [
      '#tree' => FALSE,
      '#type' => 'actions',
    ];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save features'),
      '#button_type' => 'primary',
    ];

    return $form;
  }

  /**
   * Apply filters submit handler.
   */
  public function applyFilters(array &$form, FormStateInterface $form_state): void {
    // Just rebuild the form - values will be taken from user input.
    $form_state->setRebuild(TRUE);
  }

  /**
   * Reset filters submit handler.
   */
  public function resetFilters(array &$form, FormStateInterface $form_state): void {
    // Clear all user input.
    $form_state->setUserInput([]);
    $form_state->setRebuild(TRUE);
  }

  /**
   * Builds the main "Features" portion of the form.
   *
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   * @param string $filter_group
   *   The group filter value.
   * @param string $filter_type
   *   The value type filter value.
   * @param string $filter_status
   *   The status filter value.
   *
   * @return array
   *   The features form structure.
   */
  protected function buildFeaturesForm(FormStateInterface $form_state, string $filter_group = '', string $filter_type = '', string $filter_status = ''): array {
    $groups = $this->getFeatureGroups();
    $entities = $this->load();

    // Group features by their group property.
    $features = [];
    /** @var \Drupal\ps_features\Entity\FeatureInterface $entity */
    foreach ($entities as $entity_id => $entity) {
      // Apply group filter.
      $entity_group = $entity->getGroup() ?? 'ungrouped';
      if ($filter_group && $entity_group !== $filter_group) {
        continue;
      }

      // Apply type filter.
      if ($filter_type && $entity->getValueType() !== $filter_type) {
        continue;
      }

      // Apply status filter.
      if ($filter_status === 'active' && !$entity->status()) {
        continue;
      }
      if ($filter_status === 'inactive' && $entity->status()) {
        continue;
      }

      $features[$entity_group][$entity_id] = [
        'label' => $entity->label(),
        'entity_id' => $entity_id,
        'weight' => $entity->getWeight(),
        'entity' => $entity,
        'value_type' => $entity->getValueType(),
        'status' => $entity->status(),
      ];
    }

    $form = [
      '#type' => 'table',
      '#header' => [
        $this->t('Feature'),
        $this->t('Value Type'),
        $this->t('Group'),
        $this->t('Weight'),
        $this->t('Operations'),
      ],
      '#attributes' => [
        'id' => 'features',
      ],
    ];

    // Weight delta should be at least half the number of features.
    $weight_delta = round(count($entities) / 2);

    // Loop over each group and build features.
    foreach ($groups as $group_code => $group_label) {
      // Skip group if there are no features in it.
      if (empty($features[$group_code])) {
        continue;
      }

      // Configure tabledrag for this group.
      $form['#tabledrag'][] = [
        'action' => 'match',
        'relationship' => 'sibling',
        'group' => 'feature-group-select',
        'subgroup' => 'feature-group-' . $group_code,
        'hidden' => FALSE,
      ];
      $form['#tabledrag'][] = [
        'action' => 'order',
        'relationship' => 'sibling',
        'group' => 'feature-weight',
        'subgroup' => 'feature-weight-' . $group_code,
      ];

      // Group title row.
      $form['group-' . $group_code] = [
        '#attributes' => [
          'class' => ['group-title', 'group-title-' . $group_code],
          'no_striping' => TRUE,
        ],
      ];
      $form['group-' . $group_code]['title'] = [
        '#theme_wrappers' => [
          'container' => [
            '#attributes' => ['class' => 'group-title__action'],
          ],
        ],
        '#prefix' => $group_label,
        '#type' => 'link',
        '#title' => $this->t('Add feature <span class="visually-hidden">in the %group group</span>', ['%group' => $group_label]),
        '#url' => Url::fromRoute('entity.ps_feature.add_form', [], ['query' => ['group' => $group_code]]),
        '#wrapper_attributes' => [
          'colspan' => 5,
        ],
        '#attributes' => [
          // Removed 'use-ajax' to allow normal navigation since no dialog/ajax behavior is attached.
          'class' => ['button', 'button--small'],
        ],
      ];

      // Empty message row for this group.
      $form['group-' . $group_code . '-message'] = [
        '#attributes' => [
          'class' => [
            'group-message',
            'group-' . $group_code . '-message',
            (\array_key_exists($group_code, $features) ? 'group-populated' : 'group-empty'),
          ],
        ],
      ];
      $form['group-' . $group_code . '-message']['message'] = [
        '#markup' => '<em>' . $this->t('No features in this group') . '</em>',
        '#wrapper_attributes' => [
          'colspan' => 5,
        ],
      ];

      // Build feature rows for this group.
      if (\array_key_exists($group_code, $features)) {
        foreach ($features[$group_code] as $info) {
          $entity_id = $info['entity_id'];
          $entity = $info['entity'];

          $form[$entity_id] = [
            '#attributes' => [
              'class' => ['draggable'],
            ],
          ];

          // Add status class for inactive features.
          if (!$info['status']) {
            $form[$entity_id]['#attributes']['class'][] = 'feature-disabled';
          }

          // Feature name.
          $form[$entity_id]['info'] = [
            '#wrapper_attributes' => [
              'class' => ['feature'],
            ],
          ];
          // Show label with (disabled) suffix if inactive.
          if ($info['status']) {
            $form[$entity_id]['info']['#plain_text'] = $info['label'];
          }
          else {
            $form[$entity_id]['info']['#markup'] = $this->t('@label <em>(disabled)</em>', ['@label' => $info['label']]);
          }

          // Value type.
          $form[$entity_id]['type'] = [
            '#markup' => $this->getValueTypeLabel($info['value_type']),
          ];

          // Group selector.
          $form[$entity_id]['group']['group'] = [
            '#type' => 'select',
            '#default_value' => $group_code,
            '#required' => TRUE,
            '#title' => $this->t('Group for @feature feature', ['@feature' => $info['label']]),
            '#title_display' => 'invisible',
            '#options' => $groups,
            '#attributes' => [
              'class' => ['feature-group-select', 'feature-group-' . $group_code],
            ],
            '#parents' => ['features', $entity_id, 'group'],
          ];

          // Weight.
          $form[$entity_id]['weight'] = [
            '#type' => 'weight',
            '#default_value' => $info['weight'],
            '#delta' => $weight_delta,
            '#title' => $this->t('Weight for @feature', ['@feature' => $info['label']]),
            '#title_display' => 'invisible',
            '#attributes' => [
              'class' => ['feature-weight', 'feature-weight-' . $group_code],
            ],
          ];

          // Operations.
          $form[$entity_id]['operations'] = $this->buildOperations($entity);
        }
      }
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state): void {
    // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
    // No validation needed.
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $features = $form_state->getValue('features');

    foreach ($features as $feature_id => $feature_data) {
      if (!isset($feature_data['group']) || !isset($feature_data['weight'])) {
        continue;
      }

      /** @var \Drupal\ps_features\Entity\Feature|null $entity */
      $entity = $this->storage->load($feature_id);
      if (!$entity instanceof Feature) {
        continue;
      }

      $changed = FALSE;
      $new_group = (string) $feature_data['group'];
      $new_weight = (int) $feature_data['weight'];

      if ($entity->getGroup() !== $new_group) {
        $entity->set('group', $new_group);
        $changed = TRUE;
      }
      if ($entity->getWeight() !== $new_weight) {
        $entity->set('weight', $new_weight);
        $changed = TRUE;
      }
      if ($changed) {
        $entity->save();
      }
    }

    $this->messenger()->addStatus($this->t('The feature configuration has been saved.'));
  }

  /**
   * Gets feature groups from dictionary.
   *
   * @return array<string, string>
   *   Array of group_code => group_label.
   */
  protected function getFeatureGroups(): array {
    $groups = [];

    // Load from feature_group dictionary.
    if ($this->dictionaryManager) {
      try {
        $options = $this->dictionaryManager->getOptions('feature_group');
        if (!empty($options)) {
          // Options are UPPERCASE codes => labels; convert to lowercase for storage compatibility.
          foreach ($options as $code => $label) {
            $groups[strtolower($code)] = $label;
          }
        }
      }
      catch (\Throwable $e) {
        // Dictionary not available, return empty with ungrouped only.
      }
    }

    // Always include ungrouped at the end.
    $groups['ungrouped'] = (string) $this->t('Ungrouped');
    return $groups;
  }

  /**
   * Gets human-readable label for value type.
   *
   * @param string $type
   *   The value type code.
   *
   * @return string|\Drupal\Core\StringTranslation\TranslatableMarkup
   *   The translated label.
   */
  protected function getValueTypeLabel(string $type) {
    $types = [
      'boolean' => $this->t('Boolean'),
      'dictionary' => $this->t('Dictionary'),
      'string' => $this->t('String'),
      'numeric' => $this->t('Numeric'),
      'range' => $this->t('Range'),
    ];
    return $types[$type] ?? $type;
  }

}
