<?php

declare(strict_types=1);

namespace Drupal\ps_features\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface for Feature config entities.
 *
 * Features define the technical characteristics that can be associated with
 * property offers. Each feature has a type (flag, yesno, numeric, range, etc.)
 * and optional validation rules.
 *
 * @see \Drupal\ps_features\Entity\Feature
 * @see docs/specs/04-ps-features.md
 */
interface FeatureInterface extends ConfigEntityInterface {

  /**
   * Gets the feature value type.
   *
   * @return string
   *   One of: 'flag', 'yesno', 'dictionary', 'string', 'numeric', 'range'.
   */
  public function getValueType(): string;

  /**
   * Gets the dictionary type for dictionary-based features.
   *
   * @return string|null
   *   The dictionary type ID, or NULL if not applicable.
   */
  public function getDictionaryType(): ?string;

  /**
   * Gets the unit of measurement.
   *
   * @return string|null
   *   The unit (e.g., 'm', 'm²', '%'), or NULL if not applicable.
   */
  public function getUnit(): ?string;

  /**
   * Checks if this feature is required.
   *
   * @return bool
   *   TRUE if required, FALSE otherwise.
   */
  public function isRequired(): bool;

  /**
   * Gets validation rules for this feature.
   *
   * @return array<string, mixed>
   *   Validation rules (e.g., min, max, allowed_values).
   */
  public function getValidationRules(): array;

  /**
   * Gets the feature group.
   *
   * @return string|null
   *   The group ID (FeatureGroup entity), or NULL if not set.
   */
  public function getGroup(): ?string;

  /**
   * Gets the feature weight (for sorting within group).
   *
   * @return int
   *   The weight value.
   */
  public function getWeight(): int;

  /**
   * Gets feature metadata.
   *
   * @return array<string, mixed>
   *   Metadata (e.g., icon, help_text).
   */
  public function getMetadata(): array;

  /**
   * Gets the feature description.
   *
   * @return string|null
   *   The description, or NULL if not set.
   */
  public function getDescription(): ?string;

  /**
   * Indicates if this feature is exposed as a search facet.
   *
   * @return bool
   *   TRUE if facetable, FALSE otherwise.
   */
  public function isFacetable(): bool;

}
