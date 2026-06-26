<?php

declare(strict_types=1);

namespace Drupal\ps_feature\Plugin\ImportGovernance;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\ps_core\Plugin\ImportGovernance\ImportGovernancePolicyBase;
use Drupal\ps_core\Plugin\ImportGovernance\ImportGovernanceCatalogueImportPolicyInterface;
use Drupal\ps_core\Plugin\ImportGovernance\ImportGovernancePostImportPolicyInterface;
use Drupal\ps_feature\Service\FeatureCatalogueGovernance;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Import governance policy for the feature catalogue.
 *
 * @ImportGovernancePolicy(
 *   id = "features",
 *   admin_label = @Translation("Features"),
 *   description = @Translation("Feature catalogue CRM/XML, CSV and offer import rules."),
 *   settings_route = "ps_feature.governance_domain_settings",
 *   weight = 0,
 * )
 */
final class FeatureImportGovernancePolicy extends ImportGovernancePolicyBase implements ContainerFactoryPluginInterface, ImportGovernanceCatalogueImportPolicyInterface, ImportGovernancePostImportPolicyInterface {

  /**
   * Feature XML migrations synchronized after import.
   *
   * @var string[]
   */
  private const FEATURE_XML_MIGRATION_IDS = [
    'ps_feature_groups_from_xml',
    'ps_feature_definitions_from_xml',
  ];

  /**
   * Definition display fields preserved when CRM overwrite is disabled.
   *
   * @var string[]
   */
  private const DEFINITION_DISPLAY_FIELDS = [
    'icon',
    'expose_as_filter',
  ];

  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    private readonly FeatureCatalogueGovernance $catalogueGovernance,
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
      $container->get('ps_feature.catalogue_governance'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getEntityTypeIds(): array {
    return [
      'fb_feature_definition',
      'fb_feature_group',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function shouldSkipProtectedRow(EntityInterface $entity): bool {
    return $this->catalogueGovernance->shouldSkipProtectedRow($entity);
  }

  /**
   * {@inheritdoc}
   */
  public function shouldPreserveProtectedFields(EntityInterface $entity): bool {
    return $this->catalogueGovernance->shouldPreserveProtectedFields($entity);
  }

  /**
   * {@inheritdoc}
   */
  public function resolveEffectiveLockStrategy(string $entityTypeId): string {
    return $this->catalogueGovernance->resolveEffectiveLockStrategy($entityTypeId);
  }

  /**
   * {@inheritdoc}
   */
  public function getAdditionalPreservedProperties(EntityInterface $entity): array {
    if ($entity->getEntityTypeId() !== 'fb_feature_definition') {
      return [];
    }
    if ($this->catalogueGovernance->allowCrmOverwriteDisplay()) {
      return [];
    }

    return self::DEFINITION_DISPLAY_FIELDS;
  }

  /**
   * {@inheritdoc}
   */
  public function getDefaultImportGroupId(): string {
    return $this->catalogueGovernance->getDefaultImportGroupId();
  }

  /**
   * {@inheritdoc}
   */
  public function shouldCreateStubDefinitionForMissingOfferValue(): bool {
    return $this->catalogueGovernance->shouldCreateStubDefinitionForMissingOfferValue();
  }

  /**
   * {@inheritdoc}
   */
  public function shouldSyncDefinitionLabelsFromOfferImport(): bool {
    return $this->catalogueGovernance->shouldSyncDefinitionLabelsFromOfferImport();
  }

  /**
   * {@inheritdoc}
   */
  public function getSupportedMigrationIds(): array {
    return self::FEATURE_XML_MIGRATION_IDS;
  }

  /**
   * {@inheritdoc}
   */
  public function shouldReactivatePresentInXml(): bool {
    return $this->catalogueGovernance->shouldReactivatePresentInXml();
  }

  /**
   * {@inheritdoc}
   */
  public function shouldDeactivateMissingGroup(EntityInterface $group, bool $shouldBeActive): bool {
    return $this->catalogueGovernance->shouldDeactivateMissingGroup($group, $shouldBeActive);
  }

  /**
   * {@inheritdoc}
   */
  public function shouldDeactivateMissingDefinition(EntityInterface $definition, bool $shouldBeActive): bool {
    return $this->catalogueGovernance->shouldDeactivateMissingDefinition($definition, $shouldBeActive);
  }

  /**
   * {@inheritdoc}
   */
  public function getSnapshotFieldSyncEntityKeys(): array {
    return $this->catalogueGovernance->getSnapshotFieldSyncEntityKeys();
  }

  /**
   * {@inheritdoc}
   */
  public function getSnapshotFieldSyncFields(string $entityKey): array {
    return $this->catalogueGovernance->getSnapshotFieldSyncFields($entityKey);
  }

}
