<?php

declare(strict_types=1);

namespace Drupal\ps_context\Service;

use Drupal\node\NodeInterface;
use Drupal\ps_offer\OfferContextResolverInterface;

/**
 * Bridges ps_context evaluation to ps_offer consumers.
 */
final class OfferMatrixContextResolver implements OfferContextResolverInterface {

  public function __construct(
    private readonly ContextRuleEvaluatorInterface $evaluator,
  ) {}

  public function isTabVisible(NodeInterface $offer, string $tab): bool {
    if ($offer->bundle() !== 'offer') {
      return TRUE;
    }

    return $this->evaluator->resolveFromNode($offer)->isTabVisible($tab);
  }

  public function isFieldVisible(NodeInterface $offer, string $field): bool {
    if ($offer->bundle() !== 'offer') {
      return TRUE;
    }

    return $this->evaluator->resolveFromNode($offer)->isFieldVisible($field);
  }

  public function isCapacityDriven(NodeInterface $offer): bool {
    if ($offer->bundle() !== 'offer') {
      return FALSE;
    }

    return $this->evaluator->resolveFromNode($offer)->isCapacityDriven();
  }

}
