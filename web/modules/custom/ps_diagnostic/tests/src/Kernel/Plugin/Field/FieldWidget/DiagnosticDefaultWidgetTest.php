<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_diagnostic\Kernel\Plugin\Field\FieldWidget;

use Drupal\Core\Form\FormState;
use Drupal\entity_test\Entity\EntityTest;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\KernelTests\KernelTestBase;
use Drupal\ps_diagnostic\Entity\PsDiagnostic;

/**
 * Tests the 'ps_diagnostic_default' field widget.
 *
 * @group ps_diagnostic
 * @coversDefaultClass \Drupal\ps_diagnostic\Plugin\Field\FieldWidget\DiagnosticDefaultWidget
 */
final class DiagnosticDefaultWidgetTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'field',
    'entity_test',
    'user',
    'system',
    'ps',
    'ps_dictionary',
    'ps_diagnostic',
  ];

  /**
   * The entity being tested.
   *
   * @var \Drupal\entity_test\Entity\EntityTest
   */
  private EntityTest $entity;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installEntitySchema('entity_test');
    $this->installEntitySchema('user');
    $this->installConfig(['ps_diagnostic']);

    // Create field storage and config.
    FieldStorageConfig::create([
      'field_name' => 'field_diagnostic_test',
      'entity_type' => 'entity_test',
      'type' => 'ps_diagnostic',
      'cardinality' => -1,
    ])->save();

    FieldConfig::create([
      'field_name' => 'field_diagnostic_test',
      'entity_type' => 'entity_test',
      'bundle' => 'entity_test',
      'label' => 'Diagnostic Test',
      'required' => FALSE,
    ])->save();

    // Create test diagnostic config entity (only if doesn't exist).
    $storage = $this->container->get('entity_type.manager')->getStorage('diagnostic');
    if (!$storage->load('dpe')) {
      PsDiagnostic::create([
        'id' => 'dpe',
        'label' => 'DPE',
        'unit' => 'kWh/m²/year',
        'icon' => 'energy',
        'classes' => [
          'a' => ['label' => 'A', 'color' => '#00A651', 'range_max' => 70],
          'b' => ['label' => 'B', 'color' => '#8DC63F', 'range_max' => 110],
          'c' => ['label' => 'C', 'color' => '#FFF200', 'range_max' => 180],
          'd' => ['label' => 'D', 'color' => '#FDBA12', 'range_max' => 250],
        ],
      ])->save();
    }

    $this->entity = EntityTest::create();
  }

  /**
   * Tests widget form element structure.
   *
   * @covers ::formElement
   */
  public function testFormElement(): void {
    /** @var \Drupal\Core\Entity\EntityDisplayRepositoryInterface $displayRepository */
    $displayRepository = $this->container->get('entity_display.repository');
    $formDisplay = $displayRepository->getFormDisplay('entity_test', 'entity_test');

    $formDisplay->setComponent('field_diagnostic_test', [
      'type' => 'ps_diagnostic_default',
      'weight' => 10,
    ]);

    $form = [];
    $formState = new FormState();
    $formDisplay->buildForm($this->entity, $form, $formState);

    // Verify field structure exists.
    $this->assertArrayHasKey('field_diagnostic_test', $form);
    $this->assertArrayHasKey('widget', $form['field_diagnostic_test']);

    $widget = $form['field_diagnostic_test']['widget'];

    // Verify delta 0 element.
    $this->assertArrayHasKey(0, $widget);
    $element = $widget[0];

    // Verify main containers.
    $this->assertArrayHasKey('_primary', $element);
    $this->assertArrayHasKey('_validity', $element);
    $this->assertArrayHasKey('_flags', $element);

    // Verify primary fields.
    $this->assertArrayHasKey('type_id', $element['_primary']);
    $this->assertArrayHasKey('value', $element['_primary']);
    $this->assertArrayHasKey('class', $element['_primary']);

    // Verify type_id is select with options.
    $this->assertSame('select', $element['_primary']['type_id']['#type']);
    $this->assertArrayHasKey('#options', $element['_primary']['type_id']);
    $this->assertArrayHasKey('dpe', $element['_primary']['type_id']['#options']);

    // Verify value is number field.
    $this->assertSame('number', $element['_primary']['value']['#type']);
    $this->assertSame(0.01, $element['_primary']['value']['#step']);

    // Verify class is textfield.
    $this->assertSame('textfield', $element['_primary']['class']['#type']);

    // Verify validity fields.
    $this->assertArrayHasKey('valid_from', $element['_validity']);
    $this->assertArrayHasKey('valid_to', $element['_validity']);
    $this->assertSame('date', $element['_validity']['valid_from']['#type']);
    $this->assertSame('date', $element['_validity']['valid_to']['#type']);

    // Verify flag fields.
    $this->assertArrayHasKey('no_classification', $element['_flags']);
    $this->assertArrayHasKey('non_applicable', $element['_flags']);
    $this->assertSame('checkbox', $element['_flags']['no_classification']['#type']);
    $this->assertSame('checkbox', $element['_flags']['non_applicable']['#type']);
  }

  /**
   * Tests widget form element with existing value.
   *
   * @covers ::formElement
   */
  public function testFormElementWithExistingValue(): void {
    $this->entity->field_diagnostic_test->appendItem([
      'type_id' => 'dpe',
      'value' => 150.0,
      'class' => 'C',
      'valid_from' => '2022-01-15',
      'valid_to' => '2032-01-15',
      'no_classification' => FALSE,
      'non_applicable' => FALSE,
    ]);

    /** @var \Drupal\Core\Entity\EntityDisplayRepositoryInterface $displayRepository */
    $displayRepository = $this->container->get('entity_display.repository');
    $formDisplay = $displayRepository->getFormDisplay('entity_test', 'entity_test');

    $formDisplay->setComponent('field_diagnostic_test', [
      'type' => 'ps_diagnostic_default',
    ]);

    $form = [];
    $formState = new FormState();
    $formDisplay->buildForm($this->entity, $form, $formState);

    $element = $form['field_diagnostic_test']['widget'][0];

    // Verify default values.
    $this->assertSame('dpe', $element['_primary']['type_id']['#default_value']);
    $this->assertSame(150.0, $element['_primary']['value']['#default_value']);
    $this->assertSame('C', $element['_primary']['class']['#default_value']);
    $this->assertSame('2022-01-15', $element['_validity']['valid_from']['#default_value']);
    $this->assertSame('2032-01-15', $element['_validity']['valid_to']['#default_value']);
    $this->assertFalse($element['_flags']['no_classification']['#default_value']);
    $this->assertFalse($element['_flags']['non_applicable']['#default_value']);
  }

  /**
   * Tests AJAX callbacks are defined.
   *
   * @covers ::formElement
   */
  public function testAjaxCallbacksDefined(): void {
    /** @var \Drupal\Core\Entity\EntityDisplayRepositoryInterface $displayRepository */
    $displayRepository = $this->container->get('entity_display.repository');
    $formDisplay = $displayRepository->getFormDisplay('entity_test', 'entity_test');

    $formDisplay->setComponent('field_diagnostic_test', [
      'type' => 'ps_diagnostic_default',
    ]);

    $form = [];
    $formState = new FormState();
    $formDisplay->buildForm($this->entity, $form, $formState);

    $element = $form['field_diagnostic_test']['widget'][0];

    // Verify type_id has AJAX.
    $this->assertArrayHasKey('#ajax', $element['_primary']['type_id']);
    $this->assertArrayHasKey('callback', $element['_primary']['type_id']['#ajax']);

    // Verify value has AJAX.
    $this->assertArrayHasKey('#ajax', $element['_primary']['value']);
    $this->assertArrayHasKey('callback', $element['_primary']['value']['#ajax']);
  }

  /**
   * Tests massageFormValues() method.
   *
   * @covers ::massageFormValues
   */
  public function testMassageFormValues(): void {
    /** @var \Drupal\Core\Field\WidgetPluginManager $widgetManager */
    $widgetManager = $this->container->get('plugin.manager.field.widget');

    $fieldConfig = FieldConfig::loadByName('entity_test', 'entity_test', 'field_diagnostic_test');

    /** @var \Drupal\ps_diagnostic\Plugin\Field\FieldWidget\DiagnosticDefaultWidget $widget */
    $widget = $widgetManager->createInstance('ps_diagnostic_default', [
      'field_definition' => $fieldConfig,
      'settings' => [],
      'third_party_settings' => [],
    ]);

    $formState = new FormState();
    $form = [];

    // Simulate form submission values with nested structure.
    $values = [
      0 => [
        '_primary' => [
          'type_id' => 'dpe',
          'value' => '175.5',
          'class' => '',
        ],
        '_validity' => [
          'valid_from' => '2022-06-15',
          'valid_to' => '2032-06-15',
        ],
        '_flags' => [
          'no_classification' => 0,
          'non_applicable' => 0,
        ],
      ],
    ];

    $massaged = $widget->massageFormValues($values, $form, $formState);

    // Verify flattened structure.
    $this->assertArrayHasKey(0, $massaged);
    $this->assertArrayHasKey('type_id', $massaged[0]);
    $this->assertArrayHasKey('value', $massaged[0]);
    $this->assertArrayHasKey('class', $massaged[0]);
    $this->assertArrayHasKey('valid_from', $massaged[0]);
    $this->assertArrayHasKey('valid_to', $massaged[0]);
    $this->assertArrayHasKey('no_classification', $massaged[0]);
    $this->assertArrayHasKey('non_applicable', $massaged[0]);

    // Verify values.
    $this->assertSame('dpe', $massaged[0]['type_id']);
    $this->assertSame(175.5, $massaged[0]['value']);
    $this->assertSame('2022-06-15', $massaged[0]['valid_from']);
    $this->assertSame('2032-06-15', $massaged[0]['valid_to']);
    $this->assertFalse($massaged[0]['no_classification']);
    $this->assertFalse($massaged[0]['non_applicable']);
  }

  /**
   * Tests massageFormValues() with auto-calculation.
   *
   * @covers ::massageFormValues
   */
  public function testMassageFormValuesWithAutoCalculation(): void {
    /** @var \Drupal\Core\Field\WidgetPluginManager $widgetManager */
    $widgetManager = $this->container->get('plugin.manager.field.widget');

    $fieldConfig = FieldConfig::loadByName('entity_test', 'entity_test', 'field_diagnostic_test');

    /** @var \Drupal\ps_diagnostic\Plugin\Field\FieldWidget\DiagnosticDefaultWidget $widget */
    $widget = $widgetManager->createInstance('ps_diagnostic_default', [
      'field_definition' => $fieldConfig,
      'settings' => [],
      'third_party_settings' => [],
    ]);

    $formState = new FormState();
    $form = [];

    // Simulate form submission with value but no class.
    $values = [
      0 => [
        '_primary' => [
          'type_id' => 'dpe',
          'value' => '150.0',
          'class' => '',
        ],
        '_validity' => [
          'valid_from' => '',
          'valid_to' => '',
        ],
        '_flags' => [
          'no_classification' => 0,
          'non_applicable' => 0,
        ],
      ],
    ];

    $massaged = $widget->massageFormValues($values, $form, $formState);

    // Class should be auto-calculated to 'C' (150.0 is in range for C).
    $this->assertSame('C', $massaged[0]['class']);
  }

  /**
   * Tests widget with required field.
   *
   * @covers ::formElement
   */
  public function testRequiredField(): void {
    // Update field config to be required.
    $fieldConfig = FieldConfig::loadByName('entity_test', 'entity_test', 'field_diagnostic_test');
    $fieldConfig->setRequired(TRUE);
    $fieldConfig->save();

    /** @var \Drupal\Core\Entity\EntityDisplayRepositoryInterface $displayRepository */
    $displayRepository = $this->container->get('entity_display.repository');
    $formDisplay = $displayRepository->getFormDisplay('entity_test', 'entity_test');

    $formDisplay->setComponent('field_diagnostic_test', [
      'type' => 'ps_diagnostic_default',
    ]);

    $form = [];
    $formState = new FormState();
    $formDisplay->buildForm($this->entity, $form, $formState);

    $element = $form['field_diagnostic_test']['widget'][0];

    // Verify type_id is marked as required.
    $this->assertTrue($element['_primary']['type_id']['#required']);
  }

  /**
   * Tests widget with multiple values.
   *
   * @covers ::formElement
   */
  public function testMultipleValues(): void {
    $this->entity->field_diagnostic_test->appendItem([
      'type_id' => 'dpe',
      'class' => 'A',
      'value' => 50.0,
    ]);
    $this->entity->field_diagnostic_test->appendItem([
      'type_id' => 'dpe',
      'class' => 'D',
      'value' => 200.0,
    ]);

    /** @var \Drupal\Core\Entity\EntityDisplayRepositoryInterface $displayRepository */
    $displayRepository = $this->container->get('entity_display.repository');
    $formDisplay = $displayRepository->getFormDisplay('entity_test', 'entity_test');

    $formDisplay->setComponent('field_diagnostic_test', [
      'type' => 'ps_diagnostic_default',
    ]);

    $form = [];
    $formState = new FormState();
    $formDisplay->buildForm($this->entity, $form, $formState);

    $widget = $form['field_diagnostic_test']['widget'];

    // Verify both deltas exist.
    $this->assertArrayHasKey(0, $widget);
    $this->assertArrayHasKey(1, $widget);

    // Verify different values.
    $this->assertSame('A', $widget[0]['_primary']['class']['#default_value']);
    $this->assertSame('D', $widget[1]['_primary']['class']['#default_value']);
  }

  /**
   * Tests getDiagnosticTypeOptions() private method indirectly.
   *
   * @covers ::formElement
   */
  public function testDiagnosticTypeOptions(): void {
    // Create additional diagnostic type (only if doesn't exist).
    $storage = $this->container->get('entity_type.manager')->getStorage('diagnostic');
    if (!$storage->load('ges')) {
      PsDiagnostic::create([
        'id' => 'ges',
        'label' => 'GES',
        'unit' => 'kg CO₂/m²/year',
        'classes' => [],
      ])->save();
    }

    /** @var \Drupal\Core\Entity\EntityDisplayRepositoryInterface $displayRepository */
    $displayRepository = $this->container->get('entity_display.repository');
    $formDisplay = $displayRepository->getFormDisplay('entity_test', 'entity_test');

    $formDisplay->setComponent('field_diagnostic_test', [
      'type' => 'ps_diagnostic_default',
    ]);

    $form = [];
    $formState = new FormState();
    $formDisplay->buildForm($this->entity, $form, $formState);

    $options = $form['field_diagnostic_test']['widget'][0]['_primary']['type_id']['#options'];

    // Verify empty option.
    $this->assertArrayHasKey('', $options);

    // Verify both types present.
    $this->assertArrayHasKey('dpe', $options);
    $this->assertArrayHasKey('ges', $options);
    $this->assertEquals('Energy consumption', (string) $options['dpe']);
    $this->assertEquals('Greenhouse gas emissions', (string) $options['ges']);
  }

}
