<?php

declare(strict_types=1);

$nodeIds = [7, 8, 10];

foreach ($nodeIds as $nid) {
  $node = \Drupal\node\Entity\Node::load($nid);
  if (!$node) continue;
  
  echo "\n=== Node $nid: " . $node->label() . " ===\n";
  $refs = $node->get('field_features')->getValue();
  echo "features: " . count($refs) . "\n";
  
  $ids = array_column($refs, 'target_id');
  $features = \Drupal::entityTypeManager()
    ->getStorage('entity_offer_feature')
    ->loadMultiple(array_slice($ids, 0, 5));
  
  foreach ($features as $feature) {
    $defId = $feature->getFeatureDefinitionId();
    $def = \Drupal::entityTypeManager()
      ->getStorage('fb_feature_definition')
      ->load($defId);
    
    $exposed = $def ? ($def->isExposeAsFilter() ? 'exposed' : 'hidden') : 'def-not-found';
    $type = $def ? $def->get('type_driver') : 'unknown';
    echo "  - $defId [$type] [$exposed]\n";
  }
}
