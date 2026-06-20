<?php

declare(strict_types=1);

namespace Drupal\ps_migrate\Commands;

use Drupal\ps_migrate\Service\ImportPipeline;
use Drush\Attributes as CLI;
use Drush\Commands\DrushCommands;

/**
 * Drush commands for the CRM XML import pipeline.
 */
final class ImportPipelineCommands extends DrushCommands {

  public function __construct(
    private readonly ImportPipeline $importPipeline,
  ) {
    parent::__construct();
  }

  /**
   * Runs the CRM XML import pipeline (incoming → migrate → archive).
   */
  #[CLI\Command(name: 'ps:import:run', aliases: ['ps-import'])]
  #[CLI\Option(name: 'limit', description: 'Max XML files to process (0 = config default).')]
  #[CLI\Option(name: 'mode', description: 'Import mode: full or delta.')]
  #[CLI\Option(name: 'seed-sample', description: 'Seed dev sample XML into incoming/ when empty (1/0).')]
  #[CLI\Usage(name: 'drush ps:import:run', description: 'Process all pending XML files in incoming/.')]
  #[CLI\Usage(name: 'drush ps:import:run --limit=1 --mode=full', description: 'Process one file in full mode.')]
  public function run(array $options = ['limit' => NULL, 'mode' => NULL, 'seed-sample' => NULL]): void {
    if ((int) ($options['seed-sample'] ?? 0) === 1) {
      $sample = dirname(DRUPAL_ROOT, 2) . '/data/xml/bnppre_sample_100_per_type.xml';
      if ($this->importPipeline->seedSampleIfEmpty($sample)) {
        $this->logger()->notice('Seeded sample XML into incoming/.');
      }
    }

    $limit = isset($options['limit']) && $options['limit'] !== NULL
      ? (int) $options['limit']
      : NULL;
    $mode = isset($options['mode']) && $options['mode'] !== NULL && $options['mode'] !== ''
      ? (string) $options['mode']
      : NULL;

    $summary = $this->importPipeline->run($limit, $mode);

    if (($summary['processed'] ?? 0) === 0) {
      $this->io()->warning($summary['message'] ?? 'No files processed.');
      return;
    }

    foreach ($summary['runs'] as $run) {
      if (($run['status'] ?? '') === 'success') {
        $this->io()->success(sprintf('Imported %s', $run['filename']));
      }
      else {
        $this->io()->error(sprintf('Failed %s: %s', $run['filename'], $run['error'] ?? 'unknown'));
      }
    }

    $this->io()->writeln(sprintf(
      'Done: processed=%d success=%d failed=%d',
      $summary['processed'],
      $summary['success'],
      $summary['failed'],
    ));

    if (($summary['failed'] ?? 0) > 0) {
      throw new \RuntimeException('Import pipeline completed with failures.');
    }
  }

}
