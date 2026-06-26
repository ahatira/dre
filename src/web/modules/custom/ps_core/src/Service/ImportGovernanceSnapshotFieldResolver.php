<?php

declare(strict_types=1);

namespace Drupal\ps_core\Service;

use Drupal\Core\Config\Entity\ConfigEntityInterface;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\ps_core\ConfigEntityProtection\ConfigEntityProtectionRegistry;
use Drupal\ps_core\ImportGovernance\ImportGovernanceSnapshotEntityKey;

/**
 * Resolves entity fields eligible for CRM XML snapshot synchronization.
 */
final class ImportGovernanceSnapshotFieldResolver {

  /**
   * Config entity properties always excluded from snapshot sync UI and runtime.
   *
   * @var string[]
   */
  private const CONFIG_ENTITY_EXCLUDED_PROPERTIES = [
    'id',
    'uuid',
    'internal_lock',
    'type_locked',
    'source',
    'source_tracking',
    'checksum',
    'field_locks',
  ];

  /**
   * Content entity base fields excluded from snapshot sync UI and runtime.
   *
   * @var string[]
   */
  private const CONTENT_ENTITY_EXCLUDED_FIELDS = [
    'nid',
    'vid',
    'uuid',
    'type',
    'langcode',
    'default_langcode',
    'status',
    'created',
    'changed',
    'uid',
    'promote',
    'sticky',
    'revision_timestamp',
    'revision_uid',
    'revision_log',
    'revision_default',
    'revision_translation_affected',
    'path',
    'menu_link',
    'content_translation_source',
    'content_translation_outdated',
    'content_translation_uid',
    'content_translation_created',
    'field_internal_lock',
    'field_source_tracking',
    'field_source_checksum',
  ];

  public function __construct(
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly EntityFieldManagerInterface $entityFieldManager,
    private readonly ConfigEntityProtectionRegistry $configEntityProtectionRegistry,
  ) {}

  /**
   * Returns eligible field machine names for an entity key.
   *
   * @return string[]
   *   Field or property names that may be synchronized.
   */
  public function getEligibleFieldIds(string $entityKey): array {
    ['entity_type_id' => $entityTypeId, 'bundle' => $bundle] = ImportGovernanceSnapshotEntityKey::decode($entityKey);
    if ($entityTypeId === '') {
      return [];
    }

    if ($this->entityTypeManager->getDefinition($entityTypeId)->entityClassImplements(ConfigEntityInterface::class)) {
      return $this->getConfigEntityEligibleFieldIds($entityTypeId);
    }

    return $this->getContentEntityEligibleFieldIds($entityTypeId, $bundle);
  }

  /**
   * Filters configured field names against the eligible list for an entity key.
   *
   * @param string[] $configuredFields
   *   Raw configured field names.
   *
   * @return string[]
   *   Normalized eligible field names.
   */
  public function filterConfiguredFields(string $entityKey, array $configuredFields): array {
    $eligible = array_flip($this->getEligibleFieldIds($entityKey));
    $normalized = [];

    foreach ($configuredFields as $field) {
      if (!is_string($field)) {
        continue;
      }
      $field = trim($field);
      if ($field !== '' && isset($eligible[$field])) {
        $normalized[] = $field;
      }
    }

    return array_values(array_unique($normalized));
  }

  /**
   * Returns eligible config entity export properties.
   *
   * @return string[]
   *   Property names.
   */
  private function getConfigEntityEligibleFieldIds(string $entityTypeId): array {
    $entityType = $this->entityTypeManager->getDefinition($entityTypeId);
    $exported = $entityType->get('config_export');
    if (!is_array($exported)) {
      return [];
    }

    $definition = $this->configEntityProtectionRegistry->getDefinition($entityTypeId);
    $excluded = self::CONFIG_ENTITY_EXCLUDED_PROPERTIES;
    if ($definition !== NULL) {
      $excluded = array_merge($excluded, array_filter([
        $definition->getLockProperty(),
        $definition->getTrackingProperty(),
        $definition->getChecksumProperty(),
        $definition->getFieldLocksProperty(),
      ]));
    }

    $excluded = array_flip(array_unique($excluded));
    $fields = [];
    foreach ($exported as $property) {
      if (!is_string($property) || $property === '' || isset($excluded[$property])) {
        continue;
      }
      $fields[] = $property;
    }

    sort($fields);
    return $fields;
  }

  /**
   * Returns eligible content entity field machine names.
   *
   * @return string[]
   *   Field machine names.
   */
  private function getContentEntityEligibleFieldIds(string $entityTypeId, ?string $bundle): array {
    if ($bundle === NULL || $bundle === '') {
      $bundle = $entityTypeId;
    }

    $definitions = $this->entityFieldManager->getFieldDefinitions($entityTypeId, $bundle);
    $excluded = array_flip(self::CONTENT_ENTITY_EXCLUDED_FIELDS);
    $fields = [];

    foreach ($definitions as $fieldName => $definition) {
      if (isset($excluded[$fieldName]) || $definition->isComputed() || $definition->isReadOnly()) {
        continue;
      }
      $fields[] = $fieldName;
    }

    sort($fields);
    return $fields;
  }

}
