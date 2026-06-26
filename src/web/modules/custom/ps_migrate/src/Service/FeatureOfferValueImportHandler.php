<?php

declare(strict_types=1);

namespace Drupal\ps_migrate\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\language\Config\LanguageConfigFactoryOverride;
use Drupal\ps_core\Plugin\ImportGovernance\ImportGovernanceCatalogueImportPolicyInterface;
use Drupal\ps_core\Service\ImportGovernanceRegistry;
use Drupal\ps_feature\Entity\FeatureDefinition;
use Drupal\ps_feature\Service\FeatureDefinitionSource;
use Psr\Log\LoggerInterface;

/**
 * Resolves catalogue definitions and label sync during offer feature imports.
 */
final class FeatureOfferValueImportHandler {

  public function __construct(
    private readonly FeatureImportResolver $importResolver,
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly ImportGovernanceRegistry $governanceRegistry,
    private readonly FeaturePayloadDefaultsNormalizer $payloadDefaultsNormalizer,
    private readonly LanguageConfigFactoryOverride $languageConfigOverride,
    private readonly LanguageManagerInterface $languageManager,
    private readonly LoggerInterface $logger,
  ) {}

  /**
   * Loads or optionally creates the catalogue definition for an offer item.
   *
   * @param array<string, mixed> $element
   *   Normalized technical element data.
   */
  public function resolveDefinitionForOfferItem(array $element): ?FeatureDefinition {
    $featureCode = strtoupper(trim((string) ($element['feature_code'] ?? '')));
    if ($featureCode === '') {
      return NULL;
    }

    $existing = $this->importResolver->loadDefinition($featureCode);
    if ($existing instanceof FeatureDefinition) {
      return $existing;
    }

    $definitionId = $this->importResolver->buildDefinitionId($featureCode);
    if ($this->catalogueImportPolicy()?->shouldCreateStubDefinitionForMissingOfferValue() ?? FALSE) {
      return $this->createStubDefinition($element, $featureCode, $definitionId);
    }

    $this->logger->warning('Skipping feature item for missing definition @definition_id', [
      '@definition_id' => $definitionId,
    ]);

    return NULL;
  }

  /**
   * Writes a translated label override when governance allows it.
   */
  public function syncDefinitionLabel(string $definitionId, string $xmlLanguage, string $label): void {
    if (!($this->catalogueImportPolicy()?->shouldSyncDefinitionLabelsFromOfferImport() ?? FALSE)) {
      return;
    }

    $label = trim($label);
    if ($label === '' || strtoupper($xmlLanguage) === 'FR' || $this->isTemplatePlaceholder($label)) {
      return;
    }

    $langcode = strtolower($xmlLanguage);
    if (!$this->languageManager->getLanguage($langcode)) {
      return;
    }

    $configName = 'ps_feature.feature_definition.' . $definitionId;
    $override = $this->languageConfigOverride->getOverride($langcode, $configName);
    if ($override->get('label') === $label) {
      return;
    }

    $override->set('label', $label);
    $override->save();
  }

  /**
   * Creates a minimal catalogue definition from an offer technical element.
   *
   * @param array<string, mixed> $element
   *   Normalized technical element data.
   * @param string $featureCode
   *   CRM feature code.
   * @param string $definitionId
   *   Normalized catalogue definition ID.
   */
  private function createStubDefinition(array $element, string $featureCode, string $definitionId): ?FeatureDefinition {
    $groupId = $this->importResolver->resolveGroupId(
      $featureCode,
      (string) ($element['group_code'] ?? ''),
    );

    $group = $this->entityTypeManager->getStorage('fb_feature_group')->load($groupId);
    if ($group === NULL) {
      $this->logger->warning(
        'Cannot create stub definition @definition_id: feature group @group_id not found.',
        [
          '@definition_id' => $definitionId,
          '@group_id' => $groupId,
        ],
      );
      return NULL;
    }

    $payload = is_array($element['payload'] ?? NULL) ? $element['payload'] : [];
    $label = trim((string) ($element['label'] ?? ''));
    if ($label === '') {
      $label = $featureCode;
    }

    $description = trim((string) ($payload['complement'] ?? ''));
    if ($description === '') {
      $description = $label;
    }

    $payloadDefaults = [];
    if (!empty($payload['unit'])) {
      $payloadDefaults['unit'] = $payload['unit'];
    }

    /** @var \Drupal\ps_feature\Entity\FeatureDefinition $definition */
    $definition = $this->entityTypeManager->getStorage('fb_feature_definition')->create([
      'id' => $definitionId,
      'label' => $label,
      'description' => $description,
      'code' => $featureCode,
      'group' => $groupId,
      'type_driver' => (string) ($element['type_driver'] ?? 'flag'),
      'weight' => (int) ($element['source_index'] ?? 0),
      'status' => TRUE,
      'expose_as_filter' => FALSE,
      'required_asset_types' => [],
      'source' => FeatureDefinitionSource::XML,
      'type_locked' => FALSE,
      'internal_lock' => FALSE,
      'source_tracking' => json_encode([
        'source_system' => 'CRM_XML',
        'origin' => 'offer_value_stub',
      ], JSON_THROW_ON_ERROR),
      'checksum' => '',
      'field_locks' => [],
      'payload_defaults' => $this->payloadDefaultsNormalizer->normalize($payloadDefaults),
    ]);
    $definition->save();

    $this->logger->info('Created stub feature definition @definition_id from offer import.', [
      '@definition_id' => $definitionId,
    ]);

    return $definition;
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

  /**
   * Returns the feature catalogue import governance policy, if registered.
   */
  private function catalogueImportPolicy(): ?ImportGovernanceCatalogueImportPolicyInterface {
    return $this->governanceRegistry->getCatalogueImportPolicyForEntityType('fb_feature_definition');
  }

}
