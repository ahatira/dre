<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_migrate\Kernel;

use Drupal\Core\File\FileSystemInterface;
use Drupal\ps_migrate\Service\XmlParseCacheService;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;

/**
 * Kernel tests for the scoped XML parse cache.
 */
#[Group('ps_migrate')]
#[RunTestsInSeparateProcesses]
final class XmlParseCacheServiceKernelTest extends PsMigrateKernelTestBase {

  private XmlParseCacheService $cache;

  private FileSystemInterface $fileSystem;

  private string $stagingUri;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->cache = $this->container->get('ps_migrate.xml_parse_cache');
    $this->fileSystem = $this->container->get('file_system');
    $this->stagingUri = 'public://crm/kernel_test_offers.xml';

    $directory = 'public://crm';
    $this->fileSystem->prepareDirectory($directory, FileSystemInterface::CREATE_DIRECTORY);
    $path = $this->fileSystem->realpath($this->stagingUri);
    $this->assertNotFalse($path);
    file_put_contents($path, $this->sampleXml());
  }

  public function testBeginRunParsesOnceAndClearRun(): void {
    $this->cache->beginRun($this->stagingUri);
    $this->assertTrue($this->cache->isActive());
    $this->assertTrue($this->cache->matchesUrl($this->stagingUri));

    $offers = $this->cache->getOffers();
    $this->assertCount(1, $offers);
    $this->assertSame('KERNEL-1', trim((string) ($offers[0]->BUSINESS_ID ?? '')));

    $raw = $this->cache->getRawContent($this->stagingUri);
    $this->assertStringContainsString('KERNEL-1', $raw);
    $this->assertNotEmpty($this->cache->getTempFilePath());

    $this->cache->clearRun();
    $this->assertFalse($this->cache->isActive());
  }

  private function sampleXml(): string {
    return <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<OFFERS_LIST>
  <OFFER>
    <BUSINESS_ID>KERNEL-1</BUSINESS_ID>
    <TYPE_CODE>OFF</TYPE_CODE>
  </OFFER>
</OFFERS_LIST>
XML;
  }

}
