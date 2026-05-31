<?php

namespace Drupal\Tests\ps_feature\Kernel\Plugin\Field\FieldWidget;

use Drupal\Core\Form\FormState;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\KernelTests\KernelTestBase;
use Drupal\node\Entity\NodeType;
use Drupal\ps_feature\Entity\FeatureDefinition;
use Drupal\ps_feature\Plugin\Field\FieldWidget\FeatureBuilderWidget;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;

/**
 * Kernel integration test for FeatureBuilderWidget::extractFormValues().
 *
 * Boots the full Drupal entity system, creates real fb_feature_definition
 * config entities, and verifies that extractFormValues() correctly persists
 * values to a FieldItemList.
 */
#[CoversClass(FeatureBuilderWidget::class)]
#[Group('ps_feature')]
#[RunTestsInSeparateProcesses]
class FeatureBuilderWidgetExtractionTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'system',
    'user',
    'node',
    'field',
    'text',
    'ps_feature',
  ];

  /**
   * The widget under test.
   *
   * @var \Drupal\ps_feature\Plugin\Field\FieldWidget\FeatureBuilderWidget
   */
  protected FeatureBuilderWidget $widget;

  /**
   * A real field item list backed by a node entity.
   *
   * @var \Drupal\Core\Field\FieldItemListInterface
   */
  protected $items;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installEntitySchema('node');
    $this->installConfig(['system', 'field', 'node', 'ps_feature']);

    // Create a minimal content type.
    NodeType::create(['type' => 'test_offer', 'name' => 'Test Offer'])->save();

    // Create the field storage for the feature field.
    FieldStorageConfig::create([
      'field_name' => 'field_features',
      'entity_type' => 'node',
      'type' => 'feature',
      'cardinality' => -1,
    ])->save();

    // Attach field to the test content type.
    FieldConfig::create([
      'field_name' => 'field_features',
      'entity_type' => 'node',
      'bundle' => 'test_offer',
      'label' => 'Features',
    ])->save();

    // Create two enabled and one disabled FeatureDefinition config entities.
    FeatureDefinition::create([
      'id' => 'surface_totale',
      'label' => 'Surface totale',
      'type_driver' => 'numeric',
      'status' => TRUE,
      'payload_defaults' => ['unit' => 'm²'],
    ])->save();

    FeatureDefinition::create([
      'id' => 'parking_inclus',
      'label' => 'Parking inclus',
      'type_driver' => 'flag',
      'status' => TRUE,
      'payload_defaults' => [],
    ])->save();

    FeatureDefinition::create([
      'id' => 'disabled_feature',
      'label' => 'Disabled feature',
      'type_driver' => 'numeric',
      'status' => FALSE,
      'payload_defaults' => [],
    ])->save();

    // Instantiate the widget via the plugin manager.
    /** @var \Drupal\Core\Field\WidgetPluginManager $widgetManager */
    $widgetManager = $this->container->get('plugin.manager.field.widget');
    $field_definition = FieldConfig::loadByName('node', 'test_offer', 'field_features');

    $this->widget = $widgetManager->getInstance([
      'field_definition' => $field_definition,
      'form_mode' => 'default',
      'prepare' => TRUE,
      'configuration' => ['type' => 'feature_builder', 'settings' => [], 'third_party_settings' => []],
    ]);

    // Build a node with the field for later use.
    $node = $this->container->get('entity_type.manager')
      ->getStorage('node')
      ->create(['type' => 'test_offer', 'title' => 'Test']);
    $this->items = $node->get('field_features');
  }

  // ---------------------------------------------------------------------------
  // Happy path: valid JSON persists features
  // ---------------------------------------------------------------------------

  /**
   * Valid features are written to the field item list.
   */
  public function testValidFeaturesAreSaved(): void {
    $state_json = json_encode([
      'features' => [
        ['id' => 'surface_totale', 'payload' => ['value' => 85.5, 'unit' => 'm²']],
        ['id' => 'parking_inclus', 'payload' => ['present' => TRUE]],
      ],
    ]);

    $form_state = new FormState();
    $form_state->setUserInput(['fb_state_field_features' => $state_json]);

    $this->widget->extractFormValues($this->items, [], $form_state);

    $values = $this->items->getValue();
    $this->assertCount(2, $values);

    $ids = array_column($values, 'feature_definition_id');
    $this->assertContains('surface_totale', $ids);
    $this->assertContains('parking_inclus', $ids);
  }

  /**
   * The payload is stored as a JSON string in the field column.
   */
  public function testPayloadIsStoredAsJson(): void {
    $payload = ['value' => 85.5, 'unit' => 'm²'];
    $state_json = json_encode([
      'features' => [
        ['id' => 'surface_totale', 'payload' => $payload],
      ],
    ]);

    $form_state = new FormState();
    $form_state->setUserInput(['fb_state_field_features' => $state_json]);

    $this->widget->extractFormValues($this->items, [], $form_state);

    $values = $this->items->getValue();
    $stored_payload = json_decode($values[0]['payload'], TRUE);
    $this->assertSame(85.5, $stored_payload['value']);
    $this->assertSame('m²', $stored_payload['unit']);
  }

  // ---------------------------------------------------------------------------
  // Security: disabled feature IDs are rejected
  // ---------------------------------------------------------------------------

  /**
   * A disabled feature definition ID must be filtered out.
   */
  public function testDisabledFeatureDefinitionIsRejected(): void {
    $state_json = json_encode([
      'features' => [
        ['id' => 'disabled_feature', 'payload' => ['value' => 99]],
        ['id' => 'surface_totale', 'payload' => ['value' => 50, 'unit' => 'm²']],
      ],
    ]);

    $form_state = new FormState();
    $form_state->setUserInput(['fb_state_field_features' => $state_json]);

    $this->widget->extractFormValues($this->items, [], $form_state);

    $values = $this->items->getValue();
    $ids = array_column($values, 'feature_definition_id');
    $this->assertNotContains('disabled_feature', $ids);
    $this->assertContains('surface_totale', $ids);
  }

  /**
   * An entirely unknown feature definition ID must be filtered out.
   */
  public function testUnknownFeatureDefinitionIsRejected(): void {
    $state_json = json_encode([
      'features' => [
        ['id' => 'nonexistent_id', 'payload' => []],
      ],
    ]);

    $form_state = new FormState();
    $form_state->setUserInput(['fb_state_field_features' => $state_json]);

    $this->widget->extractFormValues($this->items, [], $form_state);

    $this->assertCount(0, $this->items->getValue());
  }

  // ---------------------------------------------------------------------------
  // Security: oversized payloads are rejected entirely
  // ---------------------------------------------------------------------------

  /**
   * A state JSON exceeding 512KB must cause no values to be written.
   */
  public function testOversizedPayloadIsRejected(): void {
    // Guarantee we exceed the 524288 byte limit.
    $oversized = str_repeat('x', 524289);
    $form_state = new FormState();
    $form_state->setUserInput(['fb_state_field_features' => $oversized]);

    $this->widget->extractFormValues($this->items, [], $form_state);

    $this->assertCount(0, $this->items->getValue());
  }

  // ---------------------------------------------------------------------------
  // Structural guards
  // ---------------------------------------------------------------------------

  /**
   * Invalid JSON must produce no written values.
   */
  public function testInvalidJsonProducesNoValues(): void {
    $form_state = new FormState();
    $form_state->setUserInput(['fb_state_field_features' => 'not-valid-json{']);

    $this->widget->extractFormValues($this->items, [], $form_state);

    $this->assertCount(0, $this->items->getValue());
  }

  /**
   * Absent input key (no POST for the field) must produce no values.
   */
  public function testAbsentInputKeyProducesNoValues(): void {
    $form_state = new FormState();
    $form_state->setUserInput([]);

    $this->widget->extractFormValues($this->items, [], $form_state);

    $this->assertCount(0, $this->items->getValue());
  }

  /**
   * Empty features array must result in no field values.
   */
  public function testEmptyFeaturesArrayClearsField(): void {
    // Pre-populate the field.
    $this->items->setValue([
      ['feature_definition_id' => 'surface_totale', 'payload' => '{}'],
    ]);

    $state_json = json_encode(['features' => []]);
    $form_state = new FormState();
    $form_state->setUserInput(['fb_state_field_features' => $state_json]);

    $this->widget->extractFormValues($this->items, [], $form_state);

    $this->assertCount(0, $this->items->getValue());
  }

}
