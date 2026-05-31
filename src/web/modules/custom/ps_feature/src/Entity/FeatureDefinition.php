<?php

namespace Drupal\ps_feature\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\Core\Entity\Attribute\ConfigEntityType;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Defines the Feature Definition configuration entity.
 */
#[ConfigEntityType(
  id: 'fb_feature_definition',
  label: new TranslatableMarkup('Feature definition'),
  label_collection: new TranslatableMarkup('Feature definitions'),
  label_singular: new TranslatableMarkup('feature definition'),
  label_plural: new TranslatableMarkup('feature definitions'),
  label_count: [
    'singular' => '@count feature definition',
    'plural' => '@count feature definitions',
  ],
  handlers: [
    'list_builder' => 'Drupal\ps_feature\FeatureDefinitionListBuilder',
    'route_provider' => [
      'html' => 'Drupal\ps_feature\Routing\FeatureDefinitionHtmlRouteProvider',
    ],
    'form' => [
      'add' => 'Drupal\ps_feature\Form\FeatureDefinitionForm',
      'edit' => 'Drupal\ps_feature\Form\FeatureDefinitionForm',
      'delete' => 'Drupal\Core\Entity\EntityDeleteForm',
    ],
  ],
  config_prefix: 'feature_definition',
  admin_permission: 'administer ps features',
  entity_keys: [
    'id' => 'id',
    'label' => 'label',
    'weight' => 'weight',
  ],
  config_export: [
    'id',
    'label',
    'description',
    'code',
    'group',
    'type_driver',
    'required_asset_types',
    'weight',
    'status',
    'payload_defaults',
    'expose_as_filter',
  ],
  links: [
    'add-form' => '/admin/ps/content/features/add',
    'edit-form' => '/admin/ps/content/features/{fb_feature_definition}/edit',
    'delete-form' => '/admin/ps/content/features/{fb_feature_definition}/delete',
    'collection' => '/admin/ps/content/features',
  ],
)]
class FeatureDefinition extends ConfigEntityBase {

  /**
   * The feature definition ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The feature definition label.
   *
   * @var string
   */
  protected $label;

  /**
   * The feature definition description.
   *
   * @var string
   */
  protected $description;

  /**
   * The feature code (unique per group, for import).
   *
   * @var string
   */
  protected $code;

  /**
   * The feature group ID this definition belongs to.
   *
   * @var string
   */
  protected $group;

  /**
   * The type driver (flag, numeric, etc.).
   *
   * @var string
   */
  protected $type_driver;

  /**
   * Asset types this feature is required for.
   *
   * @var array
   */
  protected $required_asset_types = [];

  /**
   * The weight of this definition.
   *
   * @var int
   */
  protected $weight = 0;

  /**
   * Default payload values.
   *
   * @var array
   */
  protected $payload_defaults = [];

  /**
   * Whether this feature should be exposed as a search filter.
   *
   * @var bool
   */
  protected $expose_as_filter = FALSE;

  /**
   * Gets the description.
   *
   * @return string
   *   The description text.
   */
  public function getDescription(): string {
    return $this->description ?? '';
  }

  /**
   * Gets the feature code.
   *
   * @return string
   *   The feature code.
   */
  public function getCode(): string {
    return $this->code ?? '';
  }

  /**
   * Gets the feature group.
   *
   * @return string
   *   The group ID.
   */
  public function getGroup(): string {
    return $this->group ?? '';
  }

  /**
   * Gets the type driver.
   *
   * @return string
   *   The type driver ID.
   */
  public function getTypeDriver(): string {
    return $this->type_driver ?? '';
  }

  /**
   * Gets the required asset types.
   *
   * @return array
   *   Array of asset type codes.
   */
  public function getRequiredAssetTypes(): array {
    return $this->required_asset_types ?? [];
  }

  /**
   * Gets the weight.
   *
   * @return int
   *   The weight.
   */
  public function getWeight(): int {
    return $this->weight ?? 0;
  }

  /**
   * Gets the payload defaults.
   *
   * @return array
   *   The default payload values.
   */
  public function getPayloadDefaults(): array {
    $defaults = $this->payload_defaults ?? [];
    
    // Handle legacy string format (should be array).
    if (is_string($defaults)) {
      $decoded = json_decode($defaults, TRUE);
      return is_array($decoded) ? $decoded : [];
    }
    
    return is_array($defaults) ? $defaults : [];
  }

  /**
   * Checks if this feature is applicable to an asset type.
   *
   * @param string $asset_type
   *   The asset type code.
   *
   * @return bool
   *   TRUE if applicable, FALSE otherwise.
   */
  public function isApplicableToAssetType(string $asset_type): bool {
    // If no required types specified, applies to all.
    if (empty($this->required_asset_types)) {
      return TRUE;
    }
    return in_array($asset_type, $this->required_asset_types, TRUE);
  }

  /**
   * Whether this feature should be exposed as a search filter.
   *
   * @return bool
   *   TRUE if should be exposed, FALSE otherwise.
   */
  public function isExposeAsFilter(): bool {
    return $this->expose_as_filter ?? FALSE;
  }

}
