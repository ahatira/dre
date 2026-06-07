<?php

namespace Drupal\ps_feature\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\Core\Entity\Attribute\ConfigEntityType;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ps_core\Utility\IconIdUtility;

/**
 * Defines the Feature Group configuration entity.
 */
#[ConfigEntityType(
  id: 'fb_feature_group',
  label: new TranslatableMarkup('Feature group'),
  label_collection: new TranslatableMarkup('Feature groups'),
  label_singular: new TranslatableMarkup('feature group'),
  label_plural: new TranslatableMarkup('feature groups'),
  label_count: [
    'singular' => '@count feature group',
    'plural' => '@count feature groups',
  ],
  handlers: [
    'list_builder' => 'Drupal\ps_feature\FeatureGroupListBuilder',
    'route_provider' => [
      'html' => 'Drupal\ps_feature\Routing\FeatureGroupHtmlRouteProvider',
    ],
    'form' => [
      'add' => 'Drupal\ps_feature\Form\FeatureGroupForm',
      'edit' => 'Drupal\ps_feature\Form\FeatureGroupForm',
      'delete' => 'Drupal\Core\Entity\EntityDeleteForm',
    ],
  ],
  config_prefix: 'feature_group',
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
    'icon',
    'asset_types',
    'weight',
    'status',
  ],
  links: [
    'add-form' => '/admin/ps/structure/features/add',
    'edit-form' => '/admin/ps/structure/features/{fb_feature_group}/edit',
    'delete-form' => '/admin/ps/structure/features/{fb_feature_group}/delete',
    'collection' => '/admin/ps/structure/features',
  ],
)]
class FeatureGroup extends ConfigEntityBase {

  /**
   * The feature group ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The feature group label.
   *
   * @var string
   */
  protected $label;

  /**
   * The feature group description.
   *
   * @var string
   */
  protected $description;

  /**
   * UI Icon identifier (pack:id) for the group heading.
   *
   * @var string
   */
  protected $icon = '';

  /**
   * Asset types this group applies to.
   *
   * @var array
   */
  protected $asset_types = [];

  /**
   * The weight of this group.
   *
   * @var int
   */
  protected $weight = 0;

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
   * Gets the UI Icon identifier.
   */
  public function getIcon(): string {
    $icon = $this->icon ?? '';
    if (is_array($icon)) {
      return IconIdUtility::extractFromSubmission($icon, '');
    }

    return is_string($icon) ? $icon : '';
  }

  /**
   * Sets the UI Icon identifier.
   */
  public function setIcon(string $icon): static {
    $this->icon = $icon;
    return $this;
  }

  /**
   * Gets the asset types.
   *
   * @return array
   *   Array of asset type codes.
   */
  public function getAssetTypes(): array {
    return $this->asset_types ?? [];
  }

  /**
   * Sets the asset types.
   *
   * @param array $asset_types
   *   Array of asset type codes.
   *
   * @return $this
   */
  public function setAssetTypes(array $asset_types): static {
    $this->asset_types = $asset_types;
    return $this;
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
   * Sets the weight.
   *
   * @param int $weight
   *   The weight.
   *
   * @return $this
   */
  public function setWeight(int $weight): static {
    $this->weight = $weight;
    return $this;
  }

}
