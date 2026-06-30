#!/usr/bin/env bash
# B2B tests — contact hub webform email handlers (notification + confirmation via Mailpit).
# Submits completed WebformSubmission entities via Drush and asserts handler payloads.
set -euo pipefail

# shellcheck source=/dev/null
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/../../../../.." && pwd)/scripts/e2e/common.sh"

MAILPIT_API="${MAILPIT_API:-http://localhost:8025/api/v1}"
MAILPIT="${MAILPIT_URL:-http://localhost:8025}"

WEBFORMS=(
  find_property
  entrust_search
  get_advice
  entrust_property
  invest_sell
  other_request
)

PASS=0
CURRENT_WEBFORM=""

pass() { echo "    PASS: $1"; PASS=$((PASS + 1)); }
fail() {
  echo ""
  echo "  *** ARRET — échec sur webform « ${CURRENT_WEBFORM} » ***"
  echo "  FAIL: $1"
  echo ""
  echo "Passed before stop: $PASS"
  exit 1
}

mailpit_clear() {
  curl -sS -X DELETE "${MAILPIT_API}/messages" >/dev/null 2>&1 || true
}

mailpit_count() {
  curl -sS "${MAILPIT_API}/messages" 2>/dev/null \
    | python3 -c "import json,sys; print(json.load(sys.stdin).get('total', 0))" 2>/dev/null \
    || echo "0"
}

# submit_contact_webform WEBFORM_ID JSON_DATA
# Prints: OK:sid=N or FAIL:reason
submit_contact_webform() {
  local webform_id="$1"
  local json_data="$2"
  ps_e2e_drush php:eval "
use Drupal\\webform\\Entity\\WebformSubmission;

\$webformId = '${webform_id}';
\$data = json_decode('${json_data}', TRUE);
if (!is_array(\$data)) {
  print 'FAIL:invalid_json';
  return;
}
\$webform = \\Drupal::entityTypeManager()->getStorage('webform')->load(\$webformId);
if (!\$webform) {
  print 'FAIL:missing_webform';
  return;
}
try {
  \$submission = WebformSubmission::create([
    'webform_id' => \$webformId,
    'data' => \$data,
  ]);
  \$submission->set('in_draft', FALSE);
  \$submission->save();
  if (!\$submission->isCompleted()) {
    print 'FAIL:not_completed';
    return;
  }
  print 'OK:sid=' . \$submission->id();
}
catch (\\Throwable \$e) {
  print 'FAIL:' . preg_replace('/\\s+/', ' ', \$e->getMessage());
}
" 2>/dev/null | tail -1
}

# fetch_handler_meta WEBFORM_ID
# Prints: site_mail=...|notif_subject=...|confirm_subject=...
fetch_handler_meta() {
  local webform_id="$1"
  ps_e2e_drush php:eval "
\$webform = \\Drupal::entityTypeManager()->getStorage('webform')->load('${webform_id}');
if (!\$webform) { print 'FAIL:missing_webform'; return; }
\$notif = \$webform->getHandler('email_notification');
\$confirm = \$webform->getHandler('email_confirmation');
if (!\$notif || !\$confirm) { print 'FAIL:missing_handlers'; return; }
\$ns = \$notif->getSettings();
\$cs = \$confirm->getSettings();
print 'site_mail=' . \\Drupal::config('system.site')->get('mail') . '|';
print 'notif_subject=' . (\$ns['subject'] ?? '') . '|';
print 'confirm_subject=' . (\$cs['subject'] ?? '') . '|';
print 'notif_to=' . (\$ns['to_mail'] ?? '') . '|';
print 'confirm_to=' . (\$cs['to_mail'] ?? '');
" 2>/dev/null | tail -1
}

# assert_emails META VISITOR_EMAIL JSON_ASSERTIONS
# META: site_mail=...|notif_subject=...|confirm_subject=...|...
# JSON_ASSERTIONS: {"notification":["needle1",...],"confirmation":["needle1",...]}
assert_emails() {
  local meta="$1"
  local visitor_email="$2"
  local assertions_json="$3"

  local site_mail notif_subject confirm_subject
  site_mail="$(printf '%s' "$meta" | sed -n 's/.*site_mail=\([^|]*\).*/\1/p')"
  notif_subject="$(printf '%s' "$meta" | sed -n 's/.*notif_subject=\([^|]*\).*/\1/p')"
  confirm_subject="$(printf '%s' "$meta" | sed -n 's/.*confirm_subject=\([^|]*\).*/\1/p')"

  sleep 1
  local count
  count="$(mailpit_count)"
  if [[ "$count" != "2" ]]; then
    fail "Mailpit expected 2 messages, got ${count}"
  fi
  pass "Mailpit received 2 messages"

  local result
  result="$(MAILPIT_API="${MAILPIT_API}" \
    SITE_MAIL="${site_mail}" \
    VISITOR_EMAIL="${visitor_email}" \
    NOTIF_SUBJECT="${notif_subject}" \
    CONFIRM_SUBJECT="${confirm_subject}" \
    ASSERTIONS="${assertions_json}" \
    python3 - <<'PY'
import json, os, sys, urllib.request

api = os.environ["MAILPIT_API"]
site_mail = os.environ["SITE_MAIL"].lower()
visitor = os.environ["VISITOR_EMAIL"].lower()
notif_subj = os.environ["NOTIF_SUBJECT"]
confirm_subj = os.environ["CONFIRM_SUBJECT"]
assertions = json.loads(os.environ["ASSERTIONS"])

def fetch_messages():
    return json.load(urllib.request.urlopen(f"{api}/messages?limit=10")).get("messages") or []

def fetch_full(msg_id):
    return json.load(urllib.request.urlopen(f"{api}/message/{msg_id}"))

def addr_list(field):
    if isinstance(field, list):
        return [((e.get("Address") or "").lower(), (e.get("Name") or "")) for e in field]
    if isinstance(field, dict):
        return [((field.get("Address") or "").lower(), (field.get("Name") or ""))]
    return []

def find_message(messages, to_email, subject):
    for msg in messages:
        subj = msg.get("Subject") or ""
        if subject and subject not in subj:
            continue
        for addr, _name in addr_list(msg.get("To") or []):
            if to_email in addr:
                return msg
    return None

messages = fetch_messages()
notif_list = find_message(messages, site_mail, notif_subj)
confirm_list = find_message(messages, visitor, confirm_subj)
if not notif_list:
    print("FAIL:notification_not_found")
    sys.exit(0)
if not confirm_list:
    print("FAIL:confirmation_not_found")
    sys.exit(0)

notif = fetch_full(notif_list["ID"])
confirm = fetch_full(confirm_list["ID"])

def body_text(msg):
    return (msg.get("Text") or "") + (msg.get("HTML") or "")

def check(msg, label, checks):
    text = body_text(msg)
    missing = [n for n in checks.get("contains", []) if n not in text]
    if missing:
        print(f"FAIL:{label}_missing:" + ",".join(missing[:3]))
        return False
    for needle in checks.get("not_contains", []):
        if needle in text:
            print(f"FAIL:{label}_unexpected:{needle}")
            return False
    return True

# Notification headers.
from_addr = (notif.get("From") or {}).get("Address", "").lower()
from_name = (notif.get("From") or {}).get("Name", "")
reply_addrs = [a for a, _n in addr_list(notif.get("ReplyTo") or [])]
if visitor not in from_addr:
    print("FAIL:notification_from")
    sys.exit(0)
if "B2B" not in from_name or "Tester" not in from_name:
    print("FAIL:notification_from_name")
    sys.exit(0)
if not any(visitor in r for r in reply_addrs):
    print("FAIL:notification_reply_to")
    sys.exit(0)

# Confirmation headers.
confirm_to = [a for a, _n in addr_list(confirm.get("To") or [])]
if not any(visitor in t for t in confirm_to):
    print("FAIL:confirmation_to")
    sys.exit(0)
confirm_from = (confirm.get("From") or {}).get("Address", "").lower()
if site_mail not in confirm_from:
    print("FAIL:confirmation_from")
    sys.exit(0)

if not check(notif, "notification", assertions.get("notification", {})):
    sys.exit(0)
if not check(confirm, "confirmation", assertions.get("confirmation", {})):
    sys.exit(0)

print("PASS:emails_ok")
PY
)"

  if [[ "$result" == "PASS:emails_ok" ]]; then
    pass "Notification subject, headers and body"
    pass "Confirmation subject, headers and body"
  else
    fail "Email assertions (${result})"
  fi
}

# Per-webform submission payloads (flat checkbox keys for PostgreSQL delta column).
declare -A WEBFORM_DATA=(
  [find_property]='{"transaction_type":"LOC","search_type":"BUR","firstname":"B2B","lastname":"Tester","company_name":"PS QA Corp","job_title":"Commercial","prof_phone":"+33102030405","prof_email_address":"VISITOR","qualification_comment":"B2B email handler test — find_property."}'
  [entrust_search]='{"transaction_type":"LOC","search_type":"BUR","firstname":"B2B","lastname":"Tester","company_name":"PS QA Corp","job_title":"Commercial","prof_phone":"+33102030405","prof_email_address":"VISITOR","qualification_comment":"B2B email handler test — entrust_search."}'
  [get_advice]='{"consulting_type":["strategy"],"firstname":"B2B","lastname":"Tester","company_name":"PS QA Corp","job_title":"Commercial","prof_phone":"+33102030405","prof_email_address":"VISITOR","qualification_comment":"B2B email handler test — get_advice."}'
  [entrust_property]='{"tf_assetpostalcode":"75002","totale_surface":"500","transaction_type":"LOC","search_type":"BUR","firstname":"B2B","lastname":"Tester","company_name":"PS QA Corp","job_title":"Commercial","prof_phone":"+33102030405","prof_email_address":"VISITOR","qualification_comment":"B2B email handler test — entrust_property."}'
  [invest_sell]='{"transaction_type":"VEN","search_type":"BUR","totale_surface":"1200","firstname":"B2B","lastname":"Tester","company_name":"PS QA Corp","job_title":"Commercial","prof_phone":"+33102030405","prof_email_address":"VISITOR","qualification_comment":"B2B email handler test — invest_sell."}'
  [other_request]='{"other_need":["services"],"firstname":"B2B","lastname":"Tester","company_name":"PS QA Corp","prof_phone":"+33102030405","prof_email_address":"VISITOR","qualification_comment":"B2B email handler test — other_request."}'
)

declare -A WEBFORM_ASSERTIONS=(
  [find_property]='{"notification":{"contains":["Find property","Rent","Office","PS QA Corp","Sales","contact-b2b-find_property@example.com","B2B email handler test — find_property."]},"confirmation":{"contains":["Rent","Office","PS QA Corp","B2B email handler test — find_property."],"not_contains":["Thank you for your message","See you soon!","Submitted on","{Empty}"]}}'
  [entrust_search]='{"notification":{"contains":["Entrust search","Rent","Office","PS QA Corp","B2B email handler test — entrust_search."]},"confirmation":{"contains":["Rent","Office","PS QA Corp","B2B email handler test — entrust_search."],"not_contains":["Thank you for your message","See you soon!","Submitted on","{Empty}"]}}'
  [get_advice]='{"notification":{"contains":["Consulting","Real estate strategy","PS QA Corp","B2B email handler test — get_advice."]},"confirmation":{"contains":["Real estate strategy","PS QA Corp","B2B email handler test — get_advice."],"not_contains":["Thank you for your message","See you soon!","Submitted on","{Empty}"]}}'
  [entrust_property]='{"notification":{"contains":["Entrust property","75002","500","Office","B2B email handler test — entrust_property."]},"confirmation":{"contains":["75002","500","Office","B2B email handler test — entrust_property."],"not_contains":["Thank you for your message","See you soon!","Submitted on","{Empty}"]}}'
  [invest_sell]='{"notification":{"contains":["Sell","Office","1200","PS QA Corp","B2B email handler test — invest_sell."]},"confirmation":{"contains":["Sell","1200","PS QA Corp","B2B email handler test — invest_sell."],"not_contains":["Thank you for your message","See you soon!","Submitted on","{Empty}"]}}'
  [other_request]='{"notification":{"contains":["Other contact","Propose services","PS QA Corp","B2B email handler test — other_request."],"not_contains":["JOB TITLE"]},"confirmation":{"contains":["Propose services","PS QA Corp","B2B email handler test — other_request."],"not_contains":["Thank you for your message","See you soon!","Submitted on","{Empty}","JOB TITLE"]}}'
)

echo "=== PS Form B2B — Contact hub email handlers / Mailpit ($BASE) ==="

if ! curl -sS -o /dev/null -w '%{http_code}' "${MAILPIT_API}/messages" | grep -qE '^200$'; then
  echo "FAIL: Mailpit unreachable at ${MAILPIT_API}"
  exit 1
fi
pass "Mailpit reachable at ${MAILPIT}"

GLOBAL_META="$(fetch_handler_meta find_property)"
if [[ "$GLOBAL_META" == FAIL:* ]]; then
  echo "FAIL: Could not load handler metadata (${GLOBAL_META})"
  exit 1
fi
SITE_MAIL="$(printf '%s' "$GLOBAL_META" | sed -n 's/.*site_mail=\([^|]*\).*/\1/p')"
pass "Site notification mail: ${SITE_MAIL}"

EMAIL_DISPLAY_TITLE="$(ps_e2e_drush cget ps_email.contact webforms.find_property.display_title --format=string 2>/dev/null || echo 'Your request has been sent')"
EMAIL_GREETING_PREFIX="$(ps_e2e_drush cget ps_email.contact webforms.find_property.greeting_prefix --format=string 2>/dev/null || echo 'Hello')"
COMMON_CONFIRM_ASSERT="$(EMAIL_DISPLAY_TITLE="${EMAIL_DISPLAY_TITLE}" EMAIL_GREETING_PREFIX="${EMAIL_GREETING_PREFIX}" python3 - <<'PY'
import json, os
common = {
  "contains": [
    os.environ["EMAIL_DISPLAY_TITLE"],
    os.environ["EMAIL_GREETING_PREFIX"] + " B2B,",
    "For reference, your request",
    "Data Protection Notice",
  ],
  "not_contains": [
    "See you soon!",
    "Submitted on",
    "{Empty}",
  ],
}
print(json.dumps(common))
PY
)"
pass "Confirmation email config loaded (title: ${EMAIL_DISPLAY_TITLE})"

for webform in "${WEBFORMS[@]}"; do
  CURRENT_WEBFORM="$webform"
  visitor_email="contact-b2b-${webform}@example.com"

  echo ""
  echo "=== Webform: ${webform} ==="

  meta="$(fetch_handler_meta "$webform")"
  if [[ "$meta" == FAIL:* ]]; then
    fail "Handler metadata (${meta})"
  fi
  pass "Handlers loaded (notification + confirmation)"

  mailpit_clear
  pass "Mailpit cleared"

  json_data="${WEBFORM_DATA[$webform]//VISITOR/${visitor_email}}"

  echo "  --- Submit completed submission via Drush ---"
  submit_result="$(submit_contact_webform "$webform" "$json_data")"
  if [[ "$submit_result" != OK:sid=* ]]; then
    fail "WebformSubmission save (${submit_result})"
  fi
  pass "Submission saved (${submit_result})"

  echo "  --- Assert notification + confirmation emails ---"
  merged_assert="$(COMMON_CONFIRM_ASSERT="${COMMON_CONFIRM_ASSERT}" WEBFORM_ASSERT="${WEBFORM_ASSERTIONS[$webform]}" python3 - <<'PY'
import json, os
base = json.loads(os.environ["WEBFORM_ASSERT"])
common = json.loads(os.environ["COMMON_CONFIRM_ASSERT"])
confirm = base.get("confirmation", {})
confirm["contains"] = list(dict.fromkeys((confirm.get("contains") or []) + common.get("contains", [])))
confirm["not_contains"] = list(dict.fromkeys((confirm.get("not_contains") or []) + common.get("not_contains", [])))
base["confirmation"] = confirm
print(json.dumps(base))
PY
)"
  assert_emails "$meta" "$visitor_email" "$merged_assert"
done

echo ""
echo "=== Results: $PASS passed, 0 failed (${#WEBFORMS[@]} webforms × 2 emails) ==="
