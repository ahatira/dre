<?php

declare(strict_types=1);

namespace Drupal\ps_search\Plugin\search_api\processor;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\search_api\Datasource\DatasourceInterface;
use Drupal\search_api\IndexInterface;
use Drupal\search_api\Item\ItemInterface;
use Drupal\search_api\Processor\ProcessorPluginBase;
use Drupal\search_api\Processor\ProcessorProperty;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Indexes features by their group (equipments, services, etc.).
 *
 * @SearchApiProcessor(
 *   id = "ps_feature_by_group",
 *   label = @Translation("Feature by group"),
 *   description = @Translation("Exposes feature fields grouped by feature group."),
 *   stages = {
 *     "add_properties" = 0,
 *   }
 * )
 */
final class FeatureByGroupProcessor extends ProcessorPluginBase {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): static {
    $instance = parent::create($container, $configuration, $plugin_id, $plugin_definition);
    $instance->entityTypeManager = $container->get('entity_type.manager');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public static function supportsIndex(IndexInterface $index): bool {
    foreach ($index->getDatasources() as $datasource) {
      if ($datasource->getEntityTypeId() === 'node') {
        return TRUE;
      }
    }
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function getPropertyDefinitions(DatasourceInterface $datasource = NULL): array {
    if ($datasource !== NULL) {
      return [];
    }

    $properties = [];

    // Load all feature groups to create dynamic properties
    try {
      $groups = $this->entityTypeManager
        ->getStorage('fb_feature_group')
        ->loadMultiple();

      foreach ($groups as $group_id => $group) {
        $property_id = 'feature_group_' . $group_id;
        $properties[$property_id] = new ProcessorProperty([
          'label' => $this->t('Features: @group', ['@group' => $group->label()]),
          'description' => $this->t('Feature IDs from group: @group', ['@group' => $group->label()]),
          'type' => 'string',
          'processor_id' => $this->getPluginId(),
          'is_list' => TRUE,
        ]);
      }
    }
    catch (\Exception $e) {
      // Fail silently if feature groups not available
    }

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public function addFieldValues(ItemInterface $item): void {
    try {
      $node = $item->getOriginalObject()->getValue();
      
      if (!$node || !$node->hasField('field_features')) {
        return;
      }

      // Get all feature entity IDs
      $feature_values = $node->get('field_features')->getValue();
      if (empty($feature_values)) {
        return;
      }

      // Load feature entities
      $feature_ids = array_column($feature_values, 'target_id');
      $features = $this->entityTypeManager
        ->getStorage('entity_offer_feature')
        ->loadMultiple($feature_ids);
      
      if (empty($features)) {
        return;
      }

      // Group features by their definition's group
      $grouped_features = [];
      
      foreach ($features as $feature) {
        $definition_id = $feature->getFeatureDefinitionId();
        if (empty($definition_id)) {
          continue;
        }

        // Load feature definition
        $definition = $this->entityTypeManager
          ->getStorage('fb_feature_definition')
          ->load($definition_id);
        
        if (!$definition) {
          continue;
        }

        $group = $definition->getGroup();
        if (empty($group)) {
          continue;
        }

        // Initialize group array if needed
        if (!isset($grouped_features[$group])) {
          $grouped_features[$group] = [];
        }

        // Add feature definition ID to this group
        $grouped_features[$group][] = $definition_id;
      }

      // Add grouped features to search fields
      foreach ($grouped_features as $group_id => $feature_ids) {
        $field_id = 'feature_group_' . $group_id;
        $fields = $this->getFieldsHelper()
          ->filterForPropertyPath($item->getFields(), NULL, $field_id);
        
        foreach ($fields as $field) {
          $field->setValues($feature_ids);
        }
      }
    }
    catch (\Exception $e) {
      // Fail silently to avoid breaking indexing
    }
  }

}
