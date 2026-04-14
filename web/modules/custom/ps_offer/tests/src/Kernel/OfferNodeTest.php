<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_offer\Kernel;

use Drupal\KernelTests\Core\Entity\EntityKernelTestBase;
use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;

/**
 * Tests for Offer node type and business rules.
 *
 * @group ps_offer
 */
class OfferNodeTest extends EntityKernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'system',
    'user',
    'field',
    'filter',
    'text',
    'options',
    'file',
    'node',
    'media',
    'media_library',
    'address',
    'geofield',
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
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->installEntitySchema('file');
    $this->installEntitySchema('media');
    $this->installConfig(['node', 'ps_dictionary', 'ps_features', 'ps_price', 'ps_surface', 'ps_diagnostic', 'ps_offer']);
  }

  /**
   * Tests that the offer reference is generated from business rules.
   */
  public function testOfferReferenceIsGeneratedFromBusinessRules(): void {
    $offer = Node::create([
      'type' => 'offer',
      'title' => str_repeat('Long office title ', 20),
      'field_property_type' => 'BUR',
      'field_transaction_types' => 'LOC',
      'field_client_type' => 'B2B',
      'status' => NodeInterface::PUBLISHED,
    ]);
    $offer->save();

    $reference = (string) $offer->get('field_reference')->value;
    $this->assertStringStartsWith('OLBUR' . date('y'), $reference);
    $this->assertSame(12, strlen($reference));
  }

  /**
   * Tests that the offer body field stores rich text content.
   */
  public function testOfferBodyStoresRichText(): void {
    $offer = Node::create([
      'type' => 'offer',
      'title' => 'Offer with body',
      'field_property_type' => 'ACT',
      'field_transaction_types' => 'LOC',
      'field_client_type' => 'B2B',
      'body' => [
        'value' => '<p>Offer body</p>',
        'format' => 'basic_html',
      ],
    ]);
    $offer->save();

    $this->assertSame('<p>Offer body</p>', (string) $offer->get('body')->value);
  }

  /**
   * Tests the OfferManager service.
   */
  public function testOfferManager(): void {
    $manager = $this->container->get('ps_offer.manager');
    $this->assertNotNull($manager);

    $offer = $manager->createOffer(['title' => 'Test']);
    $this->assertSame('offer', $offer->getType());
  }

}
