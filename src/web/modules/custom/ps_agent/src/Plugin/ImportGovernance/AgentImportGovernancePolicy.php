<?php

declare(strict_types=1);

namespace Drupal\ps_agent\Plugin\ImportGovernance;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\ps_agent\Service\AgentImportGovernance;
use Drupal\ps_core\Plugin\ImportGovernance\ImportGovernancePolicyBase;
use Drupal\ps_core\Plugin\ImportGovernance\ImportGovernanceSnapshotPostImportPolicyInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Import governance policy for CRM agents.
 *
 * @ImportGovernancePolicy(
 *   id = "agent",
 *   admin_label = @Translation("Agents"),
 *   description = @Translation("Agent CRM/XML import lock strategy and contact protection."),
 *   settings_route = "ps_agent.governance_domain_settings",
 *   weight = 20,
 * )
 */
final class AgentImportGovernancePolicy extends ImportGovernancePolicyBase implements ContainerFactoryPluginInterface, ImportGovernanceSnapshotPostImportPolicyInterface {

  /**
   * Contact fields preserved when CRM overwrite is disabled.
   *
   * @var string[]
   */
  private const CONTACT_FIELDS = [
    'email',
    'phone',
  ];

  /**
   * Agent XML migrations synchronized after import.
   *
   * @var string[]
   */
  private const AGENT_XML_MIGRATION_IDS = [
    'ps_agent_from_xml',
  ];

  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    private readonly AgentImportGovernance $importGovernance,
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
      $container->get('ps_agent.import_governance'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getEntityTypeIds(): array {
    return [AgentImportGovernance::ENTITY_TYPE_ID];
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
    if ($this->importGovernance->allowCrmOverwriteContact()) {
      return [];
    }

    return self::CONTACT_FIELDS;
  }

  /**
   * {@inheritdoc}
   */
  public function getSupportedMigrationIds(): array {
    return self::AGENT_XML_MIGRATION_IDS;
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
