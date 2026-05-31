<?php

declare(strict_types=1);

$node = \Drupal\node\Entity\Node::load(4);
if (!$node) {
  echo "Node 4 not found\n";
  return;
}

echo "Node title: " . $node->label() . "\n";
echo "Has field_features: " . ($node->hasField('field_features') ? 'yes' : 'no') . "\n";

if ($node->hasField('field_features')) {
  $values = $node->get('field_features')->getValue();
  echo "feature_count=" . count($values) . "\n";
  foreach ($values as $v) {
    echo "target_id=" . $v['target_id'] . "\n";
  }
  
  if (!empty($values)) {
    $ids = array_column($values, 'target_id');
    $features = \Drupal::entityTypeManager()
      ->getStorage('entity_offer_feature')
      ->loadMultiple($ids);
    
    foreach ($features as $feature) {
      echo "\n--- Feature ---\n";
      echo "id=" . $feature->id() . "\n";
      $def_id = $feature->getFeatureDefinitionId();
      echo "definition_id=" . $def_id . "\n";
      $def = \Drupal::entityTypeManager()->getStorage('fb_feature_definition')->load($def_id);
      if ($def) {
        echo "expose_as_filter=" . ($def->isExposeAsFilter() ? 'yes' : 'no') . "\n";
        echo "type_driver=" . $def->get('type_driver') . "\n";
      }
    }
  }
}
