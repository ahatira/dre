<?php

declare(strict_types=1);

namespace Drupal\ps_dictionary\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines dictionary entry config entity.
 *
 * @ConfigEntityType(
 *   id = "ps_dictionary_entry",
 *   label = @Translation("Dictionary entry"),
 *   handlers = {
 *     "list_builder" = "Drupal\ps_dictionary\DictionaryEntryListBuilder",
 *     "form" = {
 *       "add" = "Drupal\ps_dictionary\Form\DictionaryEntryForm",
 *       "edit" = "Drupal\ps_dictionary\Form\DictionaryEntryForm",
 *       "delete" = "Drupal\ps_dictionary\Form\DictionaryEntryDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider"
 *     }
 *   },
 *   config_prefix = "entry",
 *   admin_permission = "manage ps_dictionary",
 *   translatable = TRUE,
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "weight" = "weight",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "edit-form" = "/admin/ps/structure/dictionary/entry/{ps_dictionary_entry}/edit",
 *     "delete-form" = "/admin/ps/structure/dictionary/entry/{ps_dictionary_entry}/delete"
 *   },
 *   config_export = {
 *     "id",
 *     "type",
 *     "code",
 *     "label",
 *     "description",
 *     "icon",
 *     "weight"
 *   }
 * )
 */
final class DictionaryEntry extends ConfigEntityBase implements DictionaryEntryInterface {

  protected string $id;

  protected string $type;

  protected string $code;

  protected string $label;

  protected ?string $description = NULL;

  protected ?string $icon = NULL;

  protected int $weight = 0;

  public function getType(): string {
    return $this->type;
  }

  public function getDescription(): string {
    return $this->description ?? '';
  }

  public function getCode(): string {
    return $this->code;
  }

  public function getIcon(): string {
    return $this->icon ?? '';
  }

  public function getWeight(): int {
    return $this->weight;
  }

}
