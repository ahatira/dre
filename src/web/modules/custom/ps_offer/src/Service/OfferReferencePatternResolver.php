<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\ps_offer\Entity\OfferReferencePatternInterface;

final class OfferReferencePatternResolver {

  public function __construct(
    private readonly EntityTypeManagerInterface $entityTypeManager,
  ) {}

  public function resolveForBundle(string $bundle): ?OfferReferencePatternInterface {
    $storage = $this->entityTypeManager->getStorage('ps_offer_reference_pattern');
    $patterns = $storage->loadMultiple();
    $patterns = array_filter($patterns, static fn (OfferReferencePatternInterface $pattern): bool => $pattern->status() && in_array($bundle, $pattern->getTargetBundles(), TRUE));

    usort($patterns, static fn (OfferReferencePatternInterface $left, OfferReferencePatternInterface $right): int => $left->getWeight() <=> $right->getWeight());

    return $patterns[0] ?? NULL;
  }

}