<?php

declare(strict_types=1);

namespace Drupal\ps_division\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;
use Drupal\Core\Entity\Attribute\ConfigEntityType;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Defines the Division Type configuration entity.
 *
 * Division types act as bundles for the Division content entity,
 * allowing different field configurations per type (e.g., 'lot', 'floor').
 *
 * @see \Drupal\ps_division\Entity\DivisionTypeInterface
 */
#[ConfigEntityType(
  id: 'division_type',
  label: new TranslatableMarkup('Division Type'),
  label_collection: new TranslatableMarkup('Division Types'),
  label_singular: new TranslatableMarkup('division type'),
  label_plural: new TranslatableMarkup('division types'),
  label_count: [
    'singular' => '@count division type',
    'plural' => '@count division types',
  ],
  handlers: [
    'list_builder' => 'Drupal\\ps_division\\DivisionTypeListBuilder',
    'form' => [
      'add' => 'Drupal\\ps_division\\Form\\DivisionTypeForm',
      'edit' => 'Drupal\\ps_division\\Form\\DivisionTypeForm',
      'delete' => 'Drupal\\Core\\Entity\\EntityDeleteForm',
    ],
  ],
  admin_permission: 'administer ps_division entities',
  config_prefix: 'division_type',
  bundle_of: 'ps_division',
  entity_keys: [
    'id' => 'id',
    'label' => 'label',
  ],
  config_export: [
    'id',
    'label',
    'description',
  ],
  links: [
    'add-form' => '/admin/ps/structure/division-types/add',
    'edit-form' => '/admin/ps/structure/division-types/manage/{division_type}',
    'delete-form' => '/admin/ps/structure/division-types/manage/{division_type}/delete',
    'collection' => '/admin/ps/structure/division-types',
  ],
)]
final class DivisionType extends ConfigEntityBundleBase implements DivisionTypeInterface {

  /**
   * The division type ID.
   */
  protected string $id;

  /**
   * The division type label.
   */
  protected string $label;

  /**
   * The division type description.
   */
  protected string $description = '';

  /**
   * {@inheritdoc}
   */
  public function getDescription(): string {
    return $this->description;
  }

  /**
   * {@inheritdoc}
   */
  public function setDescription(string $description): static {
    $this->description = $description;
    return $this;
  }

}
