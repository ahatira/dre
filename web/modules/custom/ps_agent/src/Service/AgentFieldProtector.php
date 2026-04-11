<?php

declare(strict_types=1);

namespace Drupal\ps_agent\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\ps\Service\SettingsManagerInterface;
use Drupal\ps_agent\Entity\AgentInterface;

/**
 * Agent Field Protector service.
 *
 * Manages BO-protected fields during CRM imports. These fields are preserved
 * when agents are updated from external CRM systems.
 *
 * @see \Drupal\ps_agent\Service\AgentFieldProtectorInterface
 */
final class AgentFieldProtector implements AgentFieldProtectorInterface {

  /**
   * Default BO-protected fields.
   *
   * @var array<int, string>
   */
  private const BO_PROTECTED_FIELDS = [
    'email',
    'phone',
    'mobile',
    'internal_notes',
  ];

  /**
   * Constructs AgentFieldProtector.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   * @param \Drupal\ps\Service\SettingsManagerInterface $settingsManager
   *   The settings manager.
   */
  public function __construct(
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly SettingsManagerInterface $settingsManager,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function isBoEditableField(string $fieldName): bool {
    return in_array($fieldName, $this->getBoEditableFields(), TRUE);
  }

  /**
   * {@inheritdoc}
   */
  public function getBoEditableFields(): array {
    $configured = $this->settingsManager->get('ps_agent.bo_protected_fields', []);

    if (!empty($configured)) {
      return $configured;
    }

    return self::BO_PROTECTED_FIELDS;
  }

  /**
   * {@inheritdoc}
   */
  public function getBoEditableValues(AgentInterface $agent): array {
    $values = [];
    $fields = $this->getBoEditableFields();

    foreach ($fields as $fieldName) {
      $getter = 'get' . str_replace('_', '', ucwords($fieldName, '_'));
      if (method_exists($agent, $getter)) {
        $values[$fieldName] = $agent->{$getter}();
      }
    }

    return $values;
  }

  /**
   * {@inheritdoc}
   */
  public function restoreBoEditableValues(AgentInterface $agent, array $values): void {
    $fields = $this->getBoEditableFields();

    foreach ($values as $fieldName => $value) {
      if (!in_array($fieldName, $fields, TRUE)) {
        continue;
      }

      $setter = 'set' . str_replace('_', '', ucwords($fieldName, '_'));
      if (method_exists($agent, $setter) && $value !== NULL) {
        $agent->{$setter}($value);
      }
    }
  }

}
