#!/usr/bin/env bash
# B2B smoke tests — More filters (per-feature, lazy-load, count API, group labels).
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
  local url="${BASE}/api/ps/count"
  if [[ -n "$qs" ]]; then
    url="${url}?${qs}"
  fi
  curl -s -m 60 "$url" 2>/dev/null | sed -n 's/.*"count":\([0-9]*\).*/\1/p' | head -1
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
assert_html_contains "$BASE/find-property" 'ps-filter-type-menu' "Desktop filter popin menu ids"
assert_html_contains "$BASE/find-property" 'aria-haspopup="dialog"' "Filter toggles expose aria-haspopup"
assert_html_contains "$BASE/find-property" 'role="dialog"' "Filter popins expose dialog role"

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
assert_lazy_group "equipment" "HTMX equipment group"
assert_lazy_group "amenagements" "HTMX amenagements group"

echo "--- Transport group routed via Nearby transport (not lazy accordion) ---"
assert_html_contains "$BASE/find-property" 'Nearby transport' "Nearby transport in Other criteria"
assert_not_screaming_snake "$BASE/find-property" 'data-group-id="acces_vehicules"' "Transport group not in lazy accordion"
TRANSPORT_HTMX=$(curl -s -m 60 -H "HX-Request: true" "${BASE}/api/ps/htmx/more-criteria/acces_vehicules" 2>/dev/null || echo "")
if grep -q 'js-ps-more-filter' <<< "$TRANSPORT_HTMX"; then
  fail "Transport HTMX group should not expose per-feature filters"
else
  pass "Transport HTMX group has no feature filter inputs"
fi

echo "--- Nearby transport contains search (count API) ---"
BASE_OFFICE=$(count_api "operation_type=LOC&asset_type=BUR")
TRANSPORT_COUNT=$(count_api "operation_type=LOC&asset_type=BUR&nearby_transport=bus")
if [[ -n "$BASE_OFFICE" && "$BASE_OFFICE" -gt 0 && -n "$TRANSPORT_COUNT" && "$TRANSPORT_COUNT" -ge 0 && "$TRANSPORT_COUNT" -le "$BASE_OFFICE" ]]; then
  pass "nearby_transport filter count (base=$BASE_OFFICE filtered=$TRANSPORT_COUNT)"
else
  fail "nearby_transport count API (base=$BASE_OFFICE filtered=$TRANSPORT_COUNT)"
fi

echo "--- Core criteria widgets present ---"
assert_html_contains "$BASE/find-property" 'data-param="nearby_transport"' "nearby_transport input"
assert_html_contains "$BASE/find-property" 'js-ps-transport-suggest' "transport autocomplete input"
assert_html_contains "$BASE/find-property" 'data-param="reference"' "reference input"
assert_html_contains "$BASE/find-property" 'data-param="has_immersive_tour"' "immersive tour checkbox"
assert_html_contains "$BASE/find-property" 'data-param="has_video"' "video checkbox"

TRANSPORT_SUGGEST=$(curl -s -m 30 "${BASE}/api/ps/transport-suggest?q=bus" 2>/dev/null || echo "")
if grep -qE 'feature_name|feature_value|Bus|bus' <<< "$TRANSPORT_SUGGEST"; then
  pass "transport-suggest API returns bus-related suggestions"
else
  fail "transport-suggest API (body=${TRANSPORT_SUGGEST:0:120})"
fi

if ! curl -s "$BASE/find-property" | grep -q 'data-param="ceiling_height"'; then
  pass "ceiling height removed from more filters"
else
  fail "ceiling height still present in more filters"
fi

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
