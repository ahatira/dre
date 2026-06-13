<?php

/**
 * @file
 * Manual recette OFF-01 — B2B LOC BUR non divisible (brouillon).
 */

declare(strict_types=1);

use Drupal\node\Entity\Node;

$editor = reset(\Drupal::entityTypeManager()->getStorage('user')->loadByProperties(['name' => 'content.editor']));
if (!$editor) {
  print "OFF01=fail_user\n";
  return;
}

\Drupal::service('account_switcher')->switchTo($editor);

try {
  $node = Node::create([
    'type' => 'offer',
    'title' => 'OFF-01-LOC-BUR Recette',
    'langcode' => 'en',
    'status' => 0,
    'uid' => $editor->id(),
  ]);
  $node->set('field_client_type', 'B2B');
  $node->set('field_operation_type', 'LOC');
  $node->set('field_asset_type', 'BUR');
  $node->set('field_divisible', 0);
  if ($node->hasField('field_reference') && $node->get('field_reference')->isEmpty()) {
    $node->set('field_reference', 'REC-OFF01-' . time());
  }
  $violations = $node->validate();
  if ($violations->count() > 0) {
    print 'OFF01=fail_validation:' . $violations->get(0)->getMessage() . PHP_EOL;
    return;
  }
  $node->save();
  print 'OFF01=pass_nid:' . $node->id() . PHP_EOL;
}
catch (\Throwable $e) {
  print 'OFF01=fail_exception:' . $e->getMessage() . PHP_EOL;
}
finally {
  \Drupal::service('account_switcher')->switchBack();
}
