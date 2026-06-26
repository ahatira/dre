<?php

declare(strict_types=1);

namespace Drupal\ps_media\Plugin\ImportGovernance;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\ps_core\Plugin\ImportGovernance\ImportGovernancePolicyBase;
use Drupal\ps_core\Plugin\ImportGovernance\ImportGovernanceSnapshotPostImportPolicyInterface;
use Drupal\ps_media\Service\MediaImportGovernance;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Import governance policy for CRM offer media.
 *
 * @ImportGovernancePolicy(
 *   id = "media",
 *   admin_label = @Translation("Media"),
 *   description = @Translation("Offer media CRM/XML import lock strategy and alt text protection."),
 *   settings_route = "ps_media.governance_domain_settings",
 *   weight = 40,
 * )
 */
final class MediaImportGovernancePolicy extends ImportGovernancePolicyBase implements ContainerFactoryPluginInterface, ImportGovernanceSnapshotPostImportPolicyInterface {

  /**
   * Destination properties preserved when CRM alt overwrite is disabled.
   *
   * @var string[]
   */
  private const ALT_PROPERTIES = [
    'field_media_image/alt',
    'field_media_link/title',
  ];

  /**
   * Media XML migrations synchronized after import.
   *
   * @var string[]
   */
  private const MEDIA_XML_MIGRATION_IDS = [
    'ps_media_from_xml',
    'ps_media_virtual_tour_from_xml',
  ];

  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    private readonly MediaImportGovernance $importGovernance,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(
    ContainerInterface $container,
    array $configuration,
    $plugin_id,
    $plugin_definition,
  ): static {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('ps_media.import_governance'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getEntityTypeIds(): array {
    return [MediaImportGovernance::ENTITY_TYPE_ID];
  }

  /**
   * {@inheritdoc}
   */
  public function shouldSkipProtectedRow(EntityInterface $entity): bool {
    return $this->importGovernance->shouldSkipProtectedRow($entity);
  }

  /**
   * {@inheritdoc}
   */
  public function shouldPreserveProtectedFields(EntityInterface $entity): bool {
    return $this->importGovernance->shouldPreserveProtectedFields($entity);
  }

  /**
   * {@inheritdoc}
   */
  public function resolveEffectiveLockStrategy(string $entityTypeId): string {
    return $this->importGovernance->resolveEffectiveLockStrategy($entityTypeId);
  }

  /**
   * {@inheritdoc}
   */
  public function getAdditionalPreservedProperties(EntityInterface $entity): array {
    if ($this->importGovernance->allowCrmOverwriteAlt()) {
      return [];
    }

    return self::ALT_PROPERTIES;
  }

  /**
   * {@inheritdoc}
   */
  public function getSupportedMigrationIds(): array {
    return self::MEDIA_XML_MIGRATION_IDS;
  }

  /**
   * {@inheritdoc}
   */
  public function shouldReactivatePresentInSnapshot(): bool {
    return $this->importGovernance->shouldReactivatePresentInSnapshot();
  }

  /**
   * {@inheritdoc}
   */
  public function shouldDeactivateMissingEntity(EntityInterface $entity, bool $shouldBeActive): bool {
    return $this->importGovernance->shouldDeactivateMissingEntity($entity, $shouldBeActive);
  }

  /**
   * {@inheritdoc}
   */
  public function getSnapshotFieldSyncEntityKeys(): array {
    return $this->importGovernance->getSnapshotFieldSyncEntityKeys();
  }

  /**
   * {@inheritdoc}
   */
  public function getSnapshotFieldSyncFields(string $entityKey): array {
    return $this->importGovernance->getSnapshotFieldSyncFields($entityKey);
  }

}
