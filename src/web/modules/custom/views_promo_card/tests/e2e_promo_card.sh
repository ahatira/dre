#!/usr/bin/env bash
# E2E tests — promo card injection via views_promo_card.
set -euo pipefail

# shellcheck source=/dev/null
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/../../../../.." && pwd)/scripts/e2e/common.sh"

SEARCH_URL="${BASE_URL}/fr/a-louer/bureaux/"
CTA_URL="/calculator"
CARD_ID="ps_search_card_push"
PLACEMENT_ID="ps_search_card_push_search"

echo "== Views promo card E2E =="

ps_e2e_drush pm:enable views_promo_card -y 2>/dev/null || true
ps_e2e_drush php:eval "
use Drupal\\views_promo_card\\Entity\\PromoCard;
use Drupal\\views_promo_card\\Entity\\PromoCardPlacement;
\$card = PromoCard::load('${CARD_ID}') ?? PromoCard::create(['id' => '${CARD_ID}', 'label' => 'Search push E2E']);
\$card->set('status', TRUE);
\$card->set('pattern_id', 'ps_theme:search-push-card');
\$card->set('ui_patterns', [
  'component_id' => 'ps_theme:search-push-card',
  'variant_id' => NULL,
  'props' => [
    'title' => ['source_id' => 'textfield', 'source' => ['value' => 'Calculate the target surface area of your future offices!']],
    'body' => ['source_id' => 'textfield', 'source' => ['value' => 'Quick and easy to use, the surface area calculator lets you define the m2 surface area you need in just a few clicks.']],
    'cta_label' => ['source_id' => 'textfield', 'source' => ['value' => 'Start calculator']],
    'cta_url' => ['source_id' => 'url', 'source' => ['value' => '${CTA_URL}']],
  ],
  'slots' => [],
]);
\$card->save();
\$placement = PromoCardPlacement::load('${PLACEMENT_ID}') ?? PromoCardPlacement::create([
  'id' => '${PLACEMENT_ID}', 'label' => 'E2E search', 'view_id' => 'ps_search_offers', 'display_id' => 'page_list',
]);
\$placement->set('status', TRUE);
\$placement->set('cards', [['promo_card' => '${CARD_ID}', 'weight' => 0]]);
\$placement->set('placement_rules', [['type' => 'fixed', 'position' => 1]]);
\$placement->set('conditions', [
  ['id' => 'promo_card_pager_page', 'negate' => FALSE, 'max_page' => 0],
  ['id' => 'promo_card_min_results', 'negate' => FALSE, 'minimum' => 2],
]);
\$placement->set('conditions_logic', 'and');
\$placement->save();
"
ps_e2e_drush cr

PAGE_CODE=$(curl -sS -o /tmp/ps-search-push-page.html -w '%{http_code}' "${SEARCH_URL}")
echo "Search page HTTP: ${PAGE_CODE}"
test "${PAGE_CODE}" = "200"

grep -q 'ps-search-push-card' /tmp/ps-search-push-page.html
grep -q 'promo-card-slot' /tmp/ps-search-push-page.html
grep -q 'Calculate the target surface area' /tmp/ps-search-push-page.html
grep -q 'Start calculator' /tmp/ps-search-push-page.html
grep -q 'href="/calculator"' /tmp/ps-search-push-page.html
echo "Promo card markup OK."

ps_e2e_drush php:eval "
\$view = \\Drupal\\views\\Views::getView('ps_search_offers');
\$view->setDisplay('page_list');
\$view->execute();
\$insertion = \\Drupal::service('views_promo_card.insertion_manager');
\$slots = \$insertion->buildSlots(\$view, count(\$view->result));
if ((int) (\$view->total_rows ?? 0) > 1 && count(\$view->result) >= 1 && \$slots === []) {
  throw new \\RuntimeException('Expected promo card slots when results exceed minimum.');
}
echo 'insertion-manager:ok';
"

echo
echo "Insertion manager OK."

ps_e2e_drush php:eval "
\$placement = \\Drupal\\views_promo_card\\Entity\\PromoCardPlacement::load('${PLACEMENT_ID}');
if (\$placement) { \$placement->set('status', FALSE)->save(); }
\$card = \\Drupal\\views_promo_card\\Entity\\PromoCard::load('${CARD_ID}');
if (\$card) { \$card->set('status', FALSE)->save(); }
"
ps_e2e_drush cr

echo "Promo card disabled again after test."
echo "== Views promo card E2E passed =="
