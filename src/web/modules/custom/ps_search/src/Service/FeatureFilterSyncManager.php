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

  public function __construct(
    private readonly ConfigFactoryInterface $configFactory,
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

    $expected = $this->buildExpectedFeatureMap();

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
   * Builds expected filter metadata from feature definitions.
   *
   * @return array<string, array<string, string>>
   *   Map keyed by Search API field machine name.
   */
  private function buildExpectedFeatureMap(): array {
    $definitions = $this->entityTypeManager
      ->getStorage('fb_feature_definition')
      ->loadMultiple();

    $expected = [];
    foreach ($definitions as $definition) {
      if (!$definition->isExposeAsFilter()) {
        continue;
      }

      $feature_id = (string) $definition->id();
      $field_name = 'feature_' . $this->normalizeFeatureSuffix($feature_id);

      $type_driver = (string) $definition->getTypeDriver();
      $field_type = match ($type_driver) {
        'flag', 'yes_no' => 'boolean',
        'numeric', 'range' => 'decimal',
        default => 'string',
      };

      $expected[$field_name] = [
        'label' => (string) $definition->label(),
        'type_driver' => $type_driver,
        'field_type' => $field_type,
      ];
    }

    ksort($expected);
    return $expected;
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
        if (!str_starts_with((string) $field_name, 'feature_')) {
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
        if (str_starts_with((string) $field_name, 'feature_') && !isset($expected[$field_name])) {
          unset($filters[$field_name]);
          $stats['removed']++;
          $changed = TRUE;
        }
      }

      foreach (array_keys($bef_filters) as $field_name) {
        if (str_starts_with((string) $field_name, 'feature_') && !isset($expected[$field_name])) {
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
   * Builds one view filter definition for a dynamic feature field.
   */
  private function buildFilterConfig(string $field_name, string $label, string $type_driver, array &$identifierRegistry): array {
    $identifier = $this->buildUniqueIdentifier($field_name, $identifierRegistry);

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
   * Builds a stable and unique exposed filter identifier.
   */
  private function buildUniqueIdentifier(string $field_name, array &$identifierRegistry): string {
    $base = preg_replace('/[^a-z0-9_]+/', '_', strtolower($field_name)) ?? '';
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
   * Views field keys for Search API collapse repeated underscores.
   */
  /**
   * Views field keys for Search API collapse repeated underscores.
   */
  private function normalizeViewsFieldName(string $fieldName): string {
    return preg_replace('/_{2,}/', '_', $fieldName) ?? $fieldName;
  }

  /**
   * Best-effort reverse normalization for pruning legacy entries.
   */
  private function denormalizeViewsFieldName(string $fieldName): string {
    return str_replace('_tec_', '__tec_', $fieldName);
  }

  /**
   * Normalizes a feature suffix so generated field IDs stay Views-compatible.
   */
  private function normalizeFeatureSuffix(string $suffix): string {
    return preg_replace('/_{2,}/', '_', $suffix) ?? $suffix;
  }

}
