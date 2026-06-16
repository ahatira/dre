<?php

/**
 * @file
 * Sets placement conditions from JSON argv for browser E2E tests.
 *
 * Usage:
 *   drush php:script .../set_placement_conditions.php -- '[{"id":"request_path",...}]'
 */

declare(strict_types=1);

use Drupal\views_promo_card\Entity\PromoCardPlacement;

$args = $extra ?? [];
if ($args === []) {
  throw new RuntimeException('Pass JSON conditions array as script argument.');
}

$json = is_array($args) ? ($args[0] ?? '') : (string) $args;
$conditions = json_decode($json, TRUE, 512, JSON_THROW_ON_ERROR);

$placement = PromoCardPlacement::load('b2b_search_calc_row2');
if ($placement === NULL) {
  throw new RuntimeException('Placement b2b_search_calc_row2 not found.');
}

$placement->set('conditions', $conditions);
$placement->save();
\Drupal::service('cache_tags.invalidator')->invalidateTags(['promo_card_placement_list']);
drupal_static_reset();

echo 'OK: saved ' . count($conditions) . " condition(s)\n";
