<?php

declare(strict_types=1);

namespace Drupal\ps_context\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\ps_context\Entity\PsContextLabelProfileInterface;

/**
 * Resolves search and offer labels from ps_context.label_profile entities.
 *
 * Replaces hardcoded SearchBudgetFilterResolver logic. Profiles are merged
 * from least to most specific (wildcards first, then higher weight).
 */
final class ContextLabelResolver {

  private const OP_SALE = 'VEN';

  /**
   * @var list<\Drupal\ps_context\Entity\PsContextLabelProfileInterface>|null
   */
  private ?array $sortedProfiles = NULL;

  public function __construct(
    private readonly EntityTypeManagerInterface $entityTypeManager,
  ) {}

  /**
   * Resolves merged label map for asset × operation context.
   *
   * @return array<string, string>
   */
  public function resolveLabels(?string $assetType, ?string $operationType = NULL): array {
    $asset = $this->normalizeAsset($assetType);
    $operation = $this->normalizeOperation($operationType);

    $merged = [];
    foreach ($this->loadSortedProfiles() as $profile) {
      if (!$this->profileMatches($profile, $asset, $operation)) {
        continue;
      }
      foreach ($profile->getLabels() as $key => $value) {
        if ($value !== '') {
          $merged[$key] = $value;
        }
      }
    }

    return $merged;
  }

  /**
   * Search page budget filter presentation (backward-compatible shape).
   *
   * @return array{
   *   field_label: string,
   *   toggle_default: string,
   *   min_label: string,
   *   max_label: string,
   *   input_unit: string,
   *   value_suffix: string,
   *   step: int,
   *   budget_unit: string|null,
   *   budget_period: string|null
   *   }
   */
  public function resolve(?string $assetType, ?string $operationType = NULL): array {
    $labels = $this->resolveLabels($assetType, $operationType);
    $inputUnit = $labels['search_budget_input_unit'] ?? '€';
    $fieldLabel = $labels['search_budget_field_label'] ?? 'Budget';

    return [
      'field_label' => $fieldLabel,
      'toggle_default' => $fieldLabel,
      'min_label' => $labels['search_budget_min_label'] ?? 'Min budget (€)',
      'max_label' => $labels['search_budget_max_label'] ?? 'Max budget (€)',
      'input_unit' => $inputUnit,
      'value_suffix' => $labels['search_budget_value_suffix'] ?? (' ' . $inputUnit),
      'step' => (int) ($labels['search_budget_step'] ?? 10),
      'budget_unit' => $this->nullableCode($labels['search_budget_budget_unit'] ?? NULL),
      'budget_period' => $this->nullableCode($labels['search_budget_budget_period'] ?? NULL),
    ];
  }

  /**
   * Homepage hero budget field presentation.
   *
   * @return array{max_label: string, max_placeholder: string, step: int}
   */
  public function resolveHomepageEntry(?string $assetType, ?string $operationType = NULL): array {
    $labels = $this->resolveLabels($assetType, $operationType);

    $maxLabel = $labels['hero_budget_max_label'] ?? 'Max. budget';

    return [
      'max_label' => $maxLabel,
      'max_placeholder' => $labels['hero_budget_max_placeholder'] ?? $maxLabel,
      'step' => (int) ($labels['search_budget_step'] ?? 10),
    ];
  }

  /**
   * Homepage hero capacity field presentation.
   *
   * @return array{
   *   field_label: string,
   *   hide_operation: bool
   * }
   */
  public function resolveHomepageCapacity(?string $assetType, ?string $operationType = NULL): array {
    $labels = $this->resolveLabels($assetType, $operationType);

    return [
      'field_label' => $labels['hero_capacity_field_label']
        ?? ($labels['search_capacity_field_label'] ?? 'Capacity'),
      'hide_operation' => ($labels['hero_hide_operation_toggle'] ?? '') === '1',
    ];
  }

  /**
   * Search page capacity filter presentation.
   *
   * @return array{field_label: string, min_label: string, max_label: string}
   */
  public function resolveCapacity(?string $assetType, ?string $operationType = NULL): array {
    $labels = $this->resolveLabels($assetType, $operationType);
    $fieldLabel = $labels['search_capacity_field_label'] ?? 'Capacity';

    return [
      'field_label' => $fieldLabel,
      'min_label' => $labels['search_capacity_min_label'] ?? ('Min ' . $fieldLabel),
      'max_label' => $labels['search_capacity_max_label'] ?? ('Max ' . $fieldLabel),
    ];
  }

  /**
   * Search page UI flags (operation toggle, more filters).
   *
   * @return array{hide_operation: bool, hide_more_filters: bool}
   */
  public function resolveSearchUi(?string $assetType, ?string $operationType = NULL): array {
    $labels = $this->resolveSearchUiLabels($assetType, $operationType);

    return [
      'hide_operation' => ($labels['search_hide_operation_toggle'] ?? $labels['hero_hide_operation_toggle'] ?? '') === '1',
      'hide_more_filters' => ($labels['search_hide_more_filters'] ?? '') === '1',
    ];
  }

  /**
   * Per-asset map for client-side search UI toggles.
   *
   * @param list<string> $assetCodes
   *
   * @return array<string, array{hide_operation: bool, hide_more_filters: bool}>
   */
  public function buildSearchUiConfigMap(array $assetCodes): array {
    $map = [
      '' => $this->resolveSearchUi(NULL, NULL),
    ];

    foreach ($assetCodes as $code) {
      $asset = strtoupper($code);
      $map[$asset] = $this->resolveSearchUi($asset, NULL);
    }

    return $map;
  }

  /**
   * Nested map for homepage hero capacity labels.
   *
   * @param list<string> $assetCodes
   *
   * @return array<string, array<string, array{field_label: string, hide_operation: bool}>>
   */
  public function buildHomepageCapacityConfigMap(array $assetCodes): array {
    return $this->buildNestedMap($assetCodes, fn(?string $asset, ?string $op): array => $this->resolveHomepageCapacity($asset, $op));
  }

  /**
   * Nested map for client-side budget label updates.
   *
   * @param list<string> $assetCodes
   *
   * @return array<string, array<string, array<string, mixed>>>
   */
  public function buildConfigMap(array $assetCodes): array {
    return $this->buildNestedMap($assetCodes, fn(?string $asset, ?string $op): array => $this->resolve($asset, $op));
  }

  /**
   * Nested map for homepage hero budget placeholders.
   *
   * @param list<string> $assetCodes
   *
   * @return array<string, array<string, array{max_label: string, max_placeholder: string, step: int}>>
   */
  public function buildHomepageConfigMap(array $assetCodes): array {
    return $this->buildNestedMap($assetCodes, fn(?string $asset, ?string $op): array => $this->resolveHomepageEntry($asset, $op));
  }

  /**
   * Nested map for client-side capacity label updates.
   *
   * @param list<string> $assetCodes
   *
   * @return array<string, array<string, array{field_label: string, min_label: string, max_label: string}>>
   */
  public function buildCapacityConfigMap(array $assetCodes): array {
    return $this->buildNestedMap($assetCodes, fn(?string $asset, ?string $op): array => $this->resolveCapacity($asset, $op));
  }

  /**
   * Merges asset+LOC profile flags when operation is not set (COW pattern).
   *
   * @return array<string, string>
   */
  private function resolveSearchUiLabels(?string $assetType, ?string $operationType = NULL): array {
    $labels = $this->resolveLabels($assetType, $operationType);

    if ($assetType !== NULL && ($operationType === NULL || $operationType === '')) {
      $locLabels = $this->resolveLabels($assetType, 'LOC');
      foreach (['search_hide_operation_toggle', 'search_hide_more_filters', 'hero_hide_operation_toggle'] as $key) {
        if (($labels[$key] ?? '') === '' && ($locLabels[$key] ?? '') !== '') {
          $labels[$key] = $locLabels[$key];
        }
      }
    }

    return $labels;
  }

  /**
   * @param list<string> $assetCodes
   * @param callable(?string, ?string): array<string, mixed> $resolver
   *
   * @return array<string, array<string, array<string, mixed>>>
   */
  private function buildNestedMap(array $assetCodes, callable $resolver): array {
    $operations = ['', 'LOC', 'VEN'];
    $map = [
      '' => [],
    ];

    foreach ($operations as $op) {
      $map[''][$op] = $resolver(NULL, $op !== '' ? $op : NULL);
    }

    foreach ($assetCodes as $code) {
      $asset = strtoupper($code);
      $map[$asset] = [];
      foreach ($operations as $op) {
        $map[$asset][$op] = $resolver($asset, $op !== '' ? $op : NULL);
      }
    }

    return $map;
  }

  /**
   * @return list<\Drupal\ps_context\Entity\PsContextLabelProfileInterface>
   */
  private function loadSortedProfiles(): array {
    if ($this->sortedProfiles !== NULL) {
      return $this->sortedProfiles;
    }

    $storage = $this->entityTypeManager->getStorage('ps_context_label_profile');
    /** @var list<\Drupal\ps_context\Entity\PsContextLabelProfileInterface> $profiles */
    $profiles = array_values($storage->loadMultiple());
    $profiles = array_filter($profiles, static fn(PsContextLabelProfileInterface $profile): bool => $profile->status());

    usort($profiles, static function (PsContextLabelProfileInterface $a, PsContextLabelProfileInterface $b): int {
      $specA = ($a->getAssetType() === '*' ? 0 : 1) + ($a->getOperationType() === '*' ? 0 : 1);
      $specB = ($b->getAssetType() === '*' ? 0 : 1) + ($b->getOperationType() === '*' ? 0 : 1);
      if ($specA !== $specB) {
        return $specA <=> $specB;
      }
      if ($a->getWeight() !== $b->getWeight()) {
        return $a->getWeight() <=> $b->getWeight();
      }
      return strcmp($a->id(), $b->id());
    });

    $this->sortedProfiles = $profiles;
    return $this->sortedProfiles;
  }

  /**
   *
   */
  private function profileMatches(PsContextLabelProfileInterface $profile, ?string $asset, ?string $operation): bool {
    $profileAsset = $profile->getAssetType();
    $profileOperation = $profile->getOperationType();

    if ($profileAsset !== '*' && $profileAsset !== $asset) {
      return FALSE;
    }
    if ($profileOperation !== '*' && $profileOperation !== $operation) {
      return FALSE;
    }
    if ($asset === NULL && $profileAsset !== '*') {
      return FALSE;
    }
    if ($operation === NULL && $profileOperation !== '*') {
      return FALSE;
    }

    return TRUE;
  }

  /**
   *
   */
  private function normalizeAsset(?string $assetType): ?string {
    if ($assetType === NULL || $assetType === '') {
      return NULL;
    }
    return strtoupper($assetType);
  }

  /**
   *
   */
  private function normalizeOperation(?string $operationType): ?string {
    if ($operationType === NULL || $operationType === '') {
      return NULL;
    }

    $op = strtoupper($operationType);
    return match ($op) {
      'RENT', 'LOC' => 'LOC',
      'SALE', 'VEN' => self::OP_SALE,
      default => $op,
    };
  }

  /**
   *
   */
  private function nullableCode(?string $value): ?string {
    if ($value === NULL || $value === '') {
      return NULL;
    }
    return strtoupper($value);
  }

}
