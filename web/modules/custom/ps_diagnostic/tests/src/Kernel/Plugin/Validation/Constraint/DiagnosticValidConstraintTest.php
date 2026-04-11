<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_diagnostic\Kernel\Plugin\Validation\Constraint;

use Drupal\KernelTests\KernelTestBase;
use Drupal\node\Entity\Node;
use Drupal\node\Entity\NodeType;
use Drupal\ps_diagnostic\Entity\PsDiagnostic;

/**
 * Kernel tests for DiagnosticValid constraint integration.
 *
 * @group ps_diagnostic
 */
class DiagnosticValidConstraintTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'system',
    'user',
    'field',
    'node',
    'ps',
    'ps_dictionary',
    'ps_diagnostic',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installEntitySchema('user');
    $this->installEntitySchema('node');
    $this->installConfig(['field', 'node', 'ps_diagnostic']);
    $this->installSchema('system', ['sequences']);

    // Create a node type.
    $nodeType = NodeType::create([
      'type' => 'test_offer',
      'name' => 'Test Offer',
    ]);
    $nodeType->save();

    // Create diagnostic entities.
    $storage = $this->container->get('entity_type.manager')->getStorage('diagnostic');

    // Only create if doesn't exist (may come from installConfig).
    if (!$storage->load('dpe')) {
      $dpe = PsDiagnostic::create([
        'id' => 'dpe',
        'label' => 'Energy consumption',
        'unit' => 'kWh/m²/year',
        'classes' => [
          'a' => ['label' => 'A', 'color' => '#00A651', 'range_max' => 70],
          'b' => ['label' => 'B', 'color' => '#8DC63F', 'range_max' => 110],
          'c' => ['label' => 'C', 'color' => '#FFF200', 'range_max' => 180],
          'd' => ['label' => 'D', 'color' => '#F7941D', 'range_max' => 250],
          'e' => ['label' => 'E', 'color' => '#ED1C24', 'range_max' => 330],
          'f' => ['label' => 'F', 'color' => '#C1272D', 'range_max' => 420],
          'g' => ['label' => 'G', 'color' => '#A10D0D', 'range_max' => NULL],
        ],
      ]);
      $dpe->save();
    }

    // Create diagnostic field on node.
    $fieldStorage = \Drupal::entityTypeManager()
      ->getStorage('field_storage_config')
      ->create([
        'field_name' => 'field_diagnostic',
        'entity_type' => 'node',
        'type' => 'ps_diagnostic',
        'cardinality' => -1,
      ]);
    $fieldStorage->save();

    $fieldConfig = \Drupal::entityTypeManager()
      ->getStorage('field_config')
      ->create([
        'field_storage' => $fieldStorage,
        'bundle' => 'test_offer',
        'label' => 'Diagnostics',
        'required' => FALSE,
      ]);
    $fieldConfig->save();
  }

  /**
   * Tests validation passes when field is empty.
   */
  public function testEmptyFieldPassesValidation(): void {
    $node = Node::create([
      'type' => 'test_offer',
      'title' => 'Test Node',
      'field_diagnostic' => [],
    ]);

    $violations = $node->validate();
    $this->assertCount(0, $violations);
  }

  /**
   * Tests validation fails when type is selected but no value/class.
   */
  public function testTypeSelectedButNoDataFails(): void {
    $node = Node::create([
      'type' => 'test_offer',
      'title' => 'Test Node',
      'field_diagnostic' => [
        [
          'type_id' => 'dpe',
          'value' => NULL,
          'class' => '',
          'no_classification' => FALSE,
        ],
      ],
    ]);

    $violations = $node->validate();
    $this->assertGreaterThan(0, $violations->count());
    $this->assertStringContainsString(
      'you must provide either a numeric value or a class label',
      (string) $violations->get(0)->getMessage()
    );
  }

  /**
   * Tests validation passes when type is selected with no_classification flag.
   */
  public function testTypeSelectedWithNoClassificationFlagPasses(): void {
    $node = Node::create([
      'type' => 'test_offer',
      'title' => 'Test Node',
      'field_diagnostic' => [
        [
          'type_id' => 'dpe',
          'value' => NULL,
          'class' => '',
          'no_classification' => TRUE,
        ],
      ],
    ]);

    $violations = $node->validate();
    $this->assertCount(0, $violations);
  }

  /**
   * Tests validation passes when type and value are provided.
   */
  public function testTypeAndValuePasses(): void {
    $node = Node::create([
      'type' => 'test_offer',
      'title' => 'Test Node',
      'field_diagnostic' => [
        [
          'type_id' => 'dpe',
          'value' => 150.5,
          'class' => '',
        ],
      ],
    ]);

    $violations = $node->validate();
    $this->assertCount(0, $violations);
  }

  /**
   * Tests validation passes when type and class are provided.
   */
  public function testTypeAndClassPasses(): void {
    $node = Node::create([
      'type' => 'test_offer',
      'title' => 'Test Node',
      'field_diagnostic' => [
        [
          'type_id' => 'dpe',
          'value' => NULL,
          'class' => 'D',
        ],
      ],
    ]);

    $violations = $node->validate();
    $this->assertCount(0, $violations);
  }

  /**
   * Tests validation passes when value and class are coherent.
   */
  public function testCoherentValueAndClassPasses(): void {
    $node = Node::create([
      'type' => 'test_offer',
      'title' => 'Test Node',
      'field_diagnostic' => [
        [
          'type_id' => 'dpe',
          'value' => 200,
          'class' => 'D',
        ],
      ],
    ]);

    $violations = $node->validate();
    $this->assertCount(0, $violations);
  }

  /**
   * Tests validation fails when value and class are incoherent.
   */
  public function testIncoherentValueAndClassFails(): void {
    $node = Node::create([
      'type' => 'test_offer',
      'title' => 'Test Node',
      'field_diagnostic' => [
        [
          'type_id' => 'dpe',
          'value' => 200,
          'class' => 'A',
        ],
      ],
    ]);

    $violations = $node->validate();
    $this->assertGreaterThan(0, $violations->count());
    $this->assertStringContainsString(
      'does not match the calculated class',
      (string) $violations->get(0)->getMessage()
    );
  }

  /**
   * Tests multiple diagnostic items validation.
   */
  public function testMultipleDiagnosticItems(): void {
    $node = Node::create([
      'type' => 'test_offer',
      'title' => 'Test Node',
      'field_diagnostic' => [
        // Valid item.
        [
          'type_id' => 'dpe',
          'value' => 150,
          'class' => 'D',
        ],
        // Invalid item (missing data).
        [
          'type_id' => 'dpe',
          'value' => NULL,
          'class' => '',
          'no_classification' => FALSE,
        ],
      ],
    ]);

    $violations = $node->validate();
    $this->assertGreaterThan(0, $violations->count());
  }

}
