<?php

declare(strict_types=1);

namespace Drupal\ps_context\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the Context Rule config entity.
 *
 * A rule defines a set of conditions (field = value) and a set of actions
 * (show/hide tab or field, set required, disable, set default value).
 * All enabled rules are evaluated client-side via drupalSettings.psContext.rules.
 *
 * @ConfigEntityType(
 *   id = "ps_context_rule",
 *   label = @Translation("Context rule"),
 *   label_collection = @Translation("Context rules"),
 *   label_singular = @Translation("context rule"),
 *   label_plural = @Translation("context rules"),
 *   label_count = @PluralTranslation(
 *     singular = "@count context rule",
 *     plural = "@count context rules",
 *   ),
 *   handlers = {
 *     "list_builder" = "Drupal\ps_context\Controller\PsContextRuleListBuilder",
 *     "form" = {
 *       "add" = "Drupal\ps_context\Form\PsContextRuleForm",
 *       "edit" = "Drupal\ps_context\Form\PsContextRuleForm",
 *       "delete" = "Drupal\Core\Entity\EntityDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider"
 *     }
 *   },
 *   config_prefix = "rule",
 *   admin_permission = "administer ps_context matrix",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid",
 *     "status" = "status",
 *     "weight" = "weight"
 *   },
 *   links = {
 *     "collection" = "/admin/ps/config/matrix",
 *     "add-form" = "/admin/ps/config/matrix/add",
 *     "edit-form" = "/admin/ps/config/matrix/{ps_context_rule}",
 *     "delete-form" = "/admin/ps/config/matrix/{ps_context_rule}/delete"
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "uuid",
 *     "status",
 *     "weight",
 *     "conditions_logic",
 *     "conditions",
 *     "actions"
 *   }
 * )
 */
final class PsContextRule extends ConfigEntityBase implements PsContextRuleInterface {

  /**
   * The machine name of the rule.
   */
  protected string $id;

  /**
   * The human-readable label of the rule.
   */
  protected string $label;

  /**
   * The weight for ordering during evaluation (lower = evaluated first).
   */
  protected int $weight = 0;

  /**
   * Conditions logic: 'AND' (all must match) or 'OR' (any must match).
   */
  protected string $conditions_logic = 'AND';

  /**
   * Condition items: [{field_name, operator, value}, ...].
   */
  protected array $conditions = [];

  /**
   * Action items: [{action_type, target, value}, ...].
   */
  protected array $actions = [];

  /**
   * {@inheritdoc}
   */
  public function getConditions(): array {
    return $this->conditions;
  }

  /**
   * {@inheritdoc}
   */
  public function getConditionsLogic(): string {
    return $this->conditions_logic;
  }

  /**
   * {@inheritdoc}
   */
  public function getActions(): array {
    return $this->actions;
  }

  /**
   * {@inheritdoc}
   */
  public function getWeight(): int {
    return $this->weight;
  }

}
