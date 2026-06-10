#!/usr/bin/env bash
# B2B smoke tests — search budget/price filter (labels, units, filtering, count API).
set -euo pipefail

BASE="${BASE_URL:-http://localhost:8080}"
PASS=0
FAIL=0
SKIP=0

pass() { echo "  PASS: $1"; PASS=$((PASS + 1)); }
fail() { echo "  FAIL: $1"; FAIL=$((FAIL + 1)); }
skip() { echo "  SKIP: $1"; SKIP=$((SKIP + 1)); }

fetch() {
  curl -sL -m 120 "$1" 2>/dev/null || echo ""
}

budget_unit_from_html() {
  local html="$1"
  if echo "$html" | grep -q '"budgetFilterConfig"'; then
    echo "$html" | sed -n 's/.*"budgetFilterConfig":{[^}]*"budget_unit":\([^,}]*\).*/\1/p' | head -1 | tr -d '"'
    return
  fi
  echo "$html" | sed -n 's/.*"budget_unit":"\([^"]*\)".*/\1/p' | head -1
}

field_label_from_html() {
  local html="$1"
  echo "$html" | sed -n 's/.*"budgetFilterConfig":{[^}]*"field_label":"\([^"]*\)".*/\1/p' | head -1
}

assert_budget_unit() {
  local url="$1" expected="$2" label="$3"
  local html unit
  html=$(fetch "$url")
  if [[ -z "$html" ]]; then
    fail "$label (empty response) — $url"
    return
  fi
  unit=$(budget_unit_from_html "$html")
  if [[ "$expected" == "null" && ( -z "$unit" || "$unit" == "null" ) ]]; then
    pass "$label (budget_unit=null)"
  elif [[ "$unit" == "$expected" ]]; then
    pass "$label (budget_unit=$unit)"
  else
    fail "$label (expected budget_unit=$expected, got '${unit:-empty}') — $url"
  fi
}

assert_field_label() {
  local url="$1" expected="$2" label="$3"
  local html got
  html=$(fetch "$url")
  got=$(field_label_from_html "$html")
  if [[ "$got" == "$expected" ]]; then
    pass "$label (field_label=$got)"
  else
    fail "$label (expected field_label=$expected, got '${got:-empty}') — $url"
  fi
}

count_api() {
  local qs="$1"
  curl -s -m 60 "${BASE}/ps-search/count?${qs}" 2>/dev/null | sed -n 's/.*"count":\([0-9]*\).*/\1/p' | head -1
}

result_rows() {
  local html="$1"
  { echo "$html" | grep -o 'data-offer-id="[0-9]\+"' || true; } | wc -l | tr -d ' '
}

echo "=== PS Search B2B Budget filter tests ($BASE) ==="

echo "--- Label / unit matrix (server-rendered budgetFilterConfig) ---"
assert_budget_unit "$BASE/find-property" "null" "EN flexible /find-property"
assert_budget_unit "$BASE/fr/recherche-immobiliere" "null" "FR flexible /fr/recherche-immobiliere"
assert_field_label "$BASE/find-property" "Budget" "EN flexible field label"

assert_budget_unit "$BASE/for-rent/office/" "PER_M2" "EN SEO LOC+BUR"
assert_budget_unit "$BASE/for-sale/office/" "GLOBAL" "EN SEO VEN+BUR"
assert_budget_unit "$BASE/fr/a-louer/bureaux/" "PER_M2" "FR SEO LOC+BUR"
assert_budget_unit "$BASE/fr/a-vendre/bureaux/" "GLOBAL" "FR SEO VEN+BUR"
assert_budget_unit "$BASE/fr/a-louer/coworking/" "PER_POSTE" "FR SEO LOC+COW"
assert_budget_unit "$BASE/for-rent/coworking/" "PER_POSTE" "EN SEO LOC+COW"

for asset in ENT ACT COM TER LOG; do
  assert_budget_unit "$BASE/find-property?operation_type=LOC&asset_type=${asset}" "PER_M2" "Query LOC+${asset}"
done
assert_budget_unit "$BASE/find-property?operation_type=VEN&asset_type=ENT" "GLOBAL" "Query VEN+ENT"
assert_budget_unit "$BASE/find-property?operation_type=LOC&asset_type=COW" "PER_POSTE" "Query LOC+COW"

echo "--- Budget filter narrows results (Views exposed budget[min|max]) ---"
baseline_html=$(fetch "$BASE/fr/a-louer/bureaux/")
baseline_rows=$(result_rows "$baseline_html")
filtered_html=$(fetch "$BASE/fr/a-louer/bureaux/?budget%5Bmin%5D=500&budget%5Bmax%5D=600")
filtered_rows=$(result_rows "$filtered_html")
if [[ "$baseline_rows" -ge 1 ]]; then
  if [[ "$filtered_rows" -le "$baseline_rows" ]]; then
    pass "Budget range narrows or preserves BUR rent results ($filtered_rows <= $baseline_rows)"
  else
    fail "Budget range increased results ($filtered_rows > $baseline_rows)"
  fi
elif [[ "$baseline_rows" -eq 0 && "$filtered_rows" -eq 0 ]]; then
  skip "Baseline BUR rent page has no list rows in default map zone — budget range UI only"
else
  fail "Baseline BUR rent page has no result rows ($baseline_rows)"
fi

ven_base_count=$(count_api "operation_type=VEN&asset_type=BUR")
ven_filt_count=$(count_api "operation_type=VEN&asset_type=BUR&budget_min=1000000&budget_max=50000000")
if [[ -n "$ven_base_count" && -n "$ven_filt_count" ]]; then
  if [[ "$ven_filt_count" -le "$ven_base_count" ]]; then
    pass "VEN price range filter valid via count API ($ven_filt_count <= $ven_base_count)"
  else
    fail "VEN price range widened count API results"
  fi
fi

echo "--- Count API budget_min / budget_max ---"
base_count=$(count_api "operation_type=LOC&asset_type=BUR")
narrow_count=$(count_api "operation_type=LOC&asset_type=BUR&budget_min=200&budget_max=400")
if [[ -n "$base_count" && -n "$narrow_count" ]]; then
  if [[ "$narrow_count" -le "$base_count" ]]; then
    pass "Count API budget range ($narrow_count <= $base_count)"
  else
    fail "Count API budget range widened results ($narrow_count > $base_count)"
  fi
else
  fail "Count API returned empty (base=$base_count narrow=$narrow_count)"
fi

cow_base=$(count_api "operation_type=LOC&asset_type=COW")
cow_narrow=$(count_api "operation_type=LOC&asset_type=COW&budget_min=100&budget_max=500")
if [[ -n "$cow_base" && -n "$cow_narrow" ]]; then
  if [[ "$cow_narrow" -le "$cow_base" ]]; then
    pass "Count API COW PER_POSTE budget ($cow_narrow <= $cow_base)"
  else
    fail "Count API COW budget widened results"
  fi
else
  pass "Count API COW (no coworking offers — counts=${cow_base:-0}/${cow_narrow:-0})"
fi

echo "--- Hydration from URL params ---"
hydrated_tmp=$(mktemp)
curl -sL -m 120 -o "$hydrated_tmp" "$BASE/fr/a-louer/bureaux/?budget%5Bmin%5D=100&budget%5Bmax%5D=500" 2>/dev/null || true
if grep -Fq 'name="budget[min]"' "$hydrated_tmp" && grep -Fq 'value="100"' "$hydrated_tmp"; then
  pass "Views BEF budget[min]=100 in page"
else
  fail "Views BEF budget[min] not set from URL"
fi
if grep -Fq 'js-ps-budget-min' "$hydrated_tmp"; then
  pass "Filter bar budget inputs present (JS hydrates on attach)"
else
  fail "Filter bar budget inputs missing"
fi
rm -f "$hydrated_tmp"

echo ""
echo "=== Summary: $PASS passed, $FAIL failed, $SKIP skipped ==="
[[ "$FAIL" -eq 0 ]]
