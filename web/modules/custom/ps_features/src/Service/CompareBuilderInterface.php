<?php

declare(strict_types=1);

namespace Drupal\ps_features\Service;

/**
 * Interface for building feature comparison sections.
 *
 * Groups features into ordered sections for comparison UI based on
 * configuration (`ps_features.settings:compare_sections`). If a feature's
 * group does not match a configured section it is placed into an "other"
 * bucket at the end.
 *
 * @see docs/specs/04-ps-features.md#feature-comparison
 */
interface CompareBuilderInterface {

  /**
   * Builds comparison data structure.
   *
   * @param array<int, FeatureInterface> $features
   *   List of feature entities.
   *
   * @return array<string, array<string, mixed>>
   *   Structured sections keyed by section code containing:
   *   - label: string Section display label.
   *   - features: array<int, FeatureInterface> Ordered features.
   */
  public function build(array $features): array;

  /**
   * Gets configured comparison sections (ordered).
   *
   * @return array<int, string>
   *   Section codes.
   */
  public function getSections(): array;

}
