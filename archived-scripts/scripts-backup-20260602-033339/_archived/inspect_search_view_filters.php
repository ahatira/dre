<?php

declare(strict_types=1);

$view = \Drupal\views\Views::getView('ps_search_offers');
if (!$view) {
  echo "View ps_search_offers not found\n";
  return;
}

$view->setDisplay('page_list');
$view->initHandlers();

$filterHandlers = $view->filter ?? [];

$targets = [
  'feature_amenagements__tec_hall_daccueil',
  'feature_amenagements_tec_hall_daccueil',
  'feature_equipements__tec_cblage_informatique',
  'feature_equipements_tec_cblage_informatique',
  'feature_hauteurs__tec_hauteur_libre',
  'feature_hauteurs_tec_hauteur_libre',
  'field_feature_ids',
];

foreach ($targets as $id) {
  $exists = isset($filterHandlers[$id]);
  echo $id . ': ' . ($exists ? 'handler_loaded' : 'missing_handler') . "\n";
  if ($exists) {
    $handler = $filterHandlers[$id];
    echo '  class=' . get_class($handler) . "\n";
    echo '  configured_plugin=' . ($handler->options['plugin_id'] ?? 'n/a') . "\n";
    echo '  exposed=' . ((int) ($handler->options['exposed'] ?? 0)) . "\n";
  }
}
