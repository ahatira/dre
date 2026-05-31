<?php

declare(strict_types=1);

use Drupal\node\Entity\Node;

$targetNodeId = 4;
$featureDefinitions = [
  'amenagements__tec_hall_daccueil' => ['type' => 'flag', 'payload' => ['present' => TRUE]],
  'equipements__tec_cblage_informatique' => ['type' => 'flag', 'payload' => ['present' => TRUE]],
  'hauteurs__tec_hauteur_libre' => ['type' => 'numeric', 'payload' => ['value' => 8.5, 'unit' => 'm']],
];

$node = Node::load($targetNodeId);
if (!$node || $node->bundle() !== 'offer' || !$node->hasField('field_features')) {
  echo "Target offer node not found or missing field_features.\n";
  return;
}

$definitionStorage = \Drupal::entityTypeManager()->getStorage('fb_feature_definition');

$existingRows = $node->get('field_features')->getValue();
$existingByDefinition = [];
foreach ($existingRows as $row) {
  $existingDefinitionId = $row['feature_definition_id'] ?? NULL;
  if (!empty($existingDefinitionId)) {
    $existingByDefinition[$existingDefinitionId] = $row;
  }
}

foreach ($featureDefinitions as $definitionId => $info) {
  $definition = $definitionStorage->load($definitionId);
  if (!$definition) {
    echo "Feature definition missing: {$definitionId}\n";
    continue;
  }

  if (!$definition->isExposeAsFilter()) {
    $definition->set('expose_as_filter', TRUE);
    $definition->save();
    echo "Enabled expose_as_filter for {$definitionId}\n";
  }

  $existingByDefinition[$definitionId] = [
    'feature_definition_id' => $definitionId,
    'payload' => json_encode($info['payload'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
  ];
}

$newRows = array_values($existingByDefinition);

$node->set('field_features', $newRows);
$node->save();

echo "Offer {$targetNodeId} feature references updated.\n";
