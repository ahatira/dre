<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_search\Unit;

use Drupal\ps_search\Api\RequestValidator;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\ps_search\Api\RequestValidator
 *
 * @group ps_search
 */
final class RequestValidatorTest extends UnitTestCase {

  private RequestValidator $validator;

  protected function setUp(): void {
    parent::setUp();
    $this->validator = new RequestValidator();
  }

  /**
   * @covers ::sanitizeLocationToken
   */
  public function testSanitizeLocationTokenPreservesRegionPrefix(): void {
    self::assertSame(
      'region:ile-de-france',
      $this->validator->sanitizeLocationToken('region:ile-de-france'),
    );
  }

  /**
   * @covers ::sanitizeText
   */
  public function testSanitizeTextStripsColonsFromFreeText(): void {
    self::assertSame(
      'regionile-de-france',
      $this->validator->sanitizeText('region:ile-de-france'),
    );
  }

}
