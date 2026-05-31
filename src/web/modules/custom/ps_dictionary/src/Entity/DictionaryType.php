<?php

declare(strict_types=1);

namespace Drupal\ps_dictionary\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines dictionary type config entity.
 *
 * @ConfigEntityType(
 *   id = "ps_dictionary_type",
 *   label = @Translation("Dictionary type"),
 *   handlers = {
 *     "list_builder" = "Drupal\ps_dictionary\DictionaryTypeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\ps_dictionary\Form\DictionaryTypeForm",
 *       "edit" = "Drupal\ps_dictionary\Form\DictionaryTypeForm",
 *       "delete" = "Drupal\Core\Entity\EntityDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider"
 *     }
 *   },
 *   config_prefix = "type",
 *   admin_permission = "manage ps_dictionary",
 *   translatable = TRUE,
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "collection" = "/admin/ps/structure/dictionary",
 *     "add-form" = "/admin/ps/structure/dictionary/add",
 *     "edit-form" = "/admin/ps/structure/dictionary/manage/{ps_dictionary_type}/edit",
 *     "delete-form" = "/admin/ps/structure/dictionary/manage/{ps_dictionary_type}/delete",
 *     "entry-add" = "/admin/ps/structure/dictionary/manage/{ps_dictionary_type}/add"
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "description"
 *   }
 * )
 */
final class DictionaryType extends ConfigEntityBase implements DictionaryTypeInterface {

  protected string $id;

  protected string $label;

  protected ?string $description = NULL;

  public function getDescription(): string {
    return $this->description ?? '';
  }

}
