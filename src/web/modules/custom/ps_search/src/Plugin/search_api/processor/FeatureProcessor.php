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
 * Indexes individual features as separate fields.
 *
 * @SearchApiProcessor(
 *   id = "ps_feature",
 *   label = @Translation("Feature indexer"),
 *   description = @Translation("Exposes each feature as a separate indexed field."),
 *   stages = {
 *     "add_properties" = 0,
 *   }
 * )
 */
final class FeatureProcessor extends ProcessorPluginBase {

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
  public function getPropertyDefinitions(?DatasourceInterface $datasource = NULL): array {
    if ($datasource !== NULL) {
      return [];
    }

    $properties = [];

    // Load all feature definitions
    $definitions = $this->entityTypeManager
      ->getStorage('fb_feature_definition')
      ->loadMultiple();

    foreach ($definitions as $definition) {
      // Only expose features that have expose_as_filter = TRUE
      if (!$definition->isExposeAsFilter()) {
        continue;
      }

      $feature_id = $this->normalizeFeatureSuffix((string) $definition->id());
      $type_driver = (string) ($definition->get('type_driver') ?? '');
      $label = $definition->label();
      $group = $definition->get('group') ?? 'other';

      // Determine Search API field type based on feature type_driver.
      $field_type = match ($type_driver) {
        'flag', 'yes_no' => 'boolean',
        'numeric', 'range' => 'decimal',
        'date' => 'date',
        'text', 'select', 'multiselect', 'dictionary', 'taxonomy', 'list' => 'string',
        default => 'string',
      };

      $property = new ProcessorProperty([
        'label' => $this->t('@label (@group)', [
          '@label' => $label,
          '@group' => strtoupper($group),
        ]),
        'description' => $this->t('Feature: @label', ['@label' => $label]),
        'type' => $field_type,
        'is_list' => FALSE,
        'processor_id' => $this->getPluginId(),
      ]);

      $properties["feature_$feature_id"] = $property;
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

      // Get all feature values (embedded field, not entity reference)
      $feature_values = $node->get('field_features')->getValue();
      if (empty($feature_values)) {
        return;
      }

      $fields = $item->getFields(FALSE);

      // Index each feature directly from field values
      foreach ($feature_values as $feature_value) {
        $definition_id = $feature_value['feature_definition_id'] ?? NULL;
        if (empty($definition_id)) {
          continue;
        }

        $raw_payload = $feature_value['payload'] ?? '{}';
        $payload = is_string($raw_payload)
          ? (json_decode($raw_payload, TRUE) ?? [])
          : (array) $raw_payload;

        // Load feature definition to get type_driver
        $definition = $this->entityTypeManager
          ->getStorage('fb_feature_definition')
          ->load($definition_id);
        
        if (!$definition) {
          continue;
        }

        // Skip disabled feature filters to stay consistent with properties.
        if (!$definition->isExposeAsFilter()) {
          continue;
        }

        $type_driver = (string) ($definition->get('type_driver') ?? '');
        $field_name = 'feature_' . $this->normalizeFeatureSuffix((string) $definition_id);

        // Extract value based on type_driver.
        $value = $this->extractIndexedValue($type_driver, $payload);

        // Set field value if we have one.
        if ($value !== NULL) {
          $matching_fields = $this->getFieldsHelper()->filterForPropertyPath($fields, NULL, $field_name);
          foreach ($matching_fields as $field) {
            $field->addValue($value);
          }
        }
      }
    }
    catch (\Exception $e) {
      // Log error but don't break indexing
      \Drupal::logger('ps_search')->error('Error indexing features: @message', [
        '@message' => $e->getMessage(),
      ]);
    }
  }

  /**
   * Extracts the indexed value from a feature payload.
   */
  private function extractIndexedValue(string $typeDriver, array $payload): mixed {
    return match ($typeDriver) {
      'flag' => TRUE,
      'yes_no' => (bool) ($payload['value'] ?? FALSE),
      'numeric' => isset($payload['value']) ? (float) $payload['value'] : NULL,
      'range' => isset($payload['min'])
        ? (float) $payload['min']
        : (isset($payload['value']) ? (float) $payload['value'] : NULL),
      'text', 'select' => isset($payload['value']) ? (string) $payload['value'] : NULL,
      'multiselect' => isset($payload['values']) && is_array($payload['values'])
        ? implode(' ', $payload['values']) : NULL,
      'dictionary' => isset($payload['code']) ? (string) $payload['code'] : NULL,
      'taxonomy' => isset($payload['tid']) ? (string) (int) $payload['tid'] : NULL,
      'list' => isset($payload['codes']) && is_array($payload['codes'])
        ? implode(' ', $payload['codes']) : NULL,
      'date' => isset($payload['value']) ? (string) $payload['value'] : NULL,
      default => NULL,
    };
  }

  /**
   * Normalizes feature IDs for Search API field compatibility.
   */
  private function normalizeFeatureSuffix(string $suffix): string {
    return preg_replace('/_{2,}/', '_', $suffix) ?? $suffix;
  }

}
