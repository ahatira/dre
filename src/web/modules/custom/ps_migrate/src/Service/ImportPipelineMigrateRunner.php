<?php

declare(strict_types=1);

namespace Drupal\ps_migrate\Service;

use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\migrate\MigrateExecutable;
use Drupal\migrate\MigrateMessage;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate\Plugin\MigrationPluginManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Runs CRM migrate definitions for the import pipeline.
 */
final class ImportPipelineMigrateRunner {

  public function __construct(
    private readonly MigrationPluginManagerInterface $migrationPluginManager,
    private readonly ModuleHandlerInterface $moduleHandler,
    private readonly LoggerInterface $logger,
    private readonly ImportPipelineMigrationSourceAlterer $sourceAlterer,
    private readonly ImportPipelineMigrationOrderResolver $migrationOrderResolver,
  ) {}

  /**
   * Ensures migrate stack modules are enabled.
   */
  public function ensureModulesEnabled(): void {
    $this->moduleHandler->loadInclude('migrate', 'module');
    foreach (['migrate', 'migrate_plus', 'migrate_tools', 'ps_migrate'] as $module) {
      if (!$this->moduleHandler->moduleExists($module)) {
        throw new \RuntimeException(sprintf('Required module not enabled: %s', $module));
      }
    }
  }

  /**
   * Runs migrations for the given mode.
   *
   * @return array<string, mixed>
   *   Aggregate stats per migration.
   */
  public function run(string $mode, bool $update = TRUE): array {
    $this->ensureModulesEnabled();
    $order = $this->migrationOrderResolver->getOrder($mode);
    $stats = [
      'mode' => $mode,
      'migrations' => [],
      'failed' => FALSE,
      'error' => '',
    ];

    foreach ($order as $migrationId) {
      try {
        $stats['migrations'][$migrationId] = $this->importMigration($migrationId, $update);
      }
      catch (\Throwable $exception) {
        $stats['failed'] = TRUE;
        $stats['error'] = $exception->getMessage();
        $this->logger->error('Migration @id failed: @message', [
          '@id' => $migrationId,
          '@message' => $exception->getMessage(),
        ]);
        break;
      }
    }

    return $stats;
  }

  /**
   * Imports a single migration plugin.
   *
   * @return array<string, int|string>
   *   Result counters.
   */
  private function importMigration(string $migrationId, bool $update): array {
    /** @var \Drupal\migrate\Plugin\MigrationInterface|null $migration */
    $migration = $this->migrationPluginManager->createInstance($migrationId);
    if (!$migration instanceof MigrationInterface) {
      throw new \RuntimeException(sprintf('Migration not found: %s', $migrationId));
    }

    $this->sourceAlterer->applyPipelineOverrides($migration);

    if ($update) {
      $migration->getIdMap()->prepareUpdate();
    }

    $message = new MigrateMessage();
    $executable = new MigrateExecutable($migration, $message);
    $result = $executable->import();

    return [
      'result' => $result,
      'imported' => $migration->getIdMap()->importedCount(),
      'updated' => $migration->getIdMap()->updateCount(),
      'skipped' => $migration->getIdMap()->messageCount(),
      'failed' => $migration->getIdMap()->errorCount(),
    ];
  }

}
