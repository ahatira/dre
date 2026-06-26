<?php

declare(strict_types=1);

namespace Drupal\ps_media\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\ps_core\Service\ImportGovernanceGlobalResolver;

/**
 * Reads media import governance settings and resolves CRM import behaviour.
 */
class MediaImportGovernance {

  public const CONFIG_NAME = 'ps_media.import_governance';

  public const STRATEGY_INHERIT = 'inherit';

  public const STRATEGY_SKIP_ROW = 'skip_row';

  public const STRATEGY_SKIP_FIELD = 'skip_field';

  public const ACTION_UNPUBLISH = 'unpublish';

  public const ACTION_KEEP_PUBLISHED = 'keep_published';

  public const ENTITY_TYPE_ID = 'media';

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

  public function allowCrmOverwriteAlt(): bool {
    return (bool) $this->config()->get('allow_crm_overwrite_alt');
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

  public function getMissingFromXmlMediaAction(): string {
    return $this->normalizeMissingAction((string) $this->config()->get('missing_from_xml.media_action'));
  }

  public function getProtectedMediaMissingAction(): string {
    $value = (string) $this->config()->get('missing_from_xml.protected_media_action');
    return in_array($value, [self::ACTION_UNPUBLISH, self::ACTION_KEEP_PUBLISHED], TRUE)
      ? $value
      : self::ACTION_KEEP_PUBLISHED;
  }

  public function shouldLockOnBoCreate(): bool {
    return (bool) $this->config()->get('bo_create.default_internal_lock');
  }

  public function shouldUnpublishMissingMedia(EntityInterface $media, bool $shouldBePublished): bool {
    if (!$media instanceof ContentEntityInterface) {
      return FALSE;
    }
    if ($shouldBePublished || !(bool) $media->get('status')->value) {
      return FALSE;
    }

    if ($this->isMediaProtected($media)) {
      return $this->getProtectedMediaMissingAction() === self::ACTION_UNPUBLISH;
    }

    return $this->getMissingFromXmlMediaAction() === self::ACTION_UNPUBLISH;
  }

  public function shouldDeactivateMissingEntity(EntityInterface $media, bool $shouldBeActive): bool {
    return $this->shouldUnpublishMissingMedia($media, $shouldBeActive);
  }

  /**
   * Builds a snapshot lookup key from CRM media source identifiers.
   */
  public static function buildSnapshotKey(string $businessIdParent, int $order): string {
    return trim($businessIdParent) . ':' . $order;
  }

  private function normalizeMissingAction(string $action): string {
    return in_array($action, [self::ACTION_UNPUBLISH, self::ACTION_KEEP_PUBLISHED], TRUE)
      ? $action
      : self::ACTION_UNPUBLISH;
  }

  private function isMediaProtected(EntityInterface $media): bool {
    if (!$media instanceof ContentEntityInterface || !$media->hasField('field_internal_lock')) {
      return FALSE;
    }

    return (bool) $media->get('field_internal_lock')->value;
  }

  private function getGlobalLockStrategy(): string {
    return $this->globalResolver->getGlobalLockStrategy();
  }

  private function config() {
    return $this->configFactory->get(self::CONFIG_NAME);
  }

}
