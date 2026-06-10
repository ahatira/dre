<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_search_filters\Unit\Service;

use Drupal\ps_search_filters\Service\FilterBarHtmxSettings;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\ps_search_filters\Service\FilterBarHtmxSettings
 * @group ps_search_filters
 */
final class FilterBarHtmxSettingsTest extends UnitTestCase {

  /**
   * @covers ::buildJsSettings
   * @covers ::getPopin
   */
  public function testBuildJsSettingsIncludesTypePopin(): void {
    $settings = new FilterBarHtmxSettings();
    $js = $settings->buildJsSettings();

    $this->assertTrue($js['enabled']);
    $this->assertSame(FilterBarHtmxSettings::COUNT_LABEL_ROUTE, $js['countUrl']);
    $this->assertSame(FilterBarHtmxSettings::MORE_CRITERIA_ROUTE, $js['moreCriteriaGroupUrl']);
    $this->assertSame(FilterBarHtmxSettings::RESULTS_HEADER_ROUTE, $js['resultsHeaderUrl']);
    $this->assertSame(FilterBarHtmxSettings::RESULTS_HEADER_TARGET_ID, $js['resultsHeaderTargetId']);
    $this->assertArrayHasKey('type', $js['popins']);
    $this->assertSame('ps-filter-type-count-label', $js['popins']['type']['targetId']);
    $this->assertSame(FilterBarHtmxSettings::APPLY_TYPE_ROUTE, $js['popins']['type']['applyUrl']);
    $this->assertArrayHasKey('location', $js['popins']);
    $this->assertSame('ps-filter-location-count-label', $js['popins']['location']['targetId']);
    $this->assertSame(FilterBarHtmxSettings::APPLY_LOCATION_ROUTE, $js['popins']['location']['applyUrl']);

    $popin = $settings->getPopin('type');
    $this->assertNotNull($popin);
    $this->assertSame('ps-filter-bar__item--type', $popin['dropdownClass']);
    $this->assertNull($settings->getPopin('unknown'));

    $location = $settings->getPopin('location');
    $this->assertNotNull($location);
    $this->assertSame('.js-ps-location-toggle', $location['toggleSelector']);

    $this->assertArrayHasKey('surface', $js['popins']);
    $this->assertSame('ps-filter-surface-count-label', $js['popins']['surface']['targetId']);
    $this->assertSame(FilterBarHtmxSettings::APPLY_SURFACE_ROUTE, $js['popins']['surface']['applyUrl']);
    $this->assertArrayHasKey('capacity', $js['popins']);
    $this->assertSame('ps-filter-capacity-count-label', $js['popins']['capacity']['targetId']);
    $this->assertArrayHasKey('budget', $js['popins']);
    $this->assertSame('ps-filter-budget-count-label', $js['popins']['budget']['targetId']);
    $this->assertSame(FilterBarHtmxSettings::APPLY_BUDGET_ROUTE, $js['popins']['budget']['applyUrl']);
    $this->assertArrayHasKey('mobile', $js['popins']);
    $this->assertSame('ps-filter-mobile-count-label', $js['popins']['mobile']['targetId']);
    $this->assertSame(FilterBarHtmxSettings::APPLY_MOBILE_ROUTE, $js['popins']['mobile']['applyUrl']);
    $this->assertArrayHasKey('more', $js['popins']);
    $this->assertSame('ps-filter-more-count-label', $js['popins']['more']['targetId']);
  }

}
