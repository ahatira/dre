<?php

declare(strict_types=1);

namespace Drupal\ps_features\Hook;

use Drupal\Core\Cache\RefinableCacheableDependencyInterface;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Hook\Attribute\Hook;

/**
 * Hook implementations for ps_features module.
 */
final class PsFeaturesHooks {

  /**
   * Implements hook_theme().
   *
   * Registers theme hooks for feature display components.
   *
   * @param array<string, mixed> $existing
   *   Existing theme hooks.
   * @param string $type
   *   The type of theme.
   * @param string $theme
   *   The theme name.
   * @param string $path
   *   The theme path.
   *
   * @return array<string, mixed>
   *   The theme hooks.
   *
   * @see docs/specs/04-ps-features.md#theming
   */
  #[Hook('theme')]
  public function theme(array $existing, string $type, string $theme, string $path): array {
    return [
      'ps_feature_value' => [
        'variables' => [
          'feature_id' => NULL,
          'feature_label' => NULL,
          'value' => NULL,
          'value_type' => NULL,
          'description' => NULL,
        ],
        'template' => 'ps-feature-value',
      ],
      'ps_features_grouped' => [
        'variables' => [
          'group' => NULL,
          'features' => [],
          'collapsible' => TRUE,
          'collapsed' => FALSE,
        ],
        'template' => 'ps-features-grouped',
      ],
      'ps_feature_widget_builder' => [
        'variables' => [
          'features_by_group' => [],
          'existing_values' => [],
          'field_name' => NULL,
          'delta' => 0,
          'element' => [],
        ],
        'template' => 'ps-feature-widget-builder',
      ],
    ];
  }

  /**
   * Implements hook_ENTITY_TYPE_insert() for ps_feature.
   *
   * Invalidates render cache when a feature is created.
   */
  #[Hook('ps_feature_insert')]
  public function featureInsert(EntityInterface $entity): void {
    Cache::invalidateTags(['ps_features_list']);
  }

  /**
   * Implements hook_ENTITY_TYPE_update() for ps_feature.
   *
   * Invalidates render cache when a feature is updated.
   */
  #[Hook('ps_feature_update')]
  public function featureUpdate(EntityInterface $entity): void {
    Cache::invalidateTags(['ps_features_list']);
  }

  /**
   * Implements hook_ENTITY_TYPE_delete() for ps_feature.
   *
   * Invalidates render cache when a feature is deleted.
   */
  #[Hook('ps_feature_delete')]
  public function featureDelete(EntityInterface $entity): void {
    Cache::invalidateTags(['ps_features_list']);
  }

  /**
   * Implements hook_menu_local_tasks_alter().
   *
   * Simplifies the title of config_translation local tasks.
   *
   * @param array<string, mixed> $data
   *   The local tasks data structure.
   * @param string $route_name
   *   The route name.
   * @param \Drupal\Core\Cache\RefinableCacheableDependencyInterface $cacheability
   *   The cacheability metadata.
   */
  #[Hook('menu_local_tasks_alter')]
  public function menuLocalTasksAlter(array &$data, string $route_name, RefinableCacheableDependencyInterface &$cacheability): void {
    // Simplify the title of auto-generated config_translation local tasks.
    // By default they show "Translate [entity label]", we want just "Translate".
    $tasks_to_simplify = [
      'config_translation.local_tasks:entity.ps_feature.config_translation_overview',
    ];

    foreach ($tasks_to_simplify as $task_id) {
      if (isset($data['tabs'][0][$task_id])) {
        $data['tabs'][0][$task_id]['#link']['title'] = t('Translate');
      }
    }
  }

}
