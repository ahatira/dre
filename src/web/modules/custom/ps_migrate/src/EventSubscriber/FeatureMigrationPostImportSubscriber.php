<?php

declare(strict_types=1);

namespace Drupal\ps_migrate\EventSubscriber;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\migrate\Event\MigrateEvents;
use Drupal\migrate\Event\MigrateImportEvent;
use Drupal\ps_core\Plugin\ImportGovernance\ImportGovernancePostImportPolicyInterface;
use Drupal\ps_core\Service\EntityProtectionManagerInterface;
use Drupal\ps_core\Service\ImportGovernanceRegistry;
use Drupal\ps_core\Service\ImportGovernanceSnapshotSynchronizer;
use Drupal\ps_feature\Entity\FeatureDefinition;
use Drupal\ps_migrate\Service\FeatureImportResolver;
use Drupal\ps_migrate\Service\FeaturePayloadDefaultsNormalizer;
use Drupal\ps_migrate\Service\FeatureTechnicalElementSourceLoader;
use Drupal\ps_migrate\Service\FeatureTechnicalElementValidator;
use Drupal\ps_migrate\Service\ImportRunSnapshotCollector;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Synchronizes feature writers after feature migrations complete.
 */
final class FeatureMigrationPostImportSubscriber implements EventSubscriberInterface {

  private const DEFINITION_ENTITY_KEY = 'fb_feature_definition';

  private const GROUP_ENTITY_KEY = 'fb_feature_group';

  /**
   * Constructs a FeatureMigrationPostImportSubscriber object.
   */
  public function __construct(
    private readonly FeatureTechnicalElementSourceLoader $sourceLoader,
    private readonly FeatureImportResolver $importResolver,
    private readonly FeatureTechnicalElementValidator $validator,
    private readonly FeaturePayloadDefaultsNormalizer $payloadDefaultsNormalizer,
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly EntityProtectionManagerInterface $protectionManager,
    private readonly ImportGovernanceRegistry $governanceRegistry,
    private readonly ImportGovernanceSnapshotSynchronizer $snapshotSynchronizer,
    private readonly ImportRunSnapshotCollector $snapshotCollector,
    private readonly LoggerInterface $logger,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {
    return [
      MigrateEvents::POST_IMPORT => ['onPostImport', 0],
    ];
  }

  /**
   * Synchronizes entities after a feature migration completes.
   */
  public function onPostImport(MigrateImportEvent $event): void {
    $migration = $event->getMigration();
    $migration_id = $migration->id();
    $policy = $this->governanceRegistry->getPostImportPolicyForMigration($migration_id);
    if ($policy === NULL) {
      return;
    }

    $files = $migration->getSourceConfiguration()['files'] ?? [];
    $files = is_array($files) ? $files : [$files];
    $files = array_values(array_filter(array_map('trim', $files), static fn(string $file): bool => $file !== ''));
    if ($files === []) {
      return;
    }

    if ($migration_id === 'ps_feature_groups_from_xml') {
      $this->synchronizeGroups($files, $policy);
      return;
    }

    $this->synchronizeDefinitions($files, $policy);
  }

  /**
   * Synchronizes feature groups against the latest XML snapshot.
   *
   * @param string[] $files
   *   Source XML files.
   * @param \Drupal\ps_core\Plugin\ImportGovernance\ImportGovernancePostImportPolicyInterface $policy
   *   Domain post-import governance policy.
   */
  private function synchronizeGroups(array $files, ImportGovernancePostImportPolicyInterface $policy): void {
    $active_groups = $this->buildGroupSnapshot($files);
    $storage = $this->entityTypeManager->getStorage('fb_feature_group');
    $existing_ids = $storage->getQuery()->accessCheck(FALSE)->execute();

    foreach ($storage->loadMultiple($existing_ids) as $group) {
      $group_id = $group->id();
      $should_be_active = isset($active_groups[$group_id]);
      $is_active = (bool) $group->status();

      if ($policy->shouldReactivatePresentInXml() && $should_be_active && !$is_active) {
        $group->set('status', TRUE);
        $group->save();
        $this->snapshotCollector->recordFeatureGroupStatusChange($group_id, FALSE, TRUE);
        $this->logger->info('Reactivated feature group @group_id from XML snapshot.', ['@group_id' => $group_id]);
      }
      elseif ($policy->shouldDeactivateMissingGroup($group, $should_be_active)) {
        $group->set('status', FALSE);
        $group->save();
        $this->snapshotCollector->recordFeatureGroupStatusChange($group_id, TRUE, FALSE);
        $this->logger->warning('Deactivated feature group @group_id because it disappeared from XML.', ['@group_id' => $group_id]);
      }

      if ($should_be_active) {
        $this->syncGroupFieldsFromSnapshot($group, $active_groups[$group_id], $policy);
      }
    }
  }

  /**
   * Synchronizes feature definitions against the latest XML snapshot.
   *
   * @param string[] $files
   *   Source XML files.
   * @param \Drupal\ps_core\Plugin\ImportGovernance\ImportGovernancePostImportPolicyInterface $policy
   *   Domain post-import governance policy.
   */
  private function synchronizeDefinitions(array $files, ImportGovernancePostImportPolicyInterface $policy): void {
    $active_definitions = $this->buildDefinitionSnapshot($files);
    $storage = $this->entityTypeManager->getStorage('fb_feature_definition');
    $existing_ids = $storage->getQuery()->accessCheck(FALSE)->execute();

    foreach ($storage->loadMultiple($existing_ids) as $definition) {
      if (!$definition instanceof FeatureDefinition) {
        continue;
      }

      $definition_id = $definition->id();
      $should_be_active = isset($active_definitions[$definition_id]);
      $is_active = (bool) $definition->status();

      if ($policy->shouldReactivatePresentInXml() && $should_be_active && !$is_active) {
        $definition->set('status', TRUE);
        $definition->save();
        $this->snapshotCollector->recordFeatureDefinitionStatusChange($definition_id, FALSE, TRUE);
        $this->logger->info('Reactivated feature definition @definition_id from XML snapshot.', ['@definition_id' => $definition_id]);
      }
      elseif ($policy->shouldDeactivateMissingDefinition($definition, $should_be_active)) {
        $definition->set('status', FALSE);
        $definition->save();
        $this->snapshotCollector->recordFeatureDefinitionStatusChange($definition_id, TRUE, FALSE);
        $this->logger->warning('Deactivated feature definition @definition_id because it disappeared from XML.', ['@definition_id' => $definition_id]);
      }

      if (!$should_be_active || $this->protectionManager->isCatalogueProtected($definition)) {
        continue;
      }

      $this->syncDefinitionFieldsFromSnapshot(
        $definition,
        $active_definitions[$definition_id],
        $policy,
      );
    }
  }

  /**
   * Applies configured snapshot fields to a feature definition.
   */
  private function syncDefinitionFieldsFromSnapshot(
    FeatureDefinition $definition,
    array $source,
    ImportGovernancePostImportPolicyInterface $policy,
  ): void {
    $fields = $policy->getSnapshotFieldSyncFields(self::DEFINITION_ENTITY_KEY);
    if ($fields === []) {
      return;
    }

    $changed = $this->snapshotSynchronizer->synchronizeFields($definition, $source, $fields);
    if (!$changed) {
      return;
    }

    $definition->save();

    if (in_array('payload_defaults', $fields, TRUE)) {
      $this->logger->info(
        'Normalized payload defaults for feature definition @definition_id.',
        ['@definition_id' => $definition->id()],
      );
    }
  }

  /**
   * Applies configured snapshot fields to a feature group.
   */
  private function syncGroupFieldsFromSnapshot(
    EntityInterface $group,
    array $source,
    ImportGovernancePostImportPolicyInterface $policy,
  ): void {
    if ($this->protectionManager->isCatalogueProtected($group)) {
      return;
    }

    $fields = $policy->getSnapshotFieldSyncFields(self::GROUP_ENTITY_KEY);
    if ($fields === []) {
      return;
    }

    if ($this->snapshotSynchronizer->synchronizeFields($group, $source, $fields)) {
      $group->save();
    }
  }

  /**
   * Builds the active group snapshot from the XML source.
   *
   * @param string[] $files
   *   Source XML files.
   *
   * @return array<string, array<string, mixed>>
   *   Group snapshot keyed by group ID.
   */
  private function buildGroupSnapshot(array $files): array {
    $snapshot = [];

    foreach ($this->loadElements($files) as $element) {
      $group_id = $this->importResolver->resolveGroupId($element['feature_code'], $element['group_code']);
      if ($group_id === '') {
        continue;
      }

      $snapshot[$group_id] = [
        'label' => $element['group_code'],
        'description' => $element['group_code'],
        'weight' => $element['source_index'],
        'status' => TRUE,
      ];
    }

    return $snapshot;
  }

  /**
   * Builds the active definition snapshot from the XML source.
   *
   * @param string[] $files
   *   Source XML files.
   *
   * @return array<string, array<string, mixed>>
   *   Definition snapshot keyed by definition ID.
   */
  private function buildDefinitionSnapshot(array $files): array {
    $snapshot = [];

    foreach ($this->loadElements($files) as $element) {
      $definition_id = $this->importResolver->buildDefinitionId($element['feature_code']);
      if ($definition_id === '' || isset($snapshot[$definition_id])) {
        continue;
      }

      $snapshot[$definition_id] = [
        'label' => $element['label'],
        'description' => $element['description'],
        'code' => $element['feature_code'],
        'group' => $this->importResolver->resolveGroupId($element['feature_code'], $element['group_code']),
        'type_driver' => $element['type_driver'],
        'weight' => $element['weight'],
        'status' => TRUE,
        'payload_defaults' => $this->payloadDefaultsNormalizer->normalize($element['payload_defaults']),
      ];
    }

    return $snapshot;
  }

  /**
   * Loads and normalizes the source technical elements.
   *
   * @param string[] $files
   *   Source XML files.
   *
   * @return array<int, array<string, mixed>>
   *   Normalized source rows.
   */
  private function loadElements(array $files): array {
    $elements = [];

    foreach ($files as $file) {
      foreach ($this->sourceLoader->loadFromFile($file) as $element) {
        $record = $element->toRecord();
        if (!$record['valid']) {
          continue;
        }

        $validation = $this->validator->validate([
          'group_code' => $record['group_code'],
          'feature_code' => $record['feature_code'],
          'definition_id' => $this->importResolver->buildDefinitionId($record['feature_code']),
          'type_driver' => $this->guessTypeDriver($record['payload']),
          'payload' => $record['payload'],
        ]);
        if ($validation['errors'] !== []) {
          continue;
        }

        $elements[] = [
          'group_code' => $record['group_code'],
          'feature_code' => $record['feature_code'],
          'label' => $record['label'],
          'description' => $record['payload']['complement'] ?? $record['label'],
          'type_driver' => $this->guessTypeDriver($record['payload']),
          'weight' => $record['source_index'],
          'payload_defaults' => $this->extractPayloadDefaults($record['payload']),
          'source_index' => $record['source_index'],
        ];
      }
    }

    return $elements;
  }

  /**
   * Extracts payload defaults from the normalized payload.
   */
  private function extractPayloadDefaults(array $payload): array {
    $defaults = [];

    if (!empty($payload['unit'])) {
      $defaults['unit'] = $payload['unit'];
    }

    return $this->payloadDefaultsNormalizer->normalize($defaults);
  }

  /**
   * Guesses the type driver from the payload shape.
   */
  private function guessTypeDriver(array $payload): string {
    $value = $payload['value'] ?? NULL;
    $unit = $payload['unit'] ?? NULL;

    if ($value === NULL && $unit === NULL) {
      return 'flag';
    }

    if ($value !== NULL && is_numeric(str_replace(',', '.', (string) $value))) {
      return 'numeric';
    }

    if ($value !== NULL && preg_match('/^(yes|no|true|false|oui|non|0|1)$/i', (string) $value) === 1) {
      return 'yes_no';
    }

    return 'text';
  }

}
