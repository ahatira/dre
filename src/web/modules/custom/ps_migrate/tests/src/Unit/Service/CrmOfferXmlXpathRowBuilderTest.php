<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_migrate\Unit\Service;

use Drupal\ps_migrate\Service\CrmOfferXmlXpathRowBuilder;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Drupal\ps_migrate\Service\CrmOfferXmlXpathRowBuilder
 * @group ps_migrate
 */
final class CrmOfferXmlXpathRowBuilderTest extends TestCase {

  /**
   * @covers ::buildRow
   */
  public function testBuildRowExtractsScalarAndNodeValues(): void {
    $xml = new \SimpleXMLElement(
      '<OFFER><BUSINESS_ID>ABC123</BUSINESS_ID><TYPE_CODE>OFF</TYPE_CODE></OFFER>'
    );

    $builder = new CrmOfferXmlXpathRowBuilder();
    $row = $builder->buildRow($xml, [
      ['name' => 'business_id', 'selector' => 'BUSINESS_ID'],
      ['name' => 'type_code', 'selector' => 'TYPE_CODE'],
      ['name' => 'offer_xml_node', 'selector' => '.'],
    ]);

    $this->assertSame('ABC123', $row['business_id']);
    $this->assertSame('OFF', $row['type_code']);
    $this->assertInstanceOf(\SimpleXMLElement::class, $row['offer_xml_node']);
  }

}
