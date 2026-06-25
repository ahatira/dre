<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_migrate\Kernel;

use Drupal\ps_migrate\Service\ImportRunSnapshotCollector;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;

/**
 * Kernel tests for import run snapshot collection.
 */
#[Group('ps_migrate')]
#[RunTestsInSeparateProcesses]
final class ImportRunSnapshotCollectorKernelTest extends PsMigrateKernelTestBase {

  private ImportRunSnapshotCollector $collector;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->collector = $this->container->get('ps_migrate.import_run_snapshot_collector');
  }

  public function testSnapshotTracksCreatedAndUpdatedOffers(): void {
    $this->collector->begin(7, 'delta');
    $this->collector->recordOfferCreated(101, 'BID-101');
    $this->collector->stageOfferUpdate(202, 9001, 'BID-202');

    $snapshot = $this->collector->buildSnapshot();
    $this->assertSame(1, $snapshot['version']);
    $this->assertSame(7, $snapshot['import_run_id']);
    $this->assertSame('delta', $snapshot['import_mode']);
    $this->assertCount(1, $snapshot['offers']['created']);
    $this->assertCount(1, $snapshot['offers']['updated']);
    $this->assertSame(9001, $snapshot['offers']['updated'][0]['revision_id']);
  }

  public function testUpdatedOfferReplacesCreatedEntry(): void {
    $this->collector->begin(8, 'full');
    $this->collector->recordOfferCreated(303, 'BID-303');
    $this->assertTrue($this->collector->hasStagedOfferUpdate(303) === FALSE);

    $this->collector->stageOfferUpdate(303, 8003, 'BID-303');
    $snapshot = $this->collector->buildSnapshot();

    $this->assertSame([], $snapshot['offers']['created']);
    $this->assertCount(1, $snapshot['offers']['updated']);
    $this->assertTrue($this->collector->hasStagedOfferUpdate(303));
  }

  public function testClearResetsCollector(): void {
    $this->collector->begin(9, 'delta');
    $this->collector->recordOfferCreated(1, 'X');
    $this->collector->clear();

    $this->assertSame([], $this->collector->buildSnapshot()['offers']['created'] ?? ['unexpected']);
  }

}
