<?php

declare(strict_types=1);

namespace Drupal\ps_feature\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\ps_feature\Entity\FeatureDefinition;

/**
 * Migrates legacy feature governance fields to ps_core protection properties.
 */
final class FeatureGovernanceConfigUpdater {

  public function __construct(
    private readonly EntityTypeManagerInterface $entityTypeManager,
  ) {}

  /**
   * Migrates all feature groups and definitions to unified governance schema.
   */
  public function migrateAll(): string {
    $groupCount = $this->migrateGroups();
    $definitionCount = $this->migrateDefinitions();

    return sprintf(
      'Migrated governance fields on %d feature groups and %d feature definitions.',
      $groupCount,
      $definitionCount,
    );
  }

  /**
   * Ensures canonical groups are protected and have tracking metadata.
   */
  public function migrateGroups(): int {
    $storage = $this->entityTypeManager->getStorage('fb_feature_group');
    $updated = 0;

    foreach ($storage->loadMultiple() as $group) {
      $changed = FALSE;

      if (in_array($group->id(), FeatureCanonicalGroupRegistry::CANONICAL_GROUP_IDS, TRUE) && !$group->isInternallyLocked()) {
        $group->setInternallyLocked(TRUE);
        $changed = TRUE;
      }

      if ($group->getSourceTracking() === '') {
        $group->setSourceTracking(json_encode([
          'source_system' => 'PS_INSTALL',
          'source_id' => $group->id(),
          'migrated' => TRUE,
        ], JSON_THROW_ON_ERROR));
        $changed = TRUE;
      }

      if ($changed) {
        $group->save();
        $updated++;
      }
    }

    return $updated;
  }

  /**
   * Maps legacy source/type_locked values to ps_core governance properties.
   */
  public function migrateDefinitions(): int {
    $storage = $this->entityTypeManager->getStorage('fb_feature_definition');
    $updated = 0;

    foreach ($storage->loadMultiple() as $definition) {
      if (!$definition instanceof FeatureDefinition) {
        continue;
      }

      if (!$this->migrateDefinition($definition)) {
        continue;
      }

      $definition->save();
      $updated++;
    }

    return $updated;
  }

  /**
   * Migrates one feature definition when legacy values need mapping.
   */
  public function migrateDefinition(FeatureDefinition $definition): bool {
    $changed = FALSE;
    $source = $definition->getSource();

    if ($source === FeatureDefinitionSource::BO && !$definition->isInternallyLocked()) {
      $definition->setInternallyLocked(TRUE);
      $changed = TRUE;
    }

    if ($definition->isTypeLocked() && !$definition->isFieldLocked('type_driver')) {
      $definition->setFieldLocked('type_driver', TRUE);
      $changed = TRUE;
    }

    if ($definition->getSourceTracking() === '') {
      $definition->setSourceTracking(json_encode([
        'source_system' => $this->mapLegacySourceSystem($source),
        'legacy_source' => $source,
        'source_id' => $definition->id(),
        'migrated' => TRUE,
      ], JSON_THROW_ON_ERROR));
      $changed = TRUE;
    }

    return $changed;
  }

  /**
   * Maps legacy catalogue source values to source tracking systems.
   */
  private function mapLegacySourceSystem(string $source): string {
    return match ($source) {
      FeatureDefinitionSource::BO => 'PS_BO',
      FeatureDefinitionSource::XML => 'CRM_XML',
      default => 'LEGACY',
    };
  }

}
