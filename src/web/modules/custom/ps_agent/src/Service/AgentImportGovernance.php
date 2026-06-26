<?php

declare(strict_types=1);

namespace Drupal\ps_agent\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\ps_core\Service\ImportGovernanceGlobalResolver;

/**
 * Reads agent import governance settings and resolves CRM import behaviour.
 */
class AgentImportGovernance {

  public const CONFIG_NAME = 'ps_agent.import_governance';

  public const STRATEGY_INHERIT = 'inherit';

  public const STRATEGY_SKIP_ROW = 'skip_row';

  public const STRATEGY_SKIP_FIELD = 'skip_field';

  public const ACTION_DEACTIVATE = 'deactivate';

  public const ACTION_KEEP_ACTIVE = 'keep_active';

  public const ENTITY_TYPE_ID = 'ps_agent';

  public function __construct(
    private readonly ConfigFactoryInterface $configFactory,
    private readonly ImportGovernanceGlobalResolver $globalResolver,
  ) {}

  public function getCrmRowStrategyOverride(): string {
    $value = (string) $this->config()->get('crm_row_strategy_override');
    return in_array($value, [
      self::STRATEGY_INHERIT,
      self::STRATEGY_SKIP_FIELD,
      self::STRATEGY_SKIP_ROW,
    ], TRUE) ? $value : self::STRATEGY_INHERIT;
  }

  public function allowCrmOverwriteContact(): bool {
    return (bool) $this->config()->get('allow_crm_overwrite_contact');
  }

  public function resolveEffectiveLockStrategy(string $entityTypeId): string {
    if ($entityTypeId !== self::ENTITY_TYPE_ID) {
      return $this->getGlobalLockStrategy();
    }

    $override = $this->getCrmRowStrategyOverride();
    if ($override === self::STRATEGY_INHERIT) {
      return $this->getGlobalLockStrategy();
    }

    return $override;
  }

  public function shouldSkipProtectedRow(EntityInterface $entity): bool {
    return $this->resolveEffectiveLockStrategy($entity->getEntityTypeId())
      === self::STRATEGY_SKIP_ROW;
  }

  public function shouldPreserveProtectedFields(EntityInterface $entity): bool {
    return $this->resolveEffectiveLockStrategy($entity->getEntityTypeId())
      === self::STRATEGY_SKIP_FIELD;
  }

  public function shouldReactivatePresentInSnapshot(): bool {
    return (bool) $this->config()->get('present_in_xml.reactivate');
  }

  public function getMissingFromXmlAgentAction(): string {
    return $this->normalizeMissingAction((string) $this->config()->get('missing_from_xml.agent_action'));
  }

  public function getProtectedAgentMissingAction(): string {
    $value = (string) $this->config()->get('missing_from_xml.protected_agent_action');
    return in_array($value, [self::ACTION_DEACTIVATE, self::ACTION_KEEP_ACTIVE], TRUE)
      ? $value
      : self::ACTION_KEEP_ACTIVE;
  }

  public function shouldLockOnBoCreate(): bool {
    return (bool) $this->config()->get('bo_create.default_internal_lock');
  }

  public function shouldDeactivateMissingEntity(EntityInterface $agent, bool $shouldBeActive): bool {
    if ($shouldBeActive || !$agent instanceof ContentEntityInterface || !(bool) $agent->get('status')->value) {
      return FALSE;
    }

    if ($this->isAgentProtected($agent)) {
      return $this->getProtectedAgentMissingAction() === self::ACTION_DEACTIVATE;
    }

    return $this->getMissingFromXmlAgentAction() === self::ACTION_DEACTIVATE;
  }

  private function normalizeMissingAction(string $action): string {
    return in_array($action, [self::ACTION_DEACTIVATE, self::ACTION_KEEP_ACTIVE], TRUE)
      ? $action
      : self::ACTION_DEACTIVATE;
  }

  private function isAgentProtected(EntityInterface $agent): bool {
    if (!$agent instanceof ContentEntityInterface || !$agent->hasField('internal_lock')) {
      return FALSE;
    }

    return (bool) $agent->get('internal_lock')->value;
  }

  private function getGlobalLockStrategy(): string {
    return $this->globalResolver->getGlobalLockStrategy();
  }

  private function config() {
    return $this->configFactory->get(self::CONFIG_NAME);
  }

}
