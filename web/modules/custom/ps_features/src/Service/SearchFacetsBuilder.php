<?php

declare(strict_types=1);

namespace Drupal\ps_features\Service;

use Drupal\ps_features\Entity\FeatureInterface;

/**
 * Builds search facet definitions from facetable features.
 *
 * @see \Drupal\ps_features\Service\SearchFacetsBuilderInterface
 * @see docs/specs/04-ps-features.md#search-facets
 */
final class SearchFacetsBuilder implements SearchFacetsBuilderInterface {

  public function __construct(private readonly FeatureManagerInterface $featureManager) {}

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    $definitions = [];
    foreach ($this->featureManager->getFeatures() as $feature) {
      if (!$feature->isFacetable()) {
        continue;
      }
      $facetType = $this->getFacetType($feature);
      $definitions[] = $this->createDefinition($feature, $facetType, $this->getWidgetType($feature));
    }
    return $definitions;
  }

  /**
   * {@inheritdoc}
   */
  public function getFacetType(FeatureInterface $feature): string {
    return match ($feature->getValueType()) {
      'flag', 'yesno' => 'boolean',
      'dictionary' => 'terms',
      'numeric' => 'numeric',
      'range' => 'range',
      default => 'text',
    };
  }

  /**
   * {@inheritdoc}
   */
  public function getWidgetType(FeatureInterface $feature): string {
    return match ($feature->getValueType()) {
      'flag', 'yesno' => 'checkbox',
      'dictionary' => 'select',
      'numeric' => 'slider',
      'range' => 'range',
      default => 'text',
    };
  }

  /**
   * Creates a facet definition array.
   *
   * @param \Drupal\ps_features\Entity\FeatureInterface $feature
   *   The feature entity.
   * @param string $facetType
   *   The facet type.
   * @param string $widgetType
   *   Suggested widget type.
   *
   * @return array<string, mixed>
   *   The facet definition.
   */
  private function createDefinition(FeatureInterface $feature, string $facetType, string $widgetType): array {
    $definition = [
      'id' => $feature->id(),
      'label' => $feature->label(),
      'facet_type' => $facetType,
      'value_type' => $feature->getValueType(),
      'widget' => $widgetType,
    ];
    if ($feature->getDictionaryType()) {
      $definition['dictionary_type'] = $feature->getDictionaryType();
    }
    if ($feature->getUnit()) {
      $definition['unit'] = $feature->getUnit();
    }
    return $definition;
  }

}
