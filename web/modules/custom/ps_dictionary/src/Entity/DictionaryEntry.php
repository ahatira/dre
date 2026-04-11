<?php

declare(strict_types=1);

namespace Drupal\ps_dictionary\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\Core\Entity\Attribute\ConfigEntityType;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ps_dictionary\Form\DictionaryEntryForm;
use Drupal\ps_dictionary\Form\DictionaryEntryDeleteForm;

/**
 * Defines the Dictionary Entry configuration entity.
 *
 * A dictionary entry represents a single code-label pair within a
 * dictionary type. Entries support weighting, status flags, and custom
 * metadata.
 */
#[ConfigEntityType(
  id: 'ps_dictionary_entry',
  label: new TranslatableMarkup('Dictionary Entry'),
  label_collection: new TranslatableMarkup('Dictionary Entries'),
  label_singular: new TranslatableMarkup('dictionary entry'),
  label_plural: new TranslatableMarkup('dictionary entries'),
  label_count: [
    'singular' => '@count dictionary entry',
    'plural' => '@count dictionary entries',
  ],
  handlers: [
    'list_builder' => DictionaryEntryListBuilder::class,
    'form' => [
      'add' => DictionaryEntryForm::class,
      'edit' => DictionaryEntryForm::class,
      'delete' => DictionaryEntryDeleteForm::class,
    ],
  ],
  config_prefix: 'entry',
  admin_permission: 'administer dictionaries',
  entity_keys: [
    'id' => 'id',
    'label' => 'label',
    'uuid' => 'uuid',
    'weight' => 'weight',
  ],
  config_export: [
    'id',
    'dictionary_type',
    'code',
    'label',
    'description',
    'weight',
    'status',
    'deprecated',
    'metadata',
  ],
  links: [
    'canonical' => '/admin/ps/structure/entries/{ps_dictionary_entry}',
    'edit-form' => '/admin/ps/structure/entries/{ps_dictionary_entry}/edit',
    'delete-form' => '/admin/ps/structure/entries/{ps_dictionary_entry}/delete',
    'collection' => '/admin/ps/structure/dictionaries',
  ],
)]
class DictionaryEntry extends ConfigEntityBase implements DictionaryEntryInterface {

  /**
   * The entity ID.
   */
  protected string $id = '';

  /**
   * The dictionary type ID.
   */
  protected string $dictionary_type = '';

  /**
   * The machine-readable code.
   */
  protected string $code = '';

  /**
   * The human-readable label.
   */
  protected string $label = '';

  /**
   * Optional description.
   */
  protected ?string $description = NULL;

  /**
   * Display weight.
   */
  protected int $weight = 0;

  /**
   * Deprecated flag.
   */
  protected bool $deprecated = FALSE;

  /**
   * Custom metadata.
   *
   * @var array<string, mixed>
   */
  protected array $metadata = [];

  /**
   * {@inheritdoc}
   */
  public function id(): string {
    return $this->id;
  }

  /**
   * {@inheritdoc}
   */
  public function getType(): string {
    return $this->dictionary_type;
  }

  /**
   * {@inheritdoc}
   */
  public function getCode(): string {
    return $this->code;
  }

  /**
   * {@inheritdoc}
   */
  public function setCode(string $code): static {
    $this->code = $code;
    return $this;
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
  public function getWeight(): int {
    return $this->weight;
  }

  /**
   * {@inheritdoc}
   */
  public function setWeight(int $weight): static {
    $this->weight = $weight;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function isActive(): bool {
    return (bool) $this->status;
  }

  /**
   * {@inheritdoc}
   */
  public function isDeprecated(): bool {
    return $this->deprecated;
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
  public function setMetadata(array $metadata): static {
    $this->metadata = $metadata;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getMetadataValue(string $key, mixed $default = NULL): mixed {
    return $this->metadata[$key] ?? $default;
  }

  /**
   * {@inheritdoc}
   */
  public function setMetadataValue(string $key, mixed $value): static {
    $this->metadata[$key] = $value;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function hasMetadataKey(string $key): bool {
    return isset($this->metadata[$key]);
  }

  /**
   * {@inheritdoc}
   */
  public function getMetadataTyped(string $key, string $type = 'string', mixed $default = NULL): mixed {
    $value = $this->metadata[$key] ?? $default;

    if ($value === NULL) {
      return $default;
    }

    return match($type) {
      'string' => (string) $value,
      'int' => (int) $value,
      'float' => (float) $value,
      'bool' => (bool) $value,
      'array' => is_array($value) ? $value : [$value],
      default => $value,
    };
  }

}
