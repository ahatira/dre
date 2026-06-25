<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_migrate\Kernel;

use Drupal\ps_migrate\Entity\ImportRun;
use Drupal\ps_migrate\Entity\ImportRunInterface;
use Drupal\ps_migrate\Service\ImportPipelineRollbackService;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;

/**
 * Kernel tests for import run rollback eligibility.
 */
#[Group('ps_migrate')]
#[RunTestsInSeparateProcesses]
final class ImportPipelineRollbackKernelTest extends PsMigrateKernelTestBase {

  private ImportPipelineRollbackService $rollback;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->rollback = $this->container->get('ps_migrate.import_pipeline_rollback');
  }

  public function testCanRollbackRequiresSuccessSnapshotAndNoNewerRun(): void {
    $older = $this->createRun('older.xml', 1000, ImportRunInterface::STATUS_SUCCESS, [
      'offers' => ['created' => [], 'updated' => []],
    ]);
    $this->assertTrue($this->rollback->canRollback($older));

    $newer = $this->createRun('newer.xml', 2000, ImportRunInterface::STATUS_SUCCESS, [
      'offers' => ['created' => [], 'updated' => []],
    ]);
    $this->assertFalse($this->rollback->canRollback($older));
    $this->assertTrue($this->rollback->canRollback($older, TRUE));
    $this->assertTrue($this->rollback->canRollback($newer));
  }

  public function testCanRollbackRejectsFailedOrAlreadyRolledBack(): void {
    $failed = $this->createRun('failed.xml', 3000, ImportRunInterface::STATUS_FAILED, [
      'offers' => ['created' => [], 'updated' => []],
    ]);
    $this->assertFalse($this->rollback->canRollback($failed));

    $rolledBack = $this->createRun('done.xml', 4000, ImportRunInterface::STATUS_SUCCESS, [
      'offers' => ['created' => [], 'updated' => []],
    ]);
    $rolledBack->set('rollback_status', ImportRunInterface::ROLLBACK_ROLLED_BACK);
    $rolledBack->save();
    $this->assertFalse($this->rollback->canRollback($rolledBack));
  }

  /**
   * @param array<string, mixed> $snapshot
   */
  private function createRun(string $filename, int $started, string $status, array $snapshot): ImportRunInterface {
    $run = ImportRun::create([
      'filename' => $filename,
      'pipeline_status' => $status,
      'import_mode' => ImportRunInterface::MODE_DELTA,
      'started' => $started,
      'finished' => $started + 60,
      'snapshot' => json_encode($snapshot, JSON_THROW_ON_ERROR),
      'rollback_status' => ImportRunInterface::ROLLBACK_NONE,
    ]);
    $run->save();
    return $run;
  }

}
