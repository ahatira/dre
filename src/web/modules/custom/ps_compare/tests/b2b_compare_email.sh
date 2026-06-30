#!/usr/bin/env bash
# B2B smoke tests — Compare share email (Mailpit).
set -euo pipefail

# shellcheck source=/dev/null
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/../../../../.." && pwd)/scripts/e2e/common.sh"

MAILPIT="${MAILPIT_URL:-http://localhost:8025}"
PASS=0
FAIL=0
TEST_EMAIL="compare-test@example.com"

pass() { echo "  PASS: $1"; PASS=$((PASS + 1)); }
fail() { echo "  FAIL: $1"; FAIL=$((FAIL + 1)); }

echo "=== PS Compare B2B — Share email / Mailpit ($BASE) ==="

echo "--- Reset Mailpit ---"
curl -s -X DELETE "$MAILPIT/api/v1/messages" >/dev/null || true
pass "Mailpit messages cleared"

echo "--- Send comparison email via Drush ---"
SEND_RESULT=$(ps_e2e_drush php:eval "
\$recipient = '${TEST_EMAIL}';
\$account = \\Drupal\\user\\Entity\\User::load(1);
if (\$account === NULL) { print 'FAIL:no_admin'; return; }
\\Drupal::service('account_switcher')->switchTo(\$account);
\\Drupal::database()->delete('ps_compare_item')->condition('uid', (int) \$account->id())->execute();
\$storage = \\Drupal::entityTypeManager()->getStorage('node');
\$ids = array_values(\$storage->getQuery()->accessCheck(TRUE)->condition('type', 'offer')->range(0, 2)->execute());
if (count(\$ids) < 2) { \\Drupal::service('account_switcher')->switchBack(); print 'FAIL:not_enough_offers'; return; }
\$manager = \\Drupal::service('ps_compare.manager');
foreach (\$ids as \$id) { \$manager->addCompare(\$storage->load(\$id)); }
if (!\\Drupal::service('ps_compare.email_sender')->sendFromForm(\$recipient, 'Automated compare email test.')) {
  \\Drupal::service('account_switcher')->switchBack();
  print 'FAIL:send_failed';
  return;
}
\\Drupal::service('account_switcher')->switchBack();
print 'PASS:sent';
" 2>/dev/null | tail -1 || echo "FAIL:drush_error")

if [[ "$SEND_RESULT" == "PASS:sent" ]]; then
  pass "CompareEmailSender delivered message"
else
  fail "CompareEmailSender ($SEND_RESULT)"
  echo "=== Results: $PASS passed, $FAIL failed ==="
  exit 1
fi

sleep 1

echo "--- Verify Mailpit payload ---"
MAIL_JSON=$(curl -s "$MAILPIT/api/v1/messages?limit=1" 2>/dev/null || echo "")
if [[ -z "$MAIL_JSON" ]]; then
  fail "Mailpit API unreachable at $MAILPIT"
  echo "=== Results: $PASS passed, $FAIL failed ==="
  exit 1
fi

python3 - <<'PY' "$MAIL_JSON" "$TEST_EMAIL"
import json, sys, re
payload = json.loads(sys.argv[1])
recipient = sys.argv[2]
messages = payload.get("messages") or []
if not messages:
    print("FAIL:no_messages")
    sys.exit(0)
msg = messages[0]
to_list = msg.get("To") or []
if not any(recipient.lower() in (entry.get("Address") or "").lower() for entry in to_list):
    print("FAIL:wrong_recipient")
    sys.exit(0)
msg_id = msg.get("ID")
print(f"ID:{msg_id}")
PY

MSG_ID=$(python3 - <<'PY' "$MAIL_JSON" "$TEST_EMAIL"
import json, sys
payload = json.loads(sys.argv[1])
recipient = sys.argv[2]
messages = payload.get("messages") or []
if not messages:
    sys.exit(1)
msg = messages[0]
to_list = msg.get("To") or []
if not any(recipient.lower() in (entry.get("Address") or "").lower() for entry in to_list):
    sys.exit(1)
print(msg.get("ID") or "")
PY
)

if [[ -z "$MSG_ID" ]]; then
  fail "Mailpit message for $TEST_EMAIL not found"
else
  pass "Mailpit received message for $TEST_EMAIL"
fi

if [[ -n "$MSG_ID" ]]; then
  BODY=$(curl -s "$MAILPIT/api/v1/message/$MSG_ID" 2>/dev/null || echo "")
  CHECK=$(python3 - <<'PY' "$BODY"
import json, sys
try:
  data = json.loads(sys.argv[1])
except Exception:
  print("FAIL:invalid_json")
  sys.exit(0)
html = data.get("HTML") or ""
checks = {
  "data:image": "data:image" in html,
  "full_comparison": "View full comparison online" in html,
  "comparison_table": "role=\"presentation\"" in html and "border-collapse:collapse" in html,
  "share_url_with_ref": "refs=" in html,
  "no_property_cards": "View listing" not in html.split("View full comparison online", 1)[-1][:500] if "View full comparison online" in html else True,
  "no_summary_banner": "properties compared" not in html.lower(),
  "no_escaped_doctype": "&lt;!DOCTYPE" not in html and "&lt;html" not in html,
  "wrapper_logo": "cid:ps-email-header-logo@ps-project" in html or "header-logo.svg" in html,
  "header_logo_cid": "cid:ps-email-header-logo@ps-project" in html,
  "no_header_logo_base64": "data:image/svg+xml;base64" not in html.split("header-logo", 1)[0],
  "wrapper_green_title": "#00915a" in html.lower() or "00915a" in html.lower(),
  "wrapper_signoff": "See you soon" in html,
  "green_cta": "background:#00915a" in html.lower() or "background: #00915a" in html.lower(),
  "responsive_css": "@media only screen and (max-width:479px)" in html,
  "responsive_container": "border-collapse:collapse" in html and 'role="presentation"' in html,
}
failed = [k for k, ok in checks.items() if not ok]
print("PASS:body_ok" if not failed else "FAIL:" + ",".join(failed))
PY
)
  if [[ "$CHECK" == "PASS:body_ok" ]]; then
    pass "Email HTML contains comparison table and share URL with references"
  else
    fail "Email HTML checks ($CHECK)"
  fi
fi

echo ""
echo "=== Results: $PASS passed, $FAIL failed ==="
if [[ "$FAIL" -gt 0 ]]; then
  exit 1
fi
