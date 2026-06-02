<?php

declare(strict_types=1);

use Drupal\node\NodeInterface;

$storage = \Drupal::entityTypeManager()->getStorage('node');
$nids = $storage->getQuery()
  ->accessCheck(FALSE)
  ->condition('type', 'offer')
  ->execute();

$nodes = $storage->loadMultiple($nids);
$total = count($nodes);
$already_published = 0;
$published = 0;
$skipped = 0;
$errors = [];
$skipped_items = [];

foreach ($nodes as $node) {
  if (!$node instanceof NodeInterface || $node->bundle() !== 'offer') {
    continue;
  }

  if ($node->isPublished()) {
    $already_published++;
    continue;
  }

  $node->setPublished(TRUE);

  try {
    $node->save();
    $published++;
  }
  catch (\Throwable $e) {
    $skipped++;
    $business_id = '';
    if ($node->hasField('field_business_id') && !$node->get('field_business_id')->isEmpty()) {
      $business_id = (string) ($node->get('field_business_id')->value ?? '');
    }

    $reference = '';
    if ($node->hasField('field_reference') && !$node->get('field_reference')->isEmpty()) {
      $reference = (string) ($node->get('field_reference')->value ?? '');
    }

    $reason = $e->getMessage();
    $errors[] = sprintf('%d|%s', (int) $node->id(), $reason);
    $skipped_items[] = [
      'nid' => (int) $node->id(),
      'business_id' => $business_id,
      'reference' => $reference,
      'reason' => $reason,
    ];
  }
}

$report = [
  'generated_at' => \Drupal::time()->getCurrentTime(),
  'total' => $total,
  'already_published' => $already_published,
  'published' => $published,
  'skipped' => $skipped,
  'skipped_items' => $skipped_items,
];

\Drupal::state()->set('ps_migrate.last_import_publication_report', $report);

echo sprintf(
  "Offers publish summary: total=%d already_published=%d published=%d skipped=%d\n",
  $total,
  $already_published,
  $published,
  $skipped
);

if (!empty($errors)) {
  echo "Skipped offers (first 20):\n";
  foreach (array_slice($errors, 0, 20) as $line) {
    echo ' - ' . $line . "\n";
  }
}

echo "Report state key: ps_migrate.last_import_publication_report\n";
