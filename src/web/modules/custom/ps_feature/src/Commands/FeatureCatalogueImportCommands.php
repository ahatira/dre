<?php

declare(strict_types=1);

namespace Drupal\ps_feature\Commands;

use Drupal\ps_feature\Service\FeatureCatalogueCsvImporterInterface;
use Drush\Attributes as CLI;
use Drush\Commands\DrushCommands;

/**
 * Drush commands for ps_feature catalogue CSV import.
 */
final class FeatureCatalogueImportCommands extends DrushCommands {

  public function __construct(
    private readonly FeatureCatalogueCsvImporterInterface $csvImporter,
  ) {
    parent::__construct();
  }

  /**
   * Import feature catalogue definitions from a business CSV file.
   */
  #[CLI\Command(name: 'ps:features:import-catalogue', aliases: ['ps-fci'])]
  #[CLI\Argument(name: 'file', description: 'Absolute path to the CSV file. Defaults to the module template.')]
  #[CLI\Option(name: 'dry-run', description: 'Validate the CSV without persisting changes.')]
  #[CLI\Usage(name: 'drush ps:features:import-catalogue', description: 'Import from the bundled template CSV.')]
  #[CLI\Usage(name: 'drush ps:features:import-catalogue /path/catalogue.csv --dry-run', description: 'Validate a custom CSV file.')]
  public function import(string $file = '', array $options = ['dry-run' => FALSE]): void {
    if ($file === '') {
      $file = $this->defaultFixturePath();
    }

    $dryRun = (bool) $options['dry-run'];
    $this->io()->title(sprintf(
      '%s feature catalogue from: %s',
      $dryRun ? 'Validating' : 'Importing',
      $file,
    ));

    $result = $this->csvImporter->importFromCsv($file, $dryRun);

    foreach ($result['errors'] as $error) {
      $this->io()->warning($error);
    }

    $this->io()->success(sprintf(
      'Done — valid rows: %d, skipped: %d, messages: %d%s',
      $result['imported'],
      $result['skipped'],
      count($result['errors']),
      $dryRun ? ' (dry run)' : '',
    ));
  }

  /**
   * Returns the path to the default bundled template CSV.
   */
  private function defaultFixturePath(): string {
    return dirname(__DIR__, 2) . '/data/feature_catalogue_import.template.csv';
  }

}
