<?php

declare(strict_types=1);

namespace Drupal\ps_features\Service;

use Drupal\ps_features\Entity\FeatureInterface;

/**
 * Interface for building search facet definitions from features.
 *
 * Translates configured Feature entities into a normalized facet
 * specification consumable by the search layer (e.g. ps_search module).
 * Only features flagged as facetable are included.
 *
 * Facet type mapping (by feature value type):
 * - flag|yesno => boolean
 * - dictionary => terms
 * - numeric => numeric
 * - range => range
 * - string => text
 *
 * @see docs/specs/04-ps-features.md#search-facets
 */
interface SearchFacetsBuilderInterface {

  /**
   * Builds facet definitions for all facetable features.
   *
   * @return array<int, array<string, mixed>>
   *   List of facet definition arrays. Each item contains:
   *   - id: string Feature machine name.
   *   - label: string Human label.
   *   - facet_type: string (boolean|terms|numeric|range|text).
   *   - value_type: string Original feature value type.
   *   - widget: string Suggested widget (checkbox|select|slider|range|text).
   *   - dictionary_type?: string Dictionary type (if applicable).
   *   - unit?: string Measurement unit (if applicable).
   */
  public function build(): array;

  /**
   * Maps a feature to its facet type.
   *
   * @param \Drupal\ps_features\Entity\FeatureInterface $feature
   *   The feature entity.
   *
   * @return string
   *   The facet type.
   */
  public function getFacetType(FeatureInterface $feature): string;

  /**
   * Suggests a widget type for a feature facet.
   *
   * @param \Drupal\ps_features\Entity\FeatureInterface $feature
   *   The feature entity.
   *
   * @return string
   *   The widget type (checkbox|select|slider|range|text).
   */
  public function getWidgetType(FeatureInterface $feature): string;

}
