<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_migrate\Unit\Service;

use Drupal\ps_migrate\Service\FeatureTechnicalElementParser;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;

/**
 * Tests the feature technical element parser.
 */
#[CoversClass(FeatureTechnicalElementParser::class)]
#[Group('ps_migrate')]
final class FeatureTechnicalElementParserTest extends UnitTestCase {

  /**
   * Ensures the parser extracts and normalizes technical elements.
   */
  public function testParseAndMapTechnicalElements(): void {
    $parser = new FeatureTechnicalElementParser();
    $xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<ROOT>
  <TECHNICAL_ELEMENTS_LIST>
    <TECHNICAL_ELEMENT>
      <CODE_GROUP>AM_NAGEMENTS</CODE_GROUP>
      <CODE_ELEMENT>TEC_HALL_DACCUEIL</CODE_ELEMENT>
      <LIBELLE_ELEMENT>Hall accueil fallback</LIBELLE_ELEMENT>
      <ML_LABEL>
        <LABEL LANGUAGE="FR">Hall d'accueil</LABEL>
        <LABEL LANGUAGE="EN">Reception hall</LABEL>
      </ML_LABEL>
      <VALUE></VALUE>
      <UNIT>M2</UNIT>
      <ML_COMPLEMENT>
        <COMPLEMENT LANGUAGE="FR">Lobby principal</COMPLEMENT>
        <COMPLEMENT LANGUAGE="EN">Main lobby</COMPLEMENT>
      </ML_COMPLEMENT>
    </TECHNICAL_ELEMENT>
    <TECHNICAL_ELEMENT>
      <CODE_GROUP>EQUIPEMENTS</CODE_GROUP>
      <CODE_ELEMENT>TEC_CLIMATISATION</CODE_ELEMENT>
      <LIBELLE_ELEMENT>Climatisation fallback</LIBELLE_ELEMENT>
      <VALUE>Ventilo convecteur</VALUE>
      <UNIT></UNIT>
      <ML_COMPLEMENT></ML_COMPLEMENT>
    </TECHNICAL_ELEMENT>
    <TECHNICAL_ELEMENT>
      <CODE_GROUP>AM_NAGEMENTS</CODE_GROUP>
      <CODE_ELEMENT>TEC_EMPTY_PAYLOAD</CODE_ELEMENT>
      <ML_LABEL>Empty payload example</ML_LABEL>
    </TECHNICAL_ELEMENT>
  </TECHNICAL_ELEMENTS_LIST>
</ROOT>
XML;

    $elements = $parser->parseString($xml);

    self::assertCount(3, $elements);
    self::assertTrue($elements[0]->isValid());
    self::assertSame('AM_NAGEMENTS', $elements[0]->getGroupCode());
    self::assertSame('TEC_HALL_DACCUEIL', $elements[0]->getFeatureCode());
    self::assertSame('Hall d\'accueil', $elements[0]->getLabel());
    self::assertSame('M2', $elements[0]->getUnit());

    $record = $parser->map($elements[0]);
    self::assertSame('AM_NAGEMENTS', $record['group_code']);
    self::assertSame('TEC_HALL_DACCUEIL', $record['feature_code']);
    self::assertSame('Hall d\'accueil', $record['label']);
    self::assertSame('M2', $record['payload']['unit']);
    self::assertSame('Lobby principal', $record['payload']['complement']);

    self::assertSame('EQUIPEMENTS', $elements[1]->getGroupCode());
    self::assertSame('TEC_CLIMATISATION', $elements[1]->getFeatureCode());
    self::assertSame('Climatisation fallback', $elements[1]->getLabel());
    self::assertSame('Ventilo convecteur', $elements[1]->getValue());

    self::assertSame('TEC_EMPTY_PAYLOAD', $elements[2]->getFeatureCode());
    self::assertContains('Technical element has no payload value.', $elements[2]->getWarnings());
  }

  /**
   * Ensures validation surfaces missing codes.
   */
  public function testValidationReportsMissingCodes(): void {
    $parser = new FeatureTechnicalElementParser();
    $xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<ROOT>
  <TECHNICAL_ELEMENTS_LIST>
    <TECHNICAL_ELEMENT>
      <CODE_GROUP></CODE_GROUP>
      <CODE_ELEMENT></CODE_ELEMENT>
      <VALUE>Orphan payload</VALUE>
    </TECHNICAL_ELEMENT>
  </TECHNICAL_ELEMENTS_LIST>
</ROOT>
XML;

    $elements = $parser->parseString($xml);

    self::assertCount(1, $elements);
    self::assertFalse($elements[0]->isValid());
    self::assertContains('Missing TECHNICAL_ELEMENT/CODE_ELEMENT value.', $elements[0]->getErrors());
    self::assertContains('Missing TECHNICAL_ELEMENT/CODE_GROUP value; canonical fallback group will be used.', $elements[0]->getWarnings());
  }

  /**
   * Ensures missing CODE_GROUP is a warning on otherwise valid elements.
   */
  public function testMissingGroupCodeIsWarningOnly(): void {
    $parser = new FeatureTechnicalElementParser();
    $xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<ROOT>
  <TECHNICAL_ELEMENTS_LIST>
    <TECHNICAL_ELEMENT>
      <CODE_GROUP></CODE_GROUP>
      <CODE_ELEMENT>TEC_NO_GROUP</CODE_ELEMENT>
      <VALUE>Example</VALUE>
    </TECHNICAL_ELEMENT>
  </TECHNICAL_ELEMENTS_LIST>
</ROOT>
XML;

    $elements = $parser->parseString($xml);

    self::assertCount(1, $elements);
    self::assertTrue($elements[0]->isValid());
    self::assertSame([], $elements[0]->getErrors());
    self::assertContains('Missing TECHNICAL_ELEMENT/CODE_GROUP value; canonical fallback group will be used.', $elements[0]->getWarnings());
  }

}