#!/usr/bin/env bash
# B2B — Filter apply URL matrix (SEO path + query params, count consistency).
# Simulates browser pushState targets via direct navigation (curl).
set -euo pipefail

# shellcheck source=/dev/null
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/../../../../.." && pwd)/scripts/e2e/common.sh"

PASS=0
FAIL=0

pass() { echo "  PASS: $1"; PASS=$((PASS + 1)); }
fail() { echo "  FAIL: $1"; FAIL=$((FAIL + 1)); }

fetch() {
  curl -sL -m 120 "$1" 2>/dev/null || echo ""
}

status_code() {
  curl -sL -m 120 -o /dev/null -w '%{http_code}' "$1" 2>/dev/null || echo "000"
}

count_api() {
  local qs="$1"
  local url="${BASE}/api/ps/count"
  if [[ -n "$qs" ]]; then
    url="${url}?${qs}"
  fi
  curl -s -m 60 "$url" 2>/dev/null | sed -n 's/.*"count":\([0-9]*\).*/\1/p' | head -1
}

page_global_count() {
  local url="$1"
  local tmp
  tmp=$(mktemp)
  fetch "$url" > "$tmp"
  python3 -c "
import re
html = open('$tmp', encoding='utf-8', errors='replace').read()
m = re.search(r'\"globalCount\"\s*:\s*(\d+)', html)
if m:
    print(m.group(1))
    raise SystemExit
m = re.search(r'class=\"[^\"]*js-ps-results-header-headline-count[^\"]*\"[^>]*>\s*(\d+)', html)
if m:
    print(m.group(1))
    raise SystemExit
print('-1')
"
  rm -f "$tmp"
}

assert_http_200() {
  local url="$1" label="$2"
  local code
  code=$(status_code "$url")
  if [[ "$code" == "200" ]]; then
    pass "$label (HTTP 200)"
  else
    fail "$label (HTTP $code) — $url"
  fi
}

assert_query_forbidden() {
  local url="$1" forbidden="$2" label="$3"
  local effective
  effective=$(curl -sL -m 60 -o /dev/null -w '%{url_effective}' "$url" 2>/dev/null || echo "")
  if [[ "$effective" == *"$forbidden"* ]]; then
    fail "$label (forbidden query present: $forbidden in $effective)"
  else
    pass "$label (no $forbidden in effective URL)"
  fi
}

assert_count_match() {
  local url="$1" api_qs="$2" label="$3"
  local api_count page_count
  api_count=$(count_api "$api_qs")
  page_count=$(page_global_count "$url")
  if [[ ! "$api_count" =~ ^[0-9]+$ || ! "$page_count" =~ ^[0-9]+$ ]]; then
    fail "$label (parse api=$api_count page=$page_count)"
    return
  fi
  if [[ "$api_count" == "$page_count" ]]; then
    pass "$label (api=$api_count page=$page_count)"
  else
    fail "$label (api=$api_count page=$page_count) — $url"
  fi
}

assert_apply_more_trigger() {
  local qs="$1" label="$2"
  local headers
  headers=$(curl -sI -m 60 -H "HX-Request: true" \
    "${BASE}/api/ps/htmx/apply-more?${qs}" 2>/dev/null || echo "")
  if grep -qi 'trigger-after-settle.*ps-search-filter-htmx-apply' <<< "$headers"; then
    pass "$label"
  else
    fail "$label (missing HX-Trigger-After-Settle)"
  fi
}

echo "=== PS Search B2B — Filter apply URL matrix ($BASE) ==="

echo ""
echo "--- Warm-up ---"
curl -sL -m 120 -o /dev/null "$BASE/for-rent/office/" 2>/dev/null || true

BASE_OFFICE_API_QS="operation_type=LOC&asset_type=BUR"
BASE_OFFICE=$(count_api "$BASE_OFFICE_API_QS")
TRANSPORT_API_QS="${BASE_OFFICE_API_QS}&nearby_transport=bus"
TRANSPORT_COUNT=$(count_api "$TRANSPORT_API_QS")

if [[ "$BASE_OFFICE" =~ ^[0-9]+$ && "$BASE_OFFICE" -gt 0 ]]; then
  pass "Base office count API ($BASE_OFFICE)"
else
  fail "Base office count API ($BASE_OFFICE)"
fi

echo ""
echo "--- R12 — SEO path + nearby_transport (clean query) ---"
URL_R12="${BASE}/for-rent/office/?nearby_transport=bus"
assert_http_200 "$URL_R12" "R12 page loads"
assert_query_forbidden "$URL_R12" "operation_type%5B" "R12 no operation_type bracket"
assert_query_forbidden "$URL_R12" "asset_type%5B" "R12 no asset_type bracket"
assert_count_match "$URL_R12" "$TRANSPORT_API_QS" "R12 count api vs page"

echo ""
echo "--- M1 — transport + surface ---"
URL_M1="${BASE}/for-rent/office/?nearby_transport=bus&surface%5Bmin%5D=100"
API_M1="${TRANSPORT_API_QS}&surface_min=100"
assert_http_200 "$URL_M1" "M1 page loads"
assert_query_forbidden "$URL_M1" "operation_type%5BLOC%5D" "M1 no op bracket"
assert_count_match "$URL_M1" "$API_M1" "M1 count api vs page"

echo ""
echo "--- M2 — region path + transport + reference ---"
URL_M2="${BASE}/for-rent/office/ile-de-france/?nearby_transport=bus"
API_M2="operation_type=LOC&asset_type=BUR&locations=region%3Aile-de-france&nearby_transport=bus"
assert_http_200 "$URL_M2" "M2 page loads"
assert_query_forbidden "$URL_M2" "locality=" "M2 no locality query on region path"
assert_count_match "$URL_M2" "$API_M2" "M2 count api vs page"

echo ""
echo "--- M3 — dept path + surface + budget ---"
URL_M3="${BASE}/for-rent/office/paris-75/?surface%5Bmin%5D=50&budget%5Bmax%5D=500"
API_M3="operation_type=LOC&asset_type=BUR&locations=75&surface_min=50&budget_max=500"
assert_http_200 "$URL_M3" "M3 page loads"
assert_query_forbidden "$URL_M3" "operation_type=" "M3 no scalar operation_type on SEO path"
assert_count_match "$URL_M3" "$API_M3" "M3 count api vs page"

echo ""
echo "--- A5 asset-only + query filter ---"
URL_A5="${BASE}/office/?nearby_transport=bus"
API_A5="asset_type=BUR&nearby_transport=bus"
assert_http_200 "$URL_A5" "A5 asset-only + transport loads"
assert_count_match "$URL_A5" "$API_A5" "A5 count (flexible asset)"

echo ""
echo "--- A1 flexible base + explicit facets in query ---"
URL_A1="${BASE}/find-property?operation_type=LOC&asset_type=BUR&nearby_transport=bus"
assert_http_200 "$URL_A1" "A1 flexible + filters loads"
assert_count_match "$URL_A1" "$TRANSPORT_API_QS" "A1 count on flexible base"

echo ""
echo "--- E1 — redundant facet brackets on SEO path (must not break page) ---"
URL_E1="${BASE}/for-rent/office/?operation_type%5BLOC%5D=LOC&asset_type%5BBUR%5D=BUR&nearby_transport=bus"
assert_http_200 "$URL_E1" "E1 page loads with redundant brackets"
E1_COUNT=$(page_global_count "$URL_E1")
if [[ "$E1_COUNT" == "$TRANSPORT_COUNT" ]]; then
  pass "E1 page count matches transport-only ($E1_COUNT)"
else
  fail "E1 page count=$E1_COUNT expected $TRANSPORT_COUNT"
fi

echo ""
echo "--- E4 — empty nearby_transport param ---"
URL_E4="${BASE}/for-rent/office/?nearby_transport="
assert_http_200 "$URL_E4" "E4 empty transport param loads"
E4_COUNT=$(page_global_count "$URL_E4")
if [[ "$E4_COUNT" == "$BASE_OFFICE" ]]; then
  pass "E4 empty transport → base count ($E4_COUNT)"
else
  fail "E4 count=$E4_COUNT expected base $BASE_OFFICE"
fi

echo ""
echo "--- Core more filters on SEO path ---"
assert_http_200 "${BASE}/for-rent/office/?reference=PS-TEST" "reference query loads"
assert_http_200 "${BASE}/for-rent/office/?has_immersive_tour=1" "immersive tour query loads"
assert_http_200 "${BASE}/for-rent/office/?has_video=1" "video query loads"

echo ""
echo "--- HTMX apply-more trigger (server) ---"
assert_apply_more_trigger "$TRANSPORT_API_QS" "apply-more with transport"
assert_apply_more_trigger "${API_M1}" "apply-more with transport+surface"

echo ""
echo "--- Flat range params on SEO path (NumericFilter / hero regression) ---"
assert_search_page_flat() {
  local url="$1" label="$2"
  local html code
  html=$(fetch "$url")
  code=$(status_code "$url")
  if [[ "$code" != "200" ]]; then
    fail "$label (HTTP $code) — $url"
    return
  fi
  if [[ "$html" == *"unexpected error"* ]]; then
    fail "$label (error page) — $url"
    return
  fi
  if [[ "$html" != *'"globalCount"'* ]]; then
    fail "$label (missing globalCount) — $url"
    return
  fi
  pass "$label (HTTP 200 + globalCount)"
}
assert_search_page_flat "${BASE}/for-rent/office/paris-75/?surface_min=200" "EN flat surface_min on dept path"
assert_search_page_flat "${BASE}/for-rent/office/paris-75/?surface_min=200&budget_max=500" "EN flat surface_min + budget_max"
assert_search_page_flat "${BASE}/for-rent/office/?surface_min=100" "EN flat surface_min on op+asset path"
FR_BASE="http://fr.localhost:8083"
FR_FLAT_CODE=$(status_code "${FR_BASE}/a-louer/bureaux/paris-12-75012/?surface_min=200")
if [[ "$FR_FLAT_CODE" == "200" ]]; then
  assert_search_page_flat "${FR_BASE}/a-louer/bureaux/paris-12-75012/?surface_min=200" "FR flat surface_min on arrondissement path"
  assert_search_page_flat "${FR_BASE}/a-louer/bureaux/paris-12-75012/?surface_min=200&budget_max=50" "FR flat surface_min + budget_max"
else
  echo "  SKIP: FR flat range tests (HTTP $FR_FLAT_CODE)"
fi

echo ""
echo "--- FR smoke (optional) ---"
FR_BASE="http://fr.localhost:8083"
FR_URL="${FR_BASE}/a-louer/bureaux/?nearby_transport=bus"
FR_CODE=$(curl -sL -m 60 -o /dev/null -w '%{http_code}' "$FR_URL" 2>/dev/null || echo "000")
if [[ "$FR_CODE" == "200" ]]; then
  pass "FR a-louer/bureaux + transport (HTTP 200)"
  if curl -sL -m 60 -o /dev/null -w '%{url_effective}' "$FR_URL" 2>/dev/null | grep -q 'operation_type%5B'; then
    fail "FR URL contains operation_type bracket"
  else
    pass "FR URL without operation_type bracket"
  fi
else
  echo "  SKIP: FR site unavailable ($FR_CODE)"
fi

echo ""
echo "=== Results: $PASS passed, $FAIL failed ==="
if [[ "$FAIL" -gt 0 ]]; then
  exit 1
fi
