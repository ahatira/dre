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

  /**
   * Full pipeline order (dependencies before offers).
   */
  private const FULL_ORDER = [
    'ps_agent_avatar_file_from_xml',
    'ps_agent_from_xml',
    'ps_feature_groups_from_xml',
    'ps_feature_definitions_from_xml',
    'ps_file_from_xml',
    'ps_media_from_xml',
    'ps_media_virtual_tour_from_xml',
    'ps_surface_division_from_xml',
    'ps_offer_from_xml',
    'ps_offer_translations_from_xml',
  ];

  /**
   * Delta pipeline order.
   */
  private const DELTA_ORDER = [
    'ps_offer_from_xml',
    'ps_offer_translations_from_xml',
  ];

  public function __construct(
    private readonly MigrationPluginManagerInterface $migrationPluginManager,
    private readonly ModuleHandlerInterface $moduleHandler,
    private readonly LoggerInterface $logger,
    private readonly ImportPipelineMigrationSourceAlterer $sourceAlterer,
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
    $order = $mode === 'delta' ? self::DELTA_ORDER : self::FULL_ORDER;
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
