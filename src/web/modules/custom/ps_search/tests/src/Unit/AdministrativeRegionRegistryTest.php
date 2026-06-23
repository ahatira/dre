<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_search\Unit;

use Drupal\ps_search\Service\AdministrativeRegionRegistry;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\ps_search\Service\AdministrativeRegionRegistry
 *
 * @group ps_search
 */
final class AdministrativeRegionRegistryTest extends UnitTestCase {

  private AdministrativeRegionRegistry $registry;

  protected function setUp(): void {
    parent::setUp();
    $this->registry = new AdministrativeRegionRegistry('modules/custom/ps_search');
  }

  /**
   * @covers ::searchByLabelPrefix
   */
  public function testSearchByLabelPrefixMatchesIleDeFrance(): void {
    $matches = $this->registry->searchByLabelPrefix('ile', 5);
    self::assertNotEmpty($matches);
    self::assertSame('ile-de-france', $matches[0]['slug']);
    self::assertSame('Île-de-France', $matches[0]['label']);
  }

  /**
   * @covers ::matchesSearchNeedle
   */
  public function testMatchesSearchNeedleIsAccentInsensitive(): void {
    self::assertTrue($this->registry->matchesSearchNeedle('ile', 'Île-de-France', 'ile-de-france'));
    self::assertTrue($this->registry->matchesSearchNeedle('auvergne', 'Auvergne-Rhône-Alpes', 'auvergne-rhone-alpes'));
  }

}
