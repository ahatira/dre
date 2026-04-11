<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_features\Unit\Service;

use Drupal\ps_dictionary\Service\DictionaryManagerInterface;
use Drupal\ps_features\Entity\FeatureInterface;
use Drupal\ps_features\Service\FeatureValidator;
use Drupal\Tests\UnitTestCase;
use Drupal\Core\StringTranslation\TranslationInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * @coversDefaultClass \Drupal\ps_features\Service\FeatureValidator
 * @group ps_features
 */
final class FeatureValidatorTest extends UnitTestCase {

  private FeatureValidator $validator;

  private DictionaryManagerInterface $dictionaryManager;

  private TranslationInterface $translator;

  /**
   *
   */
  protected function setUp(): void {
    parent::setUp();

    $this->dictionaryManager = $this->createMock(DictionaryManagerInterface::class);
    $this->translator = new class implements TranslationInterface {

      /**
       *
       */
      public function translate($string, array $args = [], array $options = [], ?string $langcode = NULL): string {
        return $string instanceof TranslatableMarkup ? $string->getUntranslatedString() : (string) $string;
      }

      /**
       *
       */
      public function translateString($string, array $args = [], array $options = [], ?string $langcode = NULL): string {
        return $string instanceof TranslatableMarkup ? $string->getUntranslatedString() : (string) $string;
      }

      /**
       *
       */
      public function formatPlural($count, $singular, $plural, array $args = [], array $options = [], ?string $langcode = NULL): string {
        return $count === 1 ? (string) $singular : (string) $plural;
      }

    };
    $this->validator = new FeatureValidator($this->dictionaryManager, $this->translator);
  }

  /**
   * @covers ::validateNumeric
   */
  public function testValidateNumericRules(): void {
    $feature = $this->featureStub('numeric', TRUE, ['min' => 1, 'max' => 10]);

    $this->assertSame(['Numeric value is required.'], $this->validator->validateNumeric($feature, []));
    $this->assertSame(['Value must be numeric.'], $this->validator->validateNumeric($feature, ['value_numeric' => 'nope']));
    $this->assertSame(['Value must be at least 1.'], $this->validator->validateNumeric($feature, ['value_numeric' => 0]));
    $this->assertSame(['Value must be at most 10.'], $this->validator->validateNumeric($feature, ['value_numeric' => 11]));
    $this->assertSame([], $this->validator->validateNumeric($feature, ['value_numeric' => 5]));
  }

  /**
   * @covers ::validateRange
   */
  public function testValidateRangeRules(): void {
    $feature = $this->featureStub('range', TRUE, ['min' => 2, 'max' => 8]);

    $this->assertSame(['Range values are required.'], $this->validator->validateRange($feature, []));
    $this->assertSame(['Minimum value must be numeric.'], $this->validator->validateRange($feature, ['value_range_min' => 'x']));
    $this->assertSame(['Maximum value must be numeric.'], $this->validator->validateRange($feature, ['value_range_max' => 'x']));
    $this->assertSame(['Minimum value cannot be greater than maximum value.'], $this->validator->validateRange($feature, ['value_range_min' => 9, 'value_range_max' => 3]));
    $this->assertSame(['Minimum value must be at least 2.'], $this->validator->validateRange($feature, ['value_range_min' => 1, 'value_range_max' => 5]));
    $this->assertSame(['Maximum value must be at most 8.'], $this->validator->validateRange($feature, ['value_range_min' => 3, 'value_range_max' => 10]));
    $this->assertSame([], $this->validator->validateRange($feature, ['value_range_min' => 3, 'value_range_max' => 7]));
  }

  /**
   * @covers ::validateDictionary
   */
  public function testValidateDictionaryCodes(): void {
    $feature = $this->featureStub('dictionary', TRUE, [], 'air_conditioning');

    $this->dictionaryManager
      ->expects($this->once())
      ->method('isValid')
      ->with('air_conditioning', 'INVALID')
      ->willReturn(FALSE);

    $this->assertSame(['Dictionary value is required.'], $this->validator->validateDictionary($feature, []));
    $this->assertSame(['Invalid dictionary code: INVALID'], $this->validator->validateDictionary($feature, ['value_string' => 'INVALID']));
  }

  /**
   * @covers ::validateString
   */
  public function testValidateStringAllowedValues(): void {
    $feature = $this->featureStub('string', TRUE, ['allowed_values' => ['A', 'B']]);

    $this->assertSame(['String value is required.'], $this->validator->validateString($feature, []));
    $this->assertSame(['Value must be a string.'], $this->validator->validateString($feature, ['value_string' => 12]));
    $this->assertSame(['Value must be one of: A, B'], $this->validator->validateString($feature, ['value_string' => 'C']));
    $this->assertSame([], $this->validator->validateString($feature, ['value_string' => 'A']));
  }

  /**
   *
   */
  private function featureStub(string $valueType, bool $required = FALSE, array $rules = [], ?string $dictionaryType = NULL): FeatureInterface {
    $feature = $this->createStub(FeatureInterface::class);
    $feature->method('getValueType')->willReturn($valueType);
    $feature->method('isRequired')->willReturn($required);
    $feature->method('getValidationRules')->willReturn($rules);
    $feature->method('getDictionaryType')->willReturn($dictionaryType);
    return $feature;
  }

}
