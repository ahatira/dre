<?php

declare(strict_types=1);

namespace Drupal\views_promo_card\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\Core\Entity\Attribute\ConfigEntityType;
use Drupal\Core\Entity\EntityDeleteForm;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\views_promo_card\Form\PromoCardPlacementForm;
use Drupal\views_promo_card\PromoCardPlacementListBuilder;

/**
 * Defines the Promo Card Placement config entity.
 */
#[ConfigEntityType(
  id: 'promo_card_placement',
  label: new TranslatableMarkup('Promo card placement'),
  label_collection: new TranslatableMarkup('Placements'),
  label_singular: new TranslatableMarkup('placement'),
  label_plural: new TranslatableMarkup('placements'),
  label_count: [
    'singular' => '@count placement',
    'plural' => '@count placements',
  ],
  handlers: [
    'list_builder' => PromoCardPlacementListBuilder::class,
    'form' => [
      'add' => PromoCardPlacementForm::class,
      'edit' => PromoCardPlacementForm::class,
      'delete' => EntityDeleteForm::class,
    ],
  ],
  config_prefix: 'placement',
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
    'view_id',
    'display_id',
    'cards',
    'rotation',
    'placement_rules',
    'conditions',
    'conditions_logic',
    'max_insertions_per_page',
  ],
  links: [
    'collection' => '/admin/config/services/views-promo-card/placements',
    'add-form' => '/admin/config/services/views-promo-card/placements/add',
    'edit-form' => '/admin/config/services/views-promo-card/placements/{promo_card_placement}',
    'delete-form' => '/admin/config/services/views-promo-card/placements/{promo_card_placement}/delete',
  ],
)]
final class PromoCardPlacement extends ConfigEntityBase implements PromoCardPlacementInterface {

  public const ROTATION_WEIGHT_FIRST = 'weight_first';

  public const ROTATION_ROUND_ROBIN = 'round_robin';

  public const ROTATION_RANDOM = 'random';

  /**
   * The placement ID.
   */
  protected string $id = '';

  /**
   * The human-readable label.
   */
  protected string $label = '';

  /**
   * Whether the placement is enabled.
   */
  protected $status = TRUE;

  /**
   * Sort weight among placements.
   */
  protected int $weight = 0;

  /**
   * Target Views ID.
   */
  protected string $view_id = '';

  /**
   * Target display ID.
   */
  protected string $display_id = '';

  /**
   * Referenced promo cards.
   *
   * @var array<int, array<string, mixed>>
   */
  protected array $cards = [];

  /**
   * Card rotation strategy.
   */
  protected string $rotation = self::ROTATION_WEIGHT_FIRST;

  /**
   * Placement rules (fixed / interval).
   *
   * @var array<int, array<string, mixed>>
   */
  protected array $placement_rules = [];

  /**
   * Visibility conditions.
   *
   * @var array<int, array<string, mixed>>
   */
  protected array $conditions = [];

  /**
   * Conditions logic: and or or.
   */
  protected string $conditions_logic = 'and';

  /**
   * Maximum insertions per page.
   */
  protected int $max_insertions_per_page = 3;

  /**
   * {@inheritdoc}
   */
  public function getViewId(): string {
    return $this->view_id;
  }

  /**
   * {@inheritdoc}
   */
  public function getDisplayId(): string {
    return $this->display_id;
  }

  /**
   * {@inheritdoc}
   */
  public function getCards(): array {
    return $this->cards;
  }

  /**
   * {@inheritdoc}
   */
  public function getRotation(): string {
    return $this->rotation;
  }

  /**
   * {@inheritdoc}
   */
  public function getPlacementRules(): array {
    return $this->placement_rules;
  }

  /**
   * {@inheritdoc}
   */
  public function getConditions(): array {
    return $this->conditions;
  }

  /**
   * {@inheritdoc}
   */
  public function getConditionsLogic(): string {
    return $this->conditions_logic;
  }

  /**
   * {@inheritdoc}
   */
  public function getMaxInsertionsPerPage(): int {
    return max(1, $this->max_insertions_per_page);
  }

}
