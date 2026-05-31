<?php

declare(strict_types=1);

namespace Drupal\ps_dictionary\Service;

/**
 * Interface for importing dictionary entries from a CSV source.
 */
interface DictionaryCsvImporterInterface {

  /**
   * Imports entries from a CSV file.
   *
    * The CSV must have the columns: type,code,label,weight.
    *
    * Optional translation columns are supported with label_{langcode}
    * (for example: label_fr,label_en).
   *
   * @param string $filePath
   *   Absolute path to the CSV file.
   * @param string|null $filterType
   *   If set, only entries for this dictionary type are imported.
   *
   * @return array{imported: int, skipped: int, errors: string[]}
   *   Import result summary.
   */
  public function importFromCsv(string $filePath, ?string $filterType = NULL): array;

}
