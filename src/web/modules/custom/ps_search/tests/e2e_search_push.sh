#!/usr/bin/env bash
# E2E tests — search push calculator card (Phase C §2).
set -euo pipefail

BASE_URL="${BASE_URL:-http://localhost:8080}"
SEARCH_URL="${BASE_URL}/fr/a-louer/bureaux/"
CTA_URL="/calculator"

echo "== Search push card E2E =="

docker exec -i ps_php sh -lc "cd /var/www/html && vendor/bin/drush config:set ps_search.push_settings enabled 1 -y && \
  vendor/bin/drush config:set ps_search.push_settings cta_url '${CTA_URL}' -y && \
  vendor/bin/drush config:set ps_search.push_settings body 'Quick and easy to use, the surface area calculator lets you define the m2 surface area you need in just a few clicks.' -y && \
  vendor/bin/drush cr"

PAGE_CODE=$(curl -sS -o /tmp/ps-search-push-page.html -w '%{http_code}' "${SEARCH_URL}")
echo "Search page HTTP: ${PAGE_CODE}"
test "${PAGE_CODE}" = "200"

grep -q 'ps-search-push-card' /tmp/ps-search-push-page.html
grep -q 'ps-search-push-slot' /tmp/ps-search-push-page.html
grep -q 'Calculate the target surface area' /tmp/ps-search-push-page.html
grep -q 'Start calculator' /tmp/ps-search-push-page.html
grep -q 'href="/calculator"' /tmp/ps-search-push-page.html
grep -q 'btn-outline-primary' /tmp/ps-search-push-page.html
echo "Push card markup OK."

docker exec -i ps_php sh -lc "cd /var/www/html && vendor/bin/drush ev \"
\\\$builder = \\\Drupal::service('ps_search.push_block_builder');
if (\\\$builder->build() === NULL) {
  throw new \\\RuntimeException('Push builder should return a render array when enabled.');
}
if (!\\\$builder->shouldDisplay(5, 3)) {
  throw new \\\RuntimeException('Push should display when total > after_result and page has rows.');
}
if (\\\$builder->shouldDisplay(1, 1)) {
  throw new \\\RuntimeException('Push should not display when total equals after_result.');
}
echo 'push-builder:ok';
\""

echo
echo "Push builder logic OK."

docker exec -i ps_php sh -lc "cd /var/www/html && vendor/bin/drush config:set ps_search.push_settings enabled 0 -y && vendor/bin/drush cr"

echo "Push disabled again (default)."
echo "== Search push E2E passed =="
