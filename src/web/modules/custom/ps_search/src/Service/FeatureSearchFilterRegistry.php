<?php

declare(strict_types=1);

namespace Drupal\ps_search\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\ps_core\Service\OfferSectionRegistry;
use Drupal\ps_feature\Entity\FeatureDefinition;
use Drupal\ps_feature\Entity\FeatureGroup;
use Drupal\ps_feature\Service\FeatureGroupDisplayLabelResolver;

/**
 * Builds metadata for per-feature search filters (Option A — expose_as_filter).
 */
final class FeatureSearchFilterRegistry {

  use StringTranslationTrait;

  /**
   * Core More-filters fields not driven by feature definitions.
   */
  public const CORE_FILTER_FIELDS = [
    'reference',
    'nearby_transport',
    'has_immersive_tour',
    'has_video',
  ];

  public function __construct(
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly FeatureGroupDisplayLabelResolver $groupLabelResolver,
    private readonly OfferSectionRegistry $offerSectionRegistry,
  ) {}

  /**
   * Returns exposed feature filters keyed by definition ID.
   *
   * @return array<string, array<string, mixed>>
   *   Filter metadata keyed by feature definition ID.
   */
  public function getExposedFilters(?string $activeAsset = NULL, bool $withOptions = TRUE): array {
    $definitions = $this->entityTypeManager
      ->getStorage('fb_feature_definition')
      ->loadMultiple();

    $fieldNames = [];
    foreach ($definitions as $definition) {
      if (!$this->isExposedDefinition($definition, $activeAsset)) {
        continue;
      }
      $fieldNames[(string) $definition->id()] = $this->buildFieldName((string) $definition->id());
    }

    asort($fieldNames);
    $identifierRegistry = [];
    $filters = [];

    foreach ($fieldNames as $definitionId => $fieldName) {
      $definition = $definitions[$definitionId];
      $filters[$definitionId] = $this->buildFilterMetadata(
        $definition,
        $fieldName,
        $this->buildParamIdentifier($fieldName, $identifierRegistry),
        $withOptions,
      );
    }

    return $filters;
  }

  /**
   * Builds More-filters groups from feature groups + exposed definitions.
   *
   * @return array<string, array<string, mixed>>
   *   Groups keyed by group ID, sorted by weight.
   */
  public function buildMoreCriteriaGroups(?string $activeAsset = NULL, bool $withOptions = TRUE): array {
    $filters = $this->getExposedFilters($activeAsset, $withOptions);
    if ($filters === []) {
      return [];
    }

    $groups = [];
    $groupEntities = $this->entityTypeManager
      ->getStorage('fb_feature_group')
      ->loadMultiple();

    foreach ($groupEntities as $group) {
      if (!$group->status()) {
        continue;
      }
      $groupAssetTypes = $group->getAssetTypes();
      if ($activeAsset !== NULL && $groupAssetTypes !== []
        && !in_array($activeAsset, $groupAssetTypes, TRUE)) {
        continue;
      }
      $groups[(string) $group->id()] = [
        'id' => (string) $group->id(),
        'label' => (string) $this->t($this->groupLabelResolver->resolve($group)),
        'weight' => $group->getWeight(),
        'items' => [],
      ];
    }

    $fallback = [
      'id' => 'other',
      'label' => (string) $this->t('Other'),
      'weight' => 9999,
      'items' => [],
    ];

    foreach ($filters as $filter) {
      $groupId = (string) ($filter['group_id'] ?? 'other');
      if (!isset($groups[$groupId])) {
        $groups[$groupId] = $fallback;
        $groups[$groupId]['id'] = $groupId;
        $groups[$groupId]['label'] = $this->resolveUnknownGroupLabel($groupId);
      }
      $groups[$groupId]['items'][] = $filter;
    }

    foreach ($groups as $groupId => $group) {
      if ($group['items'] === []) {
        unset($groups[$groupId]);
        continue;
      }
      usort($groups[$groupId]['items'], static function (array $a, array $b): int {
        return ($a['weight'] ?? 0) <=> ($b['weight'] ?? 0)
          ?: strcasecmp((string) $a['label'], (string) $b['label']);
      });
    }

    uasort($groups, static fn(array $a, array $b): int => ($a['weight'] ?? 0) <=> ($b['weight'] ?? 0));

    return $groups;
  }

  /**
   * Builds accordion summaries without loading dictionary/taxonomy options.
   *
   * @return array<string, array<string, mixed>>
   */
  public function buildGroupSummaries(?string $activeAsset = NULL): array {
    $groups = $this->buildMoreCriteriaGroups($activeAsset, FALSE);
    foreach ($groups as $groupId => $group) {
      $groups[$groupId]['item_count'] = count($group['items'] ?? []);
      unset($groups[$groupId]['items']);
    }
    return $groups;
  }

  /**
   * Returns full filter items for one feature group (lazy-load).
   *
   * @return list<array<string, mixed>>
   */
  public function getGroupItems(string $groupId, ?string $activeAsset = NULL): array {
    $groups = $this->buildMoreCriteriaGroups($activeAsset, TRUE);
    return $groups[$groupId]['items'] ?? [];
  }

  /**
   * Returns a flat schema for drupalSettings / count API (no options).
   *
   * @return list<array<string, mixed>>
   */
  public function buildFilterSchema(?string $activeAsset = NULL): array {
    $schema = [];
    foreach ($this->getExposedFilters($activeAsset, FALSE) as $filter) {
      $schema[] = [
        'param' => $filter['param'],
        'field' => $filter['field'],
        'widget' => $filter['widget'],
      ];
    }
    return $schema;
  }

  /**
   * Maps type_driver to More-filters widget type.
   */
  public function resolveWidgetType(string $typeDriver): string {
    return match ($typeDriver) {
      'flag' => 'checkbox',
      'yes_no' => 'yes_no',
      'dictionary', 'taxonomy', 'list' => 'tags',
      'numeric', 'range' => 'range',
      'text' => 'text',
      'select', 'multiselect' => 'select',
      'date' => 'date',
      default => 'text',
    };
  }

  /**
   * Normalizes a feature definition ID for Search API field names.
   */
  public function normalizeFeatureSuffix(string $suffix): string {
    return preg_replace('/_{2,}/', '_', $suffix) ?? $suffix;
  }

  /**
   * Builds the Search API / Solr field name for a feature definition.
   */
  public function buildFieldName(string $definitionId): string {
    return 'feature_' . $this->normalizeFeatureSuffix($definitionId);
  }

  /**
   * Builds a stable exposed-filter query parameter from a field name.
   */
  public function buildParamIdentifier(string $fieldName, array &$identifierRegistry): string {
    $base = preg_replace('/[^a-z0-9_]+/', '_', strtolower($fieldName)) ?? '';
    $base = preg_replace('/_+/', '_', $base) ?? '';
    $base = trim($base, '_');

    if ($base === '') {
      $base = 'feature';
    }

    $base = substr($base, 0, 48);
    $candidate = $base;
    $i = 2;

    while (isset($identifierRegistry[$candidate])) {
      $suffix = '_' . $i;
      $candidate = substr($base, 0, max(1, 48 - strlen($suffix))) . $suffix;
      $i++;
    }

    $identifierRegistry[$candidate] = TRUE;
    return $candidate;
  }

  /**
   * Builds expected index field map for FeatureFilterSyncManager.
   *
   * @return array<string, array<string, string>>
   */
  public function buildExpectedIndexFieldMap(): array {
    $definitions = $this->entityTypeManager
      ->getStorage('fb_feature_definition')
      ->loadMultiple();

    $expected = [];
    foreach ($definitions as $definition) {
      if (!$definition->isExposeAsFilter()) {
        continue;
      }

      $fieldName = $this->buildFieldName((string) $definition->id());
      $typeDriver = (string) $definition->getTypeDriver();
      $fieldType = match ($typeDriver) {
        'flag', 'yes_no' => 'boolean',
        'numeric', 'range' => 'decimal',
        default => 'string',
      };

      $expected[$fieldName] = [
        'label' => (string) $definition->label(),
        'type_driver' => $typeDriver,
        'field_type' => $fieldType,
      ];
    }

    ksort($expected);
    return $expected;
  }

  /**
   * Resolves a label for orphaned feature groups.
   */
  private function resolveUnknownGroupLabel(string $groupId): string {
    $group = $this->entityTypeManager->getStorage('fb_feature_group')->load($groupId);
    if ($group instanceof FeatureGroup) {
      return (string) $this->t($this->groupLabelResolver->resolve($group));
    }
    return $this->groupLabelResolver->isMachineLabel($groupId, $groupId)
      ? ucwords(str_replace('_', ' ', $groupId))
      : $groupId;
  }

  /**
   * Checks whether a definition is exposed and applicable.
   */
  private function isExposedDefinition(FeatureDefinition $definition, ?string $activeAsset): bool {
    if (!$definition->status() || !$definition->isExposeAsFilter()) {
      return FALSE;
    }
    if ($this->isTransportGroupDefinition($definition)) {
      return FALSE;
    }
    if ($activeAsset !== NULL && !$definition->isApplicableToAssetType($activeAsset)) {
      return FALSE;
    }
    return TRUE;
  }

  /**
   * Transport features are searched via Nearby transport (core criteria), not per-feature filters.
   */
  private function isTransportGroupDefinition(FeatureDefinition $definition): bool {
    return (string) $definition->getGroup() === $this->offerSectionRegistry->getLocationTransportGroup();
  }

  /**
   * Returns the transport feature group ID (shared with offer location section).
   */
  public function getTransportGroupId(): string {
    return $this->offerSectionRegistry->getLocationTransportGroup();
  }

  /**
   * Builds one filter metadata array.
   */
  private function buildFilterMetadata(
    FeatureDefinition $definition,
    string $fieldName,
    string $param,
    bool $withOptions = TRUE,
  ): array {
    $typeDriver = (string) $definition->getTypeDriver();
    $payloadDefaults = $definition->getPayloadDefaults();
    $widget = $this->resolveWidgetType($typeDriver);

    return [
      'id' => (string) $definition->id(),
      'label' => (string) $definition->label(),
      'group_id' => (string) $definition->getGroup(),
      'type_driver' => $typeDriver,
      'widget' => $widget,
      'field' => $fieldName,
      'param' => $param,
      'weight' => $definition->getWeight(),
      'options' => $withOptions ? $this->loadFilterOptions($typeDriver, $payloadDefaults) : [],
      'unit' => (string) ($payloadDefaults['unit'] ?? ''),
      'step' => $this->resolveRangeStep($typeDriver, $payloadDefaults),
    ];
  }

  /**
   * Loads selectable options for dictionary, taxonomy, and list drivers.
   *
   * @return list<array{id: string, label: string, value: string}>
   */
  private function loadFilterOptions(string $typeDriver, array $payloadDefaults): array {
    if ($typeDriver === 'dictionary') {
      $dictType = $payloadDefaults['dictionary_id'] ?? $payloadDefaults['dictionary_type'] ?? NULL;
      if (!is_string($dictType) || $dictType === '') {
        return [];
      }
      return $this->loadDictionaryOptions($dictType);
    }

    if ($typeDriver === 'taxonomy') {
      $vocabularyId = $payloadDefaults['vocabulary_id'] ?? NULL;
      if (!is_string($vocabularyId) || $vocabularyId === '') {
        return [];
      }
      return $this->loadTaxonomyOptions($vocabularyId);
    }

    if ($typeDriver === 'list') {
      if (!empty($payloadDefaults['options']) && is_array($payloadDefaults['options'])) {
        $options = [];
        foreach ($payloadDefaults['options'] as $rawOption) {
          $option = (string) $rawOption;
          if ($option === '') {
            continue;
          }
          $options[] = [
            'id' => $option,
            'label' => $option,
            'value' => $option,
          ];
        }
        return $options;
      }

      $dictType = $payloadDefaults['dictionary_id'] ?? $payloadDefaults['dictionary_type'] ?? NULL;
      if (is_string($dictType) && $dictType !== '') {
        return $this->loadDictionaryOptions($dictType);
      }
    }

    if ($typeDriver === 'yes_no') {
      return [
        ['id' => '1', 'label' => (string) $this->t('Yes'), 'value' => '1'],
        ['id' => '0', 'label' => (string) $this->t('No'), 'value' => '0'],
      ];
    }

    return [];
  }

  /**
   * Loads dictionary entries for filter options.
   *
   * @return list<array{id: string, label: string, value: string}>
   */
  private function loadDictionaryOptions(string $dictType): array {
    $entryStorage = $this->entityTypeManager->getStorage('ps_dictionary_entry');
    $ids = $entryStorage->getQuery()
      ->condition('type', $dictType)
      ->accessCheck(FALSE)
      ->sort('weight')
      ->execute();

    $options = [];
    foreach ($entryStorage->loadMultiple($ids) as $entry) {
      $code = (string) $entry->getCode();
      $options[] = [
        'id' => $code,
        'label' => (string) $entry->label(),
        'value' => $code,
      ];
    }
    return $options;
  }

  /**
   * Loads taxonomy terms for filter options.
   *
   * @return list<array{id: string, label: string, value: string}>
   */
  private function loadTaxonomyOptions(string $vocabularyId): array {
    if (!$this->entityTypeManager->hasDefinition('taxonomy_term')) {
      return [];
    }

    $termStorage = $this->entityTypeManager->getStorage('taxonomy_term');
    $ids = $termStorage->getQuery()
      ->condition('vid', $vocabularyId)
      ->accessCheck(FALSE)
      ->sort('weight')
      ->execute();

    $options = [];
    foreach ($termStorage->loadMultiple($ids) as $term) {
      $tid = (string) $term->id();
      $options[] = [
        'id' => $tid,
        'label' => (string) $term->label(),
        'value' => $tid,
      ];
    }
    return $options;
  }

  /**
   * Resolves numeric step for range widgets.
   */
  private function resolveRangeStep(string $typeDriver, array $payloadDefaults): string {
    if (!in_array($typeDriver, ['numeric', 'range'], TRUE)) {
      return '1';
    }
    $decimals = (int) ($payloadDefaults['decimals'] ?? 0);
    if ($decimals <= 0) {
      return '1';
    }
    return '0.' . str_repeat('0', $decimals - 1) . '1';
  }

}
