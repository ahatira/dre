<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_offer\Unit;

use Drupal\node\NodeInterface;
use Drupal\ps_offer\Hook\OfferHooks;
use Drupal\ps_offer\Service\OfferReferenceManagerInterface;
use Drupal\ps_offer\Service\OfferValidationManagerInterface;
use Drupal\Tests\UnitTestCase;

final class OfferHooksTest extends UnitTestCase {

  public function testNodePresaveDelegatesToValidationManager(): void {
    $node = $this->createMock(NodeInterface::class);

    $validationManager = $this->createMock(OfferValidationManagerInterface::class);
    $validationManager
      ->expects($this->once())
      ->method('apply')
      ->with($node);

    $referenceManager = $this->createMock(OfferReferenceManagerInterface::class);
    $referenceManager
      ->expects($this->once())
      ->method('applyReferenceMode')
      ->with($node);

    $hooks = new OfferHooks($validationManager, $referenceManager);
    $hooks->nodePresave($node);
  }

}
