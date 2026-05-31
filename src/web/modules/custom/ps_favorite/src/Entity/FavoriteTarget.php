<?php

declare(strict_types=1);

namespace Drupal\ps_favorite\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\Core\Entity\Attribute\ConfigEntityType;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Defines a favorite target config entity.
 *
 * One entity instance represents one content target such as `node.offer`.
 */
#[ConfigEntityType(
  id: 'ps_favorite_target',
  label: new TranslatableMarkup('Favorite target'),
  label_collection: new TranslatableMarkup('Favorite targets'),
  label_singular: new TranslatableMarkup('favorite target'),
  label_plural: new TranslatableMarkup('favorite targets'),
  label_count: [
    'singular' => '@count favorite target',
    'plural' => '@count favorite targets',
  ],
  config_prefix: 'target',
  entity_keys: [
    'id' => 'id',
    'label' => 'label',
    'uuid' => 'uuid',
    'status' => 'status',
  ],
  config_export: [
    'id',
    'label',
    'uuid',
    'status',
    'entity_type_id',
    'bundle',
    'max_favorites',
    'view_mode',
  ],
)]
final class FavoriteTarget extends ConfigEntityBase {

  protected string $id;

  protected string $label;

  protected string $entity_type_id = 'node';

  protected string $bundle = '';

  protected int $max_favorites = 0;

  protected string $view_mode = '';

  public function getTargetKey(): string {
    return $this->entity_type_id . '.' . $this->bundle;
  }

  public function getEntityTypeIdTarget(): string {
    return $this->entity_type_id;
  }

  public function getBundle(): string {
    return $this->bundle;
  }

  public function getMaxFavorites(): int {
    return $this->max_favorites;
  }

  public function getViewMode(): string {
    return $this->view_mode;
  }

  public function isEnabled(): bool {
    return (bool) ($this->status ?? TRUE);
  }

  public static function buildLabel(string $entityTypeLabel, string $bundleLabel): TranslatableMarkup {
    return new TranslatableMarkup('@entity_type: @bundle', [
      '@entity_type' => $entityTypeLabel,
      '@bundle' => $bundleLabel,
    ]);
  }

}