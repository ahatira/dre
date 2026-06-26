<?php

declare(strict_types=1);

namespace Drupal\ps_dictionary\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\language\ConfigurableLanguageManagerInterface;

/**
 * Imports dictionary entries from a CSV file.
 *
 * CSV format (header row required): type,code,label,weight
 * Existing entries are updated (code+type as unique key).
 */
final class DictionaryCsvImporter implements DictionaryCsvImporterInterface {

  public function __construct(
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly LanguageManagerInterface $languageManager,
    private readonly DictionaryImportGovernance $importGovernance,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function importFromCsv(string $filePath, ?string $filterType = NULL): array {
    $result = ['imported' => 0, 'skipped' => 0, 'errors' => []];

    if (!file_exists($filePath) || !is_readable($filePath)) {
      $result['errors'][] = sprintf('File not found or not readable: %s', $filePath);
      return $result;
    }

    $handle = fopen($filePath, 'r');
    if ($handle === FALSE) {
      $result['errors'][] = sprintf('Cannot open file: %s', $filePath);
      return $result;
    }

    // Read and validate header.
    $header = fgetcsv($handle);
    if ($header === FALSE || $header === NULL) {
      fclose($handle);
      $result['errors'][] = 'CSV file is empty.';
      return $result;
    }
    $header = array_map('trim', $header);
    $required = ['type', 'code', 'label', 'weight'];
    $missing = array_diff($required, $header);
    if (!empty($missing)) {
      fclose($handle);
      $result['errors'][] = sprintf('Missing columns: %s', implode(', ', $missing));
      return $result;
    }
    $colIndex = array_flip($header);
    $translationColumns = $this->extractTranslationColumns($header);

    $storage = $this->entityTypeManager->getStorage('ps_dictionary_entry');
    $typeStorage = $this->entityTypeManager->getStorage('ps_dictionary_type');
    $canWriteLanguageOverrides = $this->languageManager instanceof ConfigurableLanguageManagerInterface;
    $availableLanguages = $this->languageManager->getLanguages();

    $row = 1;
    while (($line = fgetcsv($handle)) !== FALSE) {
      $row++;
      if (count($line) < count($required)) {
        $result['errors'][] = sprintf('Row %d: too few columns, skipped.', $row);
        $result['skipped']++;
        continue;
      }

      $type = trim($line[$colIndex['type']]);
      $code = strtoupper(trim($line[$colIndex['code']]));
      $label = trim($line[$colIndex['label']]);
      $weight = (int) $line[$colIndex['weight']];

      if ($type === '' || $code === '' || $label === '') {
        $result['errors'][] = sprintf('Row %d: type, code or label is empty, skipped.', $row);
        $result['skipped']++;
        continue;
      }

      if ($filterType !== NULL && $type !== $filterType) {
        $result['skipped']++;
        continue;
      }

      // Ensure the type exists.
      if (!$typeStorage->load($type)) {
        $result['errors'][] = sprintf('Row %d: dictionary type "%s" does not exist, skipped.', $row, $type);
        $result['skipped']++;
        continue;
      }

      // Build the canonical entry ID (type.code_lower).
      $entryId = $type . '.' . strtolower($code);

      /** @var \Drupal\ps_dictionary\Entity\DictionaryEntryInterface|null $existing */
      $existing = $storage->load($entryId);
      if ($existing) {
        if (!$this->importGovernance->shouldPreserveExistingLabelsOnCsvImport()) {
          $existing->set('label', $label);
        }
        $existing->set('weight', $weight);
        $existing->save();
      }
      else {
        $storage->create([
          'id' => $entryId,
          'type' => $type,
          'code' => $code,
          'label' => $label,
          'weight' => $weight,
        ])->save();
      }

      if ($canWriteLanguageOverrides && !empty($translationColumns)) {
        $this->applyTranslations(
          entryId: $entryId,
          line: $line,
          colIndex: $colIndex,
          translationColumns: $translationColumns,
          availableLanguages: $availableLanguages,
        );
      }

      $result['imported']++;
    }

    fclose($handle);
    return $result;
  }

  /**
   * @param string[] $header
   *
   * @return array<string, string>
   *   Map of CSV column name => langcode (for columns label_{langcode}).
   */
  private function extractTranslationColumns(array $header): array {
    $translationColumns = [];

    foreach ($header as $column) {
      if (preg_match('/^label_([a-z0-9_\-]+)$/i', $column, $matches) === 1) {
        $translationColumns[$column] = strtolower($matches[1]);
      }
    }

    return $translationColumns;
  }

  /**
   * @param string[] $line
   * @param array<string, int> $colIndex
   * @param array<string, string> $translationColumns
   * @param array<string, \Drupal\Core\Language\LanguageInterface> $availableLanguages
   */
  private function applyTranslations(
    string $entryId,
    array $line,
    array $colIndex,
    array $translationColumns,
    array $availableLanguages,
  ): void {
    if (!$this->languageManager instanceof ConfigurableLanguageManagerInterface) {
      return;
    }

    $configName = 'ps_dictionary.entry.' . $entryId;

    foreach ($translationColumns as $columnName => $langcode) {
      $translatedLabel = trim((string) ($line[$colIndex[$columnName]] ?? ''));
      if ($translatedLabel === '') {
        continue;
      }

      if (!isset($availableLanguages[$langcode])) {
        continue;
      }

      $override = $this->languageManager->getLanguageConfigOverride($langcode, $configName);
      $override->set('label', $translatedLabel);
      $override->save();
    }
  }

}
