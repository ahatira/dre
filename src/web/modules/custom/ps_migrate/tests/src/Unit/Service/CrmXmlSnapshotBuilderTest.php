<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_migrate\Unit\Service;

use Drupal\ps_migrate\Service\CrmOfferXmlDocumentLoader;
use Drupal\ps_migrate\Service\CrmOfferXmlMode;
use Drupal\ps_migrate\Service\CrmXmlSnapshotBuilder;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;

/**
 * @coversDefaultClass \Drupal\ps_migrate\Service\CrmXmlSnapshotBuilder
 */
#[CoversClass(CrmXmlSnapshotBuilder::class)]
#[Group('ps_migrate')]
final class CrmXmlSnapshotBuilderTest extends UnitTestCase {

  /**
   * @covers ::buildOfferBusinessIds
   */
  public function testBuildOfferBusinessIds(): void {
    $offerItem = new \SimpleXMLElement('<OFFER><BUSINESS_ID>OFF-1</BUSINESS_ID></OFFER>');
    $loader = $this->createMock(CrmOfferXmlDocumentLoader::class);
    $loader->expects($this->once())
      ->method('selectItems')
      ->with('public://crm/offers.xml', CrmOfferXmlMode::itemSelector(CrmOfferXmlMode::OFFER))
      ->willReturn([$offerItem]);

    $builder = new CrmXmlSnapshotBuilder($loader);

    self::assertSame(['OFF-1' => TRUE], $builder->buildOfferBusinessIds(['public://crm/offers.xml']));
  }

  /**
   * @covers ::buildMediaExtCompositeKeys
   */
  public function testBuildMediaExtCompositeKeys(): void {
    $xml = <<<'XML'
<OFFERS_LIST>
  <OFFER>
    <BUSINESS_ID>OFF-9</BUSINESS_ID>
    <MEDIA_LIST>
      <MEDIA>
        <TYPE_CODE>EXT</TYPE_CODE>
        <ORDER>2</ORDER>
      </MEDIA>
    </MEDIA_LIST>
  </OFFER>
</OFFERS_LIST>
XML;
    $document = new \SimpleXMLElement($xml);
    $mediaItems = $document->xpath('/OFFERS_LIST/OFFER/MEDIA_LIST/MEDIA');
    $this->assertIsArray($mediaItems);
    $mediaItem = $mediaItems[0];
    $this->assertInstanceOf(\SimpleXMLElement::class, $mediaItem);

    $loader = $this->createMock(CrmOfferXmlDocumentLoader::class);
    $loader->expects($this->once())
      ->method('selectItems')
      ->with('public://crm/offers.xml', CrmOfferXmlMode::itemSelector(CrmOfferXmlMode::MEDIA_EXT))
      ->willReturn([$mediaItem]);

    $builder = new CrmXmlSnapshotBuilder($loader);

    self::assertSame(['OFF-9:2' => TRUE], $builder->buildMediaExtCompositeKeys(['public://crm/offers.xml']));
  }

}
