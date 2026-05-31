<?php

namespace Drupal\ps_feature\Service;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * Builds the catalogue of available features for the Feature Builder widget.
 */
class FeatureCatalogueBuilder {

  public function __construct(
    protected EntityTypeManagerInterface $entityTypeManager,
  ) {}

  /**
   * Build the catalogue for a given entity.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity being edited.
   *
   * @return array
   *   The catalogue array for the JS widget.
   */
  public function buildForEntity(EntityInterface $entity): array {
    $asset_type = $this->resolveAssetType($entity);

    // Load groups ordered by weight.
    $group_storage = $this->entityTypeManager->getStorage('fb_feature_group');
    $groups_raw = $group_storage->loadMultiple();
    usort($groups_raw, fn($a, $b) => $a->getWeight() <=> $b->getWeight());

    $groups = [];
    foreach ($groups_raw as $group) {
      if (!$group->status()) {
        continue;
      }
      $groups[$group->id()] = [
        'id' => $group->id(),
        'label' => $group->label(),
        'weight' => $group->getWeight(),
        'asset_types' => $group->getAssetTypes(),
      ];
    }

    // Load definitions ordered by weight.
    $def_storage = $this->entityTypeManager->getStorage('fb_feature_definition');
    $defs_raw = $def_storage->loadMultiple();
    usort($defs_raw, fn($a, $b) => $a->getWeight() <=> $b->getWeight());

    $definitions = [];
    foreach ($defs_raw as $def) {
      if (!$def->status()) {
        continue;
      }
      // Filter by asset type if the definition has restrictions.
      $required = $def->getRequiredAssetTypes();
      if (!empty($required) && $asset_type && !in_array($asset_type, $required, TRUE)) {
        continue;
      }
      $payload_defaults = $def->getPayloadDefaults();
      $type = $def->getTypeDriver();
      $options = [];
      if ($type === 'dictionary') {
        $dict_type = $payload_defaults['dictionary_id'] ?? $payload_defaults['dictionary_type'] ?? NULL;
        if (is_string($dict_type) && $dict_type !== '') {
          $options = $this->loadDictionaryOptions($dict_type);
        }
      }

      if ($type === 'list') {
        if (!empty($payload_defaults['options']) && is_array($payload_defaults['options'])) {
          foreach ($payload_defaults['options'] as $raw_option) {
            $option = (string) $raw_option;
            if ($option === '') {
              continue;
            }
            $options[] = [
              'id' => $option,
              'code' => $option,
              'label' => $option,
            ];
          }
        }
        else {
          $dict_type = $payload_defaults['dictionary_id'] ?? $payload_defaults['dictionary_type'] ?? NULL;
          if (is_string($dict_type) && $dict_type !== '') {
            $options = $this->loadDictionaryOptions($dict_type);
          }
        }
      }
      $definitions[] = [
        'id' => $def->id(),
        'label' => $def->label(),
        'group' => $def->getGroup(),
        'type' => $type,
        'code' => $def->getCode(),
        'weight' => $def->getWeight(),
        'payload_defaults' => $payload_defaults,
        'options' => $options,
      ];
    }

    return [
      'groups' => array_values($groups),
      'definitions' => $definitions,
    ];
  }

  /**
   * Load dictionary entries for a given dictionary type ID.
   */
  protected function loadDictionaryOptions(string $dict_type): array {
    $entry_storage = $this->entityTypeManager->getStorage('ps_dictionary_entry');
    $ids = $entry_storage->getQuery()
      ->condition('type', $dict_type)
      ->accessCheck(FALSE)
      ->sort('weight')
      ->execute();

    $options = [];
    foreach ($entry_storage->loadMultiple($ids) as $entry) {
      $options[] = [
        'id' => $entry->id(),
        'code' => $entry->getCode(),
        'label' => $entry->label(),
      ];
    }
    return $options;
  }

  /**
   * Try to resolve asset type from the entity (offer node).
   */
  protected function resolveAssetType(EntityInterface $entity): ?string {
    if ($entity->hasField('field_asset_type') && !$entity->get('field_asset_type')->isEmpty()) {
      return $entity->get('field_asset_type')->value;
    }
    if ($entity->hasField('asset_type') && !$entity->get('asset_type')->isEmpty()) {
      return $entity->get('asset_type')->value;
    }
    return NULL;
  }

}
