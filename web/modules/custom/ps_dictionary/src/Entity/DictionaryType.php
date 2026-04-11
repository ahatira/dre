<?php

declare(strict_types=1);

namespace Drupal\ps_dictionary\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\Core\Entity\Attribute\ConfigEntityType;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ps_dictionary\Form\DictionaryTypeForm;
use Drupal\ps_dictionary\Form\DictionaryTypeDeleteForm;

/**
 * Defines the Dictionary Type configuration entity.
 *
 * A dictionary type represents a category of business codes
 * (e.g., property_type, transaction_type, offer_status).
 */
#[ConfigEntityType(
  id: 'ps_dictionary_type',
  label: new TranslatableMarkup('Dictionary Type'),
  label_collection: new TranslatableMarkup('Dictionary Types'),
  label_singular: new TranslatableMarkup('dictionary type'),
  label_plural: new TranslatableMarkup('dictionary types'),
  label_count: [
    'singular' => '@count dictionary type',
    'plural' => '@count dictionary types',
  ],
  handlers: [
    'list_builder' => DictionaryTypeListBuilder::class,
    'form' => [
      'add' => DictionaryTypeForm::class,
      'edit' => DictionaryTypeForm::class,
      'delete' => DictionaryTypeDeleteForm::class,
    ],
  ],
  config_prefix: 'type',
  admin_permission: 'administer dictionaries',
  entity_keys: [
    'id' => 'id',
    'label' => 'label',
  ],
  config_export: [
    'id',
    'label',
    'description',
    'locked',
    'metadata_schema',
  ],
  links: [
    'canonical' => '/admin/ps/structure/dictionaries/{ps_dictionary_type}/edit',
    'collection' => '/admin/ps/structure/dictionaries',
    'add-form' => '/admin/ps/structure/dictionaries/add',
    'edit-form' => '/admin/ps/structure/dictionaries/{ps_dictionary_type}/edit',
    'delete-form' => '/admin/ps/structure/dictionaries/{ps_dictionary_type}/delete',
    'entries' => '/admin/ps/structure/dictionaries/{ps_dictionary_type}/entries',
  ],
)]
class DictionaryType extends ConfigEntityBase implements DictionaryTypeInterface {

  /**
   * The entity ID.
   */
  protected string $id = '';

  /**
   * The human-readable label.
   */
  protected string $label = '';

  /**
   * Optional description.
   */
  protected ?string $description = NULL;

  /**
   * Whether the type is locked.
   */
  protected bool $locked = FALSE;

  /**
   * Metadata schema definition.
   *
   * @var string|null
   *
   * Defines the structure of metadata fields for entries of this type.
   * Format: YAML string defining field definitions.
   *
   * Example:
   * @code
   * iso_code:
   *   type: textfield
   *   label: 'ISO Code'
   *   required: true
   * decimal_places:
   *   type: number
   *   label: 'Decimal Places'
   *   default: 2
   * @endcode
   */
  protected ?string $metadata_schema = NULL;

  /**
   * {@inheritdoc}
   */
  public function id(): string {
    return $this->id;
  }

  /**
   * {@inheritdoc}
   */
  public function getLabel(): string {
    return $this->label;
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
  public function setDescription(?string $description): static {
    $this->description = $description;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getMetadataSchema(): ?string {
    return $this->metadata_schema;
  }

  /**
   * {@inheritdoc}
   */
  public function setMetadataSchema(?string $schema): static {
    $this->metadata_schema = $schema;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function isLocked(): bool {
    return $this->locked ?? FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function setLocked(bool $locked): static {
    $this->locked = $locked;
    return $this;
  }

}
