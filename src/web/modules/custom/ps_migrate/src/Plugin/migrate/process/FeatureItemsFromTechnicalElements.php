<?php

declare(strict_types=1);

namespace Drupal\ps_migrate\Plugin\migrate\process;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate\Row;
use Drupal\ps_migrate\Service\CanonicalCountryLanguageResolver;
use Drupal\ps_migrate\Service\FeatureImportResolver;
use Drupal\ps_migrate\Service\FeatureOfferValueImportHandler;
use Drupal\ps_migrate\Service\FeatureTechnicalElementParser;
use Drupal\ps_migrate\Service\FeatureTechnicalElementValidator;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Converts technical elements into feature field items for offers.
 *
 * @MigrateProcessPlugin(
 *   id = "feature_items_from_technical_elements"
 * )
 */
final class FeatureItemsFromTechnicalElements extends ProcessPluginBase implements ContainerFactoryPluginInterface {

  /**
   * Constructs a FeatureItemsFromTechnicalElements object.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    private readonly FeatureTechnicalElementParser $parser,
    private readonly FeatureImportResolver $importResolver,
    private readonly FeatureTechnicalElementValidator $validator,
    private readonly FeatureOfferValueImportHandler $offerValueImportHandler,
    private readonly CanonicalCountryLanguageResolver $resolver,
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
      $container->get('ps_migrate.feature_technical_element_parser'),
      $container->get('ps_migrate.feature_import_resolver'),
      $container->get('ps_migrate.feature_technical_element_validator'),
      $container->get('ps_migrate.feature_offer_value_import_handler'),
      $container->get('ps_migrate.canonical_country_language_resolver'),
      $container->get('logger.channel.ps_migrate'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property): array {
    $nodes = $this->normalizeInput($value);
    if ($nodes === []) {
      return [];
    }

    $items = [];
    $index = 0;
    $countrySource = (string) ($this->configuration['country_source'] ?? '');
    $canonicalXmlLanguage = $this->resolver->resolvePreferredXmlLanguage($countrySource !== '' ? (string) $row->getSourceProperty($countrySource) : NULL);
    foreach ($nodes as $node) {
      $element = $this->normalizeNode($node, $index, $canonicalXmlLanguage);
      $index++;
      if ($element === NULL) {
        continue;
      }

      $validation = $this->validator->validate([
        'group_code' => $element['group_code'] ?? '',
        'feature_code' => $element['feature_code'] ?? '',
        'definition_id' => $this->importResolver->buildDefinitionId((string) ($element['feature_code'] ?? '')),
        'type_driver' => (string) ($element['type_driver'] ?? ''),
        'payload' => is_array($element['payload'] ?? NULL) ? $element['payload'] : [],
      ]);
      if ($validation['errors'] !== []) {
        $codes = implode(', ', array_column($validation['errors'], 'code'));
        $this->logger->warning('Skipping feature item due to validation errors: @codes', ['@codes' => $codes]);
        continue;
      }

      if ($this->isTemplatePlaceholder((string) ($element['group_code'] ?? '')) || $this->isTemplatePlaceholder((string) ($element['feature_code'] ?? ''))) {
        continue;
      }

      $definition_id = $this->importResolver->buildDefinitionId((string) ($element['feature_code'] ?? ''));
      $definition = $this->offerValueImportHandler->resolveDefinitionForOfferItem($element);
      if (!$definition) {
        continue;
      }

      $this->offerValueImportHandler->syncDefinitionLabel(
        $definition_id,
        $canonicalXmlLanguage,
        (string) ($element['label'] ?? ''),
      );

      $items[] = [
        'feature_definition_id' => $definition_id,
        'payload' => json_encode($this->buildPayload($element, (string) $definition->getTypeDriver()), JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION),
      ];
    }

    return $items;
  }

  /**
   * Normalizes the raw source value into a node list.
   */
  private function normalizeInput(mixed $value): array {
    if ($value === NULL || $value === '' || $value === []) {
      return [];
    }

    if ($value instanceof \SimpleXMLElement) {
      return $value->count() > 0 ? iterator_to_array($value, FALSE) : [$value];
    }

    if (is_array($value)) {
      return array_is_list($value) ? $value : [$value];
    }

    return [$value];
  }

  /**
   * Normalizes a technical element row.
   */
  private function normalizeNode(mixed $node, int $index, string $preferredLanguage): ?array {
    if ($node instanceof \SimpleXMLElement) {
      $element = $this->parser->normalizeNode($node, $index, $preferredLanguage);
      $record = $element->toRecord();
      if ($this->isTemplatePlaceholder((string) ($record['group_code'] ?? '')) || $this->isTemplatePlaceholder((string) ($record['feature_code'] ?? ''))) {
        return NULL;
      }

      $label = (string) ($record['label'] ?? '');
      if ($this->isTemplatePlaceholder($label)) {
        $label = '';
      }

      $payload = is_array($record['payload'] ?? NULL) ? $record['payload'] : [];
      foreach (['value', 'unit', 'complement'] as $payloadKey) {
        if (isset($payload[$payloadKey]) && $this->isTemplatePlaceholder((string) $payload[$payloadKey])) {
          $payload[$payloadKey] = NULL;
        }
      }

      return [
        'group_code' => $record['group_code'],
        'feature_code' => $record['feature_code'],
        'label' => $label,
        'payload' => $payload,
        'source_index' => $record['source_index'],
        'type_driver' => $this->guessTypeDriverFromPayload($payload),
      ];
    }

    if (!is_array($node)) {
      return NULL;
    }

    $group_code = $this->extractCode($node, ['group_code', 'CODE_GROUP', 'GROUP_CODE']);
    $feature_code = $this->extractCode($node, ['feature_code', 'CODE_ELEMENT', 'ELEMENT_CODE']);
    if ($this->isTemplatePlaceholder($group_code)) {
      $group_code = '';
    }
    if ($this->isTemplatePlaceholder($feature_code)) {
      return NULL;
    }

    if ($feature_code === '') {
      return NULL;
    }

    $value = $this->extractScalar($node, ['value', 'VALUE']);
    $unit = $this->extractScalar($node, ['unit', 'UNIT']);
    $complement = $this->extractScalar($node, ['complement', 'ML_COMPLEMENT']);
    if ($this->isTemplatePlaceholder($value)) {
      $value = '';
    }
    if ($this->isTemplatePlaceholder($unit)) {
      $unit = '';
    }
    if ($this->isTemplatePlaceholder($complement)) {
      $complement = '';
    }

    $label = $this->extractScalar($node, ['label', 'ML_LABEL']) ?: $feature_code;
    if ($this->isTemplatePlaceholder($label)) {
      $label = $feature_code;
    }

    return [
      'group_code' => $group_code,
      'feature_code' => $feature_code,
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
  }

  /**
   * Extracts a normalized code value from nested source data.
   */
  private function extractCode(array $node, array $candidates): string {
    $value = $this->extractScalar($node, $candidates);
    return strtoupper(trim($value));
  }

  /**
   * Extracts the first scalar value for one of the candidate keys.
   */
  private function extractScalar(array $node, array $candidates): string {
    foreach ($candidates as $candidate) {
      $raw = $this->extractByKey($node, $candidate);
      if ($raw === NULL) {
        continue;
      }

      $value = trim($raw);
      if ($value !== '') {
        return $value;
      }
    }

    return '';
  }

  /**
   * Finds a key in nested arrays and returns its first scalar value.
   */
  private function extractByKey(array $node, string $key): ?string {
    if (array_key_exists($key, $node)) {
      return $this->mixedToScalar($node[$key]);
    }

    foreach ($node as $value) {
      if (is_array($value)) {
        $found = $this->extractByKey($value, $key);
        if ($found !== NULL) {
          return $found;
        }
      }
      elseif ($value instanceof \SimpleXMLElement) {
        $arrayValue = (array) $value;
        $found = $this->extractByKey($arrayValue, $key);
        if ($found !== NULL) {
          return $found;
        }
      }
    }

    return NULL;
  }

  /**
   * Converts nested values into the first scalar string found.
   */
  private function mixedToScalar(mixed $value): ?string {
    if ($value instanceof \SimpleXMLElement) {
      $stringValue = trim((string) $value);
      return $stringValue !== '' ? $stringValue : NULL;
    }

    if (is_scalar($value)) {
      return (string) $value;
    }

    if (!is_array($value)) {
      return NULL;
    }

    foreach ($value as $nested) {
      $scalar = $this->mixedToScalar($nested);
      if ($scalar !== NULL && trim($scalar) !== '') {
        return $scalar;
      }
    }

    return NULL;
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
   * Builds a payload for the destination feature field.
   */
  private function buildPayload(array $element, string $typeDriver): array {
    $payload = $element['payload'] ?? [];
    $value = $payload['value'] ?? NULL;
    $unit = $payload['unit'] ?? NULL;
    $complement = $payload['complement'] ?? NULL;

    $result = [];

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
      elseif ($value !== NULL) {
        $result['value'] = (string) $value;
      }

      if ($unit !== NULL && trim((string) $unit) !== '') {
        $result['unit'] = trim((string) $unit);
      }
    }
    else {
      if ($value !== NULL && trim((string) $value) !== '') {
        $result['value'] = trim((string) $value);
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
