<?php

declare(strict_types=1);

namespace Drupal\ps_context\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\ps_context\Entity\PsContextRuleInterface;

/**
 * Applies context rules to the offer node form.
 *
 * Rules are loaded from ps_context_rule config entities.
 * All enabled rules (sorted by weight) are:
 *  1. Passed to drupalSettings.psContext.rules — evaluated client-side (JS).
 *  2. Evaluated server-side for PHP-only actions (required, optional, disabled).
 *
 * Tab-level and field-level visibility are fully delegated to JS.
 */
final class OfferMatrixRules {

  public function __construct(
    private readonly EntityTypeManagerInterface $entityTypeManager,
  ) {}

  /**
   * Applies all active context rules to the offer form.
   */
  public function applyFormRules(array &$form, FormStateInterface $form_state): void {
    /** @var \Drupal\ps_context\Entity\PsContextRuleInterface[] $rules */
    $rules = $this->entityTypeManager
      ->getStorage('ps_context_rule')
      ->loadByProperties(['status' => TRUE]);

    // Sort by weight ascending (lower weight = evaluated first).
    uasort($rules, static fn(PsContextRuleInterface $a, PsContextRuleInterface $b): int => $a->getWeight() <=> $b->getWeight());

    // Serialize rules for the JS engine.
    $rules_data = array_map(
      static fn(PsContextRuleInterface $rule): array => [
        'id' => $rule->id(),
        'conditions_logic' => $rule->getConditionsLogic(),
        'conditions' => $rule->getConditions(),
        'actions' => $rule->getActions(),
      ],
      array_values($rules),
    );

    // Apply server-side PHP actions (required, optional, disabled, default)
    // based on the current entity's stored field values (initial form state).
    $form_object = $form_state->getFormObject();
    if (method_exists($form_object, 'getEntity')) {
      $node = $form_object->getEntity();
      foreach ($rules as $rule) {
        if ($this->evaluateConditions($rule, $node)) {
          $this->applyServerSideActions($form, $rule->getActions(), $node);
        }
      }
    }

    // Attach JS library + pass rules to drupalSettings.
    $form['#attached']['library'][] = 'ps_context/offer.form';
    $form['#attached']['drupalSettings']['psContext']['rules'] = $rules_data;
  }

  // -----------------------------------------------------------------------
  // Server-side rule evaluation.
  // -----------------------------------------------------------------------

  /**
   * Evaluates whether a rule's conditions are met for a given node.
   */
  private function evaluateConditions(PsContextRuleInterface $rule, object $node): bool {
    $conditions = $rule->getConditions();

    if (empty($conditions)) {
      return TRUE;
    }

    $results = array_map(fn(array $c): bool => $this->evaluateCondition($c, $node), $conditions);

    return $rule->getConditionsLogic() === 'OR'
      ? in_array(TRUE, $results, TRUE)
      : !in_array(FALSE, $results, TRUE);
  }

  /**
   * Evaluates a single condition against a node's field value.
   */
  private function evaluateCondition(array $condition, object $node): bool {
    $field_name = $condition['field_name'] ?? '';
    $operator = $condition['operator'] ?? 'equals';
    $expected = (string) ($condition['value'] ?? '');

    if (!$node->hasField($field_name)) {
      return FALSE;
    }

    $field = $node->get($field_name);
    $actual = (string) ($field->value ?? $field->target_id ?? '');

    return match ($operator) {
      'equals' => $actual === $expected,
      'not_equals' => $actual !== $expected,
      'empty' => $actual === '',
      'filled' => $actual !== '',
      'contains' => str_contains($actual, $expected),
      default => FALSE,
    };
  }

  /**
   * Applies server-side actions that affect the form structure.
   *
   * Only handles PHP-level changes (required, optional, disabled, default).
   * Visibility (show/hide) is handled entirely client-side by JS.
   */
  private function applyServerSideActions(array &$form, array $actions, object $node): void {
    foreach ($actions as $action) {
      $action_type = $action['action_type'] ?? '';
      $target = $action['target'] ?? '';
      $value = $action['value'] ?? '';

      if (empty($target) || !isset($form[$target])) {
        continue;
      }

      switch ($action_type) {
        case 'set_required':
          $form[$target]['#required'] = TRUE;
          break;

        case 'set_optional':
          $form[$target]['#required'] = FALSE;
          break;

        case 'disable_field':
          $form[$target]['#disabled'] = TRUE;
          break;

        case 'set_default':
          // Only set default on new nodes when field is empty.
          if ($node->isNew() && $value !== '') {
            $form[$target]['widget']['#default_value'] = $value;
          }
          break;
      }
    }
  }

}
