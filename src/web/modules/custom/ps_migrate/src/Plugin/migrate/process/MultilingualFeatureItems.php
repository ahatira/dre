<?php

declare(strict_types=1);

namespace Drupal\ps_migrate\Plugin\migrate\process;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate\Row;
use Drupal\ps_migrate\Service\FeatureImportResolver;
use Drupal\ps_migrate\Service\FeatureOfferValueImportHandler;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Converts technical elements into multilingual feature field items.
 *
 * This plugin extracts ML_LABEL and ML_COMPLEMENT values for a specific language.
 *
 * Configuration:
 * @code
 * field_features:
 *   plugin: multilingual_feature_items
 *   source: technical_elements
 *   language: EN  # or FR, NL, ES, IT, PL, DE
 * @endcode
 *
 * @MigrateProcessPlugin(
 *   id = "multilingual_feature_items"
 * )
 */
final class MultilingualFeatureItems extends ProcessPluginBase implements ContainerFactoryPluginInterface {

  /**
   * Constructs a MultilingualFeatureItems object.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    private readonly FeatureImportResolver $importResolver,
    private readonly FeatureOfferValueImportHandler $offerValueImportHandler,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition, ?MigrationInterface $migration = NULL): self {
    return new self(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('ps_migrate.feature_import_resolver'),
      $container->get('ps_migrate.feature_offer_value_import_handler'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property): array {
    [$technicalElementsNode, $languageFromInput] = $this->normalizeInputs($value);
    if (!$technicalElementsNode instanceof \SimpleXMLElement) {
      return [];
    }

    $language = $languageFromInput !== '' ? $languageFromInput : strtoupper((string) ($this->configuration['language'] ?? 'FR'));
    $elements = $technicalElementsNode->xpath('TECHNICAL_ELEMENT');
    if (empty($elements)) {
      return [];
    }

    $items = [];
    foreach ($elements as $index => $xmlElement) {
      $groupCode = $this->extractText($xmlElement, 'CODE_GROUP');
      $featureCode = $this->extractText($xmlElement, 'CODE_ELEMENT');

      if ($this->isTemplatePlaceholder($groupCode) || $this->isTemplatePlaceholder($featureCode)) {
        continue;
      }

      if ($featureCode === '') {
        continue;
      }

      $definitionId = $this->importResolver->buildDefinitionId($featureCode);
      $label = $this->extractMultilingualLabel($xmlElement, 'ML_LABEL', 'LABEL', $language);
      $complement = $this->extractMultilingualLabel($xmlElement, 'ML_COMPLEMENT', 'COMPLEMENT', $language);
      $value = $this->extractText($xmlElement, 'VALUE');
      $unit = $this->extractText($xmlElement, 'UNIT');

      $normalizedElement = [
        'group_code' => $groupCode,
        'feature_code' => $featureCode,
        'label' => $label,
        'payload' => [
          'value' => $value,
          'unit' => $unit,
          'complement' => $complement,
        ],
        'source_index' => $index,
        'type_driver' => $this->guessTypeDriverFromPayload([
          'value' => $value,
          'unit' => $unit,
        ]),
      ];

      $definition = $this->offerValueImportHandler->resolveDefinitionForOfferItem($normalizedElement);
      if (!$definition) {
        continue;
      }

      if ($this->isTemplatePlaceholder($label)) {
        $label = '';
      }
      if ($this->isTemplatePlaceholder($complement)) {
        $complement = '';
      }
      if ($this->isTemplatePlaceholder($value)) {
        $value = '';
      }
      if ($this->isTemplatePlaceholder($unit)) {
        $unit = '';
      }

      $this->offerValueImportHandler->syncDefinitionLabel($definitionId, $language, $label);

      $payload = $this->buildPayload([
        'label' => $label,
        'value' => $value,
        'unit' => $unit,
        'complement' => $complement,
      ], (string) $definition->getTypeDriver());

      $items[] = [
        'feature_definition_id' => $definitionId,
        'payload' => json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION),
      ];
    }

    return $items;
  }

  /**
   * Normalizes plugin inputs.
   *
   * @return array{0:mixed,1:string}
   *   Technical elements node and target XML language.
   */
  private function normalizeInputs(mixed $value): array {
    if (is_array($value)) {
      return [
        $value[0] ?? NULL,
        strtoupper(trim((string) ($value[1] ?? ''))),
      ];
    }

    return [$value, ''];
  }

  /**
   * Extracts a text value from an XML element.
   */
  private function extractText(\SimpleXMLElement $element, string $childName): string {
    $children = $element->xpath($childName);
    if (empty($children)) {
      return '';
    }
    return trim((string) $children[0]);
  }

  /**
   * Extracts a multilingual label from ML_LABEL or ML_COMPLEMENT structure.
   */
  private function extractMultilingualLabel(\SimpleXMLElement $element, string $containerName, string $entryName, string $language): string {
    $containers = $element->xpath($containerName);
    if (empty($containers)) {
      return '';
    }

    $container = $containers[0];
    $entries = $container->xpath("{$entryName}[@LANGUAGE='{$language}']");
    if (!empty($entries)) {
      $text = trim((string) $entries[0]);
      if ($text !== '' && !$this->isTemplatePlaceholder($text)) {
        return $text;
      }
    }

    // Fallback to first non-empty entry.
    $allEntries = $container->xpath($entryName);
    foreach ($allEntries as $entry) {
      $text = trim((string) $entry);
      if ($text !== '' && !$this->isTemplatePlaceholder($text)) {
        return $text;
      }
    }

    return '';
  }

  /**
   * Builds a payload for the destination feature field.
   */
  private function buildPayload(array $data, string $typeDriver): array {
    $label = $data['label'] ?? NULL;
    $value = $data['value'] ?? NULL;
    $unit = $data['unit'] ?? NULL;
    $complement = $data['complement'] ?? NULL;

    $result = [];

    // Add translated label to payload.
    if ($label !== NULL && trim((string) $label) !== '') {
      $result['label'] = trim((string) $label);
    }

    if ($typeDriver === 'flag') {
      // CRM technical elements mapped to flag features are considered present
      // by default when imported.
      $result['present'] = TRUE;
    }
    elseif ($typeDriver === 'yes_no') {
      $result['value'] = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
      if ($result['value'] === NULL) {
        $result['value'] = FALSE;
      }
    }
    elseif ($typeDriver === 'numeric' || $typeDriver === 'range') {
      if ($value !== NULL && is_numeric(str_replace(',', '.', (string) $value))) {
        $result['value'] = (float) str_replace(',', '.', (string) $value);
      }
      if ($unit !== NULL && trim((string) $unit) !== '') {
        $result['unit'] = trim((string) $unit);
      }
    }
    else {
      if ($value !== NULL && trim((string) $value) !== '') {
        $result['value'] = trim((string) $value);
      }
      if ($unit !== NULL && trim((string) $unit) !== '') {
        $result['unit'] = trim((string) $unit);
      }
    }

    if ($complement !== NULL && trim((string) $complement) !== '') {
      $result['complement'] = trim((string) $complement);
    }

    return $result;
  }

  /**
   * Guesses a type driver from payload shape.
   */
  private function guessTypeDriverFromPayload(array $payload): string {
    $value = $payload['value'] ?? NULL;
    $unit = $payload['unit'] ?? NULL;

    if ($value === NULL && $unit === NULL) {
      return 'flag';
    }

    if ($value !== NULL && is_numeric(str_replace(',', '.', (string) $value))) {
      return 'numeric';
    }

    if ($value !== NULL && preg_match('/^(yes|no|true|false|oui|non|0|1)$/i', (string) $value) === 1) {
      return 'yes_no';
    }

    return 'text';
  }

  /**
   * Returns true when value looks like a CRM template token ({{TOKEN}}).
   */
  private function isTemplatePlaceholder(string $value): bool {
    $value = trim($value);
    if ($value === '') {
      return FALSE;
    }

    return str_contains($value, '{{') && str_contains($value, '}}');
  }

}
