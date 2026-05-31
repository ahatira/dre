<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Service;

use Drupal\node\NodeInterface;

interface OfferReferenceManagerInterface {

  public function buildContextFromNode(NodeInterface $node): array;

  public function buildContextFromFormValues(array $values): array;

  public function generateForBundle(string $bundle, array $context, int $sequence = 1): string;

  public function applyReferenceMode(NodeInterface $node): void;

}