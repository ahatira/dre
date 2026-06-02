<?php

declare(strict_types=1);

namespace Drupal\ps_migrate\Plugin\migrate\process;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\language\Config\LanguageConfigFactoryOverride;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate\Row;
use Drupal\ps_migrate\Service\FeatureMigrationKeyBuilder;
use Psr\Log\LoggerInterface;
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
    private readonly FeatureMigrationKeyBuilder $keyBuilder,
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly LanguageConfigFactoryOverride $languageConfigOverride,
    private readonly LanguageManagerInterface $languageManager,
    private readonly LoggerInterface $logger,
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
      $container->get('ps_migrate.feature_migration_key_builder'),
      $container->get('entity_type.manager'),
      $container->get('language.config_factory_override'),
      $container->get('language_manager'),
      $container->get('logger.channel.ps_migrate'),
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
    foreach ($elements as $index => $element) {
      $groupCode = $this->extractText($element, 'CODE_GROUP');
      $featureCode = $this->extractText($element, 'CODE_ELEMENT');

      if ($this->isTemplatePlaceholder($groupCode) || $this->isTemplatePlaceholder($featureCode)) {
        continue;
      }

      if ($groupCode === '' || $featureCode === '') {
        continue;
      }

      $definitionId = $this->keyBuilder->buildDefinitionId($groupCode, $featureCode);
      $definition = $this->entityTypeManager->getStorage('fb_feature_definition')->load($definitionId);
      if (!$definition) {
        $this->logger->warning('Skipping feature item for missing definition @definition_id', [
          '@definition_id' => $definitionId,
        ]);
        continue;
      }

      $label = $this->extractMultilingualLabel($element, 'ML_LABEL', 'LABEL', $language);
      $complement = $this->extractMultilingualLabel($element, 'ML_COMPLEMENT', 'COMPLEMENT', $language);
      $value = $this->extractText($element, 'VALUE');
      $unit = $this->extractText($element, 'UNIT');

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

      // Keep feature definition labels translated per language.
      $this->syncFeatureDefinitionTranslation($definitionId, $language, $label);

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
   * Writes a translated label override for a feature definition.
   */
  private function syncFeatureDefinitionTranslation(string $definitionId, string $language, string $label): void {
    if ($label === '' || $language === 'FR' || $this->isTemplatePlaceholder($label)) {
      return;
    }

    if (!$this->languageManager->getLanguage(strtolower($language))) {
      return;
    }

    $configName = 'ps_feature.feature_definition.' . $definitionId;
    $override = $this->languageConfigOverride->getOverride(strtolower($language), $configName);
    if ($override->get('label') === $label) {
      return;
    }

    $override->set('label', $label);
    $override->save();
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

    // Fallback to first non-empty entry
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

    // Add translated label to payload
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
