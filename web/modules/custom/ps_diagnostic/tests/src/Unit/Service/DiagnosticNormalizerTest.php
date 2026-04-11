<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_diagnostic\Unit\Service;

use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Logger\LoggerChannelInterface;
use Drupal\ps_diagnostic\Service\DiagnosticClassCalculatorInterface;
use Drupal\ps_diagnostic\Service\DiagnosticNormalizer;
use Drupal\Tests\UnitTestCase;

/**
 * Tests for DiagnosticNormalizer service.
 *
 * @group ps_diagnostic
 * @coversDefaultClass \Drupal\ps_diagnostic\Service\DiagnosticNormalizer
 */
final class DiagnosticNormalizerTest extends UnitTestCase {

  /**
   * The diagnostic normalizer under test.
   */
  private DiagnosticNormalizer $normalizer;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $classCalculator = $this->createMock(DiagnosticClassCalculatorInterface::class);
    $classCalculator->method('calculateClass')->willReturn('D');

    $loggerChannel = $this->createMock(LoggerChannelInterface::class);
    $loggerFactory = $this->createMock(LoggerChannelFactoryInterface::class);
    $loggerFactory->method('get')->willReturn($loggerChannel);

    $this->normalizer = new DiagnosticNormalizer(
      $classCalculator,
      $loggerFactory
    );
  }

  /**
   * @covers ::normalize
   */
  public function testNormalizeValidData(): void {
    $data = [
      'type_id' => 'dpe',
      'value' => 120.5,
      'class' => 'B',
      'valid_from' => '2024-01-01',
      'valid_to' => '2034-01-01',
      'no_classification' => FALSE,
      'non_applicable' => FALSE,
    ];

    $result = $this->normalizer->normalize($data);

    $this->assertIsArray($result);
    $this->assertEquals('dpe', $result['type_id']);
    $this->assertEquals(120.5, $result['value']);
    $this->assertEquals('B', $result['class']);
    $this->assertFalse($result['no_classification']);
    $this->assertFalse($result['non_applicable']);
  }

  /**
   * @covers ::normalize
   */
  public function testNormalizeAutoCalculateClass(): void {
    $data = [
      'type_id' => 'dpe',
      'value' => 200.0,
      'class' => NULL,
    ];

    $result = $this->normalizer->normalize($data);

    $this->assertEquals('D', $result['class']);
  }

  /**
   * @covers ::normalize
   */
  public function testNormalizeNegativeValueTruncated(): void {
    $data = ['value' => -50.0];

    $result = $this->normalizer->normalize($data);

    $this->assertNull($result['value']);
  }

  /**
   * @covers ::normalize
   */
  public function testNormalizeIncoherentDates(): void {
    $data = [
      'valid_from' => '2024-12-31',
      'valid_to' => '2024-01-01',
    ];

    $result = $this->normalizer->normalize($data);

    $this->assertNull($result['valid_to']);
  }

}
