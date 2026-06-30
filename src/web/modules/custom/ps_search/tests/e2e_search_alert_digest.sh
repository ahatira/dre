#!/usr/bin/env bash
# E2E — search alert digest email via Mailpit + matcher smoke (Phase B §5).
set -euo pipefail

# shellcheck source=/dev/null
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/../../../../.." && pwd)/scripts/e2e/common.sh"

MAILPIT_API="${MAILPIT_API:-http://localhost:8025/api/v1}"
EMAIL="alert-digest-$(date +%s)@example.com"
EMAIL_DEFAULT_IMAGE="alert-digest-default-$(date +%s)@example.com"
SEARCH_URL="${BASE_URL}/for-rent/office/"
SEARCH_PATH="/for-rent/office/"
SKIP_MAILPIT_CLEAR="${SKIP_MAILPIT_CLEAR:-0}"

echo "== Search alert digest E2E =="
echo "Recipient: ${EMAIL}"

if [[ "${SKIP_MAILPIT_CLEAR}" != "1" ]]; then
  if curl -sS -o /dev/null -w '%{http_code}' "${MAILPIT_API}/messages" | grep -qE '^200$'; then
    curl -sS -X DELETE "${MAILPIT_API}/messages" >/dev/null || true
    echo "Mailpit inbox cleared."
  else
    echo "WARNING: Mailpit not reachable at ${MAILPIT_API} — skipping inbox clear."
  fi
else
  echo "Mailpit inbox preserved (SKIP_MAILPIT_CLEAR=1)."
fi

ps_e2e_drush cset ps_search.alert_settings enabled 1 -y >/dev/null

send_digest() {
  local recipient="$1"
  local nid="$2"
  ps_e2e_drush ev "
use Drupal\\ps_search\\Entity\\SearchAlert;
use Drupal\\ps_search\\Entity\\SearchAlertInterface;

\$serializer = \\Drupal::service('ps_search.alert_criteria_serializer');
\$criteria = [
  'operation_type' => 'LOC',
  'asset_type' => 'BUR',
  'search_url' => '${SEARCH_URL}',
  'search_path' => '${SEARCH_PATH}',
  'langcode' => 'en',
];
\$json = \$serializer->toJson(\$criteria);

\$alert = SearchAlert::create([
  'alert_name' => 'Digest E2E',
  'prof_email' => '${recipient}',
  'frequence' => SearchAlertInterface::FREQUENCE_WEEKLY,
  'optout_email' => FALSE,
  'optout_sms' => FALSE,
  'optout_tel' => FALSE,
  'criteria' => \$json,
  'criteria_hash' => \$serializer->hash(\$criteria),
  'search_url' => '${SEARCH_URL}',
  'search_path' => '${SEARCH_PATH}',
  'alert_status' => SearchAlertInterface::STATUS_ACTIVE,
  'uid' => 0,
  'langcode' => 'en',
  'last_sent' => 0,
]);
\$alert->save();

\$mailer = \\Drupal::service('ps_search.alert_mailer');
\$sent = \$mailer->sendDigest(\$alert, [(int) ${nid}]);
if (!\$sent) {
  throw new \\RuntimeException('Mailer failed to send digest.');
}
\$mailer->markSent(\$alert, [(int) ${nid}]);
echo 'digest:sent alert:' . \$alert->id() . ' nid:' . (int) ${nid};
"
}

RESULT=$(ps_e2e_drush ev "
\$offerStorage = \\Drupal::entityTypeManager()->getStorage('node');
\$offerIds = \$offerStorage->getQuery()
  ->accessCheck(FALSE)
  ->condition('type', 'offer')
  ->condition('status', 1)
  ->range(0, 1)
  ->execute();
if (\$offerIds === []) {
  echo 'skip:no_offers';
  return;
}
echo 'nid:' . (int) reset(\$offerIds);
")

echo "${RESULT}"

if echo "${RESULT}" | grep -q 'skip:no_offers'; then
  echo "WARNING: No published offers — digest mailer test skipped."
  exit 0
fi

NID="${RESULT#nid:}"
DIGEST_RESULT=$(send_digest "${EMAIL}" "${NID}")
echo "${DIGEST_RESULT}"
echo "${DIGEST_RESULT}" | grep -q 'digest:sent'

sleep 1

assert_digest_html() {
  local recipient="$1"
  local label="$2"
  if ! curl -sS -o /tmp/ps-mailpit-messages.json -w '%{http_code}' "${MAILPIT_API}/messages" | grep -qE '^200$'; then
    echo "WARNING: Mailpit API unavailable — ${label} HTML checks skipped."
    return 0
  fi
  MAILPIT_API="${MAILPIT_API}" RECIPIENT="${recipient}" BASE_HOST="${BASE#http://}" python3 - <<'PY'
import json, os, re, sys, urllib.request

api = os.environ["MAILPIT_API"]
recipient = os.environ["RECIPIENT"]
base_host = os.environ.get("BASE_HOST", "com.localhost:8080")

data = json.load(urllib.request.urlopen(f"{api}/messages"))
messages = data.get("messages", [])
match = None
for m in messages:
  if any(recipient.lower() in (r.get("Address") or "").lower() for r in (m.get("To") or [])):
    match = m
    break
if match is None:
  raise SystemExit(f"No Mailpit message for {recipient}")

mid = match.get("ID") or match.get("id")
detail = json.load(urllib.request.urlopen(f"{api}/message/{mid}"))
html = (detail.get("HTML") or detail.get("Text") or "").lower()

checks = {
  "search_card": "ps-offer-email-search-card" in html,
  "criteria_block": "search criteria" in html,
  "primary_cta": "background:#00915a" in html or "background: #00915a" in html,
  "saved_search_cta": "view your saved search" in html,
  "offer_image": "ps-offer-email-search-card__image" in html,
  "valid_image_host": True,
}

if "ps-offer-email-search-card__image" in html:
  imgs = re.findall(r'<img[^>]+class="ps-offer-email-search-card__image"[^>]*>', html, re.I)
  if not imgs:
    imgs = re.findall(r'<img[^>]+ps-offer-email-search-card__image[^>]*>', html, re.I)
  if imgs:
    src = re.search(r'src="([^"]+)"', imgs[0], re.I)
    if src:
      checks["valid_image_host"] = "localhost" in src.group(1) or "cid:" in src.group(1)

failed = [k for k, ok in checks.items() if not ok]
if failed:
  raise SystemExit("Digest HTML checks failed: " + ",".join(failed))
print("mailpit:ok")
PY
  echo "Mailpit received ${label} with search card, criteria and primary CTA."
}

assert_digest_html "${EMAIL}" "digest email"

DEFAULT_RESULT=$(ps_e2e_drush ev "
\$storage = \\Drupal::entityTypeManager()->getStorage('node');
\$title = 'Digest E2E Default Image Offer';
\$ids = \$storage->getQuery()->accessCheck(FALSE)->condition('type','offer')->condition('title', \$title)->execute();
foreach (\$ids as \$id) { \$storage->load(\$id)?->delete(); }
\$node = \$storage->create(['type' => 'offer', 'title' => \$title]);
\$node->set('field_reference_auto', 0);
\$node->set('field_reference', 'DIGEST-DEFAULT-IMG');
if (\$node->hasField('field_operation_type')) { \$node->set('field_operation_type', 'LOC'); }
if (\$node->hasField('field_asset_type')) { \$node->set('field_asset_type', 'BUR'); }
if (\$node->hasField('field_budget_value')) { \$node->set('field_budget_value', 0); }
if (\$node->hasField('field_surfaces')) {
  \$node->set('field_surfaces', [['qualification' => 'TOTAL', 'value' => 120, 'unit' => 'M2']]);
}
if (\$node->hasField('field_gallery')) { \$node->set('field_gallery', []); }
if (\$node->hasField('field_media_gallery')) { \$node->set('field_media_gallery', []); }
try {
  \$node->setPublished();
  \$node->save();
  echo 'nid:' . \$node->id();
}
catch (Throwable \$e) {
  echo 'skip:seed_failed';
}
" 2>/dev/null | tail -1)

if [[ "${DEFAULT_RESULT}" == nid:* ]]; then
  DEFAULT_NID="${DEFAULT_RESULT#nid:}"
  echo "--- Default image offer digest (nid=${DEFAULT_NID}) ---"
  DEFAULT_DIGEST=$(send_digest "${EMAIL_DEFAULT_IMAGE}" "${DEFAULT_NID}")
  echo "${DEFAULT_DIGEST}"
  sleep 1
  MAILPIT_API="${MAILPIT_API}" RECIPIENT="${EMAIL_DEFAULT_IMAGE}" python3 - <<'PY'
import json, os, re, sys, urllib.request
api = os.environ["MAILPIT_API"]
recipient = os.environ["RECIPIENT"]
data = json.load(urllib.request.urlopen(f"{api}/messages"))
match = next((m for m in data.get("messages", []) if any(recipient.lower() in (r.get("Address") or "").lower() for r in (m.get("To") or []))), None)
if match is None:
  raise SystemExit(f"No Mailpit message for {recipient}")
mid = match.get("ID") or match.get("id")
html = json.load(urllib.request.urlopen(f"{api}/message/{mid}")).get("HTML") or ""
if "ps-offer-email-search-card__image" not in html.lower():
  raise SystemExit("Default-image digest missing offer image markup")
print("mailpit:default_image_ok")
PY
  echo "Default-image offer digest includes fallback image markup."
else
  echo "WARNING: Could not seed offer without gallery (${DEFAULT_RESULT})."
fi

ps_e2e_drush ps:search:alerts:process --purge=0 >/dev/null
echo "Drush ps:search:alerts:process smoke OK."

echo "Search alert digest E2E passed."
