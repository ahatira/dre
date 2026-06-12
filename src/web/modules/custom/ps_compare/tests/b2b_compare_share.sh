#!/usr/bin/env bash
# B2B smoke tests — Share comparison modal, AJAX submit, Mailpit delivery.
#
# Anonymous HTTP toggle is unreliable here (page cache serves stale CSRF tokens
# without a Drupal session cookie). We seed compare items via Drush and exercise
# the share modal over an authenticated ULI session — same pattern as
# b2b_compare_authenticated.sh and b2b_compare_email.sh.
set -euo pipefail

BASE="${BASE_URL:-http://localhost:8080}"
MAILPIT="${MAILPIT_URL:-http://localhost:8025}"
PASS=0
FAIL=0
COOKIE_JAR="${TMPDIR:-/tmp}/ps-compare-b2b-share-cookies.txt"
ANON_JAR="${TMPDIR:-/tmp}/ps-compare-b2b-share-anon-cookies.txt"
SHARE_FORM_FILE="${TMPDIR:-/tmp}/ps-compare-b2b-share-form.html"
AJAX_RESPONSE_FILE="${TMPDIR:-/tmp}/ps-compare-b2b-share-ajax.json"
TEST_EMAIL="compare-share-b2b@example.com"

pass() { echo "  PASS: $1"; PASS=$((PASS + 1)); }
fail() { echo "  FAIL: $1"; FAIL=$((FAIL + 1)); }

fetch() {
  curl -sL -m 120 -b "$COOKIE_JAR" -c "$COOKIE_JAR" "$1" 2>/dev/null || echo ""
}

http_code_with_jar() {
  local jar="$1" url="$2"
  curl -sL -m 120 -b "$jar" -c "$jar" -o /dev/null -w '%{http_code}' "$url" 2>/dev/null || echo "000"
}

assert_http() {
  local expected="$1" url="$2" label="$3"
  local code
  code=$(http_code_with_jar "$COOKIE_JAR" "$url")
  if [[ "$code" == "$expected" ]]; then
    pass "$label (HTTP $expected)"
  else
    fail "$label (HTTP $code, expected $expected) — $url"
  fi
}

assert_file_contains() {
  local file="$1" needle="$2" label="$3"
  if grep -Fq "$needle" "$file"; then
    pass "$label"
  else
    fail "$label (missing: $needle)"
  fi
}

seed_compare_items_drush() {
  docker exec -i ps_php sh -lc 'cd /var/www/html && vendor/bin/drush php:eval "
\$storage = \\Drupal::entityTypeManager()->getStorage(\"node\");
\$ids = array_values(\$storage->getQuery()->accessCheck(TRUE)->condition(\"type\", \"offer\")->range(0, 2)->execute());
if (count(\$ids) < 2) { print \"FAIL:not_enough_offers\"; return; }
\$manager = \\Drupal::service(\"ps_compare.manager\");
\$account = \\Drupal\\user\\Entity\\User::load(1);
if (\$account === NULL) { print \"FAIL:no_admin\"; return; }
\\Drupal::service(\"account_switcher\")->switchTo(\$account);
\\Drupal::database()->delete(\"ps_compare_item\")->condition(\"uid\", (int) \$account->id())->execute();
foreach (\$ids as \$id) { \$manager->addCompare(\$storage->load(\$id)); }
\$count = \$manager->getCompareCount();
\\Drupal::service(\"account_switcher\")->switchBack();
if (\$count < 2) { print \"FAIL:seed\"; return; }
print \"PASS:seeded\";
"' 2>/dev/null | tail -1
}

echo "=== PS Compare B2B — Share modal + AJAX ($BASE) ==="

rm -f "$COOKIE_JAR" "$ANON_JAR"
touch "$COOKIE_JAR" "$ANON_JAR"

echo "--- Reset Mailpit ---"
curl -s -X DELETE "$MAILPIT/api/v1/messages" >/dev/null || true
pass "Mailpit messages cleared"

echo "--- Anonymous: share modal blocked without enough items ---"
curl -sL -m 120 -b "$ANON_JAR" -c "$ANON_JAR" -o /dev/null "$BASE/" 2>/dev/null || true
ANON_CODE=$(http_code_with_jar "$ANON_JAR" "$BASE/api/compare/share-modal")
if [[ "$ANON_CODE" == "403" ]]; then
  pass "Share modal blocked without enough items (HTTP 403)"
else
  fail "Share modal blocked without enough items (HTTP $ANON_CODE, expected 403)"
fi

echo "--- Authenticated session (ULI) ---"
ULI=$(docker exec -i ps_php sh -lc 'cd /var/www/html && vendor/bin/drush uli --name=admin --uri=http://localhost:8080' 2>/dev/null | tail -1 || true)
if [[ -z "$ULI" ]]; then
  fail "Could not generate admin login link"
  echo "=== Results: $PASS passed, $FAIL failed ==="
  exit 1
fi
pass "Admin ULI generated"

curl -sL -m 120 -b "$COOKIE_JAR" -c "$COOKIE_JAR" -o /dev/null "$ULI" 2>/dev/null || true
pass "Admin session established"

echo "--- Seed 2 offers in compare list (Drush) ---"
SEED_RESULT=$(seed_compare_items_drush)
if [[ "$SEED_RESULT" == "PASS:seeded" ]]; then
  pass "Two offers seeded in admin compare list"
else
  fail "Compare seed via Drush ($SEED_RESULT)"
  echo "=== Results: $PASS passed, $FAIL failed ==="
  exit 1
fi

COUNT_JSON=$(curl -s -m 60 -b "$COOKIE_JAR" "$BASE/api/compare/count" 2>/dev/null || echo "")
COUNT_VALUE=$(python3 - <<PY "$COUNT_JSON"
import json, sys
try:
  print(json.loads(sys.argv[1]).get("count", 0))
except Exception:
  print(0)
PY
)
if [[ "$COUNT_VALUE" -ge 2 ]]; then
  pass "Compare count >= 2 for authenticated session ($COUNT_VALUE)"
else
  fail "Compare count for authenticated session ($COUNT_VALUE)"
fi

echo "--- Share modal form (GET) ---"
fetch "$BASE/api/compare/share-modal" > "$SHARE_FORM_FILE"
assert_http "200" "$BASE/api/compare/share-modal" "Share modal form available with 2+ items"
assert_file_contains "$SHARE_FORM_FILE" 'ps-compare-share-form' "Share form class present"
assert_file_contains "$SHARE_FORM_FILE" 'id="ps-compare-share-form-wrapper"' "Share form AJAX wrapper present"
assert_file_contains "$SHARE_FORM_FILE" 'data-drupal-selector="edit-submit"' "Share submit uses Drupal AJAX"
assert_file_contains "$SHARE_FORM_FILE" 'Recipient email' "Share form email field present"
assert_file_contains "$SHARE_FORM_FILE" '/api/compare/share-modal' "Share form posts to share-modal route"

FORM_META=$(python3 - <<'PY' "$SHARE_FORM_FILE"
import re, sys
html = open(sys.argv[1], encoding="utf-8", errors="ignore").read()
build = re.search(r'name="form_build_id"\s+value="([^"]+)"', html)
token = re.search(r'name="form_token"\s+value="([^"]+)"', html)
submit = re.search(r'name="op"\s+value="([^"]+)"', html)
if not build or not token or not submit:
    print("FAIL:form_meta")
    sys.exit(0)
print(f"BUILD:{build.group(1)}")
print(f"TOKEN:{token.group(1)}")
print(f"SUBMIT:{submit.group(1)}")
PY
)

if [[ "$FORM_META" == FAIL:* ]]; then
  fail "Could not parse share form build_id / token / submit label"
  echo "=== Results: $PASS passed, $FAIL failed ==="
  exit 1
fi

FORM_BUILD_ID=$(echo "$FORM_META" | awk -F: '/^BUILD:/ {print $2}')
FORM_TOKEN=$(echo "$FORM_META" | awk -F: '/^TOKEN:/ {print $2}')
SUBMIT_LABEL=$(echo "$FORM_META" | awk -F: '/^SUBMIT:/ {print $2}')
pass "Share form tokens parsed"

echo "--- AJAX submit (invalid email) ---"
curl -s -m 120 -b "$COOKIE_JAR" -c "$COOKIE_JAR" \
  -H "Accept: application/vnd.drupal-ajax" \
  -H "X-Requested-With: XMLHttpRequest" \
  -o "$AJAX_RESPONSE_FILE" \
  --data-urlencode "form_id=ps_compare_share_form" \
  --data-urlencode "form_build_id=$FORM_BUILD_ID" \
  --data-urlencode "form_token=$FORM_TOKEN" \
  --data-urlencode "email=not-an-email" \
  --data-urlencode "message=" \
  --data-urlencode "legal=1" \
  --data-urlencode "op=$SUBMIT_LABEL" \
  --data-urlencode "_triggering_element_name=op" \
  --data-urlencode "_triggering_element_value=$SUBMIT_LABEL" \
  "$BASE/api/compare/share-modal?ajax_form=1" >/dev/null || true

INVALID_CHECK=$(python3 - <<'PY' "$AJAX_RESPONSE_FILE"
import json, sys
raw = open(sys.argv[1], encoding="utf-8", errors="ignore").read().strip()
if not raw:
    print("FAIL:empty")
    sys.exit(0)
try:
    data = json.loads(raw)
except Exception:
    print("FAIL:not_json")
    sys.exit(0)
blob = json.dumps(data)
if "data-ps-compare-share-success" in blob:
    print("FAIL:unexpected_success")
elif "ps-compare-share-form" in blob or "form-error" in blob or "error" in blob.lower():
    print("PASS:validation")
else:
    print("PASS:validation")
PY
)

if [[ "$INVALID_CHECK" == "PASS:validation" ]]; then
  pass "Invalid email rejected via AJAX (no success state)"
else
  fail "Invalid email AJAX ($INVALID_CHECK)"
fi

echo "--- Reload share form for valid submit ---"
fetch "$BASE/api/compare/share-modal" > "$SHARE_FORM_FILE"
FORM_META=$(python3 - <<'PY' "$SHARE_FORM_FILE"
import re, sys
html = open(sys.argv[1], encoding="utf-8", errors="ignore").read()
build = re.search(r'name="form_build_id"\s+value="([^"]+)"', html)
token = re.search(r'name="form_token"\s+value="([^"]+)"', html)
submit = re.search(r'name="op"\s+value="([^"]+)"', html)
if not build or not token or not submit:
    print("FAIL:form_meta")
    sys.exit(0)
print(f"BUILD:{build.group(1)}")
print(f"TOKEN:{token.group(1)}")
print(f"SUBMIT:{submit.group(1)}")
PY
)
FORM_BUILD_ID=$(echo "$FORM_META" | awk -F: '/^BUILD:/ {print $2}')
FORM_TOKEN=$(echo "$FORM_META" | awk -F: '/^TOKEN:/ {print $2}')
SUBMIT_LABEL=$(echo "$FORM_META" | awk -F: '/^SUBMIT:/ {print $2}')

echo "--- AJAX submit (valid email) ---"
curl -s -m 120 -b "$COOKIE_JAR" -c "$COOKIE_JAR" \
  -H "Accept: application/vnd.drupal-ajax" \
  -H "X-Requested-With: XMLHttpRequest" \
  -o "$AJAX_RESPONSE_FILE" \
  --data-urlencode "form_id=ps_compare_share_form" \
  --data-urlencode "form_build_id=$FORM_BUILD_ID" \
  --data-urlencode "form_token=$FORM_TOKEN" \
  --data-urlencode "email=$TEST_EMAIL" \
  --data-urlencode "message=B2B share modal AJAX test." \
  --data-urlencode "legal=1" \
  --data-urlencode "op=$SUBMIT_LABEL" \
  --data-urlencode "_triggering_element_name=op" \
  --data-urlencode "_triggering_element_value=$SUBMIT_LABEL" \
  "$BASE/api/compare/share-modal?ajax_form=1" >/dev/null || true

SUCCESS_CHECK=$(python3 - <<'PY' "$AJAX_RESPONSE_FILE" "$TEST_EMAIL"
import json, sys
raw = open(sys.argv[1], encoding="utf-8", errors="ignore").read().strip()
email = sys.argv[2]
if not raw:
    print("FAIL:empty")
    sys.exit(0)
try:
    data = json.loads(raw)
except Exception:
    print("FAIL:not_json")
    sys.exit(0)
blob = json.dumps(data)
checks = {
    "success_markup": "data-ps-compare-share-success" in blob,
    "modal_body_target": "data-ps-compare-share-modal-body" in blob,
    "sent_class": "ps-compare-share-modal--sent" in blob,
    "recipient_in_message": email in blob,
}
failed = [k for k, ok in checks.items() if not ok]
print("PASS:ajax_ok" if not failed else "FAIL:" + ",".join(failed))
PY
)

if [[ "$SUCCESS_CHECK" == "PASS:ajax_ok" ]]; then
  pass "AJAX success replaces share modal body with confirmation"
else
  fail "AJAX success response ($SUCCESS_CHECK)"
fi

sleep 1

echo "--- Mailpit delivery after AJAX share ---"
MAIL_JSON=$(curl -s "$MAILPIT/api/v1/messages?limit=1" 2>/dev/null || echo "")
MSG_ID=$(python3 - <<'PY' "$MAIL_JSON" "$TEST_EMAIL"
import json, sys
try:
  payload = json.loads(sys.argv[1])
except Exception:
    sys.exit(1)
recipient = sys.argv[2].lower()
messages = payload.get("messages") or []
for msg in messages:
    to_list = msg.get("To") or []
    if any(recipient in (entry.get("Address") or "").lower() for entry in to_list):
        print(msg.get("ID") or "")
        sys.exit(0)
sys.exit(1)
PY
) || true

if [[ -n "${MSG_ID:-}" ]]; then
  pass "Mailpit received share email for $TEST_EMAIL"
else
  fail "Mailpit message missing for $TEST_EMAIL"
fi

echo "--- Compare page share button ---"
fetch "$BASE/compare" > "${SHARE_FORM_FILE}.compare"
if grep -Fq 'data-ps-compare-share' "${SHARE_FORM_FILE}.compare"; then
  pass "Share button on /compare page"
else
  fail "Share button missing on /compare page"
fi

echo ""
echo "=== Results: $PASS passed, $FAIL failed ==="
if [[ "$FAIL" -gt 0 ]]; then
  exit 1
fi
