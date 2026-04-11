<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_offer\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\node\Entity\Node;
use Drupal\node\Entity\NodeType;

/**
 * Kernel tests for ps_offer module.
 *
 * @group ps_offer
 */
class OfferModuleTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'system',
    'user',
    'field',
    'filter',
    'text',
    'node',
    'ps',
    'ps_dictionary',
    'ps_price',
    'ps_features',
    'ps_diagnostic',
    'ps_division',
    'ps_surface',
    'ps_agent',
    'search_api',
    'ps_offer',
  ];

  /**
   * Test that offer node type is created on module install.
   */
  public function testOfferNodeTypeCreated(): void {
    $this->installEntitySchema('node');
    $this->installConfig('node');

    $node_type = NodeType::load('offer');
    $this->assertNotNull($node_type);
    $this->assertEquals('offer', $node_type->id());
    $this->assertEquals('Offer', $node_type->label());
  }

  /**
   * Test that an offer node can be created.
   */
  public function testCreateOfferNode(): void {
    $this->installEntitySchema('node');
    $this->installEntitySchema('user');
    $this->installConfig('node');

    $node = Node::create([
      'type' => 'offer',
      'title' => 'Test Property Offer',
      'status' => 1,
    ]);

    $this->assertNotNull($node);
    $this->assertEquals('offer', $node->getType());
    $this->assertEquals('Test Property Offer', $node->getTitle());
  }

  /**
   * Test that ps_offer service is accessible.
   */
  public function testOfferManagerServiceAccessible(): void {
    $manager = $this->container->get('ps_offer.manager');
    $this->assertNotNull($manager);
  }

}
