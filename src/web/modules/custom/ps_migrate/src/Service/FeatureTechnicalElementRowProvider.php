<?php

declare(strict_types=1);

namespace Drupal\ps_migrate\Service;

use Drupal\ps_migrate\ValueObject\FeatureTechnicalElement;

/**
 * Builds feature migration rows from CRM technical elements.
 */
final class FeatureTechnicalElementRowProvider {

  public function __construct(
    private readonly FeatureTechnicalElementSourceLoader $sourceLoader,
    private readonly FeatureMigrationKeyBuilder $keyBuilder,
    private readonly FeatureTechnicalElementValidator $validator,
  ) {}

  /**
   * Builds unique feature group rows.
   *
   * @param string[] $files
   *
   * @return array<int, array<string, mixed>>
   */
  public function buildGroupRows(array $files): array {
    $rows = [];

    foreach ($this->loadElementsFromFiles($files) as $element) {
      $groupId = $this->keyBuilder->buildGroupId($element->getGroupCode());
      if ($groupId === '' || isset($rows[$groupId])) {
        continue;
      }

      $rows[$groupId] = [
        'group_id' => $groupId,
        'group_code' => $element->getGroupCode(),
        'label' => $element->getGroupCode(),
        'description' => $element->getGroupCode(),
        'weight' => $element->getSourceIndex(),
        'status' => 1,
      ];
    }

    return array_values($rows);
  }

  /**
   * Builds unique feature definition rows.
   *
   * @param string[] $files
   *
   * @return array<int, array<string, mixed>>
   */
  public function buildDefinitionRows(array $files): array {
    $rows = [];

    foreach ($this->loadElementsFromFiles($files) as $element) {
      $definitionRow = $this->buildDefinitionRow($element);
      if ($definitionRow === NULL || isset($rows[$definitionRow['definition_id']])) {
        continue;
      }
      $rows[$definitionRow['definition_id']] = $definitionRow;
    }

    return array_values($rows);
  }

  /**
   * @param string[] $files
   *
   * @return array<int, FeatureTechnicalElement>
   */
  private function loadElementsFromFiles(array $files): array {
    $elements = [];

    foreach ($files as $file) {
      $file = trim($file);
      if ($file === '') {
        continue;
      }

      foreach ($this->sourceLoader->loadFromFile($file) as $element) {
        $elements[] = $element;
      }
    }

    return $elements;
  }

  private function buildDefinitionRow(FeatureTechnicalElement $element): ?array {
    $groupId = $this->keyBuilder->buildGroupId($element->getGroupCode());
    $definitionId = $this->keyBuilder->buildDefinitionId($element->getGroupCode(), $element->getFeatureCode());
    $payloadDefaults = [];

    if ($element->getUnit() !== NULL) {
      $payloadDefaults['unit'] = $element->getUnit();
    }

    $record = [
      'definition_id' => $definitionId,
      'group_id' => $groupId,
      'group_code' => $element->getGroupCode(),
      'feature_code' => $element->getFeatureCode(),
      'label' => $element->getLabel(),
      'description' => $element->getComplement() ?? $element->getLabel(),
      'type_driver' => $this->guessTypeDriver($element),
      'weight' => $element->getSourceIndex(),
      'status' => 1,
      'payload_defaults' => $payloadDefaults,
      'required_asset_types' => [],
      'source_index' => $element->getSourceIndex(),
    ];

    $validation = $this->validator->validate([
      'group_code' => $record['group_code'],
      'feature_code' => $record['feature_code'],
      'definition_id' => $record['definition_id'],
      'type_driver' => $record['type_driver'],
      'payload' => [
        'value' => $element->getValue(),
        'unit' => $element->getUnit(),
        'complement' => $element->getComplement(),
      ],
    ]);
    if ($validation['errors'] !== []) {
      return NULL;
    }

    return $record;
  }

  private function guessTypeDriver(FeatureTechnicalElement $element): string {
    $value = $element->getValue();
    $unit = $element->getUnit();

    if ($value === NULL && $unit === NULL) {
      return 'flag';
    }

    if ($value !== NULL && is_numeric(str_replace(',', '.', $value))) {
      return 'numeric';
    }

    if ($value !== NULL && preg_match('/^(yes|no|true|false|oui|non|0|1)$/i', $value) === 1) {
      return 'yes_no';
    }

    return 'text';
  }

}
