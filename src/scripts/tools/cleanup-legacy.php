<?php

declare(strict_types=1);

$storage = \Drupal::entityTypeManager()->getStorage('ps_dictionary_entry');

foreach (['operation_type.rent', 'operation_type.sale'] as $legacy_id) {
  if ($entry = $storage->load($legacy_id)) {
    $entry->delete();
  }
}

print "operation_type legacy cleanup done." . PHP_EOL;
