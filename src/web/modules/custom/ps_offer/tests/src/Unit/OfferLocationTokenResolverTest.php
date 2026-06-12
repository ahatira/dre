<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_offer\Unit;

use Drupal\node\NodeInterface;
use Drupal\ps_offer\Service\OfferLocationTokenResolver;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @coversDefaultClass \Drupal\ps_offer\Service\OfferLocationTokenResolver
 */
#[Group('ps_offer')]
final class OfferLocationTokenResolverTest extends UnitTestCase {

  /**
   * @covers ::resolveFromOffer
   */
  public function testResolveFromOfferPrefersPostalCode(): void {
    $node = $this->createOfferNode([
      'postal_code' => '75012',
      'locality' => 'Paris',
    ]);

    $resolver = new OfferLocationTokenResolver();
    $this->assertSame('75012', $resolver->resolveFromOffer($node));
  }

  /**
   * @covers ::resolveFromOffer
   */
  public function testResolveFromOfferFallsBackToLocality(): void {
    $node = $this->createOfferNode([
      'locality' => 'Lyon',
    ]);

    $resolver = new OfferLocationTokenResolver();
    $this->assertSame('Lyon', $resolver->resolveFromOffer($node));
  }

  /**
   * @param array<string, string> $address
   */
  private function createOfferNode(array $address): NodeInterface&MockObject {
    $field = $this->createMock(\Drupal\Core\Field\FieldItemListInterface::class);
    $field->method('isEmpty')->willReturn($address === []);

    $item = new class($address) {
      public function __construct(private readonly array $address) {}
      public function getValue(): array {
        return $this->address;
      }
    };

    $field->method('first')->willReturn($address === [] ? NULL : $item);

    $node = $this->createMock(NodeInterface::class);
    $node->method('hasField')->with('field_address')->willReturn(TRUE);
    $node->method('get')->with('field_address')->willReturn($field);

    return $node;
  }

}
