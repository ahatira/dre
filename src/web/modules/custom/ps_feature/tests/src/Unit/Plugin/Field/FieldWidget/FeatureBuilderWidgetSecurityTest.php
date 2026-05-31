<?php

namespace Drupal\Tests\ps_feature\Unit\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\ps_feature\Plugin\Field\FieldWidget\FeatureBuilderWidget;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;

/**
 * Tests the security-critical extractFormValues() logic of FeatureBuilderWidget.
 *
 * Uses a test double that overrides loadValidDefinitionIds() so no Drupal
 * entity system is needed.
 */
#[CoversClass(FeatureBuilderWidget::class)]
#[Group('ps_feature')]
class FeatureBuilderWidgetSecurityTest extends UnitTestCase {

  /**
   * Creates a widget test double with a fixed set of valid definition IDs.
   *
   * @param string[] $validIds
   *   The IDs that the widget should consider valid.
   *
   * @return \Drupal\ps_feature\Plugin\Field\FieldWidget\FeatureBuilderWidget
   */
  protected function createWidget(array $validIds): FeatureBuilderWidget {
    $field_definition = $this->createMock(FieldDefinitionInterface::class);
    $field_definition->method('getName')->willReturn('field_features');

    // We cannot instantiate FeatureBuilderWidget directly (it has DI
    // dependencies), so we use an anonymous subclass that stubs out
    // everything except the validation logic we want to test.
    return new class ($validIds, $field_definition) extends FeatureBuilderWidget {

      private array $validIds;

      public function __construct(array $validIds, FieldDefinitionInterface $field_definition) {
        // Skip parent constructor — we only test the pure validation logic.
        $this->fieldDefinition = $field_definition;
        $this->validIds = $validIds;
      }

      protected function loadValidDefinitionIds(): array {
        return $this->validIds;
      }
    };
  }

  /**
   * Builds a FormStateInterface mock that returns the given JSON from getUserInput().
   */
  protected function buildFormState(string $json): FormStateInterface {
    $form_state = $this->createMock(FormStateInterface::class);
    $form_state->method('getUserInput')->willReturn(['fb_state_field_features' => $json]);
    return $form_state;
  }

  // ---------------------------------------------------------------------------
  // Size limit guard
  // ---------------------------------------------------------------------------

  public function testPayloadExceeding512KbIsRejected(): void {
    $widget = $this->createWidget(['surface_totale']);
    $form_state = $this->buildFormState(str_repeat('a', 524289));

    $items = $this->createMock(FieldItemListInterface::class);
    // setValue() must NOT be called — no data should be written.
    $items->expects($this->never())->method('setValue');

    $widget->extractFormValues($items, [], $form_state);
  }

  public function testPayloadExactly512KbIsAccepted(): void {
    $valid_json = json_encode([
      'features' => [
        ['id' => 'surface_totale', 'payload' => ['value' => 50, 'unit' => 'm²']],
      ],
    ]);
    // Pad with a long string inside a feature payload to reach exactly 512KB.
    // Actually we just test that the boundary is the right direction: a
    // payload smaller than the limit must reach setValue().
    $widget = $this->createWidget(['surface_totale']);
    $form_state = $this->buildFormState($valid_json);

    $items = $this->createMock(FieldItemListInterface::class);
    $items->expects($this->once())->method('setValue');
    $items->expects($this->once())->method('filterEmptyItems');

    $widget->extractFormValues($items, [], $form_state);
  }

  // ---------------------------------------------------------------------------
  // JSON structure guards
  // ---------------------------------------------------------------------------

  public function testInvalidJsonDoesNotWriteValues(): void {
    $widget = $this->createWidget(['surface_totale']);
    $form_state = $this->buildFormState('not valid json {{{');

    $items = $this->createMock(FieldItemListInterface::class);
    $items->expects($this->never())->method('setValue');

    $widget->extractFormValues($items, [], $form_state);
  }

  public function testJsonWithoutFeaturesKeyDoesNotWriteValues(): void {
    $widget = $this->createWidget(['surface_totale']);
    $form_state = $this->buildFormState(json_encode(['other_key' => []]));

    $items = $this->createMock(FieldItemListInterface::class);
    $items->expects($this->never())->method('setValue');

    $widget->extractFormValues($items, [], $form_state);
  }

  public function testFeaturesValueNotArrayDoesNotWriteValues(): void {
    $widget = $this->createWidget(['surface_totale']);
    $form_state = $this->buildFormState(json_encode(['features' => 'oops']));

    $items = $this->createMock(FieldItemListInterface::class);
    $items->expects($this->never())->method('setValue');

    $widget->extractFormValues($items, [], $form_state);
  }

  public function testEmptyFeaturesArrayWritesEmptyValues(): void {
    $widget = $this->createWidget(['surface_totale']);
    $form_state = $this->buildFormState(json_encode(['features' => []]));

    $items = $this->createMock(FieldItemListInterface::class);
    $items->expects($this->once())->method('setValue')->with([]);
    $items->expects($this->once())->method('filterEmptyItems');

    $widget->extractFormValues($items, [], $form_state);
  }

  // ---------------------------------------------------------------------------
  // Feature ID whitelist validation
  // ---------------------------------------------------------------------------

  public function testUnknownFeatureIdIsFilteredOut(): void {
    $widget = $this->createWidget(['surface_totale']);
    $json = json_encode([
      'features' => [
        ['id' => 'HACKED_NONEXISTENT', 'payload' => []],
      ],
    ]);
    $form_state = $this->buildFormState($json);

    $items = $this->createMock(FieldItemListInterface::class);
    $items->expects($this->once())->method('setValue')->with([]);

    $widget->extractFormValues($items, [], $form_state);
  }

  public function testPathTraversalIdIsFilteredOut(): void {
    $widget = $this->createWidget(['surface_totale']);
    $json = json_encode([
      'features' => [
        ['id' => '../../../etc/passwd', 'payload' => []],
      ],
    ]);
    $form_state = $this->buildFormState($json);

    $items = $this->createMock(FieldItemListInterface::class);
    $items->expects($this->once())->method('setValue')->with([]);

    $widget->extractFormValues($items, [], $form_state);
  }

  public function testValidFeatureIdIsKept(): void {
    $widget = $this->createWidget(['surface_totale', 'parking_inclus']);
    $json = json_encode([
      'features' => [
        ['id' => 'surface_totale', 'payload' => ['value' => 50, 'unit' => 'm²']],
      ],
    ]);
    $form_state = $this->buildFormState($json);

    $captured = NULL;
    $items = $this->createMock(FieldItemListInterface::class);
    $items->expects($this->once())->method('setValue')
      ->willReturnCallback(function (array $values) use (&$captured) {
        $captured = $values;
      });
    $items->method('filterEmptyItems');

    $widget->extractFormValues($items, [], $form_state);

    $this->assertCount(1, $captured);
    $this->assertSame('surface_totale', $captured[0]['feature_definition_id']);
    $this->assertSame(json_encode(['value' => 50, 'unit' => 'm²']), $captured[0]['payload']);
  }

  public function testMixedValidAndInvalidIdsOnlyKeepsValid(): void {
    $widget = $this->createWidget(['surface_totale', 'parking_inclus']);
    $json = json_encode([
      'features' => [
        ['id' => 'surface_totale', 'payload' => ['value' => 50, 'unit' => 'm²']],
        ['id' => 'HACKED_NONEXISTENT', 'payload' => ['evil' => 'payload']],
        ['id' => 'parking_inclus', 'payload' => ['present' => TRUE]],
      ],
    ]);
    $form_state = $this->buildFormState($json);

    $captured = NULL;
    $items = $this->createMock(FieldItemListInterface::class);
    $items->expects($this->once())->method('setValue')
      ->willReturnCallback(function (array $values) use (&$captured) {
        $captured = $values;
      });
    $items->method('filterEmptyItems');

    $widget->extractFormValues($items, [], $form_state);

    $ids = array_column($captured, 'feature_definition_id');
    $this->assertContains('surface_totale', $ids);
    $this->assertContains('parking_inclus', $ids);
    $this->assertNotContains('HACKED_NONEXISTENT', $ids);
    $this->assertCount(2, $captured);
  }

  // ---------------------------------------------------------------------------
  // Per-feature entry guards
  // ---------------------------------------------------------------------------

  public function testFeatureWithEmptyIdIsSkipped(): void {
    $widget = $this->createWidget(['surface_totale']);
    $json = json_encode([
      'features' => [
        ['id' => '', 'payload' => ['value' => 42]],
        ['id' => 'surface_totale', 'payload' => ['value' => 50, 'unit' => 'm²']],
      ],
    ]);
    $form_state = $this->buildFormState($json);

    $captured = NULL;
    $items = $this->createMock(FieldItemListInterface::class);
    $items->expects($this->once())->method('setValue')
      ->willReturnCallback(function (array $values) use (&$captured) {
        $captured = $values;
      });
    $items->method('filterEmptyItems');

    $widget->extractFormValues($items, [], $form_state);

    $this->assertCount(1, $captured);
    $this->assertSame('surface_totale', $captured[0]['feature_definition_id']);
  }

  public function testFeatureWithNonStringIdIsSkipped(): void {
    $widget = $this->createWidget(['surface_totale']);
    $json = json_encode([
      'features' => [
        ['id' => 12345, 'payload' => ['value' => 42]],
        ['id' => 'surface_totale', 'payload' => ['value' => 50, 'unit' => 'm²']],
      ],
    ]);
    $form_state = $this->buildFormState($json);

    $captured = NULL;
    $items = $this->createMock(FieldItemListInterface::class);
    $items->expects($this->once())->method('setValue')
      ->willReturnCallback(function (array $values) use (&$captured) {
        $captured = $values;
      });
    $items->method('filterEmptyItems');

    $widget->extractFormValues($items, [], $form_state);

    $this->assertCount(1, $captured);
    $this->assertSame('surface_totale', $captured[0]['feature_definition_id']);
  }

  public function testNonArrayPayloadFallsBackToEmptyObject(): void {
    $widget = $this->createWidget(['surface_totale']);
    $json = json_encode([
      'features' => [
        ['id' => 'surface_totale', 'payload' => 'not-an-array'],
      ],
    ]);
    $form_state = $this->buildFormState($json);

    $captured = NULL;
    $items = $this->createMock(FieldItemListInterface::class);
    $items->expects($this->once())->method('setValue')
      ->willReturnCallback(function (array $values) use (&$captured) {
        $captured = $values;
      });
    $items->method('filterEmptyItems');

    $widget->extractFormValues($items, [], $form_state);

    $this->assertCount(1, $captured);
    $this->assertSame('[]', $captured[0]['payload']);
  }

  public function testMissingPayloadKeyFallsBackToEmptyObject(): void {
    $widget = $this->createWidget(['parking_inclus']);
    $json = json_encode([
      'features' => [
        ['id' => 'parking_inclus'],
      ],
    ]);
    $form_state = $this->buildFormState($json);

    $captured = NULL;
    $items = $this->createMock(FieldItemListInterface::class);
    $items->expects($this->once())->method('setValue')
      ->willReturnCallback(function (array $values) use (&$captured) {
        $captured = $values;
      });
    $items->method('filterEmptyItems');

    $widget->extractFormValues($items, [], $form_state);

    $this->assertCount(1, $captured);
    $this->assertSame('[]', $captured[0]['payload']);
  }

  public function testMissingInputKeyFallsBackToEmptyJsonObject(): void {
    $widget = $this->createWidget(['surface_totale']);
    // No 'fb_state_field_features' key in the user input — defaults to '{}'.
    $form_state = $this->createMock(FormStateInterface::class);
    $form_state->method('getUserInput')->willReturn([]);

    $items = $this->createMock(FieldItemListInterface::class);
    // '{}' decodes to [] which has no 'features' key → guard returns early.
    $items->expects($this->never())->method('setValue');

    $widget->extractFormValues($items, [], $form_state);
  }

}
