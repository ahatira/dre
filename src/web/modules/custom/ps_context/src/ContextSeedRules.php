<?php

declare(strict_types=1);

namespace Drupal\ps_context;

/**
 * Machine names of seed rules shipped in config/install.
 */
final class ContextSeedRules {

  /**
   * @var list<string>
   */
  public const SEED_IDS = [
    'default_hide_surface',
    'default_hide_capacity',
    'default_hide_budget',
    'default_hide_lots',
    'asset_type_cow',
    'asset_selected_show_surface',
    'not_divisible_hide_surface_rows',
    'divisible_show_lots',
    'operation_selected_show_budget',
    'operation_type_ven',
    'loc_budget_period_year',
    'loc_budget_unit_per_m2',
    'loc_cow_budget_unit_per_poste',
    'ven_budget_unit_global',
    'default_budget_currency_eur',
  ];

  public static function isSeed(string $id): bool {
    return in_array($id, self::SEED_IDS, TRUE);
  }

}
