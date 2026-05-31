<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_migrate\Unit\Service;

use Drupal\ps_migrate\Service\FeaturePayloadDefaultsNormalizer;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;

/**
 * Tests payload defaults canonicalization.
 */
#[CoversClass(FeaturePayloadDefaultsNormalizer::class)]
#[Group('ps_migrate')]
final class FeaturePayloadDefaultsNormalizerTest extends UnitTestCase {

  /**
   * Ensures scalar and nested values are normalized deterministically.
   */
  public function testNormalizeCanonicalizesValues(): void {
    $normalizer = new FeaturePayloadDefaultsNormalizer();

    $result = $normalizer->normalize([
      '  unit  ' => '  M2  ',
      'options' => [
        '  wifi  ',
        ' ',
        'parking',
      ],
      'nested' => [
        'z' => 'last',
        'a' => 'first',
      ],
      'empty' => '',
    ]);

    self::assertSame([
      '  unit  ' => 'M2',
      'nested' => [
        'a' => 'first',
        'z' => 'last',
      ],
      'options' => ['wifi', 'parking'],
    ], $result);
  }

  /**
   * Ensures non-array values are converted to empty arrays.
   */
  public function testNormalizeReturnsEmptyArrayForScalarInput(): void {
    $normalizer = new FeaturePayloadDefaultsNormalizer();

    self::assertSame([], $normalizer->normalize('')); 
    self::assertSame([], $normalizer->normalize(NULL));
    self::assertSame([], $normalizer->normalize('raw text'));
  }

}