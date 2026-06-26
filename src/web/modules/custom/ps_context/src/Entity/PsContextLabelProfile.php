<?php

declare(strict_types=1);

namespace Drupal\ps_context\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Label profile for search and offer wording by asset × operation context.
 *
 * @ConfigEntityType(
 *   id = "ps_context_label_profile",
 *   label = @Translation("Context label profile"),
 *   label_collection = @Translation("Labels"),
 *   label_singular = @Translation("context label profile"),
 *   label_plural = @Translation("context label profiles"),
 *   label_count = @PluralTranslation(
 *     singular = "@count context label profile",
 *     plural = "@count context label profiles",
 *   ),
 *   handlers = {
 *     "list_builder" = "Drupal\ps_context\Controller\PsContextLabelProfileListBuilder",
 *     "form" = {
 *       "add" = "Drupal\ps_context\Form\PsContextLabelProfileForm",
 *       "edit" = "Drupal\ps_context\Form\PsContextLabelProfileForm",
 *       "delete" = "Drupal\ps_context\Form\PsContextLabelProfileDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider"
 *     }
 *   },
 *   config_prefix = "label_profile",
 *   translatable = TRUE,
 *   admin_permission = "administer ps_context matrix",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid",
 *     "status" = "status",
 *     "weight" = "weight"
 *   },
 *   links = {
 *     "collection" = "/admin/ps/config/context/labels",
 *     "add-form" = "/admin/ps/config/context/labels/add",
 *     "edit-form" = "/admin/ps/config/context/labels/{ps_context_label_profile}",
 *     "delete-form" = "/admin/ps/config/context/labels/{ps_context_label_profile}/delete"
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "uuid",
 *     "status",
 *     "weight",
 *     "asset_type",
 *     "operation_type",
 *     "labels"
 *   }
 * )
 */
final class PsContextLabelProfile extends ConfigEntityBase implements PsContextLabelProfileInterface {

  /**
   * The machine name of the profile.
   */
  protected string $id;

  /**
   * The human-readable label of the profile.
   */
  protected string $label;

  /**
   * The weight for merge ordering (lower = merged earlier).
   */
  protected int $weight = 0;

  /**
   * Asset type code (BUR, COW, …) or '*' for any.
   */
  protected string $asset_type = '*';

  /**
   * Operation type code (LOC, VEN, …) or '*' for any.
   */
  protected string $operation_type = '*';

  /**
   * Label key/value map (underscore keys — see LabelProfileKeys).
   *
   * @var array<string, string>
   */
  protected array $labels = [];

  /**
   * {@inheritdoc}
   */
  public function getAssetType(): string {
    return strtoupper($this->asset_type ?: '*');
  }

  /**
   * {@inheritdoc}
   */
  public function getOperationType(): string {
    return strtoupper($this->operation_type ?: '*');
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
  public function getLabels(): array {
    return $this->labels;
  }

}
