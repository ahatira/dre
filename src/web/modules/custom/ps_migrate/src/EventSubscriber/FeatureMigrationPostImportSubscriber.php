<?php

declare(strict_types=1);

namespace Drupal\ps_migrate\EventSubscriber;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\migrate\Event\MigrateEvents;
use Drupal\migrate\Event\MigrateImportEvent;
use Drupal\ps_migrate\Service\FeatureMigrationKeyBuilder;
use Drupal\ps_migrate\Service\FeaturePayloadDefaultsNormalizer;
use Drupal\ps_migrate\Service\FeatureTechnicalElementSourceLoader;
use Drupal\ps_migrate\Service\FeatureTechnicalElementValidator;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Synchronizes feature writers after feature migrations complete.
 */
final class FeatureMigrationPostImportSubscriber implements EventSubscriberInterface {

  /**
   * Constructs a FeatureMigrationPostImportSubscriber object.
   */
  public function __construct(
    private readonly FeatureTechnicalElementSourceLoader $sourceLoader,
    private readonly FeatureMigrationKeyBuilder $keyBuilder,
    private readonly FeatureTechnicalElementValidator $validator,
    private readonly FeaturePayloadDefaultsNormalizer $payloadDefaultsNormalizer,
    private readonly EntityTypeManagerInterface $entityTypeManager,
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
    if (!in_array($migration_id, ['ps_feature_groups_from_xml', 'ps_feature_definitions_from_xml'], TRUE)) {
      return;
    }

    $files = $migration->getSourceConfiguration()['files'] ?? [];
    $files = is_array($files) ? $files : [$files];
    $files = array_values(array_filter(array_map('trim', $files), static fn(string $file): bool => $file !== ''));
    if ($files === []) {
      return;
    }

    if ($migration_id === 'ps_feature_groups_from_xml') {
      $this->synchronizeGroups($files);
      return;
    }

    $this->synchronizeDefinitions($files);
  }

  /**
   * Synchronizes feature groups against the latest XML snapshot.
   *
   * @param string[] $files
   *   Source XML files.
   */
  private function synchronizeGroups(array $files): void {
    $active_groups = $this->buildGroupSnapshot($files);
    $storage = $this->entityTypeManager->getStorage('fb_feature_group');
    $existing_ids = $storage->getQuery()->accessCheck(FALSE)->execute();

    foreach ($storage->loadMultiple($existing_ids) as $group) {
      $group_id = $group->id();
      $should_be_active = isset($active_groups[$group_id]);
      $is_active = (bool) $group->status();

      if ($should_be_active && !$is_active) {
        $group->set('status', TRUE);
        $group->save();
        $this->logger->info('Reactivated feature group @group_id from XML snapshot.', ['@group_id' => $group_id]);
      }
      elseif (!$should_be_active && $is_active) {
        $group->set('status', FALSE);
        $group->save();
        $this->logger->warning('Deactivated feature group @group_id because it disappeared from XML.', ['@group_id' => $group_id]);
      }

      if ($should_be_active) {
        $source = $active_groups[$group_id];
        $this->syncCommonGroupFields($group, $source);
      }
    }
  }

  /**
   * Synchronizes feature definitions against the latest XML snapshot.
   *
   * @param string[] $files
   *   Source XML files.
   */
  private function synchronizeDefinitions(array $files): void {
    $active_definitions = $this->buildDefinitionSnapshot($files);
    $storage = $this->entityTypeManager->getStorage('fb_feature_definition');
    $existing_ids = $storage->getQuery()->accessCheck(FALSE)->execute();

    foreach ($storage->loadMultiple($existing_ids) as $definition) {
      $definition_id = $definition->id();
      $should_be_active = isset($active_definitions[$definition_id]);
      $is_active = (bool) $definition->status();

      if ($should_be_active && !$is_active) {
        $definition->set('status', TRUE);
        $definition->save();
        $this->logger->info('Reactivated feature definition @definition_id from XML snapshot.', ['@definition_id' => $definition_id]);
      }
      elseif (!$should_be_active && $is_active) {
        $definition->set('status', FALSE);
        $definition->save();
        $this->logger->warning('Deactivated feature definition @definition_id because it disappeared from XML.', ['@definition_id' => $definition_id]);
      }

      if (!$should_be_active) {
        continue;
      }

      $source = $active_definitions[$definition_id];
      $changed = FALSE;

      foreach (['label', 'description', 'code', 'group', 'type_driver', 'weight', 'status'] as $field) {
        $source_value = $source[$field] ?? NULL;
        $current_value = $definition->get($field);
        if ($current_value !== $source_value) {
          $definition->set($field, $source_value);
          $changed = TRUE;
        }
      }

      $current_defaults = $this->payloadDefaultsNormalizer->normalize($definition->getPayloadDefaults());
      if ($current_defaults !== $source['payload_defaults']) {
        $definition->set('payload_defaults', $source['payload_defaults']);
        $changed = TRUE;
        $this->logger->info('Normalized payload defaults for feature definition @definition_id.', ['@definition_id' => $definition_id]);
      }

      if ($changed) {
        $definition->save();
      }
    }
  }

  /**
   * Synchronizes common group fields from the XML snapshot.
   */
  private function syncCommonGroupFields(object $group, array $source): void {
    $changed = FALSE;

    foreach (['label', 'description', 'weight', 'status'] as $field) {
      $source_value = $source[$field] ?? NULL;
      $current_value = $group->get($field);
      if ($current_value !== $source_value) {
        $group->set($field, $source_value);
        $changed = TRUE;
      }
    }

    if ($changed) {
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

    foreach ($this->loadElements($files) as $index => $element) {
      $group_id = $this->keyBuilder->buildGroupId($element['group_code']);
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

    foreach ($this->loadElements($files) as $index => $element) {
      $definition_id = $this->keyBuilder->buildDefinitionId($element['group_code'], $element['feature_code']);
      if ($definition_id === '' || isset($snapshot[$definition_id])) {
        continue;
      }

      $snapshot[$definition_id] = [
        'label' => $element['label'],
        'description' => $element['description'],
        'code' => $element['feature_code'],
        'group' => $this->keyBuilder->buildGroupId($element['group_code']),
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
          'definition_id' => $this->keyBuilder->buildDefinitionId($record['group_code'], $record['feature_code']),
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