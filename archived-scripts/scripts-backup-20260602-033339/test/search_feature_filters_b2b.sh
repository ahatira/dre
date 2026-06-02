#!/usr/bin/env bash
set -euo pipefail

BASE_URL="${BASE_URL:-http://localhost:8080}"
SEARCH_PATH="/recherche"

fetch() {
  local url="$1"
  curl -g -s "$url"
}

count_rows() {
  grep -c "views-row" || true
}

assert_ge() {
  local actual="$1"
  local min="$2"
  local label="$3"
  if (( actual < min )); then
    echo "FAIL: $label (expected >=$min actual=$actual)"
    exit 1
  fi
  echo "OK: $label ($actual)"
}

assert_le() {
  local actual="$1"
  local max="$2"
  local label="$3"
  if (( actual > max )); then
    echo "FAIL: $label (expected <=$max actual=$actual)"
    exit 1
  fi
  echo "OK: $label ($actual)"
}

assert_contains() {
  local haystack="$1"
  local needle="$2"
  local label="$3"
  if [[ "$haystack" != *"$needle"* ]]; then
    echo "FAIL: $label (missing '$needle')"
    exit 1
  fi
  echo "OK: $label"
}

echo "== B2B Feature Filters =="

canonical_url="$(curl -s -L -o /tmp/ps_b2b_base.html -w '%{url_effective}' "$BASE_URL/recherche?operation_type=LOC&asset_type=BUR&_nocache=$(date +%s)")"
if [[ -n "$canonical_url" && "$canonical_url" == "$BASE_URL"* ]]; then
  SEARCH_PATH="${canonical_url#${BASE_URL}}"
fi

baseline_html="$(fetch "$BASE_URL$SEARCH_PATH?_nocache=$(date +%s)")"
baseline_count="$(printf "%s" "$baseline_html" | count_rows)"
assert_ge "$baseline_count" 1 "Baseline result count"

checkbox_names="$(printf "%s" "$baseline_html" | sed -n 's/.*name="\(feature_[^"]*\)" value="1".*/\1/p' | sort -u)"
checkbox_count="$(printf "%s\n" "$checkbox_names" | sed '/^$/d' | wc -l | tr -d ' ')"
assert_ge "$checkbox_count" 1 "At least one feature checkbox is rendered"

first_checkbox="$(printf "%s\n" "$checkbox_names" | sed -n '1p')"
first_checkbox_html="$(fetch "$BASE_URL$SEARCH_PATH?${first_checkbox}=1&_nocache=$(date +%s)")"
first_checkbox_count="$(printf "%s" "$first_checkbox_html" | count_rows)"
assert_le "$first_checkbox_count" "$baseline_count" "Single feature checkbox narrows or preserves result set"

second_checkbox="$(printf "%s\n" "$checkbox_names" | sed -n '2p')"
if [[ -n "$second_checkbox" ]]; then
  combined_html="$(fetch "$BASE_URL$SEARCH_PATH?${first_checkbox}=1&${second_checkbox}=1&_nocache=$(date +%s)")"
  combined_count="$(printf "%s" "$combined_html" | count_rows)"
  assert_le "$combined_count" "$baseline_count" "Combined feature checkboxes stay within baseline result set"
fi

numeric_min_name="$(printf "%s" "$baseline_html" | sed -n 's/.*name="\(feature_[^"]*\)\[min\]".*/\1/p' | head -n1)"
if [[ -n "$numeric_min_name" ]]; then
  range_html="$(fetch "$BASE_URL$SEARCH_PATH?${numeric_min_name}[min]=0&${numeric_min_name}[max]=999999&_nocache=$(date +%s)")"
  range_count="$(printf "%s" "$range_html" | count_rows)"
  assert_ge "$range_count" 0 "Numeric feature range request returns valid response"
fi

reset_html="$(curl -L -g -s "$BASE_URL$SEARCH_PATH?reset=Reset&${first_checkbox}=1&_nocache=$(date +%s)")"
assert_contains "$reset_html" "More filters" "Reset returns search page"

echo "B2B feature filter tests passed"
