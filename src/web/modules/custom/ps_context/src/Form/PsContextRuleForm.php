<?php

declare(strict_types=1);

namespace Drupal\ps_context\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Add / edit form for ps_context_rule config entities.
 *
 * Supports dynamic AJAX rows for conditions and actions.
 */
final class PsContextRuleForm extends EntityForm {

  // -----------------------------------------------------------------------
  // Field options — offer form scope.
  // -----------------------------------------------------------------------

  /**
   * Fields that can act as conditions (driving fields).
   */
  private const CONDITION_FIELDS = [
    'field_asset_type' => 'Asset type (field_asset_type)',
    'field_operation_type' => 'Operation type (field_operation_type)',
    'field_divisible' => 'Divisible (field_divisible)',
  ];

  /**
   * Operators for condition evaluation.
   */
  private const OPERATORS = [
    'equals' => '= (equals)',
    'not_equals' => '≠ (not equals)',
    'empty' => 'is empty',
    'filled' => 'is filled',
    'contains' => 'contains',
  ];

  /**
   * Tabs available as action targets (show_tab / hide_tab).
   */
  private const TAB_TARGETS = [
    'group_identification' => 'Identification',
    'group_content' => 'Content',
    'group_diagnostics' => 'Diagnostics',
    'group_budget' => 'Price',
    'group_surface' => 'Surface',
    'group_capacity' => 'Capacity',
    'group_location' => 'Location',
    'group_features' => 'Features',
    'group_media' => 'Media',
    'group_contacts' => 'Contacts',
  ];

  /**
   * Fields available as action targets (field actions).
   */
  private const FIELD_TARGETS = [
    'field_divisible' => 'Divisible (field_divisible)',
    'field_budget_period' => 'Price period (field_budget_period)',
    'field_budget_ht_hc' => 'Price HT/HC (field_budget_ht_hc)',
    'field_surfaces' => 'Surfaces table (field_surfaces)',
    'field_capacity' => 'Capacity (field_capacity)',
    'field_diagnostics_dpe' => 'Diagnostics DPE (field_diagnostics_dpe)',
    'field_diagnostics_ges' => 'Diagnostics GES (field_diagnostics_ges)',
    'field_client_type' => 'Client type (field_client_type)',
    'field_mandate_type' => 'Mandate type (field_mandate_type)',
    'field_asset_type' => 'Asset type (field_asset_type)',
    'field_operation_type' => 'Operation type (field_operation_type)',
  ];

  // -----------------------------------------------------------------------
  // Form build.
  // -----------------------------------------------------------------------

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state): array {
    $form = parent::form($form, $form_state);
    /** @var \Drupal\ps_context\Entity\PsContextRuleInterface $entity */
    $entity = $this->entity;

    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#default_value' => $entity->label(),
      '#maxlength' => 255,
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $entity->id(),
      '#machine_name' => [
        'exists' => '\\Drupal\\ps_context\\Entity\\PsContextRule::load',
      ],
      '#disabled' => !$entity->isNew(),
    ];

    $form['status'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enabled'),
      '#default_value' => $entity->status(),
    ];

    $form['weight'] = [
      '#type' => 'number',
      '#title' => $this->t('Weight'),
      '#default_value' => $entity->getWeight(),
      '#description' => $this->t('Rules with lower weight are evaluated first. When two rules affect the same element, the last evaluated wins.'),
      '#size' => 5,
    ];

    $form['conditions_logic'] = [
      '#type' => 'radios',
      '#title' => $this->t('Conditions logic'),
      '#options' => [
        'AND' => $this->t('AND — all conditions must match'),
        'OR' => $this->t('OR — at least one condition must match'),
      ],
      '#default_value' => $entity->getConditionsLogic(),
      '#required' => TRUE,
    ];

    // --- Conditions ---
    if (!$form_state->has('conditions')) {
      $form_state->set('conditions', $entity->getConditions() ?: []);
    }

    $form['conditions_wrapper'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Conditions'),
      '#description' => $this->t('Leave empty to make this rule always apply (unconditional).'),
      '#prefix' => '<div id="ps-context-conditions-wrapper">',
      '#suffix' => '</div>',
      '#tree' => TRUE,
    ];

    foreach ($form_state->get('conditions') as $i => $condition) {
      $form['conditions_wrapper'][$i] = $this->buildConditionRow($i, $condition);
    }

    $form['conditions_wrapper']['add_condition'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add condition'),
      '#name' => 'add_condition',
      '#submit' => ['::addCondition'],
      '#ajax' => [
        'callback' => '::updateConditionsWrapper',
        'wrapper' => 'ps-context-conditions-wrapper',
      ],
      '#limit_validation_errors' => [],
    ];

    // --- Actions ---
    if (!$form_state->has('actions')) {
      $form_state->set('actions', $entity->getActions() ?: []);
    }

    $form['actions_wrapper'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Actions'),
      '#description' => $this->t('Actions applied when conditions match.'),
      '#prefix' => '<div id="ps-context-actions-wrapper">',
      '#suffix' => '</div>',
      '#tree' => TRUE,
    ];

    foreach ($form_state->get('actions') as $i => $action) {
      $form['actions_wrapper'][$i] = $this->buildActionRow($i, $action);
    }

    $form['actions_wrapper']['add_action'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add action'),
      '#name' => 'add_action',
      '#submit' => ['::addAction'],
      '#ajax' => [
        'callback' => '::updateActionsWrapper',
        'wrapper' => 'ps-context-actions-wrapper',
      ],
      '#limit_validation_errors' => [],
    ];

    return $form;
  }

  // -----------------------------------------------------------------------
  // Row builders.
  // -----------------------------------------------------------------------

  /**
   * Builds a single condition row.
   */
  private function buildConditionRow(int $delta, array $condition): array {
    $row = [
      '#type' => 'container',
      '#attributes' => ['class' => ['container-inline', 'ps-context-condition-row']],
    ];

    $row['field_name'] = [
      '#type' => 'select',
      '#title' => $this->t('Field'),
      '#options' => $this->translateOptions(self::CONDITION_FIELDS),
      '#default_value' => $condition['field_name'] ?? '',
      '#empty_option' => $this->t('- Select field -'),
      '#required' => FALSE,
    ];

    $row['operator'] = [
      '#type' => 'select',
      '#title' => $this->t('Operator'),
      '#options' => $this->translateOptions(self::OPERATORS),
      '#default_value' => $condition['operator'] ?? 'equals',
    ];

    $row['value'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Value'),
      '#default_value' => $condition['value'] ?? '',
      '#size' => 20,
      '#description' => $this->t('E.g. BUR, COW, ENT, ACT, COM, TER (asset type) · LOC, VEN (operation type) · 1 / 0 (checkbox).'),
      '#states' => [
        'invisible' => [
          ':input[name="conditions_wrapper[' . $delta . '][operator]"]' => [
            ['value' => 'empty'],
            ['value' => 'filled'],
          ],
        ],
      ],
    ];

    $row['remove'] = [
      '#type' => 'submit',
      '#value' => $this->t('Remove'),
      '#name' => 'remove_condition_' . $delta,
      '#submit' => ['::removeCondition'],
      '#ajax' => [
        'callback' => '::updateConditionsWrapper',
        'wrapper' => 'ps-context-conditions-wrapper',
      ],
      '#delta' => $delta,
      '#limit_validation_errors' => [],
    ];

    return $row;
  }

  /**
   * Builds a single action row.
   */
  private function buildActionRow(int $delta, array $action): array {
    $row = [
      '#type' => 'container',
      '#attributes' => ['class' => ['ps-context-action-row']],
    ];

    $action_type = $action['action_type'] ?? '';
    $is_tab_action = in_array($action_type, ['show_tab', 'hide_tab'], TRUE);

    $row['action_type'] = [
      '#type' => 'select',
      '#title' => $this->t('Action'),
      '#options' => $this->getActionTypeOptions(),
      '#default_value' => $action_type,
      '#empty_option' => $this->t('- Select action -'),
      '#required' => FALSE,
    ];

    // Target for tab actions (show_tab / hide_tab).
    $row['target_tab'] = [
      '#type' => 'select',
      '#title' => $this->t('Tab'),
      '#options' => $this->translateOptions(self::TAB_TARGETS),
      '#default_value' => $is_tab_action ? ($action['target'] ?? '') : '',
      '#empty_option' => $this->t('- Select tab -'),
      '#states' => [
        'visible' => [
          ':input[name="actions_wrapper[' . $delta . '][action_type]"]' => [
            ['value' => 'show_tab'],
            ['value' => 'hide_tab'],
          ],
        ],
      ],
    ];

    // Target for field actions.
    $row['target_field'] = [
      '#type' => 'select',
      '#title' => $this->t('Field'),
      '#options' => $this->translateOptions(self::FIELD_TARGETS),
      '#default_value' => !$is_tab_action ? ($action['target'] ?? '') : '',
      '#empty_option' => $this->t('- Select field -'),
      '#states' => [
        'visible' => [
          ':input[name="actions_wrapper[' . $delta . '][action_type]"]' => [
            ['value' => 'show_field'],
            ['value' => 'hide_field'],
            ['value' => 'set_required'],
            ['value' => 'set_optional'],
            ['value' => 'set_default'],
            ['value' => 'disable_field'],
            ['value' => 'hide_surface_delta'],
          ],
        ],
      ],
    ];

    // Value — for set_default (the value to set) or hide_surface_delta (delta index).
    $row['action_value'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Value'),
      '#default_value' => $action['value'] ?? '',
      '#size' => 20,
      '#description' => $this->t('For "Set default value": value to fill in. For "Hide surface row": delta index (1 = DISPO, 2 = ETREF).'),
      '#states' => [
        'visible' => [
          ':input[name="actions_wrapper[' . $delta . '][action_type]"]' => [
            ['value' => 'set_default'],
            ['value' => 'hide_surface_delta'],
          ],
        ],
      ],
    ];

    $row['remove'] = [
      '#type' => 'submit',
      '#value' => $this->t('Remove'),
      '#name' => 'remove_action_' . $delta,
      '#submit' => ['::removeAction'],
      '#ajax' => [
        'callback' => '::updateActionsWrapper',
        'wrapper' => 'ps-context-actions-wrapper',
      ],
      '#delta' => $delta,
      '#limit_validation_errors' => [],
    ];

    return $row;
  }

  /**
   * Returns grouped action type options.
   */
  private function getActionTypeOptions(): array {
    return [
      (string) $this->t('Tabs') => [
        'show_tab' => $this->t('Show tab'),
        'hide_tab' => $this->t('Hide tab'),
      ],
      (string) $this->t('Fields') => [
        'show_field' => $this->t('Show field'),
        'hide_field' => $this->t('Hide field'),
        'set_required' => $this->t('Make required'),
        'set_optional' => $this->t('Make optional'),
        'set_default' => $this->t('Set default value'),
        'disable_field' => $this->t('Disable field'),
      ],
      (string) $this->t('Advanced') => [
        'hide_surface_delta' => $this->t('Hide surface row (delta)'),
      ],
    ];
  }

  /**
   * Translates a string-keyed array of labels.
   *
   * @param array<string, string> $items
   *
   * @return array<string, \Drupal\Core\StringTranslation\TranslatableMarkup>
   */
  private function translateOptions(array $items): array {
    $out = [];
    foreach ($items as $k => $v) {
      $out[$k] = $this->t($v);
    }
    return $out;
  }

  // -----------------------------------------------------------------------
  // Validation.
  // -----------------------------------------------------------------------

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state): void {
    parent::validateForm($form, $form_state);

    $raw_actions = $form_state->getValue('actions_wrapper') ?? [];
    foreach ($raw_actions as $i => $data) {
      if (!is_numeric($i) || !is_array($data)) {
        continue;
      }
      if (($data['action_type'] ?? '') !== 'hide_surface_delta') {
        continue;
      }
      $value = trim($data['action_value'] ?? '');
      if ($value !== '' && !ctype_digit($value)) {
        $form_state->setErrorByName(
          'actions_wrapper][' . $i . '][action_value',
          $this->t('The delta value for "Hide surface row" must be a positive integer (e.g. 1 for DISPO, 2 for ETREF).')
        );
      }
    }
  }

  // -----------------------------------------------------------------------
  // AJAX callbacks.
  // -----------------------------------------------------------------------

  /**
   * AJAX callback — returns updated conditions wrapper.
   */
  public function updateConditionsWrapper(array &$form, FormStateInterface $form_state): AjaxResponse {
    $response = new AjaxResponse();
    $response->addCommand(new ReplaceCommand('#ps-context-conditions-wrapper', $form['conditions_wrapper']));
    return $response;
  }

  /**
   * AJAX callback — returns updated actions wrapper.
   */
  public function updateActionsWrapper(array &$form, FormStateInterface $form_state): AjaxResponse {
    $response = new AjaxResponse();
    $response->addCommand(new ReplaceCommand('#ps-context-actions-wrapper', $form['actions_wrapper']));
    return $response;
  }

  // -----------------------------------------------------------------------
  // AJAX submit handlers for conditions.
  // -----------------------------------------------------------------------

  /**
   * Submit handler: add an empty condition row.
   */
  public function addCondition(array &$form, FormStateInterface $form_state): void {
    $conditions = $this->extractConditionsFromFormState($form_state);
    $conditions[] = ['field_name' => '', 'operator' => 'equals', 'value' => ''];
    $form_state->set('conditions', $conditions);
    $form_state->setRebuild();
  }

  /**
   * Submit handler: remove a condition row by delta.
   */
  public function removeCondition(array &$form, FormStateInterface $form_state): void {
    $trigger = $form_state->getTriggeringElement();
    $index = (int) $trigger['#delta'];

    $conditions = $this->extractConditionsFromFormState($form_state);
    array_splice($conditions, $index, 1);

    $form_state->set('conditions', array_values($conditions));
    $form_state->setRebuild();
  }

  // -----------------------------------------------------------------------
  // AJAX submit handlers for actions.
  // -----------------------------------------------------------------------

  /**
   * Submit handler: add an empty action row.
   */
  public function addAction(array &$form, FormStateInterface $form_state): void {
    $actions = $this->extractActionsFromFormState($form_state);
    $actions[] = ['action_type' => '', 'target' => '', 'value' => ''];
    $form_state->set('actions', $actions);
    $form_state->setRebuild();
  }

  /**
   * Submit handler: remove an action row by delta.
   */
  public function removeAction(array &$form, FormStateInterface $form_state): void {
    $trigger = $form_state->getTriggeringElement();
    $index = (int) $trigger['#delta'];

    $actions = $this->extractActionsFromFormState($form_state);
    array_splice($actions, $index, 1);

    $form_state->set('actions', array_values($actions));
    $form_state->setRebuild();
  }

  // -----------------------------------------------------------------------
  // Helpers for extracting row data from form state.
  // -----------------------------------------------------------------------

  /**
   * Extracts the current condition rows from the submitted form values.
   *
   * @return array<int, array{field_name: string, operator: string, value: string}>
   */
  private function extractConditionsFromFormState(FormStateInterface $form_state): array {
    $raw = $form_state->getValue('conditions_wrapper') ?? [];
    $conditions = [];
    foreach ($raw as $i => $data) {
      if (!is_numeric($i) || !is_array($data)) {
        continue;
      }
      $conditions[] = [
        'field_name' => $data['field_name'] ?? '',
        'operator' => $data['operator'] ?? 'equals',
        'value' => $data['value'] ?? '',
      ];
    }
    // Fall back to stored conditions if form wasn't submitted yet.
    return $conditions ?: ($form_state->get('conditions') ?? []);
  }

  /**
   * Extracts the current action rows from the submitted form values.
   *
   * @return array<int, array{action_type: string, target: string, value: string}>
   */
  private function extractActionsFromFormState(FormStateInterface $form_state): array {
    $raw = $form_state->getValue('actions_wrapper') ?? [];
    $actions = [];
    foreach ($raw as $i => $data) {
      if (!is_numeric($i) || !is_array($data)) {
        continue;
      }
      $action_type = $data['action_type'] ?? '';
      $target = in_array($action_type, ['show_tab', 'hide_tab'], TRUE)
        ? ($data['target_tab'] ?? '')
        : ($data['target_field'] ?? '');
      $actions[] = [
        'action_type' => $action_type,
        'target' => $target,
        'value' => $data['action_value'] ?? '',
      ];
    }
    return $actions ?: ($form_state->get('actions') ?? []);
  }

  // -----------------------------------------------------------------------
  // Save.
  // -----------------------------------------------------------------------

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state): int {
    /** @var \Drupal\ps_context\Entity\PsContextRule $entity */
    $entity = $this->entity;

    // Filter out incomplete conditions (no field selected — would never match).
    $conditions = array_values(array_filter(
      $this->extractConditionsFromFormState($form_state),
      static fn(array $c): bool => ($c['field_name'] ?? '') !== '',
    ));

    // Filter out incomplete actions:
    //   - no action type → no-op
    //   - any action with no target → no-op (all action types require a target)
    $actions = array_values(array_filter(
      $this->extractActionsFromFormState($form_state),
      static function (array $a): bool {
        if (($a['action_type'] ?? '') === '') {
          return FALSE;
        }
        // All action types (tab, field, surface) require a non-empty target.
        if (($a['target'] ?? '') === '') {
          return FALSE;
        }
        return TRUE;
      },
    ));

    $entity->set('conditions', $conditions);
    $entity->set('actions', $actions);

    $status = $entity->save();

    $label = $entity->label();
    if ($status === SAVED_NEW) {
      $this->messenger()->addStatus($this->t('Created context rule %label.', ['%label' => $label]));
    }
    else {
      $this->messenger()->addStatus($this->t('Updated context rule %label.', ['%label' => $label]));
    }

    if (empty($actions)) {
      $this->messenger()->addWarning($this->t('Rule %label has no valid actions and will have no effect.', ['%label' => $label]));
    }

    $form_state->setRedirectUrl($entity->toUrl('collection'));
    return $status;
  }

}
