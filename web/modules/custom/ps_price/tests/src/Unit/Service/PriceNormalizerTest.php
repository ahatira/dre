<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_price\Unit\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\ps_dictionary\Service\DictionaryManagerInterface;
use Drupal\ps_price\Plugin\Field\FieldType\PriceItem;
use Drupal\ps_price\Service\PriceNormalizer;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Tests for PriceNormalizer service.
 *
 * @coversDefaultClass \Drupal\ps_price\Service\PriceNormalizer
 * @group ps_price
 */
final class PriceNormalizerTest extends UnitTestCase {

  /**
   * The price normalizer under test.
   */
  private PriceNormalizer $normalizer;

  /**
   * Mock dictionary manager.
   *
   * @var \Drupal\ps_dictionary\Service\DictionaryManagerInterface|\PHPUnit\Framework\MockObject\MockObject
   */
  private DictionaryManagerInterface|MockObject $dictionaryManager;

  /**
   * Mock config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface|\PHPUnit\Framework\MockObject\MockObject
   */
  private ConfigFactoryInterface|MockObject $configFactory;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    // Mock dictionary manager.
    $this->dictionaryManager = $this->createMock(DictionaryManagerInterface::class);
    $this->dictionaryManager->method('getMetadata')
      ->willReturnMap([
        ['price_period', 'MEN', ['icon' => 'calendar', 'multiplier' => 12]],
        ['price_period', 'TRI', ['icon' => 'calendar', 'multiplier' => 4]],
        ['price_period', 'SEM', ['icon' => 'calendar', 'multiplier' => 52]],
        ['price_period', 'ANN', ['icon' => 'calendar', 'multiplier' => 1]],
      ]);

    // Mock config factory.
    $config = $this->createMock(ImmutableConfig::class);
    $config->method('get')->willReturnMap([
      ['normalize_on_zero_surface', 'null'],
      ['reference_period', 'ANN'],
    ]);
    $this->configFactory = $this->createMock(ConfigFactoryInterface::class);
    $this->configFactory->method('get')->willReturn($config);

    $this->normalizer = new PriceNormalizer($this->dictionaryManager, $this->configFactory);
  }

  /**
   * Tests normalizing price per m² (already in reference unit).
   *
   * @covers ::normalize
   */
  public function testNormalizePricePerSquareMeter(): void {
    $item = $this->createMockPriceItem([
      'amount' => 250.00,
      'unit_code' => 'SUR',
      'period_code' => 'ANN',
    ]);

    $result = $this->normalizer->normalize($item, 100.0);
    $this->assertEquals(250.00, $result);
  }

  /**
   * Tests normalizing global price (needs surface division).
   *
   * @covers ::normalize
   */
  public function testNormalizeGlobalPrice(): void {
    $item = $this->createMockPriceItem([
      'amount' => 50000.00,
      'unit_code' => 'GLO',
      'period_code' => 'ANN',
    ]);

    $result = $this->normalizer->normalize($item, 200.0);
    // 50000 / 200 = 250
    $this->assertEquals(250.00, $result);
  }

  /**
   * Tests normalizing monthly price to yearly.
   *
   * @covers ::normalize
   */
  public function testNormalizeMonthlyPriceToYearly(): void {
    $item = $this->createMockPriceItem([
      'amount' => 1000.00,
      'unit_code' => 'SUR',
      'period_code' => 'MEN',
    ]);

    $result = $this->normalizer->normalize($item, 1.0);
    // 1000 * 12
    $this->assertEquals(12000.00, $result);
  }

  /**
   * Tests normalizing quarterly price to yearly.
   *
   * @covers ::normalize
   */
  public function testNormalizeQuarterlyPriceToYearly(): void {
    $item = $this->createMockPriceItem([
      'amount' => 3000.00,
      'unit_code' => 'SUR',
      'period_code' => 'TRI',
    ]);

    $result = $this->normalizer->normalize($item, 1.0);
    // 3000 * 4
    $this->assertEquals(12000.00, $result);
  }

  /**
   * Tests normalizing weekly price to yearly.
   *
   * @covers ::normalize
   */
  public function testNormalizeWeeklyPriceToYearly(): void {
    $item = $this->createMockPriceItem([
      'amount' => 230.77,
      'unit_code' => 'SUR',
      'period_code' => 'SEM',
    ]);

    $result = $this->normalizer->normalize($item, 1.0);
    // 230.77 * 52 ≈ 12000
    $this->assertEquals(12000.04, $result, '', 0.01);
  }

  /**
   * Tests normalizing global monthly price to yearly per m².
   *
   * @covers ::normalize
   */
  public function testNormalizeGlobalMonthlyToYearlyPerM2(): void {
    $item = $this->createMockPriceItem([
      'amount' => 2500.00,
      'unit_code' => 'GLO',
      'period_code' => 'MEN',
    ]);

    $result = $this->normalizer->normalize($item, 100.0);
    // 2500 * 12 = 30000 (yearly)
    // 30000 / 100 = 300 (per m²).
    $this->assertEquals(300.00, $result);
  }

  /**
   * Tests normalizing NULL amount.
   *
   * @covers ::normalize
   */
  public function testNormalizeNullAmount(): void {
    $item = $this->createMockPriceItem([
      'amount' => NULL,
      'unit_code' => 'SUR',
      'period_code' => 'ANN',
    ]);

    $result = $this->normalizer->normalize($item, 100.0);
    $this->assertNull($result);
  }

  /**
   * Tests normalizing with zero surface (edge case).
   *
   * @covers ::normalize
   */
  public function testNormalizeZeroSurface(): void {
    $item = $this->createMockPriceItem([
      'amount' => 50000.00,
      'unit_code' => 'GLO',
      'period_code' => 'ANN',
    ]);

    $result = $this->normalizer->normalize($item, 0.0);
    // Should return NULL by default when surface is 0.
    // This prevents invalid comparison.
    $this->assertNull($result);
  }

  /**
   * Creates a mock PriceItem with given values.
   *
   * @param array<string, mixed> $values
   *   Field values.
   *
   * @return \Drupal\ps_price\Plugin\Field\FieldType\PriceItem&\PHPUnit\Framework\MockObject\MockObject
   *   Mock price item.
   */
  private function createMockPriceItem(array $values): PriceItem&MockObject {
    $item = $this->getMockBuilder(PriceItem::class)
      ->disableOriginalConstructor()
      ->onlyMethods(['get', 'getValue'])
      ->getMock();

    $item->amount = $values['amount'] ?? NULL;
    $item->currency_code = $values['currency_code'] ?? 'EUR';
    $item->unit_code = $values['unit_code'] ?? NULL;
    $item->period_code = $values['period_code'] ?? NULL;

    return $item;
  }

}
