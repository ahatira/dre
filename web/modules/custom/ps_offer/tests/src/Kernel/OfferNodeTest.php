<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_offer\Kernel;

use Drupal\KernelTests\Core\Entity\EntityKernelTestBase;
use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;

/**
 * Tests for Offer node type and fields.
 *
 * @group ps_offer
 */
class OfferNodeTest extends EntityKernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'ps',
    'ps_dictionary',
    'ps_agent',
    'ps_price',
    'ps_features',
    'ps_diagnostic',
    'ps_division',
    'ps_surface',
    'ps_offer',
  ];

  /**
   * Test creating an offer node.
   */
  public function testCreateOffer(): void {
    $offer = Node::create([
      'type' => 'offer',
      'title' => 'Test Office',
      'property_type_code' => 'BUR',
      'transaction_type_codes' => 'LOC',
      'address' => 'Test Address',
      'postal_code' => '75000',
      'city_label' => 'Paris',
      'status' => NodeInterface::PUBLISHED,
    ]);

    $this->assertNotNull($offer->id());
    $this->assertEqual('Test Office', $offer->getTitle());
    $this->assertEqual('BUR', $offer->property_type_code->value);
  }

  /**
   * Test OfferManager service.
   */
  public function testOfferManager(): void {
    $manager = \Drupal::service('ps_offer.manager');
    $this->assertNotNull($manager);

    $offer = $manager->createOffer(['title' => 'Test']);
    $this->assertEqual('offer', $offer->getType());
  }

}
