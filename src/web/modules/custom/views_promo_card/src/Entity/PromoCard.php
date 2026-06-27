<?php

declare(strict_types=1);

namespace Drupal\views_promo_card\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\Core\Entity\Attribute\ConfigEntityType;
use Drupal\Core\Entity\EntityDeleteForm;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\views_promo_card\Form\PromoCardForm;
use Drupal\views_promo_card\PromoCardListBuilder;

/**
 * Defines the Promo Card config entity.
 */
#[ConfigEntityType(
  id: 'promo_card',
  label: new TranslatableMarkup('Promo card'),
  label_collection: new TranslatableMarkup('Promo cards'),
  label_singular: new TranslatableMarkup('promo card'),
  label_plural: new TranslatableMarkup('promo cards'),
  label_count: [
    'singular' => '@count promo card',
    'plural' => '@count promo cards',
  ],
  handlers: [
    'list_builder' => PromoCardListBuilder::class,
    'form' => [
      'add' => PromoCardForm::class,
      'edit' => PromoCardForm::class,
      'delete' => EntityDeleteForm::class,
    ],
  ],
  config_prefix: 'card',
  admin_permission: 'administer views promo card',
  entity_keys: [
    'id' => 'id',
    'label' => 'label',
    'status' => 'status',
    'weight' => 'weight',
  ],
  config_export: [
    'id',
    'label',
    'status',
    'weight',
    'preset_id',
    'pattern_id',
    'ui_patterns',
  ],
  links: [
    'collection' => '/admin/ps/content/promo-card/cards',
    'add-form' => '/admin/ps/content/promo-card/cards/add',
    'edit-form' => '/admin/ps/content/promo-card/cards/{promo_card}',
    'delete-form' => '/admin/ps/content/promo-card/cards/{promo_card}/delete',
  ],
)]
final class PromoCard extends ConfigEntityBase implements PromoCardInterface {

  /**
   * The promo card ID.
   */
  protected string $id = '';

  /**
   * The human-readable label.
   */
  protected string $label = '';

  /**
   * Whether the card is enabled.
   *
   * @var bool
   */
  protected $status = TRUE;

  /**
   * Sort weight.
   */
  protected int $weight = 0;

  /**
   * Optional preset source ID.
   */
  protected string $preset_id = '';

  /**
   * SDC pattern ID (e.g. ps_theme:search-push-card).
   */
  protected string $pattern_id = '';

  /**
   * UI Patterns component configuration.
   *
   * @var array<string, mixed>
   */
  protected array $ui_patterns = [];

  /**
   * {@inheritdoc}
   */
  public function getPatternId(): string {
    return $this->pattern_id;
  }

  /**
   * {@inheritdoc}
   */
  public function getPresetId(): string {
    return $this->preset_id;
  }

  /**
   * {@inheritdoc}
   */
  public function getUiPatterns(): array {
    return is_array($this->ui_patterns) ? $this->ui_patterns : [];
  }

}
