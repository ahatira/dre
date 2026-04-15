<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_offer\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\node\Entity\Node;

/**
 * Tests offer price search integration helpers.
 *
 * @group ps_offer
 */
final class OfferPriceSearchTest extends KernelTestBase {

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
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installEntitySchema('user');
    $this->installEntitySchema('node');
    $this->installEntitySchema('file');
    $this->installEntitySchema('media');
    $this->installSchema('node', ['node_access']);

    $this->installConfig([
      'node',
      'ps_dictionary',
      'ps_features',
      'ps_price',
      'ps_surface',
      'ps_diagnostic',
      'ps_offer',
    ]);
  }

  /**
   * Tests derived main price fields for a standard amount.
   */
  public function testResolverForStandardPrice(): void {
    $offer = Node::create([
      'type' => 'offer',
      'title' => 'Offer search test',
      'field_prices' => [
        [
          'amount' => 10000,
          'currency_code' => 'EUR',
          'unit_code' => 'GLO',
          'period_code' => 'MEN',
          'is_vat_excluded' => 1,
        ],
      ],
      'field_surfaces' => [
        [
          'value' => 500,
          'unit' => 'M2',
        ],
      ],
    ]);

    $resolver = $this->container->get('ps_offer.price_search_value_resolver');
    $resolved = $resolver->resolve($offer);

    $this->assertSame(10000.0, $resolved['amount']);
    $this->assertSame(240.0, $resolved['normalized']);
    $this->assertFalse($resolved['on_request']);
    $this->assertIsString($resolved['display']);
    $this->assertNotSame('', $resolved['display']);
  }

  /**
   * Tests derived main price fields for an on-request price.
   */
  public function testResolverForOnRequestPrice(): void {
    $offer = Node::create([
      'type' => 'offer',
      'title' => 'Offer on request test',
      'field_prices' => [
        [
          'is_on_request' => 1,
        ],
      ],
    ]);

    $resolver = $this->container->get('ps_offer.price_search_value_resolver');
    $resolved = $resolver->resolve($offer);

    $this->assertTrue($resolved['on_request']);
    $this->assertNull($resolved['amount']);
    $this->assertNull($resolved['normalized']);
    $this->assertSame('On request', $resolved['display']);
  }

  /**
   * Tests that the Search API processor is discoverable.
   */
  public function testSearchApiProcessorDefinitionExists(): void {
    $definitions = $this->container
      ->get('plugin.manager.search_api.processor')
      ->getDefinitions();

    $this->assertArrayHasKey('ps_offer_price_main', $definitions);
  }

}
