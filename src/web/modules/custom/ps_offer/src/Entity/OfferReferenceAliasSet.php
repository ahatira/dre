<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the offer reference alias-set config entity.
 *
 * @ConfigEntityType(
 *   id = "ps_offer_reference_alias_set",
 *   label = @Translation("Offer reference alias set"),
 *   label_collection = @Translation("Offer reference alias sets"),
 *   label_singular = @Translation("offer reference alias set"),
 *   label_plural = @Translation("offer reference alias sets"),
 *   label_count = @PluralTranslation(
 *     singular = "@count offer reference alias set",
 *     plural = "@count offer reference alias sets"
 *   ),
 *   handlers = {
 *     "list_builder" = "Drupal\ps_offer\Controller\OfferReferenceAliasSetListBuilder",
 *     "form" = {
 *       "add" = "Drupal\ps_offer\Form\OfferReferenceAliasSetForm",
 *       "edit" = "Drupal\ps_offer\Form\OfferReferenceAliasSetForm",
 *       "delete" = "Drupal\Core\Entity\EntityDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider"
 *     }
 *   },
 *   config_prefix = "reference_alias_set",
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
 *     "collection" = "/admin/ps/config/offer-reference/aliases",
 *     "add-form" = "/admin/ps/config/offer-reference/aliases/add",
 *     "edit-form" = "/admin/ps/config/offer-reference/aliases/{ps_offer_reference_alias_set}",
 *     "delete-form" = "/admin/ps/config/offer-reference/aliases/{ps_offer_reference_alias_set}/delete"
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "uuid",
 *     "status",
 *     "weight",
 *     "applies_to_pattern_ids",
 *     "entries"
 *   }
 * )
 */
final class OfferReferenceAliasSet extends ConfigEntityBase implements OfferReferenceAliasSetInterface {

  protected string $id;

  protected string $label;

  protected $status = TRUE;

  protected int $weight = 0;

  protected array $applies_to_pattern_ids = [];

  protected array $entries = [];

  public function getWeight(): int {
    return $this->weight;
  }

  public function getAppliesToPatternIds(): array {
    return $this->applies_to_pattern_ids;
  }

  public function getEntries(): array {
    $entries = $this->entries;
    usort($entries, static fn (array $left, array $right): int => (int) ($left['weight'] ?? 0) <=> (int) ($right['weight'] ?? 0));
    return $entries;
  }

}