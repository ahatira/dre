<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Defines the offer reference pattern config entity.
 *
 * @ConfigEntityType(
 *   id = "ps_offer_reference_pattern",
 *   label = @Translation("Offer reference pattern"),
 *   label_collection = @Translation("Offer reference patterns"),
 *   label_singular = @Translation("offer reference pattern"),
 *   label_plural = @Translation("offer reference patterns"),
 *   label_count = @PluralTranslation(
 *     singular = "@count offer reference pattern",
 *     plural = "@count offer reference patterns",
 *   ),
 *   handlers = {
 *     "list_builder" = "Drupal\ps_offer\Controller\OfferReferencePatternListBuilder",
 *     "form" = {
 *       "add" = "Drupal\ps_offer\Form\OfferReferencePatternForm",
 *       "edit" = "Drupal\ps_offer\Form\OfferReferencePatternForm",
 *       "delete" = "Drupal\Core\Entity\EntityDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider"
 *     }
 *   },
 *   config_prefix = "reference_pattern",
 *   admin_permission = "administer ps offer reference patterns",
 *   translatable = TRUE,
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid",
 *     "status" = "status",
 *     "weight" = "weight"
 *   },
 *   links = {
 *     "collection" = "/admin/ps/config/offer/reference/patterns",
 *     "add-form" = "/admin/ps/config/offer/reference/patterns/add",
 *     "edit-form" = "/admin/ps/config/offer/reference/patterns/{ps_offer_reference_pattern}",
 *     "delete-form" = "/admin/ps/config/offer/reference/patterns/{ps_offer_reference_pattern}/delete"
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "uuid",
 *     "status",
 *     "weight",
 *     "target_bundles",
 *     "allow_manual_override",
 *     "require_uniqueness",
 *     "validate_manual_value_against_pattern",
 *     "generate_on_create",
 *     "regenerate_on_source_change",
 *     "counter_scope_mode",
 *     "segments"
 *   }
 * )
 */
final class OfferReferencePattern extends ConfigEntityBase implements OfferReferencePatternInterface {

  protected string $id;

  protected string $label;

  protected $status = TRUE;

  protected int $weight = 0;

  protected array $target_bundles = ['offer'];

  protected bool $allow_manual_override = TRUE;

  protected bool $require_uniqueness = TRUE;

  protected bool $validate_manual_value_against_pattern = TRUE;

  protected bool $generate_on_create = TRUE;

  protected bool $regenerate_on_source_change = FALSE;

  protected string $counter_scope_mode = 'prefix';

  protected array $segments = [];

  public function getWeight(): int {
    return $this->weight;
  }

  public function getTargetBundles(): array {
    return $this->target_bundles;
  }

  public function allowsManualOverride(): bool {
    return $this->allow_manual_override;
  }

  public function requiresUniqueness(): bool {
    return $this->require_uniqueness;
  }

  public function validatesManualValueAgainstPattern(): bool {
    return $this->validate_manual_value_against_pattern;
  }

  public function generatesOnCreate(): bool {
    return $this->generate_on_create;
  }

  public function regeneratesOnSourceChange(): bool {
    return $this->regenerate_on_source_change;
  }

  public function getCounterScopeMode(): string {
    return $this->counter_scope_mode;
  }

  public function getSegments(): array {
    $segments = $this->segments;
    usort($segments, static fn (array $left, array $right): int => (int) ($left['weight'] ?? 0) <=> (int) ($right['weight'] ?? 0));
    return $segments;
  }

}