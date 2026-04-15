<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_offer\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\node\Entity\Node;

/**
 * Tests offer surface/division search integration helpers.
 *
 * @group ps_offer
 */
final class OfferSurfaceDivisionSearchTest extends KernelTestBase {

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
    $this->installEntitySchema('ps_division');
    $this->installSchema('node', ['node_access']);

    $this->installConfig([
      'node',
      'ps_dictionary',
      'ps_features',
      'ps_price',
      'ps_surface',
      'ps_diagnostic',
      'ps_division',
      'ps_offer',
    ]);
  }

  /**
   * Tests derived values for matching main and division surfaces.
   */
  public function testResolverForConsistentData(): void {
    $division = \Drupal::entityTypeManager()->getStorage('ps_division')->create([
      'type' => 'division',
      'building_name' => 'D1',
      'surfaces' => [
        [
          'value' => 300,
          'unit' => 'M2',
        ],
      ],
      'status' => 1,
    ]);
    $division->save();

    $offer = Node::create([
      'type' => 'offer',
      'title' => 'Offer surface consistency test',
      'field_surfaces' => [
        [
          'value' => 300,
          'unit' => 'M2',
        ],
      ],
      'field_divisions' => [
        ['target_id' => $division->id()],
      ],
    ]);

    $resolver = $this->container->get('ps_offer.surface_division_search_value_resolver');
    $resolved = $resolver->resolve($offer);

    $this->assertSame(300.0, $resolved['main_surface_value']);
    $this->assertSame('M2', $resolved['main_surface_unit']);
    $this->assertSame(300.0, $resolved['total_surface_divisions']);
    $this->assertSame('ok', $resolved['surface_consistency_status']);
  }

  /**
   * Tests resolver status when divisions are absent.
   */
  public function testResolverWithoutDivisions(): void {
    $offer = Node::create([
      'type' => 'offer',
      'title' => 'Offer no divisions',
      'field_surfaces' => [
        [
          'value' => 450,
          'unit' => 'M2',
        ],
      ],
    ]);

    $resolver = $this->container->get('ps_offer.surface_division_search_value_resolver');
    $resolved = $resolver->resolve($offer);

    $this->assertSame(450.0, $resolved['main_surface_value']);
    $this->assertNull($resolved['total_surface_divisions']);
    $this->assertSame('warning', $resolved['surface_consistency_status']);
  }

  /**
   * Tests that the Search API processor is discoverable.
   */
  public function testSearchApiProcessorDefinitionExists(): void {
    $definitions = $this->container
      ->get('plugin.manager.search_api.processor')
      ->getDefinitions();

    $this->assertArrayHasKey('ps_offer_surface_division', $definitions);
  }

}
