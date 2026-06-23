<?php

declare(strict_types=1);

namespace Drupal\ps_search\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\search_api\Entity\Index;

/**
 * Synchronizes feature-based Search API fields and exposed view filters.
 */
final class FeatureFilterSyncManager {

  private const INDEX_CONFIG = 'search_api.index.offers';
  private const VIEW_CONFIG = 'views.view.ps_search_offers';
  private const SETTINGS_CONFIG = 'ps_search.feature_filter_sync';

  /**
   * Core More-filters fields — not managed by per-feature sync.
   */
  private const CORE_MORE_FILTER_FIELDS = [
    'nearby_transport',
    'has_immersive_tour',
    'has_video',
  ];

  public function __construct(
    private readonly ConfigFactoryInterface $configFactory,
    private readonly FeatureSearchFilterRegistry $featureFilterRegistry,
    private readonly EntityTypeManagerInterface $entityTypeManager,
  ) {}

  /**
   * Syncs index fields and view filters from exposed feature definitions.
   *
   * @param bool $prune
   *   When TRUE, removes stale feature fields/filters that are no longer exposed.
   *
   * @return array
   *   Sync stats keyed by added/updated/removed/changed.
   */
  public function sync(bool $prune = TRUE): array {
    $stats = [
      'added' => 0,
      'updated' => 0,
      'removed' => 0,
      'changed' => FALSE,
    ];

    $expected = $this->featureFilterRegistry->buildExpectedIndexFieldMap();

    $index_changed = $this->syncIndexFieldSettings($expected, $prune, $stats);
    $view_changed = $this->syncViewFilters($expected, $prune, $stats);

    $stats['changed'] = $index_changed || $view_changed;

    if ($stats['changed']) {
      $index = Index::load('offers');
      if ($index) {
        $index->reindex();
      }
    }

    return $stats;
  }

  /**
   * Runs immediate indexing after a sync.
   *
   * @param bool $rebuildTracker
   *   When TRUE, rebuilds tracker before indexing.
   *
   * @return array
   *   Sync and indexing stats.
   */
  public function syncAndIndex(bool $rebuildTracker = FALSE): array {
    $stats = $this->sync(TRUE);

    $indexed = 0;
    $index = Index::load('offers');
    if ($index) {
      if ($rebuildTracker) {
        $index->rebuildTracker();
      }
      $indexed = $index->indexItems();
    }

    $stats['indexed'] = $indexed;
    return $stats;
  }

  /**
   * Syncs index config and reindexes offers after a feature definition change.
   *
   * When sync alters index or view configuration, all pending offers are indexed.
   * Otherwise only offers that reference the definition are reindexed.
   *
   * @return array
   *   Sync stats plus indexed count when applicable.
   */
  public function handleFeatureDefinitionLifecycle(string $definitionId): array {
    $stats = $this->sync(TRUE);

    $index = Index::load('offers');
    if (!$index) {
      return $stats;
    }

    if ($stats['changed']) {
      $stats['indexed'] = (int) $index->indexItems();
      return $stats;
    }

    $nids = $this->findOfferIdsWithFeatureDefinition($definitionId);
    if ($nids === []) {
      $stats['indexed'] = 0;
      return $stats;
    }

    $item_ids = [];
    foreach ($nids as $nid) {
      $item_ids[] = 'entity:node/' . $nid;
    }
    $index->trackItemsUpdated('entity:node', $item_ids);
    $stats['indexed'] = (int) $index->indexItems(count($item_ids));

    return $stats;
  }

  /**
   * Returns offer node IDs that reference a feature definition.
   *
   * @return int[]
   */
  private function findOfferIdsWithFeatureDefinition(string $definitionId): array {
    $nids = $this->entityTypeManager->getStorage('node')->getQuery()
      ->condition('type', 'offer')
      ->condition('field_features.feature_definition_id', $definitionId)
      ->accessCheck(FALSE)
      ->execute();

    return array_map('intval', $nids);
  }

  /**
   * Syncs Search API index field_settings for dynamic feature fields.
   */
  private function syncIndexFieldSettings(array $expected, bool $prune, array &$stats): bool {
    $config = $this->configFactory->getEditable(self::INDEX_CONFIG);
    $field_settings = $config->get('field_settings') ?? [];

    $changed = FALSE;

    foreach ($expected as $field_name => $info) {
      $target = [
        'label' => $info['label'],
        'property_path' => $field_name,
        'type' => $info['field_type'],
      ];

      if (!isset($field_settings[$field_name])) {
        $field_settings[$field_name] = $target;
        $stats['added']++;
        $changed = TRUE;
        continue;
      }

      $current = $field_settings[$field_name];
      if (($current['label'] ?? NULL) !== $target['label']
        || ($current['property_path'] ?? NULL) !== $target['property_path']
        || ($current['type'] ?? NULL) !== $target['type']) {
        $field_settings[$field_name] = $target;
        $stats['updated']++;
        $changed = TRUE;
      }
    }

    if ($prune) {
      foreach (array_keys($field_settings) as $field_name) {
        if (!$this->isDynamicFeatureIndexField((string) $field_name)) {
          continue;
        }

        if (!isset($expected[$field_name])) {
          unset($field_settings[$field_name]);
          $stats['removed']++;
          $changed = TRUE;
        }
      }
    }

    if ($changed) {
      $config->set('field_settings', $field_settings)->save(TRUE);
    }

    return $changed;
  }

  /**
   * Syncs exposed filters + BEF placement in the search view.
   */
  private function syncViewFilters(array $expected, bool $prune, array &$stats): bool {
    if (!$this->shouldSyncViewFilters()) {
      return $this->pruneDynamicFeatureViewFilters($expected, $prune, $stats);
    }

    $config = $this->configFactory->getEditable(self::VIEW_CONFIG);
    $display = $config->get('display') ?? [];

    $default = $display['default'] ?? [];
    $options = $default['display_options'] ?? [];
    $filters = $options['filters'] ?? [];

    $exposed_form = $options['exposed_form'] ?? [];
    $exposed_options = $exposed_form['options'] ?? [];
    $bef = $exposed_options['bef'] ?? [];
    $bef_filters = $bef['filter'] ?? [];

    $changed = FALSE;
    $identifier_registry = [];

    foreach ($expected as $field_name => $info) {
      $views_field_name = $this->normalizeViewsFieldName($field_name);
      $filter_config = $this->buildFilterConfig(
        $views_field_name,
        $info['label'],
        $info['type_driver'],
        $identifier_registry,
      );

      if (($filters[$views_field_name] ?? NULL) !== $filter_config) {
        $filters[$views_field_name] = $filter_config;
        if (isset($options['filters'][$views_field_name])) {
          $stats['updated']++;
        }
        else {
          $stats['added']++;
        }
        $changed = TRUE;
      }

      $bef_config = [
        'plugin_id' => in_array($info['type_driver'], ['flag', 'yes_no'], TRUE) ? 'bef_single' : 'default',
        'advanced' => [
          'collapsible' => FALSE,
          'is_secondary' => TRUE,
        ],
      ];

      if ($bef_config['plugin_id'] === 'bef_single') {
        $bef_config['treat_as_false'] = FALSE;
      }

      if (($bef_filters[$views_field_name] ?? NULL) !== $bef_config) {
        $bef_filters[$views_field_name] = $bef_config;
        $changed = TRUE;
      }
    }

    if ($prune) {
      foreach (array_keys($filters) as $field_name) {
        if (!$this->isDynamicFeatureViewField((string) $field_name)) {
          continue;
        }

        if (!isset($expected[$field_name])) {
          unset($filters[$field_name]);
          $stats['removed']++;
          $changed = TRUE;
        }
      }

      foreach (array_keys($bef_filters) as $field_name) {
        if (!$this->isDynamicFeatureViewField((string) $field_name)) {
          continue;
        }

        if (!isset($expected[$field_name])) {
          unset($bef_filters[$field_name]);
          $changed = TRUE;
        }
      }
    }

    if ($changed) {
      $bef['filter'] = $bef_filters;
      $exposed_options['bef'] = $bef;
      $exposed_form['options'] = $exposed_options;
      $options['exposed_form'] = $exposed_form;
      $options['filters'] = $filters;
      $default['display_options'] = $options;
      $display['default'] = $default;

      $config->set('display', $display)->save(TRUE);
    }

    return $changed;
  }

  /**
   * Whether individual feature filters should be synced into the search view.
   */
  private function shouldSyncViewFilters(): bool {
    return (bool) $this->configFactory->get(self::SETTINGS_CONFIG)->get('sync_view_filters');
  }

  /**
   * Removes stale per-feature view filters when view sync is disabled.
   */
  private function pruneDynamicFeatureViewFilters(array $expected, bool $prune, array &$stats): bool {
    if (!$prune) {
      return FALSE;
    }

    $config = $this->configFactory->getEditable(self::VIEW_CONFIG);
    $display = $config->get('display') ?? [];
    $default = $display['default'] ?? [];
    $options = $default['display_options'] ?? [];
    $filters = $options['filters'] ?? [];
    $exposed_form = $options['exposed_form'] ?? [];
    $exposed_options = $exposed_form['options'] ?? [];
    $bef = $exposed_options['bef'] ?? [];
    $bef_filters = $bef['filter'] ?? [];

    $changed = FALSE;

    foreach (array_keys($filters) as $field_name) {
      if (!$this->isDynamicFeatureViewField((string) $field_name)) {
        continue;
      }

      unset($filters[$field_name]);
      $stats['removed']++;
      $changed = TRUE;
    }

    foreach (array_keys($bef_filters) as $field_name) {
      if (!$this->isDynamicFeatureViewField((string) $field_name)) {
        continue;
      }

      unset($bef_filters[$field_name]);
      $changed = TRUE;
    }

    if ($changed) {
      $bef['filter'] = $bef_filters;
      $exposed_options['bef'] = $bef;
      $exposed_form['options'] = $exposed_options;
      $options['exposed_form'] = $exposed_form;
      $options['filters'] = $filters;
      $default['display_options'] = $options;
      $display['default'] = $default;
      $config->set('display', $display)->save(TRUE);
    }

    return $changed;
  }

  /**
   * Checks if an index field is a dynamic per-feature field.
   */
  private function isDynamicFeatureIndexField(string $field_name): bool {
    if (in_array($field_name, self::CORE_MORE_FILTER_FIELDS, TRUE)) {
      return FALSE;
    }

    return str_starts_with($field_name, 'feature_');
  }

  /**
   * Checks if a view field is a dynamic per-feature filter.
   */
  private function isDynamicFeatureViewField(string $field_name): bool {
    if (in_array($field_name, self::CORE_MORE_FILTER_FIELDS, TRUE)) {
      return FALSE;
    }

    return str_starts_with($field_name, 'feature_');
  }

  /**
   * Builds one view filter definition for a dynamic feature field.
   */
  private function buildFilterConfig(string $field_name, string $label, string $type_driver, array &$identifierRegistry): array {
    $identifier = $this->featureFilterRegistry->buildParamIdentifier($field_name, $identifierRegistry);

    if (in_array($type_driver, ['flag', 'yes_no'], TRUE)) {
      return [
        'id' => $field_name,
        'table' => 'search_api_index_offers',
        'field' => $field_name,
        'relationship' => 'none',
        'group_type' => 'group',
        'admin_label' => '',
        'plugin_id' => 'search_api_boolean',
        'operator' => '=',
        'value' => 'All',
        'group' => 1,
        'exposed' => TRUE,
        'expose' => [
          'operator_id' => $field_name . '_op',
          'label' => $label,
          'description' => '',
          'use_operator' => FALSE,
          'operator' => $field_name . '_op',
          'operator_limit_selection' => FALSE,
          'operator_list' => [],
          'identifier' => $identifier,
          'required' => FALSE,
          'remember' => FALSE,
          'multiple' => FALSE,
          'remember_roles' => [
            'authenticated' => 'authenticated',
          ],
        ],
        'is_grouped' => FALSE,
      ];
    }

    if (in_array($type_driver, ['numeric', 'range'], TRUE)) {
      return [
        'id' => $field_name,
        'table' => 'search_api_index_offers',
        'field' => $field_name,
        'relationship' => 'none',
        'group_type' => 'group',
        'admin_label' => '',
        'plugin_id' => 'search_api_numeric',
        'operator' => 'between',
        'value' => [
          'min' => '',
          'max' => '',
          'value' => '',
        ],
        'group' => 1,
        'exposed' => TRUE,
        'expose' => [
          'operator_id' => $field_name . '_op',
          'label' => $label,
          'description' => '',
          'use_operator' => FALSE,
          'operator' => $field_name . '_op',
          'operator_limit_selection' => FALSE,
          'operator_list' => [],
          'identifier' => $identifier,
          'required' => FALSE,
          'remember' => FALSE,
          'multiple' => FALSE,
          'remember_roles' => [
            'authenticated' => 'authenticated',
          ],
        ],
        'is_grouped' => FALSE,
        'group_info' => [
          'label' => '',
          'description' => '',
          'identifier' => '',
          'optional' => TRUE,
          'widget' => 'select',
          'multiple' => FALSE,
          'remember' => FALSE,
          'default_group' => 'All',
          'default_group_multiple' => [],
          'group_items' => [],
        ],
      ];
    }

    return [
      'id' => $field_name,
      'table' => 'search_api_index_offers',
      'field' => $field_name,
      'relationship' => 'none',
      'group_type' => 'group',
      'admin_label' => '',
      'plugin_id' => 'search_api_string',
      'operator' => '=',
      'value' => [
        'value' => '',
      ],
      'group' => 1,
      'exposed' => TRUE,
      'expose' => [
        'operator_id' => $field_name . '_op',
        'label' => $label,
        'description' => '',
        'use_operator' => FALSE,
        'operator' => $field_name . '_op',
        'operator_limit_selection' => FALSE,
        'operator_list' => [],
        'identifier' => $identifier,
        'required' => FALSE,
        'remember' => FALSE,
        'multiple' => FALSE,
        'remember_roles' => [
          'authenticated' => 'authenticated',
        ],
      ],
      'is_grouped' => FALSE,
      'group_info' => [
        'label' => '',
        'description' => '',
        'identifier' => '',
        'optional' => TRUE,
        'widget' => 'select',
        'multiple' => FALSE,
        'remember' => FALSE,
        'default_group' => 'All',
        'default_group_multiple' => [],
        'group_items' => [],
      ],
    ];
  }

  /**
   * Views field keys for Search API collapse repeated underscores.
   */
  private function normalizeViewsFieldName(string $fieldName): string {
    return $this->featureFilterRegistry->normalizeFeatureSuffix($fieldName);
  }

}
