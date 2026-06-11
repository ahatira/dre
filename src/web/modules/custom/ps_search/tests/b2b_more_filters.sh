#!/usr/bin/env bash
# B2B smoke tests — More filters (per-feature, lazy-load, count API, group labels).
set -euo pipefail

BASE="${BASE_URL:-http://localhost:8080}"
PASS=0
FAIL=0

pass() { echo "  PASS: $1"; PASS=$((PASS + 1)); }
fail() { echo "  FAIL: $1"; FAIL=$((FAIL + 1)); }

fetch() {
  curl -sL -m 120 "$1" 2>/dev/null || echo ""
}

status_code() {
  local url="$1"
  local code attempt
  for attempt in 1 2; do
    code=$(curl -sL -m 120 -o /dev/null -w '%{http_code}' "$url" 2>/dev/null || echo "000")
    if [[ "$code" == "200" ]]; then
      echo "$code"
      return
    fi
    sleep 2
  done
  echo "$code"
}

count_api() {
  local qs="$1"
  curl -s -m 60 "${BASE}/api/ps/count?${qs}" 2>/dev/null | sed -n 's/.*"count":\([0-9]*\).*/\1/p' | head -1
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

assert_html_contains() {
  local url="$1" needle="$2" label="$3"
  local html
  html=$(fetch "$url")
  if [[ -z "$html" ]]; then
    fail "$label (empty response) — $url"
    return
  fi
  if grep -Fq "$needle" <<< "$html"; then
    pass "$label"
  else
    fail "$label (missing: $needle) — $url"
  fi
}

assert_not_screaming_snake() {
  local url="$1" forbidden="$2" label="$3"
  local html
  html=$(fetch "$url")
  if [[ -z "$html" ]]; then
    fail "$label (empty response) — $url"
    return
  fi
  if grep -Fq "$forbidden" <<< "$html"; then
    fail "$label (found machine label: $forbidden) — $url"
  else
    pass "$label (no $forbidden)"
  fi
}

assert_count_decreases() {
  local param="$1" label="$2"
  local base filtered
  base=$(count_api "")
  filtered=$(count_api "${param}=1")
  if [[ -z "$base" || -z "$filtered" ]]; then
    fail "$label (count API empty: base=$base filtered=$filtered)"
    return
  fi
  if [[ "$base" -gt 0 && "$filtered" -lt "$base" ]]; then
    pass "$label (base=$base filtered=$filtered)"
  elif [[ "$base" -gt 0 && "$filtered" -eq "$base" ]]; then
    fail "$label (count unchanged: $base) — param ${param}=1"
  else
    fail "$label (unexpected counts base=$base filtered=$filtered)"
  fi
}

assert_lazy_group() {
  local group_id="$1" label="$2"
  local url body code
  url="${BASE}/api/ps/htmx/more-criteria/${group_id}"
  body=$(curl -s -m 60 -H "HX-Request: true" "$url" 2>/dev/null || echo "")
  code=$(curl -s -m 60 -o /dev/null -w '%{http_code}' -H "HX-Request: true" "$url" 2>/dev/null || echo "000")
  if [[ "$code" != "200" ]]; then
    fail "$label (HTTP $code) — $url"
    return
  fi
  if grep -q 'js-ps-more-filter' <<< "$body"; then
    pass "$label (HTMX HTML with filters)"
  else
    fail "$label (200 but no filter inputs) — $url"
  fi
}

echo "=== PS Search Filters B2B More filters tests ($BASE) ==="

echo "--- Warm-up ---"
curl -sL -m 120 -o /dev/null "$BASE/find-property" 2>/dev/null || true

echo "--- Page structure (accordion + lazy-load settings) ---"
assert_http_200 "$BASE/find-property" "EN search page loads"
assert_html_contains "$BASE/find-property" 'ps-more-accordion' "Accordion markup present"
assert_html_contains "$BASE/find-property" 'psSearchFilterHtmx' "HTMX settings in drupalSettings"
assert_html_contains "$BASE/find-property" 'moreCriteriaGroupUrl' "Lazy-load HTMX URL in drupalSettings"
assert_html_contains "$BASE/find-property" 'moreFilterSchema' "Filter schema in drupalSettings"
assert_html_contains "$BASE/find-property" 'js-ps-more-group-panel' "Lazy group panels present"
assert_html_contains "$BASE/find-property" 'ps-filter-mobile-count-label' "Mobile HTMX count label present"
assert_html_contains "$BASE/find-property" 'data-ps-htmx-popin="mobile"' "Mobile HTMX popin marker"
assert_html_contains "$BASE/find-property" 'Other criteria' "Core filters section (EN)"

echo "--- Group labels (human-readable, not CRM machine names) ---"
assert_not_screaming_snake "$BASE/find-property" 'AMENAGEMENTS' "EN no AMENAGEMENTS machine label"
assert_not_screaming_snake "$BASE/find-property" 'EQUIPEMENTS' "EN no EQUIPEMENTS machine label"
assert_html_contains "$BASE/find-property" 'Equipments' "EN Equipments group label"
assert_html_contains "$BASE/find-property" 'Fittings' "EN Fittings group label"

assert_http_200 "$BASE/fr/recherche-immobiliere" "FR search page loads"
assert_html_contains "$BASE/fr/recherche-immobiliere" 'Aménagements' "FR Aménagements group label"
assert_html_contains "$BASE/fr/recherche-immobiliere" 'Équipements' "FR Équipements group label"
assert_not_screaming_snake "$BASE/fr/recherche-immobiliere" 'AMENAGEMENTS' "FR no AMENAGEMENTS machine label"

echo "--- Lazy-load HTMX endpoint ---"
assert_lazy_group "equipements" "HTMX equipements group"
assert_lazy_group "amenagements" "HTMX amenagements group"

echo "--- Count API (per-feature filters) ---"
BASE_COUNT=$(count_api "")
if [[ -n "$BASE_COUNT" && "$BASE_COUNT" -gt 0 ]]; then
  pass "Count API base ($BASE_COUNT results)"
else
  fail "Count API base (got: ${BASE_COUNT:-empty})"
fi

assert_count_decreases "feature_amenagements_tec_accs_pers_mobilit_rduit" "PMR accessibility filter reduces count"

echo "--- Apply URL preserves feature param ---"
APPLY_URL="${BASE}/find-property?feature_amenagements_tec_accs_pers_mobilit_rduit=1"
assert_http_200 "$APPLY_URL" "Navigation with feature param"

echo ""
echo "=== Results: $PASS passed, $FAIL failed ==="
if [[ "$FAIL" -gt 0 ]]; then
  exit 1
fi
