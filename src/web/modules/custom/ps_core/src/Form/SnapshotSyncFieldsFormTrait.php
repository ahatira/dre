<?php

declare(strict_types=1);

namespace Drupal\ps_core\Form;

use Drupal\Core\Config\Config;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\ps_core\Service\ImportGovernanceSnapshotFieldResolver;
use Drupal\ps_core\Service\ImportGovernanceSnapshotFieldSettings;

/**
 * Builds snapshot field sync checkbox elements for governance settings forms.
 */
trait SnapshotSyncFieldsFormTrait {

  use StringTranslationTrait;

  /**
   * Appends per-entity snapshot sync field checkboxes to a form container.
   *
   * @param array<string, mixed> $container
   *   Form container element, usually a details group.
   * @param \Drupal\Core\Config\Config $config
   *   Domain governance config object.
   * @param array<string, string> $entityTargets
   *   Entity keys mapped to section titles.
   */
  protected function appendSnapshotSyncFieldElements(
    array &$container,
    Config $config,
    array $entityTargets,
  ): void {
    $fieldResolver = $this->getSnapshotFieldResolver();
    $fieldSettings = $this->getSnapshotFieldSettings();

    foreach ($entityTargets as $entityKey => $title) {
      $options = $this->buildSnapshotSyncFieldOptions($fieldResolver, $entityKey);
      if ($options === []) {
        continue;
      }

      $container['sync_fields_by_entity'][$entityKey] = [
        '#type' => 'details',
        '#title' => $title,
        '#open' => FALSE,
        'fields' => [
          '#type' => 'checkboxes',
          '#title' => $this->t('Fields to synchronize'),
          '#description' => $this->t('Applies when the entity is present in the XML snapshot and is not catalogue-protected. Governance and tracking fields are never overwritten.'),
          '#options' => $options,
          '#default_value' => $fieldSettings->buildCheckboxDefaultValue($config, $entityKey),
        ],
      ];
    }
  }

  /**
   * Extracts submitted snapshot sync field values for config storage.
   *
   * @param array<string, mixed> $submittedValues
   *   Raw sync_fields_by_entity form values.
   *
   * @return array<string, string[]>
   *   Entity keys mapped to selected field names.
   */
  protected function extractSnapshotSyncFieldValues(array $submittedValues): array {
    $fieldSettings = $this->getSnapshotFieldSettings();
    $stored = [];

    foreach ($submittedValues as $entityKey => $values) {
      if (!is_string($entityKey) || !is_array($values)) {
        continue;
      }
      $fields = $fieldSettings->extractSubmittedFields($values['fields'] ?? [], $entityKey);
      if ($fields !== []) {
        $stored[$entityKey] = $fields;
      }
    }

    return $stored;
  }

  /**
   * Builds checkbox option labels for an entity key.
   *
   * @return array<string, string>
   *   Options keyed by field machine name.
   */
  private function buildSnapshotSyncFieldOptions(
    ImportGovernanceSnapshotFieldResolver $fieldResolver,
    string $entityKey,
  ): array {
    $options = [];
    foreach ($fieldResolver->getEligibleFieldIds($entityKey) as $fieldName) {
      $options[$fieldName] = $this->buildSnapshotSyncFieldLabel($fieldName);
    }

    return $options;
  }

  /**
   * Returns a human-readable label for a sync field machine name.
   */
  private function buildSnapshotSyncFieldLabel(string $fieldName): string {
    return match ($fieldName) {
      'payload_defaults' => (string) $this->t('Payload defaults'),
      'type_driver' => (string) $this->t('Type driver'),
      'expose_as_filter' => (string) $this->t('Expose as filter'),
      'required_asset_types' => (string) $this->t('Required asset types'),
      'field_business_id' => (string) $this->t('Business ID'),
      default => ucwords(str_replace('_', ' ', $fieldName)),
    };
  }

  /**
   * Returns the snapshot field resolver service.
   */
  abstract protected function getSnapshotFieldResolver(): ImportGovernanceSnapshotFieldResolver;

  /**
   * Returns the snapshot field settings service.
   */
  abstract protected function getSnapshotFieldSettings(): ImportGovernanceSnapshotFieldSettings;

}
