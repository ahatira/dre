<?php

/**
 * @file
 * B2E front validation: placement conditions on /fr/a-louer/bureaux/.
 */

declare(strict_types=1);

use Drupal\views_promo_card\Entity\PromoCardPlacement;

const PLACEMENT_ID = 'b2b_search_calc_row2';
const B2B_URL = 'http://localhost:8080/fr/a-louer/bureaux/';
const CALC_MARKER = 'Estimez la surface';

/**
 * Loads HTML for a URL through Drupal kernel (SEO path processing).
 */
function b2b_fetch(string $url): string {
  $request = \Symfony\Component\HttpFoundation\Request::create($url, 'GET');
  $request->headers->set('Host', 'localhost');
  $kernel = \Drupal::service('kernel');
  $response = $kernel->handle($request, \Symfony\Component\HttpKernel\HttpKernelInterface::SUB_REQUEST);
  $content = (string) $response->getContent();
  $kernel->terminate($request, $response);
  return $content;
}

/**
 * Saves placement conditions and clears relevant caches.
 */
function b2b_save_conditions(array $conditions): void {
  $placement = PromoCardPlacement::load(PLACEMENT_ID);
  if ($placement === NULL) {
    throw new RuntimeException('Placement not found.');
  }
  $placement->set('conditions', $conditions);
  $placement->save();
  \Drupal::service('cache_tags.invalidator')->invalidateTags(['promo_card_placement_list']);
  drupal_static_reset();
}

$original = PromoCardPlacement::load(PLACEMENT_ID)?->getConditions() ?? [];
$failures = [];

try {
  // Baseline: calc card visible with default conditions.
  $html = b2b_fetch(B2B_URL);
  if (!str_contains($html, CALC_MARKER)) {
    $failures[] = 'baseline: calc card missing with default config';
  }
  else {
    echo "OK baseline: calc card present\n";
  }

  // min_results too high -> hidden.
  b2b_save_conditions([
    ['id' => 'request_path', 'negate' => FALSE, 'pages' => '/find-property'],
    ['id' => 'promo_card_min_results', 'negate' => FALSE, 'minimum' => 9999],
  ]);
  $html = b2b_fetch(B2B_URL);
  if (str_contains($html, CALC_MARKER)) {
    $failures[] = 'min_results 9999: calc card should be hidden';
  }
  else {
    echo "OK min_results 9999: calc card hidden\n";
  }

  // user_role anonymous only -> hidden for kernel (anonymous sim is default).
  b2b_save_conditions([
    ['id' => 'request_path', 'negate' => FALSE, 'pages' => '/find-property'],
    ['id' => 'promo_card_min_results', 'negate' => FALSE, 'minimum' => 5],
    ['id' => 'user_role', 'negate' => FALSE, 'roles' => ['administrator' => 'administrator']],
  ]);
  $html = b2b_fetch(B2B_URL);
  if (str_contains($html, CALC_MARKER)) {
    $failures[] = 'user_role admin-only: calc card should be hidden for anonymous';
  }
  else {
    echo "OK user_role admin-only: calc card hidden (anonymous request)\n";
  }

  // pager page 0 only on page 0 -> visible on first page.
  b2b_save_conditions([
    ['id' => 'request_path', 'negate' => FALSE, 'pages' => '/find-property'],
    ['id' => 'promo_card_min_results', 'negate' => FALSE, 'minimum' => 5],
    ['id' => 'promo_card_pager_page', 'negate' => FALSE, 'max_page' => 0],
  ]);
  $html = b2b_fetch(B2B_URL);
  if (!str_contains($html, CALC_MARKER)) {
    $failures[] = 'pager_page 0: calc card should show on first page';
  }
  else {
    echo "OK pager_page 0: calc card present on first page\n";
  }

  // route name mismatch -> hidden.
  b2b_save_conditions([
    ['id' => 'request_path', 'negate' => FALSE, 'pages' => '/find-property'],
    ['id' => 'promo_card_min_results', 'negate' => FALSE, 'minimum' => 5],
    ['id' => 'promo_card_route_name', 'negate' => FALSE, 'routes' => 'entity.node.canonical'],
  ]);
  $html = b2b_fetch(B2B_URL);
  if (str_contains($html, CALC_MARKER)) {
    $failures[] = 'route_name mismatch: calc card should be hidden';
  }
  else {
    echo "OK route_name mismatch: calc card hidden\n";
  }

  // exposed filter BUR on bureaux page -> visible.
  b2b_save_conditions([
    ['id' => 'request_path', 'negate' => FALSE, 'pages' => '/find-property'],
    ['id' => 'promo_card_min_results', 'negate' => FALSE, 'minimum' => 5],
    ['id' => 'promo_card_views_exposed_filter', 'negate' => FALSE, 'filter_id' => 'asset_type', 'value' => 'BUR'],
  ]);
  $html = b2b_fetch(B2B_URL);
  if (!str_contains($html, CALC_MARKER)) {
    $failures[] = 'views_exposed_filter BUR: calc card should show on bureaux search';
  }
  else {
    echo "OK views_exposed_filter BUR: calc card present\n";
  }

  // request_path wrong -> hidden.
  b2b_save_conditions([
    ['id' => 'request_path', 'negate' => FALSE, 'pages' => '/admin'],
    ['id' => 'promo_card_min_results', 'negate' => FALSE, 'minimum' => 5],
  ]);
  $html = b2b_fetch(B2B_URL);
  if (str_contains($html, CALC_MARKER)) {
    $failures[] = 'request_path /admin: calc card should be hidden on search';
  }
  else {
    echo "OK request_path /admin: calc card hidden on search\n";
  }
}
finally {
  b2b_save_conditions($original);
}

if ($failures !== []) {
  foreach ($failures as $failure) {
    echo "FAIL {$failure}\n";
  }
  throw new RuntimeException('B2B condition tests failed.');
}

echo "All B2B condition front tests passed.\n";
