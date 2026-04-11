<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_surface\Kernel;

use Drupal\KernelTests\KernelTestBase;

/**
 * Tests that ps_surface dictionary configuration loads correctly.
 *
 * @group ps_surface
 */
final class DictionaryIntegrationTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'system',
    'user',
    'field',
    'options',
    'ps',
    'ps_dictionary',
    'ps_surface',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->installConfig([
      'ps_dictionary',
      'ps_surface',
    ]);
  }

  /**
   * Test that surface_nature dictionary type is installed.
   */
  public function testSurfaceNatureDictionaryType(): void {
    $entity_storage = $this->container->get('entity_type.manager')->getStorage('ps_dictionary_type');
    $entity = $entity_storage->load('surface_nature');

    $this->assertNotNull($entity, 'surface_nature dictionary type should exist');
    $this->assertEquals('Surface Nature', $entity->label());
  }

  /**
   * Test that surface_qualification dictionary type is installed.
   */
  public function testSurfaceQualificationDictionaryType(): void {
    $entity_storage = $this->container->get('entity_type.manager')->getStorage('ps_dictionary_type');
    $entity = $entity_storage->load('surface_qualification');

    $this->assertNotNull($entity, 'surface_qualification dictionary type should exist');
    $this->assertEquals('Surface Qualification', $entity->label());
  }

  /**
   * Test that surface_nature entries are installed.
   */
  public function testSurfaceNatureEntries(): void {
    $entity_storage = $this->container->get('entity_type.manager')->getStorage('ps_dictionary_entry');

    // Check ACT entry.
    $act = $entity_storage->load('surface_nature_act');
    $this->assertNotNull($act, 'surface_nature_act entry should exist');
    $this->assertEquals('ACT', $act->getCode());

    // Check BUR entry.
    $bur = $entity_storage->load('surface_nature_bur');
    $this->assertNotNull($bur, 'surface_nature_bur entry should exist');
    $this->assertEquals('BUR', $bur->getCode());
  }

  /**
   * Test that surface_qualification entries are installed.
   */
  public function testSurfaceQualificationEntries(): void {
    $entity_storage = $this->container->get('entity_type.manager')->getStorage('ps_dictionary_entry');

    // Check TOTAL entry.
    $total = $entity_storage->load('surface_qualification_total');
    $this->assertNotNull($total, 'surface_qualification_total entry should exist');
    $this->assertEquals('TOTAL', $total->getCode());

    // Check DISPO entry.
    $available = $entity_storage->load('surface_qualification_available');
    $this->assertNotNull($available, 'surface_qualification_available entry should exist');
    $this->assertEquals('DISPO', $available->getCode());

    // Check ETREF entry.
    $reference = $entity_storage->load('surface_qualification_reference');
    $this->assertNotNull($reference, 'surface_qualification_reference entry should exist');
    $this->assertEquals('ETREF', $reference->getCode());
  }

  /**
   * Test that dictionary manager options can be retrieved for new dictionaries.
   *
   * Note: This test verifies the options are retrievable. The actual validation
   * is tested via isValid() which is covered by the entity storage tests.
   */
  public function testDictionaryManagerOptionsRetrievable(): void {
    $dictionary_manager = $this->container->get('ps_dictionary.manager');

    // Get options for the new dictionaries we created.
    $nature_options = $dictionary_manager->getOptions('surface_nature');
    $qualification_options = $dictionary_manager->getOptions('surface_qualification');

    // Verify options are not empty (at least the entries we created exist).
    $this->assertNotEmpty($nature_options, 'surface_nature options should not be empty');
    $this->assertNotEmpty($qualification_options, 'surface_qualification options should not be empty');

    // Verify expected codes are present in options.
    $this->assertArrayHasKey('ACT', $nature_options, 'ACT code should exist in surface_nature');
    $this->assertArrayHasKey('BUR', $nature_options, 'BUR code should exist in surface_nature');

    $this->assertArrayHasKey('TOTAL', $qualification_options, 'TOTAL code should exist in surface_qualification');
    $this->assertArrayHasKey('DISPO', $qualification_options, 'DISPO code should exist in surface_qualification');
    $this->assertArrayHasKey('ETREF', $qualification_options, 'ETREF code should exist in surface_qualification');
  }

}
