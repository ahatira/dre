<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Plugin\ImportGovernance;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\ps_core\Plugin\ImportGovernance\ImportGovernancePolicyBase;
use Drupal\ps_core\Plugin\ImportGovernance\ImportGovernanceSnapshotPostImportPolicyInterface;
use Drupal\ps_offer\Service\OfferImportGovernance;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Import governance policy for property offers.
 *
 * @ImportGovernancePolicy(
 *   id = "offer",
 *   admin_label = @Translation("Offers"),
 *   description = @Translation("Offer CRM/XML import lock strategy and reference protection."),
 *   settings_route = "ps_offer.governance_domain_settings",
 *   weight = 10,
 * )
 */
final class OfferImportGovernancePolicy extends ImportGovernancePolicyBase implements ContainerFactoryPluginInterface, ImportGovernanceSnapshotPostImportPolicyInterface {

  /**
   * Reference fields preserved when CRM overwrite is disabled.
   *
   * @var string[]
   */
  private const REFERENCE_FIELDS = [
    'field_reference',
    'field_reference_auto',
  ];

  /**
   * Offer XML migrations synchronized after import.
   *
   * @var string[]
   */
  private const OFFER_XML_MIGRATION_IDS = [
    'ps_offer_from_xml',
  ];

  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    private readonly OfferImportGovernance $importGovernance,
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
      $container->get('ps_offer.import_governance'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getEntityTypeIds(): array {
    return ['node'];
  }

  /**
   * {@inheritdoc}
   */
  public function getBundleIds(): array {
    return [OfferImportGovernance::OFFER_BUNDLE];
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
    if ($this->importGovernance->allowCrmOverwriteReference()) {
      return [];
    }

    return self::REFERENCE_FIELDS;
  }

  /**
   * {@inheritdoc}
   */
  public function getSupportedMigrationIds(): array {
    return self::OFFER_XML_MIGRATION_IDS;
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
