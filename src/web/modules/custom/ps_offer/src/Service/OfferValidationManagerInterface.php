<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Service;

use Drupal\node\NodeInterface;

interface OfferValidationManagerInterface {

  public function apply(NodeInterface $node): void;

}
