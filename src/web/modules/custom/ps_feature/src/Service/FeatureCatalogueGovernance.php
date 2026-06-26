<?php

declare(strict_types=1);

namespace Drupal\ps_feature\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\ps_core\ImportGovernance\ImportGovernanceSnapshotEntityKey;
use Drupal\ps_core\Service\ImportGovernanceGlobalResolver;
use Drupal\ps_core\Service\ImportGovernanceSnapshotFieldSettings;
use Drupal\ps_core\Service\EntityProtectionManagerInterface;

/**
 * Reads feature catalogue governance settings and resolves import behaviour.
 */
class FeatureCatalogueGovernance {

  public const CONFIG_NAME = 'ps_feature.catalogue_governance';

  /**
   * Canonical feature group used when import rows have no group code.
   */
  public const DEFAULT_IMPORT_GROUP_ID = 'informations_complementaires';

  public const STRATEGY_INHERIT = 'inherit';

  public const STRATEGY_LOG_ONLY = 'log_only';

  public const STRATEGY_SKIP_ROW = 'skip_row';

  public const STRATEGY_SKIP_FIELD = 'skip_field';

  public const ACTION_DEACTIVATE = 'deactivate';

  public const ACTION_KEEP_ACTIVE = 'keep_active';

  public const MISSING_DEFINITION_SKIP_LOG = 'skip_log';

  public const MISSING_DEFINITION_CREATE_STUB = 'create_stub';

  /**
   * Feature entity types governed by this configuration.
   *
   * @var string[]
   */
  private const FEATURE_ENTITY_TYPES = [
    'fb_feature_definition',
    'fb_feature_group',
  ];

  public function __construct(
    private readonly ConfigFactoryInterface $configFactory,
    private readonly EntityProtectionManagerInterface $protectionManager,
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly ImportGovernanceGlobalResolver $globalResolver,
    private readonly ImportGovernanceSnapshotFieldSettings $snapshotFieldSettings,
  ) {}

  /**
   * Returns the CRM row lock strategy override for feature catalogue entities.
   */
  public function getCrmRowStrategyOverride(): string {
    $value = (string) $this->config()->get('crm_row_strategy_override');
    return in_array($value, [
      self::STRATEGY_INHERIT,
      self::STRATEGY_SKIP_FIELD,
      self::STRATEGY_SKIP_ROW,
    ], TRUE) ? $value : self::STRATEGY_INHERIT;
  }

  /**
   * Whether CRM imports may overwrite display-related definition fields.
   */
  public function allowCrmOverwriteDisplay(): bool {
    return (bool) $this->config()->get('allow_crm_overwrite_display');
  }

  /**
   * Resolves the effective CRM lock strategy for a feature entity type.
   */
  public function resolveEffectiveLockStrategy(string $entityTypeId): string {
    if (!in_array($entityTypeId, self::FEATURE_ENTITY_TYPES, TRUE)) {
      return $this->getGlobalLockStrategy();
    }

    $override = $this->getCrmRowStrategyOverride();
    if ($override === self::STRATEGY_INHERIT) {
      return $this->getGlobalLockStrategy();
    }

    return $override;
  }

  /**
   * Whether a protected feature entity row should be skipped entirely.
   */
  public function shouldSkipProtectedRow(EntityInterface $entity): bool {
    return $this->resolveEffectiveLockStrategy($entity->getEntityTypeId())
      === self::STRATEGY_SKIP_ROW;
  }

  /**
   * Whether protected feature entities should preserve internal field values.
   */
  public function shouldPreserveProtectedFields(EntityInterface $entity): bool {
    return $this->resolveEffectiveLockStrategy($entity->getEntityTypeId())
      === self::STRATEGY_SKIP_FIELD;
  }

  /**
   * Whether catalogue-protected entities should be kept active when absent from XML.
   */
  public function shouldKeepProtectedGroupActiveWhenMissing(): bool {
    return TRUE;
  }

  /**
   * Action for non-protected groups missing from the XML snapshot.
   */
  public function getMissingFromXmlGroupAction(): string {
    return $this->normalizeAction((string) $this->config()->get('missing_from_xml.group_action'));
  }

  /**
   * Action for non-protected definitions missing from the XML snapshot.
   */
  public function getMissingFromXmlDefinitionAction(): string {
    return $this->normalizeAction((string) $this->config()->get('missing_from_xml.definition_action'));
  }

  /**
   * Action for catalogue-protected definitions missing from the XML snapshot.
   */
  public function getProtectedDefinitionMissingAction(): string {
    $value = (string) $this->config()->get('missing_from_xml.protected_definition_action');
    return in_array($value, [self::ACTION_DEACTIVATE, self::ACTION_KEEP_ACTIVE], TRUE)
      ? $value
      : self::ACTION_KEEP_ACTIVE;
  }

  /**
   * Whether entities present in the XML snapshot should be reactivated.
   */
  public function shouldReactivatePresentInXml(): bool {
    return (bool) $this->config()->get('present_in_xml.reactivate');
  }

  /**
   * Entity keys covered by snapshot field synchronization.
   *
   * @return string[]
   *   Snapshot entity keys.
   */
  public function getSnapshotFieldSyncEntityKeys(): array {
    return [
      ImportGovernanceSnapshotEntityKey::encode('fb_feature_definition'),
      ImportGovernanceSnapshotEntityKey::encode('fb_feature_group'),
    ];
  }

  /**
   * Returns configured snapshot sync fields for an entity key.
   *
   * @return string[]
   *   Normalized field names.
   */
  public function getSnapshotFieldSyncFields(string $entityKey): array {
    return $this->snapshotFieldSettings->getConfiguredFields($this->config(), $entityKey);
  }

  /**
   * Whether BO-created definitions should default to internal lock.
   */
  public function shouldLockOnBoCreate(): bool {
    return (bool) $this->config()->get('bo_create.default_internal_lock');
  }

  /**
   * Whether CSV imports should mark definitions as internally locked.
   */
  public function shouldLockOnCsvImport(): bool {
    return (bool) $this->config()->get('csv_import.lock_on_import');
  }

  /**
   * Default feature group ID when an import row has no group/category code.
   *
   * Falls back to informations_complementaires when unset or invalid.
   */
  public function getDefaultImportGroupId(): string {
    $groupId = trim((string) $this->config()->get('import_defaults.default_group'));
    if ($groupId === '') {
      return self::DEFAULT_IMPORT_GROUP_ID;
    }

    $group = $this->entityTypeManager->getStorage('fb_feature_group')->load($groupId);
    return $group !== NULL ? $groupId : self::DEFAULT_IMPORT_GROUP_ID;
  }

  /**
   * Behaviour when an offer import references a missing catalogue definition.
   */
  public function getOfferValuesMissingDefinitionAction(): string {
    $value = (string) $this->config()->get('offer_values.missing_definition');
    return in_array($value, [
      self::MISSING_DEFINITION_SKIP_LOG,
      self::MISSING_DEFINITION_CREATE_STUB,
    ], TRUE) ? $value : self::MISSING_DEFINITION_SKIP_LOG;
  }

  /**
   * Whether offer imports may create stub catalogue definitions.
   */
  public function shouldCreateStubDefinitionForMissingOfferValue(): bool {
    return $this->getOfferValuesMissingDefinitionAction() === self::MISSING_DEFINITION_CREATE_STUB;
  }

  /**
   * Whether offer imports may update translated feature definition labels.
   */
  public function shouldSyncDefinitionLabelsFromOfferImport(): bool {
    return (bool) $this->config()->get('offer_values.sync_definition_labels');
  }

  /**
   * Whether a group should be deactivated when absent from the XML snapshot.
   */
  public function shouldDeactivateMissingGroup(EntityInterface $group, bool $shouldBeActive): bool {
    if ($shouldBeActive || !(bool) $group->get('status')) {
      return FALSE;
    }

    if ($this->protectionManager->isCatalogueProtected($group)) {
      return !$this->shouldKeepProtectedGroupActiveWhenMissing();
    }

    return $this->getMissingFromXmlGroupAction() === self::ACTION_DEACTIVATE;
  }

  /**
   * Whether a definition should be deactivated when absent from the XML snapshot.
   */
  public function shouldDeactivateMissingDefinition(EntityInterface $definition, bool $shouldBeActive): bool {
    if ($shouldBeActive || !(bool) $definition->get('status')) {
      return FALSE;
    }

    if ($this->protectionManager->isCatalogueProtected($definition)) {
      return $this->getProtectedDefinitionMissingAction() === self::ACTION_DEACTIVATE;
    }

    return $this->getMissingFromXmlDefinitionAction() === self::ACTION_DEACTIVATE;
  }

  /**
   * Normalizes a missing-from-XML action value.
   */
  private function normalizeAction(string $action): string {
    return in_array($action, [self::ACTION_DEACTIVATE, self::ACTION_KEEP_ACTIVE], TRUE)
      ? $action
      : self::ACTION_DEACTIVATE;
  }

  /**
   * Returns the global CRM import lock strategy when inheritance is enabled.
   */
  private function getGlobalLockStrategy(): string {
    return $this->globalResolver->getGlobalLockStrategy();
  }

  /**
   * Returns the editable governance config object.
   */
  private function config() {
    return $this->configFactory->get(self::CONFIG_NAME);
  }

}
