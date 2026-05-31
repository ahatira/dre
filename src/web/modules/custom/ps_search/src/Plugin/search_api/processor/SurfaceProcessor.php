<?php

declare(strict_types=1);

namespace Drupal\ps_search\Plugin\search_api\processor;

use Drupal\search_api\Datasource\DatasourceInterface;
use Drupal\search_api\IndexInterface;
use Drupal\search_api\Item\ItemInterface;
use Drupal\search_api\Processor\ProcessorPluginBase;
use Drupal\search_api\Processor\ProcessorProperty;

/**
 * Adds computed surface_total and surface_min fields to the offers index.
 *
 * - surface_total: the value of the item with qualification = 'TOTAL'.
 * - surface_min: the value of the item with qualification = 'MINIM', or
 *   falls back to surface_total when no MINIM qualifier exists.
 *
 * @SearchApiProcessor(
 *   id = "ps_surface_processor",
 *   label = @Translation("Surface fields (total & min)"),
 *   description = @Translation("Exposes surface_total and surface_min as decimal fields from field_surfaces."),
 *   stages = {
 *     "add_properties" = 0,
 *   }
 * )
 */
final class SurfaceProcessor extends ProcessorPluginBase {

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

    $properties['surface_total'] = new ProcessorProperty([
      'label' => $this->t('Surface total (m²)'),
      'description' => $this->t('Total surface area from field_surfaces (qualification = TOTAL).'),
      'type' => 'decimal',
      'processor_id' => $this->getPluginId(),
      'is_list' => FALSE,
    ]);

    $properties['surface_min'] = new ProcessorProperty([
      'label' => $this->t('Surface minimum (m²)'),
      'description' => $this->t('Minimum divisible surface from field_surfaces (qualification = MINIM, or TOTAL if absent).'),
      'type' => 'decimal',
      'processor_id' => $this->getPluginId(),
      'is_list' => FALSE,
    ]);

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public function addFieldValues(ItemInterface $item): void {
    $node = $item->getOriginalObject()->getValue();

    if (!$node || !$node->hasField('field_surfaces')) {
      return;
    }

    $surface_total = NULL;
    $surface_min = NULL;

    foreach ($node->get('field_surfaces') as $surface_item) {
      $qualification = $surface_item->qualification;
      $value = (float) $surface_item->value;

      if ($qualification === 'TOTAL') {
        $surface_total = $value;
      }
      elseif ($qualification === 'MINIM') {
        $surface_min = $value;
      }
    }

    // Fallback: if no MINIM, use TOTAL as minimum.
    if ($surface_min === NULL && $surface_total !== NULL) {
      $surface_min = $surface_total;
    }

    $fields = $item->getFields(FALSE);

    $total_fields = $this->getFieldsHelper()->filterForPropertyPath($fields, NULL, 'surface_total');
    foreach ($total_fields as $field) {
      if ($surface_total !== NULL) {
        $field->addValue($surface_total);
      }
    }

    $min_fields = $this->getFieldsHelper()->filterForPropertyPath($fields, NULL, 'surface_min');
    foreach ($min_fields as $field) {
      if ($surface_min !== NULL) {
        $field->addValue($surface_min);
      }
    }
  }

}
