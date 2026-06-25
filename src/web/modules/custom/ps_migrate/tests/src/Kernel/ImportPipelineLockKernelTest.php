<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_migrate\Kernel;

use Drupal\ps_migrate\Service\ImportPipelineLock;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;

/**
 * Kernel tests for the CRM import pipeline lock.
 */
#[Group('ps_migrate')]
#[RunTestsInSeparateProcesses]
final class ImportPipelineLockKernelTest extends PsMigrateKernelTestBase {

  private ImportPipelineLock $lock;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->lock = $this->container->get('ps_migrate.import_pipeline_lock');
  }

  public function testAcquireReleaseAndDoubleAcquire(): void {
    $this->assertTrue($this->lock->acquire('sample.xml', 0));
    $this->assertTrue($this->lock->isLocked());
    $this->assertFalse($this->lock->acquire('other.xml', 0));

    $this->lock->attachImportRunId(99);
    $payload = $this->lock->getLock();
    $this->assertSame(99, $payload['import_run_id'] ?? NULL);

    $this->lock->release();
    $this->assertFalse($this->lock->isLocked());
    $this->assertTrue($this->lock->acquire('sample.xml', 0));
  }

  public function testExpiredLockCanBeReacquired(): void {
    $this->assertTrue($this->lock->acquire('stale.xml', 0));

    $state = $this->container->get('state');
    $payload = $state->get('ps_migrate.import_pipeline.lock');
    $this->assertIsArray($payload);
    $payload['expires'] = 1;
    $state->set('ps_migrate.import_pipeline.lock', $payload);

    $this->assertTrue($this->lock->isStale());
    $this->assertTrue($this->lock->acquire('fresh.xml', 12));
    $this->assertSame(12, $this->lock->getLock()['import_run_id'] ?? NULL);
  }

}
