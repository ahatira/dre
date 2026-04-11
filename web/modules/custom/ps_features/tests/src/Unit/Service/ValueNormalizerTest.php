<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_features\Unit\Service;

use Drupal\ps_dictionary\Service\DictionaryManagerInterface;
use Drupal\ps_features\Entity\FeatureInterface;
use Drupal\ps_features\Service\ValueNormalizer;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\ps_features\Service\ValueNormalizer
 * @group ps_features
 */
final class ValueNormalizerTest extends UnitTestCase {

  /**
   * The value normalizer service.
   */
  private ValueNormalizer $normalizer;

  private DictionaryManagerInterface $dictionaryManager;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->dictionaryManager = $this->createMock(DictionaryManagerInterface::class);
    $this->normalizer = new ValueNormalizer($this->dictionaryManager);
  }

  /**
   * @covers ::normalize
   */
  public function testNormalizeCoversAllTypes(): void {
    $definitions = [
      'flag_f' => $this->featureStub('flag'),
      'yesno_f' => $this->featureStub('yesno'),
      'numeric_f' => $this->featureStub('numeric'),
      'range_f' => $this->featureStub('range'),
      'dict_f' => $this->featureStub('dictionary', 'type_code'),
      'string_f' => $this->featureStub('string'),
    ];

    $this->dictionaryManager
      ->expects($this->once())
      ->method('isValid')
      ->with('type_code', 'VALID')
      ->willReturn(TRUE);

    $result = $this->normalizer->normalize([
      'flag_f' => TRUE,
      'yesno_f' => 'true',
      'numeric_f' => '42.5',
      'range_f' => ['min' => '1.5', 'max' => '3.5'],
      'dict_f' => 'VALID',
      'string_f' => 'text',
    ], $definitions);

    $this->assertSame([
      'flag_f' => TRUE,
      'yesno_f' => 'yes',
      'numeric_f' => 42.5,
      'range_f' => ['min' => 1.5, 'max' => 3.5],
      'dict_f' => 'VALID',
      'string_f' => 'text',
    ], $result['values']);
    $this->assertSame([], $result['errors']);
  }

  /**
   * @covers ::normalize
   */
  public function testNormalizeReportsErrors(): void {
    $definitions = [
      'numeric_f' => $this->featureStub('numeric'),
      'range_f' => $this->featureStub('range'),
      'dict_f' => $this->featureStub('dictionary', 'type_code'),
      'string_f' => $this->featureStub('string'),
    ];

    $this->dictionaryManager
      ->expects($this->once())
      ->method('isValid')
      ->with('type_code', 'BAD')
      ->willReturn(FALSE);

    $result = $this->normalizer->normalize([
      'unknown' => 'value',
      'numeric_f' => 'oops',
      'range_f' => ['min' => 5, 'max' => 1],
      'dict_f' => 'BAD',
      'string_f' => '',
    ], $definitions);

    $this->assertSame('Unknown feature code', $result['errors']['unknown']);
    $this->assertSame('Invalid numeric value', $result['errors']['numeric_f']);
    $this->assertSame('Range min greater than max', $result['errors']['range_f']);
    $this->assertSame('Invalid dictionary code', $result['errors']['dict_f']);
    $this->assertArrayNotHasKey('string_f', $result['values']);
  }

  /**
   *
   */
  private function featureStub(string $type, ?string $dictionaryType = NULL): FeatureInterface {
    $feature = $this->createStub(FeatureInterface::class);
    $feature->method('getValueType')->willReturn($type);
    $feature->method('getDictionaryType')->willReturn($dictionaryType);
    return $feature;
  }

}
