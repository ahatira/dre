<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_offer\Kernel;

use Drupal\Core\Entity\Entity\EntityFormDisplay;
use Drupal\field\Entity\FieldConfig;
use Drupal\KernelTests\KernelTestBase;
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
    $this->installConfig(['node', 'ps_dictionary', 'ps_features', 'ps_price', 'ps_surface', 'ps_diagnostic', 'ps_offer']);
  }

  /**
   * Tests that the offer node type is available.
   */
  public function testOfferNodeTypeCreated(): void {
    $node_type = NodeType::load('offer');
    $this->assertNotNull($node_type);
    $this->assertSame('offer', $node_type->id());
    $this->assertSame('Offer', $node_type->label());
  }

  /**
   * Tests that business-required fields are attached to the offer bundle.
   */
  public function testOfferFieldsMatchBusinessRequirements(): void {
    foreach ([
      'external_id',
      'field_reference',
      'field_client_type',
      'field_property_type',
      'field_transaction_types',
      'field_availability',
      'field_mandate_type',
      'field_address',
      'field_geofield',
      'body',
      'field_surfaces',
      'field_divisions',
      'field_media_photos',
      'field_media_plans',
      'field_media_videos',
      'field_media_brochures',
      'field_media_virtual_tours',
      'field_agents',
    ] as $field_name) {
      $this->assertNotNull(
        FieldConfig::loadByName('node', 'offer', $field_name),
        sprintf('The %s field should exist on the offer bundle.', $field_name),
      );
    }

    $definitions = $this->container->get('entity_field.manager')->getFieldDefinitions('node', 'offer');
    $this->assertGreaterThanOrEqual(450, $definitions['title']->getSetting('max_length'));
    $this->assertSame('text_with_summary', $definitions['body']->getType());
  }

  /**
   * Tests that media fields use the editorial media widget workflow.
   */
  public function testMediaFieldsUseEditorialMediaWidget(): void {
    $display = EntityFormDisplay::load('node.offer.default');
    $this->assertNotNull($display);

    foreach (['field_media_photos', 'field_media_plans', 'field_media_videos', 'field_media_brochures', 'field_media_virtual_tours'] as $field_name) {
      $component = $display->getComponent($field_name);
      $this->assertNotEmpty($component);
      $this->assertContains($component['type'], ['media_library_widget', 'entity_reference_autocomplete']);
    }
  }

  /**
   * Tests that the offer manager service is accessible.
   */
  public function testOfferManagerServiceAccessible(): void {
    $manager = $this->container->get('ps_offer.manager');
    $this->assertNotNull($manager);
  }

}
