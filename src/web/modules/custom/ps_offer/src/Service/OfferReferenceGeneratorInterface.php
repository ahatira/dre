<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Service;

use Drupal\ps_offer\Entity\OfferReferencePatternInterface;

interface OfferReferenceGeneratorInterface {

  public function generate(OfferReferencePatternInterface $pattern, array $context, int $sequence = 1, ?int $timestamp = NULL): string;

}