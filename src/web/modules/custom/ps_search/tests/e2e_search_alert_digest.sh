#!/usr/bin/env bash
# E2E — search alert digest email via Mailpit + matcher smoke (Phase B §5).
set -euo pipefail

BASE_URL="${BASE_URL:-http://localhost:8080}"
MAILPIT_API="${MAILPIT_API:-http://localhost:8025/api/v1}"
EMAIL="alert-digest-$(date +%s)@example.com"
SEARCH_URL="${BASE_URL}/for-rent/office/"
SEARCH_PATH="/for-rent/office/"

echo "== Search alert digest E2E =="
echo "Recipient: ${EMAIL}"

# Clear Mailpit inbox when API is reachable.
if curl -sS -o /dev/null -w '%{http_code}' "${MAILPIT_API}/messages" | grep -qE '^200$'; then
  curl -sS -X DELETE "${MAILPIT_API}/messages" >/dev/null || true
  echo "Mailpit inbox cleared."
else
  echo "WARNING: Mailpit not reachable at ${MAILPIT_API} — skipping inbox clear."
fi

docker exec -i ps_php sh -lc "cd /var/www/html && vendor/bin/drush cset ps_search.alert_settings enabled 1 -y >/dev/null"

RESULT=$(docker exec -i ps_php sh -lc "cd /var/www/html && vendor/bin/drush ev \"
use Drupal\\\\ps_search\\\\Entity\\\\SearchAlert;
use Drupal\\\\ps_search\\\\Entity\\\\SearchAlertInterface;

\\\$offerStorage = \\\Drupal::entityTypeManager()->getStorage('node');
\\\$offerIds = \\\$offerStorage->getQuery()
  ->accessCheck(FALSE)
  ->condition('type', 'offer')
  ->condition('status', 1)
  ->range(0, 1)
  ->execute();
if (\\\$offerIds === []) {
  echo 'skip:no_offers';
  return;
}
\\\$nid = (int) reset(\\\$offerIds);

\\\$serializer = \\\Drupal::service('ps_search.alert_criteria_serializer');
\\\$criteria = [
  'operation_type' => 'LOC',
  'asset_type' => 'BUR',
  'search_url' => '${SEARCH_URL}',
  'search_path' => '${SEARCH_PATH}',
  'langcode' => 'en',
];
\\\$json = \\\$serializer->toJson(\\\$criteria);

\\\$alert = SearchAlert::create([
  'alert_name' => 'Digest E2E',
  'prof_email' => '${EMAIL}',
  'frequence' => SearchAlertInterface::FREQUENCE_WEEKLY,
  'optout_email' => FALSE,
  'optout_sms' => FALSE,
  'optout_tel' => FALSE,
  'criteria' => \\\$json,
  'criteria_hash' => \\\$serializer->hash(\\\$criteria),
  'search_url' => '${SEARCH_URL}',
  'search_path' => '${SEARCH_PATH}',
  'alert_status' => SearchAlertInterface::STATUS_ACTIVE,
  'uid' => 0,
  'langcode' => 'en',
  'last_sent' => 0,
]);
\\\$alert->save();

\\\$matcher = \\\Drupal::service('ps_search.alert_matcher');
\\\$matcherNids = \\\$matcher->findMatchingOfferIds(\\\$alert);

\\\$mailer = \\\Drupal::service('ps_search.alert_mailer');
\\\$sent = \\\$mailer->sendDigest(\\\$alert, [\\\$nid]);
if (!\\\$sent) {
  throw new \\\RuntimeException('Mailer failed to send digest.');
}
\\\$mailer->markSent(\\\$alert, [\\\$nid]);

echo 'digest:sent alert:' . \\\$alert->id() . ' nid:' . \\\$nid . ' matcher_count:' . count(\\\$matcherNids);
\"")

echo "${RESULT}"

if echo "${RESULT}" | grep -q 'skip:no_offers'; then
  echo "WARNING: No published offers — digest mailer test skipped."
  exit 0
fi

echo "${RESULT}" | grep -q 'digest:sent'

sleep 1

if curl -sS -o /tmp/ps-mailpit-messages.json -w '%{http_code}' "${MAILPIT_API}/messages" | grep -qE '^200$'; then
  python3 - <<'PY' "${EMAIL}"
import json, sys
email = sys.argv[1]
data = json.load(open('/tmp/ps-mailpit-messages.json'))
messages = data.get('messages', data if isinstance(data, list) else [])
found = any(
  any(email.lower() in (r.get('Address') or '').lower() for r in (m.get('To') or []))
  for m in messages
)
if not found:
  raise SystemExit(f'No Mailpit message for {email}')
print('mailpit:ok')
PY
  echo "Mailpit received digest email."
else
  echo "WARNING: Mailpit API unavailable — mail send verified via Drush only."
fi

docker exec -i ps_php sh -lc 'cd /var/www/html && vendor/bin/drush ps:search:alerts:process --purge=0' >/dev/null
echo "Drush ps:search:alerts:process smoke OK."

echo "Search alert digest E2E passed."
