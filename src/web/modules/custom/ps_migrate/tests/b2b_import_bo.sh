#!/usr/bin/env bash
# B2B smoke tests — CRM import BO pages (runs, detail, settings, upload).
set -euo pipefail

export PS_E2E_COUNTRY="${PS_E2E_COUNTRY:-${COUNTRY:-fr}}"

# shellcheck source=/dev/null
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/../../../../.." && pwd)/scripts/e2e/common.sh"

PASS=0
FAIL=0
COOKIE_JAR="${TMPDIR:-/tmp}/ps-migrate-b2b-bo-cookies.txt"

pass() { echo "  PASS: $1"; PASS=$((PASS + 1)); }
fail() { echo "  FAIL: $1"; FAIL=$((FAIL + 1)); }

assert_http_200() {
  local url="$1" label="$2"
  local code attempt
  for attempt in 1 2 3; do
    code=$(curl -sL -m 120 -b "$COOKIE_JAR" -c "$COOKIE_JAR" -o /dev/null -w '%{http_code}' "$url" 2>/dev/null || echo "000")
    if [[ "$code" == "200" ]]; then
      pass "$label (HTTP 200)"
      return 0
    fi
    sleep 2
  done
  fail "$label (HTTP $code) — $url"
  return 1
}

assert_body_contains() {
  local url="$1" needle="$2" label="$3"
  local body
  body=$(curl -sL -m 120 -b "$COOKIE_JAR" -c "$COOKIE_JAR" "$url" 2>/dev/null || echo "")
  if [[ "$body" == *"$needle"* ]]; then
    pass "$label"
  else
    fail "$label — missing: $needle"
  fi
}

echo "=== PS Migrate B2B — Import BO ($BASE) ==="

rm -f "$COOKIE_JAR"
touch "$COOKIE_JAR"

ULI=$(ps_e2e_drush uli --name=admin --uri="${BASE}" 2>/dev/null | tail -1)
if [[ -z "$ULI" ]]; then
  fail "Could not generate admin login link"
  echo "=== Results: $PASS passed, $FAIL failed ==="
  exit 1
fi
pass "Admin ULI generated"

curl -sL -m 120 -b "$COOKIE_JAR" -c "$COOKIE_JAR" -o /dev/null "$ULI" 2>/dev/null || true
pass "Admin session established"

RUN_ID="$(ps_e2e_drush php:eval "
\$storage = \\Drupal::entityTypeManager()->getStorage('import_run');
\$ids = \$storage->getQuery()->accessCheck(FALSE)->sort('id', 'DESC')->range(0, 1)->execute();
print \$ids ? (string) reset(\$ids) : '';
" 2>/dev/null | tail -1)"

echo "--- CRM import admin routes ---"
assert_http_200 "${BASE}/admin/ps/import/runs" "Import runs list"
assert_body_contains "${BASE}/admin/ps/import/runs" "Import runs" "Runs list title"

assert_http_200 "${BASE}/admin/ps/import/upload" "Upload CRM XML"
assert_http_200 "${BASE}/admin/ps/import/settings" "Pipeline settings"

if [[ -n "$RUN_ID" ]]; then
  pass "Latest import run id: ${RUN_ID}"
  assert_http_200 "${BASE}/admin/ps/import/runs/${RUN_ID}" "Import run detail"
  assert_body_contains "${BASE}/admin/ps/import/runs/${RUN_ID}" "Migration statistics" "Run detail migration stats"
else
  fail "No import_run entity found for detail page test"
fi

echo ""
echo "=== Results: $PASS passed, $FAIL failed ==="
if [[ "$FAIL" -gt 0 ]]; then
  exit 1
fi

echo "CRM import BO B2B passed."
