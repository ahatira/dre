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
use Drupal\migrate\Plugin\MigrateIdMapInterface;
use Drupal\migrate\Plugin\MigrateSourceInterface;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\node\Entity\Node;
use Drupal\node\Entity\NodeType;
use Drupal\ps_core\Plugin\ImportGovernance\ImportGovernancePolicyInterface;
use Drupal\ps_core\Plugin\ImportGovernance\ImportGovernanceSnapshotPostImportPolicyInterface;
use Drupal\ps_core\Service\EntityProtectionManagerInterface;
use Drupal\ps_core\Service\ImportGovernanceRegistry;
use Drupal\ps_core\Service\ImportGovernanceSnapshotSynchronizer;
use Drupal\ps_migrate\EventSubscriber\SnapshotMigrationPostImportSubscriber;
use Drupal\ps_migrate\Service\CrmOfferXmlDocumentLoader;
use Drupal\ps_migrate\Service\CrmXmlSnapshotBuilder;
use Drupal\ps_migrate\Service\CrmXmlSnapshotDestinationNormalizer;
use Drupal\ps_migrate\Service\CrmXmlSnapshotMigrationProjector;
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

  /**
   *
   */
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

  /**
   *
   */
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
    $projector = new CrmXmlSnapshotMigrationProjector(
      new CrmXmlSnapshotDestinationNormalizer(),
    );
    $synchronizer = new ImportGovernanceSnapshotSynchronizer(
      $this->createMock(EntityProtectionManagerInterface::class),
    );

    $subscriber = new SnapshotMigrationPostImportSubscriber(
      $snapshotBuilder,
      $projector,
      $this->container->get('entity_type.manager'),
      $registry,
      $synchronizer,
      new NullLogger(),
    );
    $subscriber->onPostImport(new MigrateImportEvent($migration, new MigrateMessage()));
  }

  /**
   *
   */
  private function buildOfferMigration(): MigrationInterface {
    $source = $this->createMock(MigrateSourceInterface::class);
    $source->method('rewind');
    $source->method('valid')->willReturn(FALSE);

    $idMap = $this->createMock(MigrateIdMapInterface::class);
    $idMap->method('setMessage');

    $migration = $this->createMock(MigrationInterface::class);
    $migration->method('id')->willReturn('ps_offer_from_xml');
    $migration->method('getSourceConfiguration')->willReturn([
      'urls' => [$this->stagingUri],
    ]);
    $migration->method('getSourcePlugin')->willReturn($source);
    $migration->method('getIdMap')->willReturn($idMap);
    $migration->method('getProcessPlugins')->willReturn([]);

    return $migration;
  }

  /**
   *
   */
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
final class TestSnapshotPostImportPolicy implements ImportGovernancePolicyInterface, ImportGovernanceSnapshotPostImportPolicyInterface {

  public function __construct(
    private readonly bool $reactivatePresent = FALSE,
  ) {}

  /**
   *
   */
  public function getAdminLabel(): string {
    return 'Test offer policy';
  }

  /**
   *
   */
  public function getAdminDescription(): string {
    return 'Test policy for kernel snapshot tests.';
  }

  /**
   *
   */
  public function getSettingsRouteName(): ?string {
    return NULL;
  }

  /**
   *
   */
  public function getWeight(): int {
    return 0;
  }

  /**
   *
   */
  public function getEntityTypeIds(): array {
    return ['node'];
  }

  /**
   *
   */
  public function getBundleIds(): array {
    return ['offer'];
  }

  /**
   *
   */
  public function shouldSkipProtectedRow(EntityInterface $entity): bool {
    return FALSE;
  }

  /**
   *
   */
  public function shouldPreserveProtectedFields(EntityInterface $entity): bool {
    return FALSE;
  }

  /**
   *
   */
  public function resolveEffectiveLockStrategy(string $entityTypeId): string {
    return 'log_only';
  }

  /**
   *
   */
  public function getAdditionalPreservedProperties(EntityInterface $entity): array {
    return [];
  }

  /**
   *
   */
  public function getPluginId(): string {
    return 'test_offer';
  }

  /**
   * {@inheritdoc}
   */
  public function getPluginDefinition(): array {
    return [];
  }

  /**
   *
   */
  public function getSupportedMigrationIds(): array {
    return ['ps_offer_from_xml'];
  }

  /**
   *
   */
  public function shouldReactivatePresentInSnapshot(): bool {
    return $this->reactivatePresent;
  }

  /**
   *
   */
  public function shouldDeactivateMissingEntity(EntityInterface $entity, bool $shouldBeActive): bool {
    if ($shouldBeActive) {
      return FALSE;
    }

    if ($entity instanceof Node) {
      return $entity->isPublished();
    }

    return (bool) $entity->get('status')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function getSnapshotFieldSyncEntityKeys(): array {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getSnapshotFieldSyncFields(string $entityKey): array {
    return [];
  }

}
