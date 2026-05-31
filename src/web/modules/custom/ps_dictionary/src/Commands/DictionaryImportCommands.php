<?php

declare(strict_types=1);

namespace Drupal\ps_dictionary\Commands;

use Drupal\ps_dictionary\Service\DictionaryCsvImporterInterface;
use Drush\Attributes as CLI;
use Drush\Commands\DrushCommands;

/**
 * Drush commands for ps_dictionary CSV import.
 */
final class DictionaryImportCommands extends DrushCommands {

  public function __construct(
    private readonly DictionaryCsvImporterInterface $csvImporter,
  ) {
    parent::__construct();
  }

  /**
   * Import dictionary entries from a CSV file.
   *
    * The CSV must have a header row with columns: type,code,label,weight.
    * Optional translation columns are supported with label_{langcode}.
   * If no file path is given, the module's default fixture is used.
   */
  #[CLI\Command(name: 'ps:dictionary:import', aliases: ['ps-di'])]
  #[CLI\Argument(name: 'file', description: 'Absolute path to the CSV file. Defaults to the module fixture.')]
  #[CLI\Option(name: 'type', description: 'Restrict import to entries of this dictionary type.')]
  #[CLI\Usage(name: 'drush ps:dictionary:import', description: 'Import all entries from the default fixture.')]
  #[CLI\Usage(name: 'drush ps:dictionary:import /path/to/file.csv --type=asset_type', description: 'Import only asset_type entries from a custom file.')]
  public function import(string $file = '', array $options = ['type' => NULL]): void {
    if ($file === '') {
      $file = $this->defaultFixturePath();
    }

    $this->io()->title(sprintf('Importing dictionary entries from: %s', $file));

    $result = $this->csvImporter->importFromCsv($file, $options['type'] ?: NULL);

    foreach ($result['errors'] as $error) {
      $this->io()->warning($error);
    }

    $this->io()->success(sprintf(
      'Done — imported: %d, skipped: %d, errors: %d',
      $result['imported'],
      $result['skipped'],
      count($result['errors']),
    ));
  }

  /**
   * Returns the path to the default bundled fixture CSV.
   */
  private function defaultFixturePath(): string {
    // Resolve relative to this file: ../../data/dictionary_entries.csv
    return dirname(__DIR__, 2) . '/data/dictionary_entries.csv';
  }

}
