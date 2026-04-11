<?php

declare(strict_types=1);

namespace Drupal\ps_features\Service;

use Drupal\ps_features\Entity\FeatureInterface;

/**
 * Interface for the Feature Manager service.
 *
 * The Feature Manager provides centralized access to feature definitions
 * with caching and validation capabilities. It serves as the primary API
 * for working with features throughout the system.
 *
 * Performance: O(1) after first load per feature (cached).
 *
 * @see \Drupal\ps_features\Service\FeatureManager
 * @see docs/specs/04-ps-features.md#feature-manager
 */
interface FeatureManagerInterface {

  /**
   * Gets a feature by ID.
   *
   * @param string $feature_id
   *   The feature machine name.
   *
   * @return \Drupal\ps_features\Entity\FeatureInterface|null
   *   The feature entity, or NULL if not found.
   */
  public function getFeature(string $feature_id): ?FeatureInterface;

  /**
   * Gets all features.
   *
   * @param array<string, mixed> $filters
   *   Optional filters:
   *   - 'value_type': string - Filter by value type
   *   - 'group': string - Filter by metadata group
   *   - 'required': bool - Filter by required status.
   *
   * @return array<string, \Drupal\ps_features\Entity\FeatureInterface>
   *   Array of feature entities keyed by feature ID.
   */
  public function getFeatures(array $filters = []): array;

  /**
   * Gets feature options for form select elements.
   *
   * @param array<string, mixed> $filters
   *   Optional filters (same as getFeatures).
   *
   * @return array<string, string>
   *   Array of feature labels keyed by feature ID.
   */
  public function getFeatureOptions(array $filters = []): array;

  /**
   * Checks if a feature exists.
   *
   * @param string $feature_id
   *   The feature machine name.
   *
   * @return bool
   *   TRUE if the feature exists, FALSE otherwise.
   */
  public function featureExists(string $feature_id): bool;

  /**
   * Checks if a feature exists (alias of featureExists).
   *
   * @param string $feature_id
   *   The feature machine name.
   *
   * @return bool
   *   TRUE if the feature exists, FALSE otherwise.
   */
  public function hasFeature(string $feature_id): bool;

  /**
   * Gets features grouped by metadata group.
   *
   * @return array<string, array{label: string, features: array<string, \Drupal\ps_features\Entity\FeatureInterface>}>
   *   Group structures keyed by group code including label and ordered features.
   */
  public function getFeaturesByGroup(): array;

  /**
   * Gets available value types.
   *
   * @return array<string, string>
   *   Array of value type labels keyed by type ID.
   */
  public function getValueTypes(): array;

  /**
   * Clears the feature cache.
   *
   * @return void
   *   Returns nothing.
   */
  public function clearCache(): void;

}
