<?php

declare(strict_types=1);

use Drupal\views\Views;

$view = Views::getView('ps_search_offers');
if ($view === NULL) {
  echo "view not found\n";
  return;
}
$view->setDisplay('page_list');
$view->execute();
echo 'total_rows: ' . ($view->total_rows ?? 0) . ' rows: ' . count($view->result) . "\n";

$resolver = \Drupal::service('views_promo_card.placement_resolver');
$placements = $resolver->resolve($view);
echo 'placements after conditions: ' . count($placements) . "\n";

$storage = \Drupal::entityTypeManager()->getStorage('promo_card_placement');
$all = $storage->loadMultiple();
echo 'all placements: ' . count($all) . "\n";

$evaluator = \Drupal::service('views_promo_card.condition_evaluator');
foreach ($all as $placement) {
  if ($placement->getViewId() !== 'ps_search_offers') {
    continue;
  }
  foreach ($placement->getConditions() as $cond) {
    $id = $cond['id'] ?? '';
    $plugin = \Drupal::service('plugin.manager.condition')->createInstance($id, $cond);
    if ($plugin instanceof \Drupal\views_promo_card\Service\ViewAwareConditionInterface) {
      $plugin->setView($view);
    }
    $result = $plugin->evaluate();
    echo "condition $id: " . ($result ? 'pass' : 'fail') . "\n";
  }
  echo 'placement ' . $placement->id() . ' matches: ' . ($evaluator->matches($placement, $view) ? 'yes' : 'no') . "\n";
}

$insertion = \Drupal::service('views_promo_card.insertion_manager');
$slots = $insertion->buildSlots($view, count($view->result));
echo 'slot keys: ' . implode(',', array_keys($slots)) . "\n";
