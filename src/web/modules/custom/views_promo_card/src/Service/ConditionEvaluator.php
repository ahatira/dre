<?php

declare(strict_types=1);

namespace Drupal\views_promo_card\Service;

use Drupal\Core\Condition\ConditionManager;
use Drupal\Core\Condition\ConditionPluginBase;
use Drupal\views_promo_card\Entity\PromoCardPlacementInterface;
use Drupal\views\ViewExecutable;

/**
 * Evaluates placement visibility conditions.
 */
final class ConditionEvaluator {

  /**
   * Constructs a ConditionEvaluator.
   */
  public function __construct(
    private readonly ConditionManager $conditionManager,
  ) {}

  /**
   * Returns TRUE when all/any conditions pass for a placement.
   */
  public function matches(PromoCardPlacementInterface $placement, ViewExecutable $view): bool {
    $conditions = $placement->getConditions();
    if ($conditions === []) {
      return TRUE;
    }

    $logic = $placement->getConditionsLogic();
    $results = [];

    foreach ($conditions as $condition_config) {
      $plugin_id = (string) ($condition_config['id'] ?? '');
      if ($plugin_id === '') {
        continue;
      }
      $configuration = $condition_config;
      unset($configuration['id']);
      /** @var \Drupal\Core\Condition\ConditionInterface $condition */
      $condition = $this->conditionManager->createInstance($plugin_id, $configuration);
      if ($condition instanceof ViewAwareConditionInterface) {
        $condition->setView($view);
      }
      $result = $condition->evaluate();
      if ($condition instanceof ConditionPluginBase && $condition->isNegated()) {
        $result = !$result;
      }
      $results[] = $result;
    }

    if ($results === []) {
      return TRUE;
    }

    return $logic === 'or'
      ? in_array(TRUE, $results, TRUE)
      : !in_array(FALSE, $results, TRUE);
  }

}
