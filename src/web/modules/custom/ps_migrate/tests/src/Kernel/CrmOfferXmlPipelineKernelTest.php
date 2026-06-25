<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_migrate\Kernel;

use Drupal\Core\File\FileSystemInterface;
use Drupal\ps_migrate\Service\CrmOfferXmlDocumentLoader;
use Drupal\ps_migrate\Service\CrmOfferXmlMode;
use Drupal\ps_migrate\Service\CrmOfferXmlXpathRowBuilder;
use Drupal\ps_migrate\Service\XmlParseCacheService;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;

/**
 * Kernel tests for unified CRM XML extraction helpers.
 */
#[Group('ps_migrate')]
#[RunTestsInSeparateProcesses]
final class CrmOfferXmlPipelineKernelTest extends PsMigrateKernelTestBase {

  private string $stagingUri;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $fileSystem = $this->container->get('file_system');
    $this->stagingUri = 'public://crm/kernel_unified_offers.xml';
    $directory = 'public://crm';
    $fileSystem->prepareDirectory($directory, FileSystemInterface::CREATE_DIRECTORY);
    $path = $fileSystem->realpath($this->stagingUri);
    $this->assertNotFalse($path);
    file_put_contents($path, $this->sampleXml());
  }

  public function testDocumentLoaderUsesParseCacheWhenActive(): void {
    /** @var \Drupal\ps_migrate\Service\XmlParseCacheService $cache */
    $cache = $this->container->get('ps_migrate.xml_parse_cache');
    $cache->beginRun($this->stagingUri);

    /** @var \Drupal\ps_migrate\Service\CrmOfferXmlDocumentLoader $loader */
    $loader = $this->container->get('ps_migrate.crm_offer_xml_document_loader');
    $items = $loader->selectItems($this->stagingUri, CrmOfferXmlMode::itemSelector(CrmOfferXmlMode::OFFER) ?? '');
    $this->assertCount(1, $items);

    $builder = $this->container->get('ps_migrate.crm_offer_xml_xpath_row_builder');
    $this->assertInstanceOf(CrmOfferXmlXpathRowBuilder::class, $builder);

    $row = $builder->buildRow($items[0], [
      ['name' => 'business_id', 'selector' => 'BUSINESS_ID'],
      ['name' => 'type_code', 'selector' => 'TYPE_CODE'],
    ]);
    $this->assertSame('UNIFIED-1', $row['business_id']);
    $this->assertSame('OFF', $row['type_code']);

    $cache->clearRun();
  }

  public function testAgentModeSelectorFindsLeader(): void {
    $loader = $this->container->get('ps_migrate.crm_offer_xml_document_loader');
    $selector = CrmOfferXmlMode::itemSelector(CrmOfferXmlMode::AGENT);
    $this->assertNotNull($selector);

    $items = $loader->selectItems($this->stagingUri, $selector);
    $this->assertCount(1, $items);

    $builder = $this->container->get('ps_migrate.crm_offer_xml_xpath_row_builder');
    $row = $builder->buildRow($items[0], [
      ['name' => 'uid', 'selector' => 'UID'],
      ['name' => 'email', 'selector' => 'MAIL'],
    ]);
    $this->assertSame('agent-42', $row['uid']);
    $this->assertSame('agent@example.com', $row['email']);
  }

  private function sampleXml(): string {
    return <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<OFFERS_LIST>
  <OFFER>
    <BUSINESS_ID>UNIFIED-1</BUSINESS_ID>
    <TYPE_CODE>OFF</TYPE_CODE>
    <BUSINESS_LEADERS_LIST>
      <BUSINESS_LEADER>
        <UID>agent-42</UID>
        <MAIL>agent@example.com</MAIL>
      </BUSINESS_LEADER>
    </BUSINESS_LEADERS_LIST>
  </OFFER>
</OFFERS_LIST>
XML;
  }

}
