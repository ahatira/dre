<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_surface\Kernel;

use Drupal\Core\Form\FormState;
use Drupal\KernelTests\KernelTestBase;
use Drupal\ps_division\Entity\Division;

/**
 * @coversDefaultClass \Drupal\ps_surface\Plugin\Field\FieldWidget\SurfaceDefaultWidget
 * @group ps_surface
 */
final class SurfaceWidgetSaveTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'system',
    'user',
    'field',
    'options',
    'text',
    'ps',
    'ps_dictionary',
    'ps_surface',
    'ps_division',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->installSchema('system', ['sequences']);
    $this->installEntitySchema('ps_division');
    $this->installConfig(['ps_dictionary', 'ps_surface', 'ps_division']);
    $this->seedDictionaries();
  }

  /**
   * Seeds minimal dictionary types and entries.
   */
  private function seedDictionaries(): void {
    $typeStorage = $this->container->get('entity_type.manager')->getStorage('ps_dictionary_type');
    $entryStorage = $this->container->get('entity_type.manager')->getStorage('ps_dictionary_entry');

    $types = [
      'surface_unit' => 'Surface unit',
      'surface_type' => 'Surface type',
      'surface_nature' => 'Surface nature',
      'surface_qualification' => 'Surface qualification',
      'floor' => 'Floor',
    ];

    foreach ($types as $id => $label) {
      if (!$typeStorage->load($id)) {
        $typeStorage->create([
          'id' => $id,
          'label' => $label,
        ])->save();
      }
    }

    $entries = [
      'surface_unit' => [
        ['code' => 'M2', 'label' => 'Square meter'],
      ],
      'surface_type' => [
        ['code' => 'USABLE', 'label' => 'Usable'],
      ],
      'surface_nature' => [
        ['code' => 'ACT', 'label' => 'Activity'],
      ],
      'surface_qualification' => [
        ['code' => 'TOTAL', 'label' => 'Total'],
      ],
      'floor' => [
        ['code' => 'PB', 'label' => 'Ground floor'],
      ],
    ];

    foreach ($entries as $type => $items) {
      foreach ($items as $item) {
        $id = $type . '_' . strtolower($item['code']);
        if (!$entryStorage->load($id)) {
          $entryStorage->create([
            'id' => $id,
            'dictionary_type' => $type,
            'code' => $item['code'],
            'label' => $item['label'],
          ])->save();
        }
      }
    }
  }

  /**
   * Tests that surfaces are properly saved when using SurfaceDefaultWidget.
   */
  public function testSurfaceDataIsSaved(): void {
    // Create a division with surfaces.
    $division = Division::create([
      'building_name' => 'Test Building',
      'floor' => 'PB',
      'type' => 'USABLE',
      'nature' => 'ACT',
    ]);

    // Add surface data directly (simulating massageFormValues).
    $division->surfaces->appendItem([
      'value' => 150.50,
      'unit' => 'M2',
      'type' => 'USABLE',
      'nature' => 'ACT',
      'qualification' => 'TOTAL',
    ]);

    // Save the division.
    $division->save();

    // Reload from database.
    $reloaded = Division::load($division->id());
    $this->assertNotNull($reloaded);

    // Verify surface data was saved.
    $this->assertTrue($reloaded->surfaces->count() > 0);
    $surface = $reloaded->surfaces->get(0);
    $this->assertNotNull($surface);
    $this->assertEquals(150.50, $surface->getValue());
    $this->assertEquals('M2', $surface->getUnit());
    $this->assertEquals('USABLE', $surface->getType());
    $this->assertEquals('ACT', $surface->getNature());
    $this->assertEquals('TOTAL', $surface->getQualification());
  }

  /**
   * Tests that massageFormValues properly flattens nested form structure.
   */
  public function testMassageFormValuesFlattens(): void {
    $widget = $this->container->get('plugin.manager.field.widget')
      ->getInstance([
        'field_definition' => Division::baseFieldDefinitions(
          $this->container->get('entity_type.manager')->getDefinition('ps_division')
        )['surfaces'],
        'form_mode' => 'default',
        'prepare' => TRUE,
      ]);

    // Verify the widget is the SurfaceDefaultWidget.
    $this->assertEquals('ps_surface_default', $widget->getPluginId());

    // Simulate form values as they come from the widget with all fields.
    $form_values = [
      [
        '_primary' => [
          'value' => 100.00,
          'unit' => 'M2',
          'type' => 'USABLE',
        ],
        '_optional' => [
          'nature' => 'ACT',
          'qualification' => 'TOTAL',
        ],
      ],
    ];

    // Create a FormStateInterface for the test.
    $form_state = new FormState();

    // Test that massageFormValues flattens the structure.
    $flattened = $widget->massageFormValues($form_values, [], $form_state);

    $this->assertEquals(1, count($flattened));
    $this->assertEquals(100.00, $flattened[0]['value']);
    $this->assertEquals('M2', $flattened[0]['unit']);
    $this->assertEquals('USABLE', $flattened[0]['type']);
    $this->assertEquals('ACT', $flattened[0]['nature']);
    $this->assertEquals('TOTAL', $flattened[0]['qualification']);
  }

  /**
   * Tests that massageFormValues handles empty values correctly.
   */
  public function testMassageFormValuesHandlesEmptyValues(): void {
    $widget = $this->container->get('plugin.manager.field.widget')
      ->getInstance([
        'field_definition' => Division::baseFieldDefinitions(
          $this->container->get('entity_type.manager')->getDefinition('ps_division')
        )['surfaces'],
        'form_mode' => 'default',
        'prepare' => TRUE,
      ]);

    // Simulate form values with empty optional fields (-- None --).
    $form_values = [
      [
        '_primary' => [
          'value' => 150.50,
          'unit' => 'M2',
          'type' => 'USABLE',
        ],
        '_optional' => [
        // Empty string from select "-- None --".
          'nature' => '',
        // Empty string from select "-- None --".
          'qualification' => '',
        ],
      ],
    ];

    $form_state = new FormState();
    $flattened = $widget->massageFormValues($form_values, [], $form_state);

    // Empty strings should be converted to NULL.
    $this->assertEquals(1, count($flattened));
    $this->assertEquals(150.50, $flattened[0]['value']);
    $this->assertEquals('M2', $flattened[0]['unit']);
    $this->assertEquals('USABLE', $flattened[0]['type']);
    $this->assertNull($flattened[0]['nature'], 'Empty nature should be NULL');
    $this->assertNull($flattened[0]['qualification'], 'Empty qualification should be NULL');
  }

}
