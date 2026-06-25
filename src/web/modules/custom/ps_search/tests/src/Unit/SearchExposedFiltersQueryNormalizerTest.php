<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_search\Unit;

use Drupal\ps_search\Service\SearchExposedFiltersQueryNormalizer;
use Drupal\Tests\UnitTestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * @coversDefaultClass \Drupal\ps_search\Service\SearchExposedFiltersQueryNormalizer
 * @group ps_search
 */
final class SearchExposedFiltersQueryNormalizerTest extends UnitTestCase {

  private SearchExposedFiltersQueryNormalizer $normalizer;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->normalizer = new SearchExposedFiltersQueryNormalizer();
  }

  /**
   * @covers ::normalize
   */
  public function testScalarSurfaceMinIsConvertedToBetweenArray(): void {
    $request = Request::create('/', 'GET', ['surface_min' => '200']);
    $this->normalizer->normalize($request);

    self::assertSame(['min' => '200', 'max' => ''], $request->query->all('surface_min'));
  }

  /**
   * @covers ::normalize
   */
  public function testFlatSurfaceMinAndMaxAreMerged(): void {
    $request = Request::create('/', 'GET', [
      'surface_min' => '200',
      'surface_max' => '500',
    ]);
    $this->normalizer->normalize($request);

    self::assertSame(['min' => '200', 'max' => '500'], $request->query->all('surface_min'));
  }

  /**
   * @covers ::normalize
   */
  public function testExistingArrayIsLeftUntouched(): void {
    $request = Request::create('/', 'GET', [
      'surface_min' => ['min' => '100', 'max' => '500'],
    ]);
    $this->normalizer->normalize($request);

    self::assertSame(['min' => '100', 'max' => '500'], $request->query->all('surface_min'));
  }

}
