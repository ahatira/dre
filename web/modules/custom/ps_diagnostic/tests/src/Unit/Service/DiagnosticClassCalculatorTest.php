<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_diagnostic\Unit\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Logger\LoggerChannelInterface;
use Drupal\ps_diagnostic\Entity\PsDiagnosticInterface;
use Drupal\ps_diagnostic\Service\DiagnosticClassCalculator;
use Drupal\Tests\UnitTestCase;

/**
 * Tests for DiagnosticClassCalculator service.
 *
 * @group ps_diagnostic
 * @coversDefaultClass \Drupal\ps_diagnostic\Service\DiagnosticClassCalculator
 */
class DiagnosticClassCalculatorTest extends UnitTestCase {

  /**
   * The class calculator service.
   */
  private DiagnosticClassCalculator $calculator;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $diagnostic = $this->createMock(PsDiagnosticInterface::class);
    $diagnostic->method('calculateClass')
      ->willReturnCallback(function (float $value): ?string {
        if ($value <= 70) {
          return 'A';
        }
        if ($value <= 110) {
          return 'B';
        }
        if ($value <= 180) {
          return 'C';
        }
        if ($value <= 250) {
          return 'D';
        }
        if ($value <= 330) {
          return 'E';
        }
        if ($value <= 420) {
          return 'F';
        }
        return 'G';
      });
    $diagnostic->method('getUnit')->willReturn('kWh/m²/year');
    $diagnostic->method('getClass')
      ->willReturnCallback(function (string $code): ?array {
        $classes = [
          'a' => ['label' => 'A', 'color' => '#00A651', 'range_max' => 70],
          'd' => ['label' => 'D', 'color' => '#F7941D', 'range_max' => 250],
        ];
        return $classes[$code] ?? NULL;
      });

    $storage = $this->createMock(EntityStorageInterface::class);
    $storage->method('load')->willReturn($diagnostic);

    $entityTypeManager = $this->createMock(EntityTypeManagerInterface::class);
    $entityTypeManager->method('getStorage')->willReturn($storage);

    $logger = $this->createMock(LoggerChannelInterface::class);
    $loggerFactory = $this->createMock(LoggerChannelFactoryInterface::class);
    $loggerFactory->method('get')->willReturn($logger);

    $this->calculator = new DiagnosticClassCalculator($entityTypeManager, $loggerFactory);
  }

  /**
   * Tests class calculation for DPE values.
   *
   * @covers ::calculateClass
   */
  public function testCalculateClassDpe(): void {
    $this->assertEquals('A', $this->calculator->calculateClass('dpe', 50.0));
    $this->assertEquals('D', $this->calculator->calculateClass('dpe', 200.0));
    $this->assertEquals('G', $this->calculator->calculateClass('dpe', 500.0));
  }

  /**
   * Tests class calculation with negative value.
   *
   * @covers ::calculateClass
   */
  public function testCalculateClassNegativeValue(): void {
    $this->assertNull($this->calculator->calculateClass('dpe', -10.0));
  }

  /**
   * Tests display info with special states.
   *
   * @covers ::getDisplayInfo
   */
  public function testGetDisplayInfoSpecialStates(): void {
    $result = $this->calculator->getDisplayInfo(['non_applicable' => TRUE]);
    $this->assertEquals('N/A', $result['display_text']);
    $this->assertTrue($result['is_special']);

    $result = $this->calculator->getDisplayInfo(['no_classification' => TRUE]);
    $this->assertEquals('?', $result['display_text']);
    $this->assertTrue($result['is_special']);
  }

  /**
   * Tests display info with class and color.
   *
   * @covers ::getDisplayInfo
   */
  public function testGetDisplayInfoWithClass(): void {
    $result = $this->calculator->getDisplayInfo([
      'type_id' => 'dpe',
      'value' => 200.0,
    ]);

    $this->assertEquals('D', $result['class']);
    $this->assertEquals('#F7941D', $result['color']);
    $this->assertEquals('kWh/m²/year', $result['unit']);
    $this->assertEquals('D', $result['display_text']);
    $this->assertFalse($result['is_special']);
  }

}
