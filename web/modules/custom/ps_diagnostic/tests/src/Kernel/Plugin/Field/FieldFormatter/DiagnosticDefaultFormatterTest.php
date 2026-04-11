<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_diagnostic\Kernel\Plugin\Field\FieldFormatter;

use Drupal\entity_test\Entity\EntityTest;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\KernelTests\KernelTestBase;
use Drupal\ps_diagnostic\Entity\PsDiagnostic;

/**
 * Tests the 'ps_diagnostic_default' field formatter.
 *
 * @group ps_diagnostic
 * @coversDefaultClass \Drupal\ps_diagnostic\Plugin\Field\FieldFormatter\DiagnosticDefaultFormatter
 */
final class DiagnosticDefaultFormatterTest extends KernelTestBase {

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
        ],
      ])->save();
    }

    $this->entity = EntityTest::create();
  }

  /**
   * Tests default settings.
   *
   * @covers ::defaultSettings
   */
  public function testDefaultSettings(): void {
    /** @var \Drupal\Core\Entity\EntityDisplayRepositoryInterface $displayRepository */
    $displayRepository = $this->container->get('entity_display.repository');
    $viewDisplay = $displayRepository->getViewDisplay('entity_test', 'entity_test', 'default');

    $viewDisplay->setComponent('field_diagnostic_test', [
      'type' => 'ps_diagnostic_default',
    ]);

    /** @var \Drupal\ps_diagnostic\Plugin\Field\FieldFormatter\DiagnosticDefaultFormatter $formatter */
    $formatter = $viewDisplay->getRenderer('field_diagnostic_test');

    $this->assertTrue($formatter->getSetting('show_numeric_value'));
    $this->assertTrue($formatter->getSetting('show_type_label'));
    $this->assertFalse($formatter->getSetting('show_validity_dates'));
    $this->assertSame('horizontal', $formatter->getSetting('default_layout'));
    $this->assertTrue($formatter->getSetting('dim_empty'));
    $this->assertSame(30, $formatter->getSetting('dim_opacity'));
  }

  /**
   * Tests settings summary.
   *
   * @covers ::settingsSummary
   */
  public function testSettingsSummary(): void {
    /** @var \Drupal\Core\Entity\EntityDisplayRepositoryInterface $displayRepository */
    $displayRepository = $this->container->get('entity_display.repository');
    $viewDisplay = $displayRepository->getViewDisplay('entity_test', 'entity_test', 'default');

    $viewDisplay->setComponent('field_diagnostic_test', [
      'type' => 'ps_diagnostic_default',
      'settings' => [
        'show_numeric_value' => TRUE,
        'show_type_label' => FALSE,
        'show_validity_dates' => TRUE,
        'default_layout' => 'vertical',
        'dim_empty' => TRUE,
        'dim_opacity' => 50,
      ],
    ]);

    /** @var \Drupal\ps_diagnostic\Plugin\Field\FieldFormatter\DiagnosticDefaultFormatter $formatter */
    $formatter = $viewDisplay->getRenderer('field_diagnostic_test');
    $summary = $formatter->settingsSummary();

    $this->assertNotEmpty($summary);
    $this->assertGreaterThan(0, count($summary));

    // Verify summary contains expected information.
    $summaryString = implode(' ', array_map('strval', $summary));
    $this->assertStringContainsString('Numeric value: visible', $summaryString);
    $this->assertStringContainsString('Type: hidden', $summaryString);
    $this->assertStringContainsString('Dates: visible', $summaryString);
    $this->assertStringContainsString('Vertical', $summaryString);
    $this->assertStringContainsString('50%', $summaryString);
  }

  /**
   * Tests viewElements() with complete data.
   *
   * @covers ::viewElements
   */
  public function testViewElementsWithCompleteData(): void {
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
    $viewDisplay = $displayRepository->getViewDisplay('entity_test', 'entity_test', 'default');

    $viewDisplay->setComponent('field_diagnostic_test', [
      'type' => 'ps_diagnostic_default',
    ]);

    $build = $viewDisplay->build($this->entity);
    $this->assertArrayHasKey('field_diagnostic_test', $build);

    $elements = $build['field_diagnostic_test'];
    $this->assertArrayHasKey(0, $elements);

    $element = $elements[0];

    // Verify theme.
    $this->assertSame('ps_diagnostic_item', $element['#theme']);

    // Verify data passed to template.
    $this->assertSame('C', $element['#class_label']);
    // Type label might be from dictionary or fallback to ID.
    $this->assertNotEmpty($element['#type_label']);
    $this->assertSame('150.0', $element['#numeric_value']);
    $this->assertSame('2022-01-15', $element['#valid_from']);
    $this->assertSame('2032-01-15', $element['#valid_to']);
    $this->assertFalse($element['#no_classification']);
    $this->assertFalse($element['#non_applicable']);

    // Verify settings passed.
    $this->assertTrue($element['#show_type_label']);
    $this->assertTrue($element['#show_numeric_value']);
    $this->assertFalse($element['#show_validity_dates']);
    $this->assertSame('horizontal', $element['#default_layout']);

    // Verify dimming (should not dim with complete data).
    $this->assertFalse($element['#dim_diagnostic']);

    // Verify library attached.
    $this->assertArrayHasKey('#attached', $element);
    $this->assertArrayHasKey('library', $element['#attached']);
    $this->assertContains('ps_diagnostic/formatter', $element['#attached']['library']);
  }

  /**
   * Tests viewElements() with no_classification flag.
   *
   * @covers ::viewElements
   */
  public function testViewElementsWithNoClassification(): void {
    $this->entity->field_diagnostic_test->appendItem([
      'type_id' => 'dpe',
      'value' => 150.0,
      'no_classification' => TRUE,
      'non_applicable' => FALSE,
    ]);

    /** @var \Drupal\Core\Entity\EntityDisplayRepositoryInterface $displayRepository */
    $displayRepository = $this->container->get('entity_display.repository');
    $viewDisplay = $displayRepository->getViewDisplay('entity_test', 'entity_test', 'default');

    $viewDisplay->setComponent('field_diagnostic_test', [
      'type' => 'ps_diagnostic_default',
    ]);

    $build = $viewDisplay->build($this->entity);
    $element = $build['field_diagnostic_test'][0];

    // Class label should be "?".
    $this->assertSame('?', $element['#class_label']);
    $this->assertTrue($element['#no_classification']);
  }

  /**
   * Tests viewElements() with non_applicable flag.
   *
   * @covers ::viewElements
   */
  public function testViewElementsWithNonApplicable(): void {
    $this->entity->field_diagnostic_test->appendItem([
      'type_id' => 'dpe',
      'non_applicable' => TRUE,
    ]);

    /** @var \Drupal\Core\Entity\EntityDisplayRepositoryInterface $displayRepository */
    $displayRepository = $this->container->get('entity_display.repository');
    $viewDisplay = $displayRepository->getViewDisplay('entity_test', 'entity_test', 'default');

    $viewDisplay->setComponent('field_diagnostic_test', [
      'type' => 'ps_diagnostic_default',
    ]);

    $build = $viewDisplay->build($this->entity);
    $element = $build['field_diagnostic_test'][0];

    // Class label should be "N/A".
    $this->assertSame('N/A', $element['#class_label']);
    $this->assertTrue($element['#non_applicable']);
  }

  /**
   * Tests viewElements() with dimming enabled.
   *
   * @covers ::viewElements
   */
  public function testViewElementsWithDimming(): void {
    $this->entity->field_diagnostic_test->appendItem([
      'type_id' => 'dpe',
      // No value, no class → should be dimmed.
    ]);

    /** @var \Drupal\Core\Entity\EntityDisplayRepositoryInterface $displayRepository */
    $displayRepository = $this->container->get('entity_display.repository');
    $viewDisplay = $displayRepository->getViewDisplay('entity_test', 'entity_test', 'default');

    $viewDisplay->setComponent('field_diagnostic_test', [
      'type' => 'ps_diagnostic_default',
      'settings' => [
        'dim_empty' => TRUE,
        'dim_opacity' => 40,
      ],
    ]);

    $build = $viewDisplay->build($this->entity);
    $element = $build['field_diagnostic_test'][0];

    // Should be dimmed.
    $this->assertTrue($element['#dim_diagnostic']);
    $this->assertSame(40, $element['#dim_opacity']);
  }

  /**
   * Tests viewElements() with dimming disabled.
   *
   * @covers ::viewElements
   */
  public function testViewElementsWithDimmingDisabled(): void {
    $this->entity->field_diagnostic_test->appendItem([
      'type_id' => 'dpe',
      // No value, no class.
    ]);

    /** @var \Drupal\Core\Entity\EntityDisplayRepositoryInterface $displayRepository */
    $displayRepository = $this->container->get('entity_display.repository');
    $viewDisplay = $displayRepository->getViewDisplay('entity_test', 'entity_test', 'default');

    $viewDisplay->setComponent('field_diagnostic_test', [
      'type' => 'ps_diagnostic_default',
      'settings' => [
        'dim_empty' => FALSE,
      ],
    ]);

    $build = $viewDisplay->build($this->entity);
    $element = $build['field_diagnostic_test'][0];

    // Should NOT be dimmed.
    $this->assertFalse($element['#dim_diagnostic']);
  }

  /**
   * Tests viewElements() with different layouts.
   *
   * @covers ::viewElements
   * @dataProvider layoutProvider
   */
  public function testViewElementsWithDifferentLayouts(string $layout): void {
    $this->entity->field_diagnostic_test->appendItem([
      'type_id' => 'dpe',
      'class' => 'A',
      'value' => 50.0,
    ]);

    /** @var \Drupal\Core\Entity\EntityDisplayRepositoryInterface $displayRepository */
    $displayRepository = $this->container->get('entity_display.repository');
    $viewDisplay = $displayRepository->getViewDisplay('entity_test', 'entity_test', 'default');

    $viewDisplay->setComponent('field_diagnostic_test', [
      'type' => 'ps_diagnostic_default',
      'settings' => [
        'default_layout' => $layout,
      ],
    ]);

    $build = $viewDisplay->build($this->entity);
    $element = $build['field_diagnostic_test'][0];

    $this->assertSame($layout, $element['#default_layout']);
  }

  /**
   * Data provider for testViewElementsWithDifferentLayouts().
   *
   * @return array<string, array<string>>
   *   Layout options.
   */
  public static function layoutProvider(): array {
    return [
      'horizontal' => ['horizontal'],
      'vertical' => ['vertical'],
      'compact' => ['compact'],
    ];
  }

  /**
   * Tests viewElements() with visibility settings.
   *
   * @covers ::viewElements
   */
  public function testViewElementsWithVisibilitySettings(): void {
    $this->entity->field_diagnostic_test->appendItem([
      'type_id' => 'dpe',
      'value' => 150.0,
      'class' => 'C',
      'valid_from' => '2022-01-15',
      'valid_to' => '2032-01-15',
    ]);

    /** @var \Drupal\Core\Entity\EntityDisplayRepositoryInterface $displayRepository */
    $displayRepository = $this->container->get('entity_display.repository');
    $viewDisplay = $displayRepository->getViewDisplay('entity_test', 'entity_test', 'default');

    $viewDisplay->setComponent('field_diagnostic_test', [
      'type' => 'ps_diagnostic_default',
      'settings' => [
        'show_numeric_value' => FALSE,
        'show_type_label' => FALSE,
        'show_validity_dates' => TRUE,
      ],
    ]);

    $build = $viewDisplay->build($this->entity);
    $element = $build['field_diagnostic_test'][0];

    $this->assertFalse($element['#show_numeric_value']);
    $this->assertFalse($element['#show_type_label']);
    $this->assertTrue($element['#show_validity_dates']);
  }

  /**
   * Tests viewElements() with multiple items.
   *
   * @covers ::viewElements
   */
  public function testViewElementsWithMultipleItems(): void {
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
    $viewDisplay = $displayRepository->getViewDisplay('entity_test', 'entity_test', 'default');

    $viewDisplay->setComponent('field_diagnostic_test', [
      'type' => 'ps_diagnostic_default',
    ]);

    $build = $viewDisplay->build($this->entity);

    // Verify both deltas.
    $this->assertArrayHasKey(0, $build['field_diagnostic_test']);
    $this->assertArrayHasKey(1, $build['field_diagnostic_test']);

    $this->assertSame('A', $build['field_diagnostic_test'][0]['#class_label']);
    $this->assertSame('D', $build['field_diagnostic_test'][1]['#class_label']);
  }

  /**
   * Tests cache metadata.
   *
   * @covers ::viewElements
   */
  public function testCacheMetadata(): void {
    $this->entity->field_diagnostic_test->appendItem([
      'type_id' => 'dpe',
      'class' => 'A',
    ]);

    /** @var \Drupal\Core\Entity\EntityDisplayRepositoryInterface $displayRepository */
    $displayRepository = $this->container->get('entity_display.repository');
    $viewDisplay = $displayRepository->getViewDisplay('entity_test', 'entity_test', 'default');

    $viewDisplay->setComponent('field_diagnostic_test', [
      'type' => 'ps_diagnostic_default',
    ]);

    $build = $viewDisplay->build($this->entity);
    $element = $build['field_diagnostic_test'][0];

    $this->assertArrayHasKey('#cache', $element);
    $this->assertArrayHasKey('tags', $element['#cache']);
    $this->assertArrayHasKey('contexts', $element['#cache']);
    $this->assertContains('languages', $element['#cache']['contexts']);
  }

}
