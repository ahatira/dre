<?php

namespace Drupal\ps_feature\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\FieldItemListInterface;

/**
 * Builds the initial state array for the Feature Builder widget from field items.
 */
class FeatureBuilderStateBuilder {

  /**
   * The feature definition storage.
   */
  private readonly EntityStorageInterface $definitionStorage;

  /**
   * Creates a new state builder instance.
   */
  public function __construct(
    EntityTypeManagerInterface $entityTypeManager,
  ) {
    $this->definitionStorage = $entityTypeManager->getStorage('fb_feature_definition');
  }

  /**
   * Build the initial state array from field items.
   *
   * @param \Drupal\Core\Field\FieldItemListInterface $items
   *   The field items.
   *
   * @return array
   *   The initial state array for the JS widget.
   */
  public function buildFromItems(FieldItemListInterface $items): array {
    $features = [];
    foreach ($items as $delta => $item) {
      if (empty($item->feature_definition_id)) {
        continue;
      }
      $payload = [];
      if (!empty($item->payload)) {
        $decoded = json_decode($item->payload, TRUE);
        if (is_array($decoded)) {
          $payload = $decoded;
        }
      }
      $def = $this->definitionStorage->load($item->feature_definition_id);
      $label = is_string($payload['label'] ?? NULL) && trim($payload['label']) !== ''
        ? trim($payload['label'])
        : ($def ? $def->label() : $item->feature_definition_id);
      $features[] = [
        'id' => $item->feature_definition_id,
        'payload' => $payload,
        'delta' => $delta,
        'group' => $def ? $def->getGroup() : '',
        'label' => $label,
        'type' => $def ? $def->getTypeDriver() : '',
      ];
    }
    return [
      'features' => $features,
    ];
  }

}
