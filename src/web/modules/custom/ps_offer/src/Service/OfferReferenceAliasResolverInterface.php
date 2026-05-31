<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Service;

use Drupal\ps_offer\Entity\OfferReferencePatternInterface;

interface OfferReferenceAliasResolverInterface {

  public function resolve(OfferReferencePatternInterface $pattern, array $segment, string $sourceField, string $sourceValue): ?string;

}