<?php

declare(strict_types=1);

namespace Drupal\ps_core\Service;

use Drupal\Core\Config\Config;

/**
 * Reads and normalizes snapshot field sync settings from domain governance config.
 */
final class ImportGovernanceSnapshotFieldSettings {

  public function __construct(
    private readonly ImportGovernanceSnapshotFieldResolver $fieldResolver,
  ) {}

  /**
   * Returns configured sync fields for an entity key.
   *
   * @return string[]
   *   Normalized eligible field names.
   */
  public function getConfiguredFields(Config $config, string $entityKey): array {
    $configured = $this->readRawConfiguredFields($config, $entityKey);
    return $this->fieldResolver->filterConfiguredFields($entityKey, $configured);
  }

  /**
   * Builds default checkbox values for a sync field element.
   *
   * @return array<string, string>
   *   Checked option keys.
   */
  public function buildCheckboxDefaultValue(Config $config, string $entityKey): array {
    $defaults = [];
    foreach ($this->getConfiguredFields($config, $entityKey) as $field) {
      $defaults[$field] = $field;
    }

    return $defaults;
  }

  /**
   * Normalizes submitted checkbox values for storage.
   *
   * @param array<string, mixed> $submittedValues
   *   Raw form values for one entity key.
   *
   * @return string[]
   *   Stored field names.
   */
  public function extractSubmittedFields(array $submittedValues, string $entityKey): array {
    $selected = array_keys(array_filter($submittedValues));
    return $this->fieldResolver->filterConfiguredFields($entityKey, $selected);
  }

  /**
   * Reads raw configured field names before eligibility filtering.
   *
   * @return string[]
   *   Raw configured field names.
   */
  private function readRawConfiguredFields(Config $config, string $entityKey): array {
    $byEntity = $config->get('present_in_xml.sync_fields_by_entity');
    if (is_array($byEntity) && isset($byEntity[$entityKey]) && is_array($byEntity[$entityKey])) {
      return $byEntity[$entityKey];
    }

    $legacyFields = $config->get('present_in_xml.sync_fields');
    if (is_array($legacyFields) && $entityKey === 'fb_feature_definition') {
      return $legacyFields;
    }

    return [];
  }

}
