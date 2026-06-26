<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\ps_core\ImportGovernance\ImportGovernanceSnapshotEntityKey;
use Drupal\ps_core\Service\ImportGovernanceGlobalResolver;
use Drupal\ps_core\Service\ImportGovernanceSnapshotFieldSettings;

/**
 * Reads offer import governance settings and resolves CRM import behaviour.
 */
class OfferImportGovernance {

  public const CONFIG_NAME = 'ps_offer.import_governance';

  public const STRATEGY_INHERIT = 'inherit';

  public const STRATEGY_SKIP_ROW = 'skip_row';

  public const STRATEGY_SKIP_FIELD = 'skip_field';

  public const ACTION_UNPUBLISH = 'unpublish';

  public const ACTION_KEEP_PUBLISHED = 'keep_published';

  public const OFFER_BUNDLE = 'offer';

  public function __construct(
    private readonly ConfigFactoryInterface $configFactory,
    private readonly ImportGovernanceGlobalResolver $globalResolver,
    private readonly ImportGovernanceSnapshotFieldSettings $snapshotFieldSettings,
  ) {}

  /**
   * Returns the CRM row lock strategy override for offer nodes.
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
   * Whether CRM imports may overwrite manually curated reference fields.
   */
  public function allowCrmOverwriteReference(): bool {
    return (bool) $this->config()->get('allow_crm_overwrite_reference');
  }

  /**
   * Resolves the effective CRM lock strategy for offer imports.
   */
  public function resolveEffectiveLockStrategy(string $entityTypeId): string {
    if ($entityTypeId !== 'node') {
      return $this->getGlobalLockStrategy();
    }

    $override = $this->getCrmRowStrategyOverride();
    if ($override === self::STRATEGY_INHERIT) {
      return $this->getGlobalLockStrategy();
    }

    return $override;
  }

  /**
   * Whether a protected offer row should be skipped entirely during import.
   */
  public function shouldSkipProtectedRow(EntityInterface $entity): bool {
    return $this->resolveEffectiveLockStrategy($entity->getEntityTypeId())
      === self::STRATEGY_SKIP_ROW;
  }

  /**
   * Whether protected offers should preserve internal field values on import.
   */
  public function shouldPreserveProtectedFields(EntityInterface $entity): bool {
    return $this->resolveEffectiveLockStrategy($entity->getEntityTypeId())
      === self::STRATEGY_SKIP_FIELD;
  }

  /**
   * Whether offers present in the XML snapshot should be republished.
   */
  public function shouldReactivatePresentInXml(): bool {
    return (bool) $this->config()->get('present_in_xml.reactivate');
  }

  /**
   * Action for non-protected offers missing from the XML snapshot.
   */
  public function getMissingFromXmlOfferAction(): string {
    return $this->normalizeMissingAction((string) $this->config()->get('missing_from_xml.offer_action'));
  }

  /**
   * Action for protected offers missing from the XML snapshot.
   */
  public function getProtectedOfferMissingAction(): string {
    $value = (string) $this->config()->get('missing_from_xml.protected_offer_action');
    return in_array($value, [self::ACTION_UNPUBLISH, self::ACTION_KEEP_PUBLISHED], TRUE)
      ? $value
      : self::ACTION_KEEP_PUBLISHED;
  }

  /**
   * Whether BO-created offers should default to internal lock.
   */
  public function shouldLockOnBoCreate(): bool {
    return (bool) $this->config()->get('bo_create.default_internal_lock');
  }

  public function shouldUnpublishMissingOffer(EntityInterface $offer, bool $shouldBePublished): bool {
    if (!$offer instanceof ContentEntityInterface) {
      return FALSE;
    }
    if ($shouldBePublished || !$offer->isPublished()) {
      return FALSE;
    }

    if ($this->isOfferProtected($offer)) {
      return $this->getProtectedOfferMissingAction() === self::ACTION_UNPUBLISH;
    }

    return $this->getMissingFromXmlOfferAction() === self::ACTION_UNPUBLISH;
  }

  /**
   * Whether an offer should be deactivated/unpublished when absent from XML.
   */
  public function shouldDeactivateMissingEntity(EntityInterface $offer, bool $shouldBeActive): bool {
    return $this->shouldUnpublishMissingOffer($offer, $shouldBeActive);
  }

  /**
   * Whether offers present in the XML snapshot should be reactivated.
   */
  public function shouldReactivatePresentInSnapshot(): bool {
    return $this->shouldReactivatePresentInXml();
  }

  /**
   * Entity keys covered by snapshot field synchronization.
   *
   * @return string[]
   *   Snapshot entity keys.
   */
  public function getSnapshotFieldSyncEntityKeys(): array {
    return [
      ImportGovernanceSnapshotEntityKey::encode('node', self::OFFER_BUNDLE),
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
   * Normalizes a missing-from-XML action value.
   */
  private function normalizeMissingAction(string $action): string {
    return in_array($action, [self::ACTION_UNPUBLISH, self::ACTION_KEEP_PUBLISHED], TRUE)
      ? $action
      : self::ACTION_UNPUBLISH;
  }

  /**
   * Whether the offer is protected against CRM overwrites.
   */
  private function isOfferProtected(EntityInterface $offer): bool {
    if (!$offer instanceof ContentEntityInterface || !$offer->hasField('field_internal_lock')) {
      return FALSE;
    }

    return (bool) $offer->get('field_internal_lock')->value;
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
