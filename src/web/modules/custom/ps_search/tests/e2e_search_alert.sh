#!/usr/bin/env bash
# E2E smoke test — search alert offcanvas + entity creation (Phase B).
set -euo pipefail

BASE_URL="${BASE_URL:-http://localhost:8080}"
SEARCH_PATH="${SEARCH_PATH:-/find-property?operation_type=LOC&asset_type=BUR}"
EMAIL="${EMAIL:-alert-e2e-$(date +%s)@example.com}"

echo "== Search alert E2E =="
echo "URL: ${BASE_URL}${SEARCH_PATH}"

OFFCANVAS_CODE=$(curl -sS -o /tmp/ps-search-alert-offcanvas.html -w '%{http_code}' \
  "${BASE_URL}/api/ps/search-alert/offcanvas?operation_type=LOC&asset_type=BUR&alert_title=E2E%20Alert")
echo "Offcanvas HTTP: ${OFFCANVAS_CODE}"
test "${OFFCANVAS_CODE}" = "200"
grep -q 'webform-submission-search-alert-form\|search_alert' /tmp/ps-search-alert-offcanvas.html

echo "Offcanvas fragment contains search alert webform."

docker exec -i ps_php sh -lc "cd /var/www/html && vendor/bin/drush ev \"
\\\$storage = \\\Drupal::entityTypeManager()->getStorage('search_alert');
\\\$before = count(\\\$storage->loadMultiple());
\\\$repo = \\\Drupal::service('ps_search.alert_repository');
\\\$serializer = \\\Drupal::service('ps_search.alert_criteria_serializer');
\\\$criteria = [
  'operation_type' => 'LOC',
  'asset_type' => 'BUR',
  'search_url' => '${BASE_URL}${SEARCH_PATH}',
  'search_path' => '/find-property',
  'langcode' => 'en',
];
\\\$entity = \\\$repo->createFromSubmission([
  'alert_name' => 'E2E Alert',
  'prof_email_address' => '${EMAIL}',
  'frequence' => 'weekly',
  'criteria_json' => \\\$serializer->toJson(\\\$criteria),
  'search_url' => '${BASE_URL}${SEARCH_PATH}',
  'search_path' => '/find-property',
]);
echo \\\$entity ? 'entity:' . \\\$entity->id() : 'entity:null';
\""

echo
echo "Search alert E2E passed."
