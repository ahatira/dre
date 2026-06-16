<?php

declare(strict_types=1);

namespace Drupal\views_promo_card\Service;

use Drupal\Component\Plugin\Exception\ContextException;
use Drupal\Component\Plugin\Exception\MissingValueContextException;
use Drupal\Core\Condition\ConditionManager;
use Drupal\Core\Condition\ConditionPluginBase;
use Drupal\Core\Plugin\Context\ContextHandlerInterface;
use Drupal\Core\Plugin\Context\ContextRepositoryInterface;
use Drupal\Core\Plugin\ContextAwarePluginInterface;
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
    private readonly ContextHandlerInterface $contextHandler,
    private readonly ContextRepositoryInterface $contextRepository,
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
      /** @var \Drupal\Core\Condition\ConditionInterface $condition */
      $condition = $this->conditionManager->createInstance($plugin_id, $condition_config);
      if ($condition instanceof ViewAwareConditionInterface) {
        $condition->setView($view);
      }
      if (!$this->applyConditionContexts($condition)) {
        $results[] = FALSE;
        continue;
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

  /**
   * Applies runtime plugin contexts required by core conditions.
   */
  private function applyConditionContexts(object $condition): bool {
    if (!$condition instanceof ContextAwarePluginInterface) {
      return TRUE;
    }
    if ($condition->getContextDefinitions() === []) {
      return TRUE;
    }
    try {
      $this->ensureContextMapping($condition);
      $contexts = $this->contextRepository->getRuntimeContexts(
        array_values($condition->getContextMapping()),
      );
      $this->contextHandler->applyContextMapping($condition, $contexts);
      return TRUE;
    }
    catch (MissingValueContextException | ContextException) {
      return FALSE;
    }
  }

  /**
   * Assigns default context mappings for core conditions (block pattern).
   */
  private function ensureContextMapping(ContextAwarePluginInterface $condition): void {
    if ($condition->getContextMapping() !== []) {
      return;
    }
    $available = $this->contextRepository->getAvailableContexts();
    $mapping = [];
    foreach ($condition->getContextDefinitions() as $slot => $definition) {
      $matching = $this->contextHandler->getMatchingContexts($available, $definition);
      if ($matching !== []) {
        $mapping[$slot] = (string) array_key_first($matching);
      }
    }
    if ($mapping !== []) {
      $condition->setContextMapping($mapping);
    }
  }

}
