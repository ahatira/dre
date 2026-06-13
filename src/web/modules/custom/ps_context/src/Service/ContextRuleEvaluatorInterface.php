<?php

declare(strict_types=1);

namespace Drupal\ps_context\Service;

use Drupal\node\NodeInterface;
use Drupal\ps_context\Value\OfferContextState;

/**
 * Evaluates ps_context_rule entities for offer field values.
 */
interface ContextRuleEvaluatorInterface {

  /**
   * Resolves matrix state from a node entity.
   */
  public function resolveFromNode(NodeInterface $node): OfferContextState;

  /**
   * Resolves matrix state from simulated field values.
   *
   * @param array<string, string> $fieldValues
   *   Keys: field_asset_type, field_operation_type, field_divisible.
   */
  public function resolveFromValues(array $fieldValues): OfferContextState;

}
