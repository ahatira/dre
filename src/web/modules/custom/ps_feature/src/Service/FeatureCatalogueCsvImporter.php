<?php

declare(strict_types=1);

namespace Drupal\ps_feature\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\language\ConfigurableLanguageManagerInterface;
use Drupal\ps_feature\Entity\FeatureDefinition;
use Drupal\ps_feature\Service\FeatureDefinitionSource;

/**
 * Imports feature catalogue definitions from a business CSV file.
 */
final class FeatureCatalogueCsvImporter implements FeatureCatalogueCsvImporterInterface {

  /**
   * Required CSV columns.
   *
   * @var string[]
   */
  private const REQUIRED_COLUMNS = [
    'code',
    'categorie',
    'libelle',
    'type_valeur',
  ];

  public function __construct(
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly LanguageManagerInterface $languageManager,
    private readonly FeatureCatalogueCsvMapper $mapper,
    private readonly FeatureTypeManager $featureTypeManager,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function importFromCsv(string $filePath, bool $dryRun = FALSE): array {
    $result = [
      'imported' => 0,
      'skipped' => 0,
      'errors' => [],
      'dry_run' => $dryRun,
    ];

    if (!file_exists($filePath) || !is_readable($filePath)) {
      $result['errors'][] = sprintf('File not found or not readable: %s', $filePath);
      return $result;
    }

    $handle = fopen($filePath, 'r');
    if ($handle === FALSE) {
      $result['errors'][] = sprintf('Cannot open file: %s', $filePath);
      return $result;
    }

    $header = fgetcsv($handle);
    if ($header === FALSE || $header === NULL) {
      fclose($handle);
      $result['errors'][] = 'CSV file is empty.';
      return $result;
    }

    $header = array_map('trim', $header);
    $missing = array_diff(self::REQUIRED_COLUMNS, $header);
    if ($missing !== []) {
      fclose($handle);
      $result['errors'][] = sprintf('Missing columns: %s', implode(', ', $missing));
      return $result;
    }

    $colIndex = array_flip($header);
    $labelTranslationColumns = $this->extractTranslationColumns($header, 'libelle');
    $descriptionTranslationColumns = $this->extractTranslationColumns($header, 'description');
    $availableTypes = array_keys($this->featureTypeManager->getAllTypes());
    $canWriteLanguageOverrides = $this->languageManager instanceof ConfigurableLanguageManagerInterface;
    $availableLanguages = $this->languageManager->getLanguages();
    $missingLanguageWarnings = [];

    if (!$canWriteLanguageOverrides && ($labelTranslationColumns !== [] || $descriptionTranslationColumns !== [])) {
      $result['errors'][] = 'Translation columns were ignored because language config overrides are not available.';
    }

    $definitionStorage = $this->entityTypeManager->getStorage('fb_feature_definition');
    $groupStorage = $this->entityTypeManager->getStorage('fb_feature_group');

    $rowNumber = 1;
    while (($line = fgetcsv($handle)) !== FALSE) {
      $rowNumber++;
      if ($this->isBlankRow($line)) {
        continue;
      }

      if (count($line) < count(self::REQUIRED_COLUMNS)) {
        $result['errors'][] = sprintf('Row %d: too few columns, skipped.', $rowNumber);
        $result['skipped']++;
        continue;
      }

      $code = $this->mapper->normalizeFeatureCode((string) ($line[$colIndex['code']] ?? ''));
      $categorie = trim((string) ($line[$colIndex['categorie']] ?? ''));
      $label = trim((string) ($line[$colIndex['libelle']] ?? ''));
      $typeValeur = trim((string) ($line[$colIndex['type_valeur']] ?? ''));

      if ($code === '' || $categorie === '' || $label === '' || $typeValeur === '') {
        $result['errors'][] = sprintf('Row %d: code, categorie, libelle or type_valeur is empty, skipped.', $rowNumber);
        $result['skipped']++;
        continue;
      }

      $groupId = $this->mapper->resolveCategory($categorie);
      if ($groupId === NULL) {
        $allowed = implode(', ', $this->mapper->getAllowedCategoryLabels());
        $result['errors'][] = sprintf('Row %d: unknown categorie "%s". Allowed values: %s.', $rowNumber, $categorie, $allowed);
        $result['skipped']++;
        continue;
      }

      if (!$groupStorage->load($groupId)) {
        $result['errors'][] = sprintf('Row %d: feature group "%s" does not exist, skipped.', $rowNumber, $groupId);
        $result['skipped']++;
        continue;
      }

      $typeDriver = $this->mapper->resolveTypeDriver($typeValeur);
      if ($typeDriver === NULL) {
        $allowed = implode(', ', $this->mapper->getAllowedValueTypeLabels());
        $result['errors'][] = sprintf('Row %d: unknown type_valeur "%s". Allowed values: %s.', $rowNumber, $typeValeur, $allowed);
        $result['skipped']++;
        continue;
      }

      if (!in_array($typeDriver, $availableTypes, TRUE)) {
        $result['errors'][] = sprintf('Row %d: type driver "%s" is not available, skipped.', $rowNumber, $typeDriver);
        $result['skipped']++;
        continue;
      }

      $definitionId = $this->mapper->normalizeDefinitionId($code);
      if ($definitionId === '') {
        $result['errors'][] = sprintf('Row %d: code "%s" could not be normalized, skipped.', $rowNumber, $code);
        $result['skipped']++;
        continue;
      }

      $description = $this->readOptionalCell($line, $colIndex, 'description');
      $unit = $this->readOptionalCell($line, $colIndex, 'unite');
      $weight = $this->parseOptionalInt($this->readOptionalCell($line, $colIndex, 'ordre'));
      $exposeAsFilter = $this->mapper->resolveExposeAsFilter($this->readOptionalCell($line, $colIndex, 'filtre_recherche'));

      $payloadDefaults = [];
      if ($typeDriver === 'numeric' && $unit !== '') {
        $payloadDefaults['unit'] = $unit;
      }

      if (!$dryRun) {
        /** @var \Drupal\ps_feature\Entity\FeatureDefinition|null $existing */
        $existing = $definitionStorage->load($definitionId);
        if ($existing instanceof FeatureDefinition) {
          $existing->set('label', $label);
          $existing->set('description', $description);
          $existing->set('code', $code);
          $existing->set('group', $groupId);
          if (!$existing->isTypeLocked()) {
            $existing->set('type_driver', $typeDriver);
          }
          $existing->set('weight', $weight);
          $existing->set('status', TRUE);
          $existing->set('expose_as_filter', $exposeAsFilter);
          $existing->set('payload_defaults', $payloadDefaults);
          $existing->setSource(FeatureDefinitionSource::BO);
          $existing->save();
        }
        else {
          $definitionStorage->create([
            'id' => $definitionId,
            'label' => $label,
            'description' => $description,
            'code' => $code,
            'group' => $groupId,
            'type_driver' => $typeDriver,
            'weight' => $weight,
            'status' => TRUE,
            'expose_as_filter' => $exposeAsFilter,
            'payload_defaults' => $payloadDefaults,
            'required_asset_types' => [],
            'source' => FeatureDefinitionSource::BO,
            'type_locked' => FALSE,
          ])->save();
        }

        if ($canWriteLanguageOverrides) {
          $this->applyTranslations(
            definitionId: $definitionId,
            line: $line,
            colIndex: $colIndex,
            labelTranslationColumns: $labelTranslationColumns,
            descriptionTranslationColumns: $descriptionTranslationColumns,
            availableLanguages: $availableLanguages,
            missingLanguageWarnings: $missingLanguageWarnings,
            result: $result,
            rowNumber: $rowNumber,
          );
        }
      }

      $result['imported']++;
    }

    fclose($handle);
    return $result;
  }

  /**
   * Extracts translation column names from the CSV header.
   *
   * @param string[] $header
   *   CSV header cells.
   * @param string $prefix
   *   Translation field prefix (libelle or description).
   *
   * @return array<string, string>
   *   Map of CSV column name to langcode.
   */
  private function extractTranslationColumns(array $header, string $prefix): array {
    $translationColumns = [];
    $pattern = '/^' . preg_quote($prefix, '/') . '_([a-z0-9_\-]+)$/i';

    foreach ($header as $column) {
      if (preg_match($pattern, $column, $matches) === 1) {
        $translationColumns[$column] = strtolower($matches[1]);
      }
    }

    return $translationColumns;
  }

  /**
   * Applies translated label and description overrides for one definition.
   *
   * @param string $definitionId
   *   Feature definition ID.
   * @param string[] $line
   *   CSV row values.
   * @param array<string, int> $colIndex
   *   Header to column index map.
   * @param array<string, string> $labelTranslationColumns
   *   Label translation columns.
   * @param array<string, string> $descriptionTranslationColumns
   *   Description translation columns.
   * @param array<string, \Drupal\Core\Language\LanguageInterface> $availableLanguages
   *   Enabled languages.
   * @param array<string, bool> $missingLanguageWarnings
   *   Deduped warning registry.
   * @param array{imported: int, skipped: int, errors: string[], dry_run: bool} $result
   *   Import result (errors appended in place).
   * @param int $rowNumber
   *   CSV row number for error messages.
   */
  private function applyTranslations(
    string $definitionId,
    array $line,
    array $colIndex,
    array $labelTranslationColumns,
    array $descriptionTranslationColumns,
    array $availableLanguages,
    array &$missingLanguageWarnings,
    array &$result,
    int $rowNumber,
  ): void {
    if (!$this->languageManager instanceof ConfigurableLanguageManagerInterface) {
      return;
    }

    $configName = 'ps_feature.feature_definition.' . $definitionId;

    foreach ([$labelTranslationColumns, $descriptionTranslationColumns] as $index => $columns) {
      $field = $index === 0 ? 'label' : 'description';
      foreach ($columns as $columnName => $langcode) {
        $translatedValue = trim((string) ($line[$colIndex[$columnName]] ?? ''));
        if ($translatedValue === '') {
          continue;
        }

        if (!isset($availableLanguages[$langcode])) {
          $key = $langcode . ':' . $field;
          if (!isset($missingLanguageWarnings[$key])) {
            $result['errors'][] = sprintf('Row %d: language "%s" is not available, %s translation skipped.', $rowNumber, $langcode, $field);
            $missingLanguageWarnings[$key] = TRUE;
          }
          continue;
        }

        $override = $this->languageManager->getLanguageConfigOverride($langcode, $configName);
        $override->set($field, $translatedValue);
        $override->save();
      }
    }
  }

  /**
   * Checks whether a CSV row is empty.
   *
   * @param string[] $line
   *   CSV row values.
   */
  private function isBlankRow(array $line): bool {
    foreach ($line as $cell) {
      if (trim((string) $cell) !== '') {
        return FALSE;
      }
    }

    return TRUE;
  }

  /**
   * Parses an optional integer CSV cell.
   *
   * @param mixed $value
   *   Raw cell value.
   *
   * @return int
   *   Parsed integer or zero.
   */
  private function parseOptionalInt(mixed $value): int {
    if (is_numeric($value)) {
      return (int) $value;
    }

    return 0;
  }

  /**
   * Reads an optional CSV cell by column name.
   *
   * @param string[] $line
   *   CSV row values.
   * @param array<string, int> $colIndex
   *   Header to column index map.
   * @param string $column
   *   Column name.
   *
   * @return string
   *   Trimmed cell value or empty string.
   */
  private function readOptionalCell(array $line, array $colIndex, string $column): string {
    if (!isset($colIndex[$column])) {
      return '';
    }

    return trim((string) ($line[$colIndex[$column]] ?? ''));
  }

}
