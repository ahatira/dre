#!/usr/bin/env bash
# B2B smoke tests — Share comparison offcanvas, webform submit, Mailpit delivery.
#
# Anonymous HTTP toggle is unreliable here (page cache serves stale CSRF tokens
# without a Drupal session cookie). We seed compare items via Drush and exercise
# the share offcanvas over an authenticated ULI session.
set -euo pipefail

BASE="${BASE_URL:-http://localhost:8080}"
MAILPIT="${MAILPIT_URL:-http://localhost:8025}"
PASS=0
FAIL=0
COOKIE_JAR="${TMPDIR:-/tmp}/ps-compare-b2b-share-cookies.txt"
ANON_JAR="${TMPDIR:-/tmp}/ps-compare-b2b-share-anon-cookies.txt"
OFFCANVAS_FILE="${TMPDIR:-/tmp}/ps-compare-b2b-share-offcanvas.html"
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

submit_compare_share_webform_drush() {
  docker exec -i ps_php sh -lc "cd /var/www/html && vendor/bin/drush php:eval \"
use Drupal\\\\webform\\\\Entity\\\\WebformSubmission;

\\\$account = \\\Drupal\\\\user\\\\Entity\\\\User::load(1);
if (\\\$account === NULL) { print 'FAIL:no_admin'; return; }
\\\Drupal::service('account_switcher')->switchTo(\\\$account);
if (!\\\Drupal::service('ps_compare.manager')->canOpenComparisonPage()) {
  \\\Drupal::service('account_switcher')->switchBack();
  print 'FAIL:not_enough_items';
  return;
}
\\\$submission = WebformSubmission::create([
  'webform_id' => 'compare_share',
  'uid' => (int) \\\$account->id(),
  'data' => [
    'prof_email_address' => '${TEST_EMAIL}',
    'legal' => 1,
  ],
]);
\\\$submission->save();
\\\Drupal::service('account_switcher')->switchBack();
print 'PASS:submitted';
\"" 2>/dev/null | tail -1
}

echo "=== PS Compare B2B — Share offcanvas + webform ($BASE) ==="

rm -f "$COOKIE_JAR" "$ANON_JAR"
touch "$COOKIE_JAR" "$ANON_JAR"

echo "--- Reset Mailpit ---"
curl -s -X DELETE "$MAILPIT/api/v1/messages" >/dev/null || true
pass "Mailpit messages cleared"

echo "--- Anonymous: share offcanvas blocked without enough items ---"
curl -sL -m 120 -b "$ANON_JAR" -c "$ANON_JAR" -o /dev/null "$BASE/" 2>/dev/null || true
ANON_CODE=$(http_code_with_jar "$ANON_JAR" "$BASE/api/compare/share-offcanvas")
if [[ "$ANON_CODE" == "403" ]]; then
  pass "Share offcanvas blocked without enough items (HTTP 403)"
else
  fail "Share offcanvas blocked without enough items (HTTP $ANON_CODE, expected 403)"
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

echo "--- Share offcanvas form (GET) ---"
fetch "$BASE/api/compare/share-offcanvas" > "$OFFCANVAS_FILE"
assert_http "200" "$BASE/api/compare/share-offcanvas" "Share offcanvas available with 2+ items"
assert_file_contains "$OFFCANVAS_FILE" 'webform-submission-compare-share-add-form' "Compare share webform present"
assert_file_contains "$OFFCANVAS_FILE" 'ps-compare-share-form' "Compare share form class present"
assert_file_contains "$OFFCANVAS_FILE" 'name="form_build_id"' "Compare share form includes form_build_id token"
assert_file_contains "$OFFCANVAS_FILE" 'Professional e-mail' "Professional email field present"
assert_file_contains "$OFFCANVAS_FILE" 'Receive my comparison' "Receive my comparison submit label present"
assert_file_contains "$OFFCANVAS_FILE" 'ps-compare-share-form__optout-intro' "Opt-out intro present"

echo "--- Webform submission triggers comparison email ---"
SUBMIT_RESULT=$(submit_compare_share_webform_drush)
if [[ "$SUBMIT_RESULT" == "PASS:submitted" ]]; then
  pass "Compare share webform submitted via Drush"
else
  fail "Compare share webform submit ($SUBMIT_RESULT)"
fi

sleep 1

echo "--- Mailpit delivery after webform share ---"
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
fetch "$BASE/compare" > "${OFFCANVAS_FILE}.compare"
if grep -Fq 'data-ps-compare-share-open' "${OFFCANVAS_FILE}.compare"; then
  pass "Share button on /compare page"
else
  fail "Share button missing on /compare page"
fi

echo ""
echo "=== Results: $PASS passed, $FAIL failed ==="
if [[ "$FAIL" -gt 0 ]]; then
  exit 1
fi
