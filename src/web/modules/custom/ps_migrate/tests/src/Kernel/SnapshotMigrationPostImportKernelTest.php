<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_migrate\Kernel;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\File\FileSystemInterface;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\KernelTests\KernelTestBase;
use Drupal\migrate\Event\MigrateImportEvent;
use Drupal\migrate\MigrateMessage;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\node\Entity\Node;
use Drupal\node\Entity\NodeType;
use Drupal\ps_core\Plugin\ImportGovernance\ImportGovernanceSnapshotPostImportPolicyInterface;
use Drupal\ps_core\Service\ImportGovernanceRegistry;
use Drupal\ps_migrate\EventSubscriber\SnapshotMigrationPostImportSubscriber;
use Drupal\ps_migrate\Service\CrmOfferXmlDocumentLoader;
use Drupal\ps_migrate\Service\CrmXmlSnapshotBuilder;
use Drupal\ps_migrate\Service\XmlParseCacheService;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Psr\Log\NullLogger;

/**
 * Kernel integration tests for snapshot post-import subscriber behaviour.
 */
#[Group('ps_migrate')]
#[RunTestsInSeparateProcesses]
final class SnapshotMigrationPostImportKernelTest extends KernelTestBase {

  private string $stagingUri;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'system',
    'user',
    'field',
    'text',
    'filter',
    'node',
    'file',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installEntitySchema('user');
    $this->installEntitySchema('node');
    $this->installSchema('node', ['node_access']);

    NodeType::create([
      'type' => 'offer',
      'name' => 'Offer',
    ])->save();

    FieldStorageConfig::create([
      'field_name' => 'field_business_id',
      'entity_type' => 'node',
      'type' => 'string',
      'cardinality' => 1,
    ])->save();
    FieldConfig::create([
      'field_name' => 'field_business_id',
      'entity_type' => 'node',
      'bundle' => 'offer',
      'label' => 'Business ID',
    ])->save();

    $fileSystem = $this->container->get('file_system');
    $this->stagingUri = 'public://crm/kernel_snapshot_post_import.xml';
    $directory = 'public://crm';
    $fileSystem->prepareDirectory($directory, FileSystemInterface::CREATE_DIRECTORY);
    $path = $fileSystem->realpath($this->stagingUri);
    $this->assertNotFalse($path);
    file_put_contents($path, $this->sampleXml());
  }

  public function testOfferPresentInSnapshotIsRepublishedAfterImport(): void {
    $inactiveOffer = Node::create([
      'type' => 'offer',
      'title' => 'Inactive but present',
      'field_business_id' => 'SNAP-1',
      'status' => 0,
    ]);
    $inactiveOffer->save();

    $policy = new TestSnapshotPostImportPolicy(reactivatePresent: TRUE);
    $this->dispatchPostImport($this->buildOfferMigration(), $policy);

    $inactiveOffer = Node::load($inactiveOffer->id());
    $this->assertNotNull($inactiveOffer);
    $this->assertTrue($inactiveOffer->isPublished());
  }

  private function dispatchPostImport(
    MigrationInterface $migration,
    ?ImportGovernanceSnapshotPostImportPolicyInterface $policy = NULL,
  ): void {
    $registry = $this->createMock(ImportGovernanceRegistry::class);
    $registry->method('getSnapshotPostImportPolicyForMigration')
      ->willReturn($policy ?? new TestSnapshotPostImportPolicy());

    $fileSystem = $this->container->get('file_system');
    $snapshotBuilder = new CrmXmlSnapshotBuilder(
      new CrmOfferXmlDocumentLoader(new XmlParseCacheService($fileSystem), $fileSystem),
    );

    $subscriber = new SnapshotMigrationPostImportSubscriber(
      $snapshotBuilder,
      $this->container->get('entity_type.manager'),
      $registry,
      new NullLogger(),
    );
    $subscriber->onPostImport(new MigrateImportEvent($migration, new MigrateMessage()));
  }

  private function buildOfferMigration(): MigrationInterface {
    $migration = $this->createMock(MigrationInterface::class);
    $migration->method('id')->willReturn('ps_offer_from_xml');
    $migration->method('getSourceConfiguration')->willReturn([
      'urls' => [$this->stagingUri],
    ]);

    return $migration;
  }

  private function sampleXml(): string {
    return <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<OFFERS_LIST>
  <OFFER>
    <BUSINESS_ID>SNAP-1</BUSINESS_ID>
    <TYPE_CODE>OFF</TYPE_CODE>
  </OFFER>
</OFFERS_LIST>
XML;
  }

}

/**
 * Test double for snapshot post-import policy decisions.
 */
final class TestSnapshotPostImportPolicy implements ImportGovernanceSnapshotPostImportPolicyInterface {

  public function __construct(
    private readonly bool $reactivatePresent = FALSE,
  ) {}

  public function getSupportedMigrationIds(): array {
    return ['ps_offer_from_xml'];
  }

  public function shouldReactivatePresentInSnapshot(): bool {
    return $this->reactivatePresent;
  }

  public function shouldDeactivateMissingEntity(EntityInterface $entity, bool $shouldBeActive): bool {
    if ($shouldBeActive) {
      return FALSE;
    }

    if ($entity instanceof Node) {
      return $entity->isPublished();
    }

    return (bool) $entity->get('status')->value;
  }

}
