<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_migrate\Unit\Service;

use Drupal\ps_migrate\Service\OfferLocationXmlReader;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;

/**
 * Tests CRM GPS and address extraction for offer import.
 */
#[CoversClass(OfferLocationXmlReader::class)]
#[Group('ps_migrate')]
final class OfferLocationXmlReaderTest extends UnitTestCase {

  /**
   * Ensures visible-address GPS is imported as WKT.
   */
  public function testGetGeoWktFromVisibleAddress(): void {
    $reader = new OfferLocationXmlReader();
    $offer = $this->loadOffer(<<<XML
<OFFER>
  <ADDRESS_LIST>
    <ADDRESS>
      <DISPLAY>true</DISPLAY>
      <PRIMARY>true</PRIMARY>
      <GPS>
        <LONGITUDE>2.3522</LONGITUDE>
        <LATITUDE>48.8566</LATITUDE>
      </GPS>
    </ADDRESS>
  </ADDRESS_LIST>
</OFFER>
XML);

    self::assertSame('POINT (2.3522 48.8566)', $reader->getGeoWkt($offer));
  }

  /**
   * Ensures hidden-address GPS is used when the visible address has none.
   */
  public function testGetGeoWktFallsBackToHiddenAddress(): void {
    $reader = new OfferLocationXmlReader();
    $offer = $this->loadOffer(<<<XML
<OFFER>
  <ADDRESS_LIST>
    <ADDRESS>
      <DISPLAY>true</DISPLAY>
      <PRIMARY>true</PRIMARY>
      <CITY>Paris</CITY>
    </ADDRESS>
    <ADDRESS>
      <DISPLAY>false</DISPLAY>
      <GPS>
        <LONGITUDE>6.141949899999999</LONGITUDE>
        <LATITUDE>45.9192139</LATITUDE>
      </GPS>
    </ADDRESS>
  </ADDRESS_LIST>
</OFFER>
XML);

    self::assertSame('POINT (6.141949899999999 45.9192139)', $reader->getGeoWkt($offer));
  }

  /**
   * Ensures missing GPS returns NULL.
   */
  public function testGetGeoWktReturnsNullWhenCoordinatesMissing(): void {
    $reader = new OfferLocationXmlReader();
    $offer = $this->loadOffer(<<<XML
<OFFER>
  <ADDRESS_LIST>
    <ADDRESS>
      <DISPLAY>true</DISPLAY>
      <CITY>Lyon</CITY>
    </ADDRESS>
  </ADDRESS_LIST>
</OFFER>
XML);

    self::assertNull($reader->getGeoWkt($offer));
  }

  /**
   * Loads one OFFER XML fragment.
   */
  private function loadOffer(string $xml): \SimpleXMLElement {
    return new \SimpleXMLElement($xml);
  }

}
