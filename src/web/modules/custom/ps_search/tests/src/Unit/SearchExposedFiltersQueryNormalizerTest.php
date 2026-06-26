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

  /**
   * @covers ::normalize
   */
  public function testBooleanArrayQueryValueIsUnwrapped(): void {
    $request = Request::create('/', 'GET', ['divisible' => ['1']]);
    $this->normalizer->normalize($request);

    self::assertSame('1', $request->query->get('divisible'));
  }

  /**
   * @covers ::normalize
   */
  public function testEmptyBooleanArrayQueryValueIsRemoved(): void {
    $request = Request::create('/', 'GET', ['has_video' => []]);
    $this->normalizer->normalize($request);

    self::assertFalse($request->query->has('has_video'));
  }

  /**
   * @covers ::normalize
   */
  public function testFeatureBooleanArrayQueryValueIsUnwrapped(): void {
    $request = Request::create('/', 'GET', ['feature_tec_parking' => ['1']]);
    $this->normalizer->normalize($request);

    self::assertSame('1', $request->query->get('feature_tec_parking'));
  }

}
