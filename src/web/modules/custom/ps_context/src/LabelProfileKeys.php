<?php

declare(strict_types=1);

namespace Drupal\ps_context;

/**
 * Canonical label keys for ps_context.label_profile entities.
 *
 * Keys use underscores (Drupal config disallows dots in mapping keys).
 */
final class LabelProfileKeys {

  /**
   * @var array<string, string>
   *   Key => admin field description.
   */
  public const KEYS = [
    'hero_budget_max_label' => 'Hero — max price field label',
    'hero_budget_max_placeholder' => 'Hero — max price placeholder',
    'hero_surface_min_label' => 'Hero — min surface label',
    'hero_surface_min_placeholder' => 'Hero — min surface placeholder',
    'hero_capacity_field_label' => 'Hero — desk capacity field label',
    'hero_hide_operation_toggle' => 'Hero — hide operation toggle (1 = hide, COW)',
    'search_budget_field_label' => 'Search page — budget filter toggle',
    'search_budget_min_label' => 'Search page — budget min label',
    'search_budget_max_label' => 'Search page — budget max label',
    'search_budget_input_unit' => 'Search page — budget input unit',
    'search_budget_value_suffix' => 'Search page — active filter suffix',
    'search_budget_step' => 'Search page — budget input step',
    'search_budget_budget_unit' => 'Search page — budget unit code (PER_M2, PER_POSTE, GLOBAL)',
    'search_budget_budget_period' => 'Search page — budget period code (YEAR, DAY, …)',
    'search_surface_field_label' => 'Search page — surface filter toggle',
    'search_capacity_field_label' => 'Search page — capacity filter toggle',
    'search_capacity_min_label' => 'Search page — capacity min label',
    'search_capacity_max_label' => 'Search page — capacity max label',
    'offer_group_budget_title' => 'Offer form — price tab title',
    'offer_group_capacity_title' => 'Offer form — capacity tab title',
    'offer_display_budget_suffix_template' => 'Offer display — budget suffix template',
    'offer_display_capacity_unit' => 'Offer display — capacity unit label',
  ];

  /**
   * @var list<string>
   */
  public const SEED_IDS = [
    'default',
    'loc_rent',
    'loc_cow',
    'ven_sale',
  ];

  /**
   *
   */
  public static function isSeed(string $id): bool {
    return in_array($id, self::SEED_IDS, TRUE);
  }

  /**
   * Resolves admin form group from a storage key.
   */
  public static function formGroup(string $key): string {
    if (str_starts_with($key, 'hero_')) {
      return 'hero';
    }
    if (str_starts_with($key, 'search_')) {
      return 'search';
    }
    return 'offer';
  }

}
