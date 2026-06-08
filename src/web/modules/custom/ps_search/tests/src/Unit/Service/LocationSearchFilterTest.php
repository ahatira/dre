<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_search\Unit\Service;

use Drupal\Core\Database\Connection;
use Drupal\ps_search\Service\LocationSearchFilter;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\ps_search\Service\LocationSearchFilter
 * @group ps_search
 */
final class LocationSearchFilterTest extends UnitTestCase {

  /**
   * @covers ::extractTokens
   */
  public function testExtractTokensDeduplicatesAndLimits(): void {
    $filter = new LocationSearchFilter($this->createMock(Connection::class));

    $tokens = $filter->extractTokens('Paris, Lyon, Paris, Nancy');

    $this->assertSame(['Paris', 'Lyon', 'Nancy'], $tokens);
  }

  /**
   * @covers ::extractTokens
   */
  public function testExtractTokensFromArray(): void {
    $filter = new LocationSearchFilter($this->createMock(Connection::class));

    $tokens = $filter->extractTokens(['75015', 'Nancy']);

    $this->assertSame(['75015', 'Nancy'], $tokens);
  }

}
