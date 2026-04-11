<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_price\Unit\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\Language\Language;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\ps_dictionary\Service\DictionaryManagerInterface;
use Drupal\ps_price\Plugin\Field\FieldType\PriceItem;
use Drupal\ps_price\Service\PriceFormatter;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Tests for PriceFormatter service.
 *
 * @coversDefaultClass \Drupal\ps_price\Service\PriceFormatter
 * @group ps_price
 */
final class PriceFormatterTest extends UnitTestCase {

  /**
   * The price formatter under test.
   */
  private PriceFormatter $formatter;

  /**
   * Mock language manager.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface&\PHPUnit\Framework\MockObject\MockObject
   */
  private LanguageManagerInterface&MockObject $languageManager;

  /**
   * Mock config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface|\PHPUnit\Framework\MockObject\MockObject
   */
  private ConfigFactoryInterface|MockObject $configFactory;

  /**
   * Mock dictionary manager.
   *
   * @var \Drupal\ps_dictionary\Service\DictionaryManagerInterface|\PHPUnit\Framework\MockObject\MockObject
   */
  private DictionaryManagerInterface|MockObject $dictionaryManager;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    // Mock language manager.
    $this->languageManager = $this->createMock(LanguageManagerInterface::class);
    $language = new Language(['id' => 'en']);
    $this->languageManager->method('getCurrentLanguage')
      ->willReturn($language);

    // Mock config factory.
    $config = $this->createMock(ImmutableConfig::class);
    $config->method('get')->willReturn(FALSE);
    $this->configFactory = $this->createMock(ConfigFactoryInterface::class);
    $this->configFactory->method('get')->willReturn($config);

    // Mock dictionary manager.
    $this->dictionaryManager = $this->createMock(DictionaryManagerInterface::class);

    // Mock string translation to avoid container dependency.
    $stringTranslation = $this->createMock('Drupal\Core\StringTranslation\TranslationInterface');
    $stringTranslation->method('translate')
      ->willReturnCallback(fn($string) => $string);

    $this->formatter = new PriceFormatter($this->languageManager, $this->configFactory, $this->dictionaryManager);
    $this->formatter->setStringTranslation($stringTranslation);
  }

  /**
   * Tests full format with all flags.
   *
   * @covers ::format
   */
  public function testFormatFull(): void {
    $item = $this->createMockPriceItem([
      'amount' => 1250.50,
      'currency_code' => 'EUR',
      'unit_code' => 'SUR',
      'period_code' => 'ANN',
      'is_from' => 1,
      'is_vat_excluded' => 1,
      'is_charges_included' => 0,
    ]);

    $result = $this->formatter->format($item, [
      'show_currency' => TRUE,
      'show_unit' => TRUE,
      'show_period' => TRUE,
      'show_flags' => TRUE,
    ]);

    // Check all required components are present.
    $this->assertStringContainsString('1,250.5', $result);
    $this->assertStringContainsString('EUR', $result);
    $this->assertStringContainsString('SUR', $result);
    $this->assertStringContainsString('ANN', $result);
  }

  /**
   * Tests short format (amount + currency only).
   *
   * @covers ::formatShort
   */
  public function testFormatShort(): void {
    $item = $this->createMockPriceItem([
      'amount' => 500000.00,
      'currency_code' => 'EUR',
      'unit_code' => 'GLO',
      'period_code' => NULL,
    ]);

    $result = $this->formatter->formatShort($item, [
      'show_currency' => TRUE,
    ]);

    $this->assertStringContainsString('EUR', $result);
    $this->assertStringNotContainsString('GLO', $result);
  }

  /**
   * Tests numeric extraction for search.
   *
   * @covers ::getNumericForSearch
   */
  public function testGetNumericForSearch(): void {
    $item = $this->createMockPriceItem([
      'amount' => 12345.67,
      'currency_code' => 'EUR',
    ]);

    $result = $this->formatter->getNumericForSearch($item);
    $this->assertEquals(12345.67, $result);
  }

  /**
   * Tests empty price item.
   *
   * @covers ::format
   */
  public function testFormatEmpty(): void {
    $item = $this->createMockPriceItem([
      'amount' => NULL,
      'currency_code' => 'EUR',
    ]);

    $result = $this->formatter->format($item);
    $this->assertIsString($result);
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

    // Configure property access via magic properties.
    $item->amount = $values['amount'] ?? NULL;
    $item->currency_code = $values['currency_code'] ?? 'EUR';
    $item->unit_code = $values['unit_code'] ?? NULL;
    $item->period_code = $values['period_code'] ?? NULL;
    $item->value_type_code = $values['value_type_code'] ?? NULL;
    $item->is_from = $values['is_from'] ?? FALSE;
    $item->is_vat_excluded = $values['is_vat_excluded'] ?? FALSE;
    $item->is_charges_included = $values['is_charges_included'] ?? FALSE;

    return $item;
  }

}
