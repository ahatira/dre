#!/usr/bin/env bash
# E2E — Flat range query params on SEO search pages (NumericFilter regression).
#
# Homepage hero and filter bar submit surface_min=200 (scalar). Views exposed
# filter surface_min expects surface_min[min]=200. Without normalization → HTTP 500.
set -euo pipefail

# shellcheck source=/dev/null
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/../../../../.." && pwd)/scripts/e2e/common.sh"

PASS=0
FAIL=0
SKIP=0

pass() { echo "  PASS: $1"; PASS=$((PASS + 1)); }
fail() { echo "  FAIL: $1"; FAIL=$((FAIL + 1)); }
skip() { echo "  SKIP: $1"; SKIP=$((SKIP + 1)); }

assert_http_200() {
  local url="$1" desc="$2"
  local code
  code=$(curl -sL -m 60 -o /dev/null -w '%{http_code}' "$url" 2>/dev/null || echo "000")
  if [[ "$code" == "200" ]]; then
    pass "$desc (HTTP 200)"
  else
    fail "$desc (HTTP $code) — $url"
  fi
}

assert_search_page() {
  local url="$1" desc="$2"
  local html code
  html=$(curl -sL -m 60 "$url" 2>/dev/null || echo "")
  code=$(curl -sL -m 60 -o /dev/null -w '%{http_code}' "$url" 2>/dev/null || echo "000")
  if [[ "$code" != "200" ]]; then
    fail "$desc (HTTP $code) — $url"
    return
  fi
  if [[ "$html" == *"erreur inattendue"* ]] || [[ "$html" == *"unexpected error"* ]]; then
    fail "$desc (Drupal error page) — $url"
    return
  fi
  if [[ "$html" != *'"globalCount"'* ]]; then
    fail "$desc (missing globalCount in drupalSettings) — $url"
    return
  fi
  pass "$desc (HTTP 200 + globalCount)"
}

echo "=== E2E flat range query params — COM ($BASE) ==="

echo ""
echo "--- EN SEO path + flat surface_min (hero / filter bar shape) ---"
assert_search_page \
  "${BASE}/for-rent/office/paris-75/?surface_min=200" \
  "EN dept path + surface_min=200"
assert_search_page \
  "${BASE}/for-rent/office/paris-75/?surface_min=200&budget_max=50" \
  "EN dept path + surface_min + budget_max"
assert_search_page \
  "${BASE}/for-rent/office/paris-75/?budget_max=50" \
  "EN dept path + budget_max only"
assert_search_page \
  "${BASE}/for-rent/office/paris-75/?surface%5Bmin%5D=200" \
  "EN dept path + surface[min]=200 (BEF bracket shape)"
assert_search_page \
  "${BASE}/for-rent/office/paris-75/?surface_max=500" \
  "EN dept path + surface_max only"
assert_search_page \
  "${BASE}/for-rent/office/paris-75/?capacity_min=5" \
  "EN dept path + capacity_min only"

echo ""
echo "--- EN flexible path + flat surface_min ---"
assert_search_page \
  "${BASE}/find-property?operation_type=LOC&asset_type=BUR&locality=75&surface_min=200" \
  "EN find-property + surface_min=200"

FR_BASE="${FR_BASE_URL:-http://fr.localhost:8083}"
echo ""
echo "--- FR SEO path + flat range params ($FR_BASE) ---"
FR_PROBE=$(curl -s -m 15 -o /dev/null -w '%{http_code}' "$FR_BASE/a-louer/bureaux/" 2>/dev/null || echo "000")
if [[ "$FR_PROBE" != "200" ]]; then
  skip "FR site unavailable (HTTP $FR_PROBE)"
else
  assert_search_page \
    "$FR_BASE/a-louer/bureaux/paris-12-75012/?surface_min=200" \
    "FR arrondissement + surface_min=200"
  assert_search_page \
    "$FR_BASE/a-louer/bureaux/paris-12-75012/?surface_min=200&budget_max=50" \
    "FR arrondissement + surface_min + budget_max"
  assert_search_page \
    "$FR_BASE/a-louer/bureaux/paris-12-75012/?budget_max=50" \
    "FR arrondissement + budget_max only"
  assert_search_page \
    "$FR_BASE/a-louer/bureaux/paris/?surface_min=200" \
    "FR city path + surface_min=200"
  assert_http_200 \
    "$FR_BASE/a-louer/bureaux/paris/" \
    "FR city path baseline (no range params)"
fi

echo ""
echo "=== Summary: $PASS passed, $FAIL failed, $SKIP skipped ==="
if [[ "$FAIL" -gt 0 ]]; then
  exit 1
fi
exit 0
