<?php

declare(strict_types=1);

namespace Drupal\ps_features\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\Core\Entity\Attribute\ConfigEntityType;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ps_features\Form\FeatureDeleteForm;
use Drupal\ps_features\Form\FeatureForm;
use Drupal\ps_features\FeatureGroupedListBuilder;

/**
 * Defines the Feature config entity.
 *
 * Features represent technical characteristics of properties with strongly
 * typed values. Each feature defines its value type (flag, yesno, numeric,
 * etc.), validation rules, and optional metadata for grouping and display.
 *
 * Examples:
 * - has_elevator (flag): Only label displayed if present
 * - partitioned_offices (yesno): "Yes" or "No" always displayed
 * - floor_number (numeric with unit "étage"): 3
 * - storage_height (range with unit "m"): min=3.5, max=5.2
 * - air_conditioning (dictionary): ref to air_conditioning_type
 *
 * @see \Drupal\ps_features\Entity\FeatureInterface
 * @see \Drupal\ps_features\Service\FeatureManagerInterface
 * @see docs/specs/04-ps-features.md#feature-entity
 */
#[ConfigEntityType(
  id: 'ps_feature',
  label: new TranslatableMarkup('Feature'),
  label_collection: new TranslatableMarkup('Features'),
  label_singular: new TranslatableMarkup('feature'),
  label_plural: new TranslatableMarkup('features'),
  label_count: [
    'singular' => '@count feature',
    'plural' => '@count features',
  ],
  handlers: [
    'list_builder' => FeatureGroupedListBuilder::class,
    'form' => [
      'add' => FeatureForm::class,
      'edit' => FeatureForm::class,
      'delete' => FeatureDeleteForm::class,
    ],
  ],
  config_prefix: 'feature',
  admin_permission: 'administer ps_features',
  entity_keys: [
    'id' => 'id',
    'label' => 'label',
  ],
  config_export: [
    'id',
    'label',
    'description',
    'value_type',
    'dictionary_type',
    'unit',
    'is_required',
    'is_facetable',
    'validation_rules',
    'group',
    'weight',
    'metadata',
  ],
  links: [
    'collection' => '/admin/ps/structure/features',
    'add-form' => '/admin/ps/structure/features/add',
    'edit-form' => '/admin/ps/structure/features/{ps_feature}/edit',
    'delete-form' => '/admin/ps/structure/features/{ps_feature}/delete',
  ],
)]
class Feature extends ConfigEntityBase implements FeatureInterface {

  /**
   * The feature ID (machine name).
   */
  protected string $id = '';

  /**
   * The feature label.
   */
  protected string $label = '';

  /**
   * The feature description.
   */
  protected ?string $description = NULL;

  /**
   * The value type: flag, yesno, boolean, dictionary, string, numeric, range.
   */
  protected string $value_type = '';

  /**
   * The dictionary type (for dictionary value_type).
   */
  protected ?string $dictionary_type = NULL;

  /**
   * The unit of measurement (e.g., 'm', 'm²', '%').
   */
  protected ?string $unit = NULL;

  /**
   * Whether this feature is required.
   */
  protected bool $is_required = FALSE;

  /**
   * Whether this feature should be exposed as a search facet.
   */
  protected bool $is_facetable = FALSE;

  /**
   * Validation rules (min, max, allowed_values, etc.).
   *
   * @var array<string, mixed>
   */
  protected array $validation_rules = [];

  /**
   * Feature group (reference to FeatureGroup entity).
   */
  protected ?string $group = NULL;

  /**
   * Weight for sorting within group.
   */
  protected int $weight = 0;

  /**
   * Feature metadata (icon, help_text, etc.).
   *
   * @var array<string, mixed>
   */
  protected array $metadata = [];

  /**
   * {@inheritdoc}
   */
  public function getValueType(): string {
    return $this->value_type;
  }

  /**
   * {@inheritdoc}
   */
  public function getDictionaryType(): ?string {
    return $this->dictionary_type;
  }

  /**
   * {@inheritdoc}
   */
  public function getUnit(): ?string {
    return $this->unit;
  }

  /**
   * {@inheritdoc}
   */
  public function isRequired(): bool {
    return $this->is_required;
  }

  /**
   * Indicates if the feature is facetable (search facet enabled).
   */
  public function isFacetable(): bool {
    return $this->is_facetable;
  }

  /**
   * {@inheritdoc}
   */
  public function getValidationRules(): array {
    return $this->validation_rules ?? [];
  }

  /**
   * {@inheritdoc}
   */
  public function getGroup(): ?string {
    return $this->group;
  }

  /**
   * {@inheritdoc}
   */
  public function getWeight(): int {
    return $this->weight;
  }

  /**
   * {@inheritdoc}
   */
  public function getMetadata(): array {
    return $this->metadata ?? [];
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription(): ?string {
    return $this->description;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTagsToInvalidate(): array {
    $tags = parent::getCacheTagsToInvalidate();
    $tags[] = 'ps_feature_list';
    return $tags;
  }

}
