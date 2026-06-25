<?php

declare(strict_types=1);

namespace Drupal\ps_feature\Service;

/**
 * Interface for importing feature catalogue definitions from a CSV source.
 */
interface FeatureCatalogueCsvImporterInterface {

  /**
   * Imports feature definitions from a business CSV file.
   *
   * Required columns: code, categorie, libelle, type_valeur.
   * Optional columns: description, unite, ordre, filtre_recherche.
   * Translation columns: libelle_{langcode}, description_{langcode}.
   *
   * @param string $filePath
   *   Absolute path to the CSV file.
   * @param bool $dryRun
   *   When TRUE, validates rows without persisting entities.
   *
   * @return array{imported: int, skipped: int, errors: string[], dry_run: bool}
   *   Import result summary.
   */
  public function importFromCsv(string $filePath, bool $dryRun = FALSE): array;

}
