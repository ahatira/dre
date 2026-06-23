<?php

declare(strict_types=1);

namespace Drupal\ps_search\Service;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\ps_core\Service\OfferSectionRegistry;
use Drupal\ps_feature\Entity\FeatureDefinition;
use Drupal\ps_feature\Service\FeatureTypeManager;

/**
 * Builds searchable text for the Nearby transport Solr field from transport features.
 *
 * Uses the same transport group configured for offer location display
 * (ps_core.offer_section_settings → location.transport_group).
 */
final class TransportFeatureSearchTextBuilder {

  public function __construct(
    private readonly OfferSectionRegistry $offerSectionRegistry,
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly FeatureTypeManager $featureTypeManager,
  ) {}

  /**
   * Returns the node translation to read field_features from when indexing/searching.
   *
   * Feature payloads are often only present on the default translation while
   * Search API still indexes language-specific items (e.g. entity:node/2:en).
   */
  public function resolveNodeForFeatureIndexing(ContentEntityInterface $node): ContentEntityInterface {
    $text = $this->buildFromNodeTranslation($node);
    if ($text !== '') {
      return $node;
    }

    if (!$node->isDefaultTranslation()) {
      $default = $node->getUntranslated();
      if ($this->buildFromNodeTranslation($default) !== '') {
        return $default;
      }
    }

    return $node;
  }

  /**
   * Returns concatenated searchable text from transport-group features on an offer.
   */
  public function buildFromNode(ContentEntityInterface $node): string {
    $node = $this->resolveNodeForFeatureIndexing($node);
    return $this->buildFromNodeTranslation($node);
  }

  /**
   * Builds transport text from one node translation without fallback.
   */
  private function buildFromNodeTranslation(ContentEntityInterface $node): string {
    if (!$node->hasField('field_features') || $node->get('field_features')->isEmpty()) {
      return '';
    }

    $transportGroupId = $this->offerSectionRegistry->getLocationTransportGroup();
    $parts = [];

    foreach ($node->get('field_features') as $featureItem) {
      $definition = $featureItem->getFeatureDefinition();
      if (!$definition instanceof FeatureDefinition) {
        continue;
      }
      if ((string) $definition->getGroup() !== $transportGroupId) {
        continue;
      }

      $payload = $featureItem->getPayloadArray();
      $typeDriver = (string) $definition->getTypeDriver();
      if ($this->shouldSkipFeature($typeDriver, $payload)) {
        continue;
      }

      $snippet = $this->formatFeatureSnippet($definition, $typeDriver, $payload);
      if ($snippet !== '') {
        $parts[] = $snippet;
      }
    }

    return trim(implode(' | ', $parts));
  }

  /**
   * Returns the configured transport feature group ID.
   */
  public function getTransportGroupId(): string {
    return $this->offerSectionRegistry->getLocationTransportGroup();
  }

  /**
   * Builds one indexed/searchable snippet (label and formatted value).
   */
  public function formatFeatureSnippet(
    FeatureDefinition $definition,
    string $typeDriver,
    array $payload,
  ): string {
    $label = trim((string) $definition->label());
    $formatted = $this->formatPayloadValue($typeDriver, $payload);

    if ($typeDriver === 'flag') {
      return $label;
    }

    if ($formatted === '') {
      return $label;
    }

    return $label !== '' ? $label . ': ' . $formatted : $formatted;
  }

  /**
   * Returns the formatted payload value without the feature label.
   */
  public function formatPayloadValue(string $typeDriver, array $payload): string {
    try {
      return trim((string) $this->featureTypeManager->createInstance($typeDriver)->format($payload));
    }
    catch (\Throwable) {
      return '';
    }
  }

  /**
   * Whether a transport feature should be omitted from index/suggest output.
   */
  public function shouldSkipIndexedFeature(string $typeDriver, array $payload): bool {
    return $this->shouldSkipFeature($typeDriver, $payload);
  }

  private function shouldSkipFeature(string $typeDriver, array $payload): bool {
    if ($typeDriver !== 'flag') {
      return FALSE;
    }
    if (array_key_exists('present', $payload)) {
      return (bool) $payload['present'] === FALSE;
    }
    if (array_key_exists('presence', $payload)) {
      return (bool) $payload['presence'] === FALSE;
    }
    return FALSE;
  }

}
