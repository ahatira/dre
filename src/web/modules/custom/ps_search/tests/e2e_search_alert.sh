#!/usr/bin/env bash
# E2E tests — search alert offcanvas, criteria, repository, webform hook (Phase B §5).
set -euo pipefail

# shellcheck source=/dev/null
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/../../../../.." && pwd)/scripts/e2e/common.sh"

EMAIL="alert-e2e-$(date +%s)@example.com"
AUTH_EMAIL="alert-auth-e2e-$(date +%s)@example.com"
SEARCH_URL="${BASE_URL}/for-rent/office/paris-12-75012/"
SEARCH_PATH="/for-rent/office/paris-12-75012/"

echo "== Search alert E2E =="
echo "Anonymous email: ${EMAIL}"

OFFCANVAS_QUERY="operation_type=LOC&asset_type=BUR&locality=75012&search_url=$(python3 -c "import urllib.parse; print(urllib.parse.quote('${SEARCH_URL}'))")&search_path=$(python3 -c "import urllib.parse; print(urllib.parse.quote('${SEARCH_PATH}'))")"

OFFCANVAS_CODE=$(curl -sS -o /tmp/ps-search-alert-offcanvas.html -w '%{http_code}' \
  "${BASE_URL}/api/ps/search-alert/offcanvas?${OFFCANVAS_QUERY}")
echo "Offcanvas HTTP: ${OFFCANVAS_CODE}"
test "${OFFCANVAS_CODE}" = "200"
grep -q 'webform-submission-search-alert-form' /tmp/ps-search-alert-offcanvas.html
grep -q 'ps-search-alert-criteria-display\|Search zones\|Search criteria' /tmp/ps-search-alert-offcanvas.html
grep -q 'Continue' /tmp/ps-search-alert-offcanvas.html
grep -q 'ps-site-urgency-help\|In a hurry' /tmp/ps-search-alert-offcanvas.html
echo "Offcanvas markup OK."

ps_e2e_drush ev "
\$serializer = \\Drupal::service('ps_search.alert_criteria_serializer');
\$criteria = [
  'operation_type' => 'LOC',
  'asset_type' => 'BUR',
  'locality' => ['75012'],
  'search_url' => '${SEARCH_URL}',
  'search_path' => '${SEARCH_PATH}',
  'langcode' => 'en',
];
\$normalized = \$serializer->normalizeCriteria(\$criteria);
\$request = \$serializer->buildRequest(\$normalized);
if (\$request->query->get('operation_type') !== 'LOC') {
  throw new \\RuntimeException('Criteria round-trip failed.');
}
echo 'criteria-roundtrip:ok';
"

echo
echo "Criteria serializer round-trip OK."

ps_e2e_drush ev "
\$repo = \\Drupal::service('ps_search.alert_repository');
\$serializer = \\Drupal::service('ps_search.alert_criteria_serializer');
\$criteria = [
  'operation_type' => 'LOC',
  'asset_type' => 'BUR',
  'locality' => ['75012'],
  'search_url' => '${SEARCH_URL}',
  'search_path' => '${SEARCH_PATH}',
  'langcode' => 'en',
];
\$payload = [
  'alert_name' => 'E2E Alert',
  'prof_email_address' => '${EMAIL}',
  'frequence' => 'weekly',
  'criteria_json' => \$serializer->toJson(\$criteria),
  'search_url' => '${SEARCH_URL}',
  'search_path' => '${SEARCH_PATH}',
];
\$entity = \$repo->createFromSubmission(\$payload);
if (\$entity === NULL) {
  throw new \\RuntimeException('Expected alert entity to be created.');
}
\$duplicate = \$repo->createFromSubmission(\$payload);
if (\$duplicate !== NULL) {
  throw new \\RuntimeException('Duplicate alert should be rejected.');
}
echo 'entity:' . \$entity->id();
"

echo
echo "Repository create + dedup OK."

ps_e2e_drush ev "
use Drupal\\user\\Entity\\User;
use Drupal\\webform\\Entity\\WebformSubmission;

\$user = User::create([
  'name' => 'alert_e2e_' . time(),
  'mail' => '${AUTH_EMAIL}',
  'status' => 1,
]);
\$user->save();
user_login_finalize(\$user);

\$serializer = \\Drupal::service('ps_search.alert_criteria_serializer');
\$criteria = [
  'operation_type' => 'VEN',
  'asset_type' => 'BUR',
  'search_url' => '${SEARCH_URL}',
  'search_path' => '${SEARCH_PATH}',
  'langcode' => 'en',
];
\$before = count(\\Drupal::entityTypeManager()->getStorage('search_alert')->loadMultiple());
\$submission = WebformSubmission::create([
  'webform_id' => 'search_alert',
  'uid' => (int) \$user->id(),
  'data' => [
    'alert_name' => 'Auth E2E Alert',
    'prof_email_address' => '${AUTH_EMAIL}',
    'frequence' => 'weekly',
    'legal' => '1',
    'criteria_json' => \$serializer->toJson(\$criteria),
    'search_url' => '${SEARCH_URL}',
    'search_path' => '${SEARCH_PATH}',
  ],
]);
\$submission->save();
\$after = count(\\Drupal::entityTypeManager()->getStorage('search_alert')->loadMultiple());
if (\$after <= \$before) {
  throw new \\RuntimeException('Webform submission did not create search alert entity.');
}
echo 'webform-auth:ok uid:' . \$user->id();
"

echo
echo "Authenticated webform submission OK."
echo "Search alert E2E passed."
