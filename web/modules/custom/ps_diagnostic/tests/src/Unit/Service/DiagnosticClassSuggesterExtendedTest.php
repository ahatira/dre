<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_diagnostic\Unit\Service;

use Drupal\ps_diagnostic\Service\DiagnosticClassSuggester;
use Drupal\ps_diagnostic\Service\DiagnosticClassSuggesterInterface;
use Drupal\Tests\UnitTestCase;

/**
 * Tests for DiagnosticClassSuggester with extended label formats.
 *
 * @group ps_diagnostic
 * @coversDefaultClass \Drupal\ps_diagnostic\Service\DiagnosticClassSuggester
 */
class DiagnosticClassSuggesterExtendedTest extends UnitTestCase {

  /**
   * The suggester service.
   */
  private DiagnosticClassSuggester $suggester;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->suggester = new DiagnosticClassSuggester();
  }

  /**
   * Tests that the class implements the interface.
   */
  public function testImplementsInterface(): void {
    $this->assertInstanceOf(DiagnosticClassSuggesterInterface::class, $this->suggester);
  }

  /**
   * Tests alphabetical progression (standard A-Z).
   *
   * @covers ::suggestNextClass
   * @covers ::getNextAlphabeticLabel
   * @dataProvider alphabeticalProgressionProvider
   */
  public function testAlphabeticalProgression(string $current, string $expected): void {
    $classes = [strtolower($current) => ['label' => $current, 'color' => '#000000', 'range_max' => NULL]];
    $suggestion = $this->suggester->suggestNextClass($classes);
    $this->assertEquals($expected, $suggestion['label']);
  }

  /**
   * Data provider for alphabetical progression.
   */
  public static function alphabeticalProgressionProvider(): array {
    return [
      'A to B' => ['A', 'B'],
      'B to C' => ['B', 'C'],
      'G to H' => ['G', 'H'],
      'Z to AA' => ['Z', 'AA'],
    ];
  }

  /**
   * Tests plus suffix progression.
   *
   * @covers ::suggestNextClass
   * @covers ::getNextAlphabeticLabel
   * @dataProvider plusSuffixProvider
   */
  public function testPlusSuffixProgression(string $current, string $expected): void {
    $classes = [strtolower($current) => ['label' => $current, 'color' => '#A10D0D', 'range_max' => NULL]];
    $suggestion = $this->suggester->suggestNextClass($classes);
    $this->assertEquals($expected, $suggestion['label'], "Failed: $current should suggest $expected");
  }

  /**
   * Data provider for plus suffix progression.
   */
  public static function plusSuffixProvider(): array {
    return [
      'G+ to G++' => ['G+', 'G++'],
      'G++ to G+++' => ['G++', 'G+++'],
      'G+++ to G++++' => ['G+++', 'G++++'],
      'A+ to A++' => ['A+', 'A++'],
    ];
  }

  /**
   * Tests minus suffix progression.
   *
   * @covers ::suggestNextClass
   * @covers ::getNextAlphabeticLabel
   * @dataProvider minusSuffixProvider
   */
  public function testMinusSuffixProgression(string $current, string $expected): void {
    $classes = [strtolower($current) => ['label' => $current, 'color' => '#00A651', 'range_max' => NULL]];
    $suggestion = $this->suggester->suggestNextClass($classes);
    $this->assertEquals($expected, $suggestion['label'], "Failed: $current should suggest $expected");
  }

  /**
   * Data provider for minus suffix progression.
   */
  public static function minusSuffixProvider(): array {
    return [
      'A- to A--' => ['A-', 'A--'],
      'A-- to A---' => ['A--', 'A---'],
      'B- to B--' => ['B-', 'B--'],
    ];
  }

  /**
   * Tests first suggestion when no classes exist.
   *
   * @covers ::suggestNextClass
   */
  public function testFirstSuggestion(): void {
    $suggestion = $this->suggester->suggestNextClass([]);
    $this->assertEquals('A', $suggestion['label']);
    $this->assertEquals('#00A651', strtoupper($suggestion['color']));
    $this->assertNull($suggestion['range_max']);
  }

  /**
   * Tests color suggestions for standard DPE classes.
   *
   * @covers ::suggestNextColor
   */
  public function testStandardColorProgression(): void {
    $classes = [
      'a' => ['label' => 'A', 'color' => '#00A651', 'range_max' => 70],
      'b' => ['label' => 'B', 'color' => '#8DC63F', 'range_max' => 110],
    ];
    $suggestion = $this->suggester->suggestNextClass($classes);
    // Should suggest C with standard yellow color.
    $this->assertEquals('C', $suggestion['label']);
    $this->assertEquals('#FFF200', strtoupper($suggestion['color']));
  }

}
