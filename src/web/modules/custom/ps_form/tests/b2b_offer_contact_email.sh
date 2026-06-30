#!/usr/bin/env bash
# B2B tests — offer contact / schedule visit confirmation emails (Mailpit).
set -euo pipefail

# shellcheck source=/dev/null
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/../../../../.." && pwd)/scripts/e2e/common.sh"

MAILPIT_API="${MAILPIT_API:-http://localhost:8025/api/v1}"
PASS=0
CURRENT=""

pass() { echo "    PASS: $1"; PASS=$((PASS + 1)); }
fail() {
  echo ""
  echo "  *** ARRET — échec sur « ${CURRENT} » ***"
  echo "  FAIL: $1"
  exit 1
}

mailpit_clear() {
  curl -sS -X DELETE "${MAILPIT_API}/messages" >/dev/null 2>&1 || true
}

seed_offer() {
  ps_e2e_drush php:eval "
\$storage = \\Drupal::entityTypeManager()->getStorage('node');
\$title = 'B2B Email Offer Card';
\$ids = \$storage->getQuery()->accessCheck(FALSE)->condition('type','offer')->condition('title', \$title)->execute();
foreach (\$ids as \$id) { \$storage->load(\$id)?->delete(); }
\$node = \$storage->create(['type' => 'offer', 'title' => \$title]);
\$node->set('field_reference_auto', 0);
\$node->set('field_reference', 'B2B-EMAIL-001');
if (\$node->hasField('field_operation_type')) { \$node->set('field_operation_type', 'LOC'); }
if (\$node->hasField('field_asset_type')) { \$node->set('field_asset_type', 'BUR'); }
if (\$node->hasField('field_budget_value')) { \$node->set('field_budget_value', 0); }
try {
  \$node->setPublished();
  \$node->save();
  print 'OK:nid=' . \$node->id() . ':ref=B2B-EMAIL-001';
}
catch (Throwable \$e) {
  \$fallback = \$storage->getQuery()->accessCheck(FALSE)->condition('type','offer')->condition('status', 1)->range(0, 1)->execute();
  if (\$fallback === []) { print 'FAIL:' . \$e->getMessage(); return; }
  \$existing = \$storage->load((int) reset(\$fallback));
  \$ref = trim((string) (\$existing->get('field_reference')->value ?? ''));
  print 'OK:nid=' . \$existing->id() . ':ref=' . (\$ref !== '' ? \$ref : 'unknown');
}
" 2>/dev/null | tail -1
}

submit_offer_webform() {
  local webform_id="$1"
  local email="$2"
  local nid="$3"
  ps_e2e_drush php:eval "
use Drupal\\webform\\Entity\\WebformSubmission;
\$submission = WebformSubmission::create([
  'webform_id' => '${webform_id}',
  'entity_type' => 'node',
  'entity_id' => (int) '${nid}',
  'data' => [
    'first_name' => 'B2B',
    'last_name' => 'Offer',
    'email' => '${email}',
    'company' => 'PS QA Corp',
    'phone' => '+33102030405',
    'message' => 'B2B offer email card test.',
  ],
]);
\$submission->set('in_draft', FALSE);
\$submission->save();
print 'OK:sid=' . \$submission->id();
" 2>/dev/null | tail -1
}

assert_offer_email() {
  local email="$1"
  local ref="$2"
  sleep 1
  MAILPIT_API="${MAILPIT_API}" EMAIL="${email}" REF="${ref}" python3 - <<'PY'
import json, os, sys, urllib.request
api = os.environ["MAILPIT_API"]
email = os.environ["EMAIL"].lower()
ref = os.environ["REF"]
messages = json.load(urllib.request.urlopen(f"{api}/messages?limit=5")).get("messages") or []
match = None
for msg in messages:
  for entry in msg.get("To") or []:
    if email in (entry.get("Address") or "").lower():
      match = msg
      break
  if match:
    break
if match is None:
  print("FAIL:no_message")
  sys.exit(0)
body = json.load(urllib.request.urlopen(f"{api}/message/{match['ID']}")).get("HTML") or ""
checks = [
  ref in body,
  "View the property" in body or "B2B Email Offer Card" in body,
  "#1f2a36" in body.lower() or "Data Protection Notice" in body,
  "See you soon" not in body,
]
if all(checks):
  print("PASS:ok")
else:
  missing = []
  if ref not in body: missing.append("reference")
  if "View the property" not in body and "B2B Email Offer Card" not in body: missing.append("offer_card")
  if "#1f2a36" not in body.lower() and "Data Protection Notice" not in body: missing.append("footer")
  if "See you soon" in body: missing.append("signoff")
  print("FAIL:" + ",".join(missing))
PY
}

echo "=== PS Form B2B — Offer contact emails / Mailpit ($BASE) ==="

if ! curl -sS -o /dev/null -w '%{http_code}' "${MAILPIT_API}/messages" | grep -qE '^200$'; then
  fail "Mailpit unreachable at ${MAILPIT_API}"
fi
pass "Mailpit reachable"

seed_result="$(seed_offer)"
if [[ "$seed_result" != OK:nid=* ]]; then
  fail "Offer seed failed (${seed_result})"
fi
OFFER_NID="${seed_result#OK:nid=}"
OFFER_NID="${OFFER_NID%%:ref=*}"
OFFER_REF="${seed_result#*:ref=}"
pass "Published offer seeded (nid=${OFFER_NID}, ref=${OFFER_REF})"

for webform in offer_contact schedule_visit; do
  CURRENT="$webform"
  visitor_email="offer-b2b-${webform}@example.com"
  echo ""
  echo "=== Webform: ${webform} ==="

  mailpit_clear
  pass "Mailpit cleared"

  submit_result="$(submit_offer_webform "$webform" "$visitor_email" "$OFFER_NID")"
  if [[ "$submit_result" != OK:sid=* ]]; then
    fail "Submission save (${submit_result})"
  fi
  pass "Submission saved (${submit_result})"

  result="$(assert_offer_email "$visitor_email" "$OFFER_REF")"
  if [[ "$result" == "PASS:ok" ]]; then
    pass "Confirmation email with offer card + rich footer"
  else
    fail "Email assertions (${result})"
  fi
done

echo ""
echo "=== Results: $PASS passed, 0 failed (2 offer webforms) ==="
