#!/usr/bin/env bash
# B2B smoke tests — Locality SEO paths, region tokens, suggest/data APIs, chip labels.
set -euo pipefail

# shellcheck source=/dev/null
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/../../../../.." && pwd)/scripts/e2e/common.sh"

PASS=0
FAIL=0
SKIP=0

pass() { PASS=$((PASS + 1)); echo "  PASS: $1"; }
fail() { FAIL=$((FAIL + 1)); echo "  FAIL: $1"; }
skip() { SKIP=$((SKIP + 1)); echo "  SKIP: $1"; }

fetch() {
  curl -sL -m 120 "$1" 2>/dev/null || echo ""
}

status_code() {
  curl -sL -m 120 -o /dev/null -w '%{http_code}' "$1" 2>/dev/null || echo "000"
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

assert_html_not_contains() {
  local url="$1" needle="$2" label="$3"
  local html
  html=$(fetch "$url")
  if [[ -z "$html" ]]; then
    fail "$label (empty response) — $url"
    return
  fi
  if grep -Fq "$needle" <<< "$html"; then
    fail "$label (forbidden string present: $needle) — $url"
  else
    pass "$label"
  fi
}

assert_drupal_setting() {
  local url="$1" key="$2" value="$3" label="$4"
  local html needle
  html=$(fetch "$url")
  needle="\"${key}\":\"${value}\""
  if grep -Fq "$needle" <<< "$html"; then
    pass "$label"
  else
    fail "$label (expected $needle) — $url"
  fi
}

assert_canonical_contains() {
  local url="$1" path_fragment="$2" label="$3"
  local html canonical
  html=$(fetch "$url")
  canonical=$(grep -oE '<link rel="canonical" href="[^"]+"' <<< "$html" | head -1 | sed 's/.*href="//;s/"$//')
  if [[ "$canonical" == *"$path_fragment"* ]]; then
    pass "$label → $canonical"
  else
    fail "$label (canonical=$canonical expected *$path_fragment*) — $url"
  fi
}

assert_json_api() {
  local url="$1" py_check="$2" label="$3"
  local body
  body=$(curl -sS -m 60 "$url" 2>/dev/null || echo "")
  if [[ -z "$body" ]]; then
    fail "$label (empty API response) — $url"
    return
  fi
  if python3 -c "import json,sys; data=json.loads(sys.argv[1]); sys.exit(0 if ($py_check) else 1)" "$body" 2>/dev/null; then
    pass "$label"
  else
    fail "$label — $url"
    echo "       body: ${body:0:200}"
  fi
}

assert_count_with_region() {
  local label="$1"
  local base region
  base=$(curl -sS -m 60 "${BASE}/api/ps/count?operation_type=LOC&asset_type=BUR" 2>/dev/null \
    | python3 -c "import json,sys; print(json.load(sys.stdin).get('count', -1))" 2>/dev/null || echo "-1")
  region=$(curl -sS -m 60 "${BASE}/api/ps/count?operation_type=LOC&asset_type=BUR&locations=region%3Aile-de-france" 2>/dev/null \
    | python3 -c "import json,sys; print(json.load(sys.stdin).get('count', -1))" 2>/dev/null || echo "-1")
  if [[ "$base" =~ ^[0-9]+$ && "$region" =~ ^[0-9]+$ && "$base" -gt 0 && "$region" -gt 0 && "$region" -le "$base" ]]; then
    pass "$label (base=$base region=$region)"
  else
    fail "$label (base=$base region=$region)"
  fi
}

maybe_skip_fr() {
  local url="$1"
  local code
  code=$(status_code "$url")
  if [[ "$code" != "200" ]]; then
    skip "FR site unavailable ($code) — $url"
    return 1
  fi
  return 0
}

echo "=== PS Search B2B — Locality / region SEO ($BASE) ==="

echo ""
echo "--- Warm-up ---"
curl -sL -m 120 -o /dev/null "$BASE/for-rent/office/" 2>/dev/null || true

echo ""
echo "--- 1. SEO paths — region / department / city (EN) ---"
assert_http_200 "$BASE/for-rent/office/ile-de-france/" "LOC+BUR + region ile-de-france"
assert_http_200 "$BASE/for-sale/office/ile-de-france/" "VEN+BUR + region ile-de-france"
assert_http_200 "$BASE/for-rent/office/paris-75/" "LOC+BUR + dept paris-75"
assert_http_200 "$BASE/for-sale/office/paris-75/" "VEN+BUR + dept paris-75"
assert_http_200 "$BASE/for-rent/office/paris-75/paris-9-75009/" "LOC+BUR + dept + arrondissement"
assert_http_200 "$BASE/office/ile-de-france/" "asset-only + region ile-de-france"
assert_http_200 "$BASE/office/paris-75/" "asset-only + dept paris-75"
assert_http_200 "$BASE/office/bouches-du-rhone-13/marseille-1-13001/" "asset-only + dept + city"

echo ""
echo "--- 2. drupalSettings initialLocality tokens ---"
assert_drupal_setting "$BASE/for-rent/office/ile-de-france/" \
  "initialLocality" "region:ile-de-france" "region page sets region token"
assert_drupal_setting "$BASE/for-sale/office/paris-75/" \
  "initialLocality" "75" "dept page sets department token"
assert_drupal_setting "$BASE/for-rent/office/paris-75/paris-9-75009/" \
  "initialLocality" "75009" "city page sets postal token"

echo ""
echo "--- 3. Chip labels (human-readable, no corrupted tokens) ---"
assert_html_contains "$BASE/for-rent/office/ile-de-france/" "Île-de-France" "region chip label Île-de-France"
assert_html_contains "$BASE/for-sale/office/paris-75/" "Paris (75)" "dept chip label Paris (75)"
assert_html_not_contains "$BASE/for-rent/office/ile-de-france/" "regionile-de-france" "no corrupted region token in HTML"

echo ""
echo "--- 4. Canonical URLs ---"
assert_canonical_contains "$BASE/for-rent/office/ile-de-france/" "/for-rent/office/ile-de-france/" "region canonical path"
assert_canonical_contains "$BASE/for-sale/office/paris-75/" "/for-sale/office/paris-75/" "dept canonical path"
assert_canonical_contains "$BASE/office/ile-de-france/" "/office/ile-de-france/" "asset-only region canonical"

echo ""
echo "--- 5. Location suggest API ---"
assert_json_api \
  "${BASE}/api/ps/location-suggest?q=ile" \
  "any(i.get('label') == 'Île-de-France' for g in data.get('groups', []) for i in g.get('items', []))" \
  "suggest q=ile returns Île-de-France"
assert_json_api \
  "${BASE}/api/ps/location-suggest?q=ile" \
  "any(i.get('type') == 'region' and i.get('region_token') == 'region:ile-de-france' for g in data.get('groups', []) for i in g.get('items', []))" \
  "suggest q=ile returns region_token region:ile-de-france"

echo ""
echo "--- 6. Location data API (token preservation + label) ---"
assert_json_api \
  "${BASE}/api/ps/location-data?localities%5B%5D=region%3Aile-de-france" \
  "data.get('data') and data['data'][0].get('label') == 'Île-de-France' and data['data'][0].get('type') == 'region'" \
  "location-data region token → Île-de-France"
assert_json_api \
  "${BASE}/api/ps/location-data?localities%5B%5D=region%3Aile-de-france" \
  "data.get('data') and data['data'][0].get('region_slug') == 'ile-de-france'" \
  "location-data region_slug ile-de-france"
assert_json_api \
  "${BASE}/api/ps/location-data?localities%5B%5D=75" \
  "data.get('data') and 'Paris' in data['data'][0].get('label', '') and data['data'][0].get('type') == 'department'" \
  "location-data dept 75 → Paris (75)"

echo ""
echo "--- 7. Count API with region filter ---"
assert_count_with_region "region filter reduces or matches office rent count"

echo ""
echo "--- 8. Page title includes region label ---"
assert_html_contains "$BASE/for-rent/office/ile-de-france/" \
  "Office for rent in Île-de-France" "H1/title contains Île-de-France"

echo ""
echo "--- 9. FR translated locality paths (skip if FR site down) ---"
if maybe_skip_fr "$BASE/fr/a-vendre/bureaux/ile-de-france/"; then
  assert_drupal_setting "$BASE/fr/a-vendre/bureaux/ile-de-france/" \
    "initialLocality" "region:ile-de-france" "FR region token"
  assert_html_contains "$BASE/fr/a-vendre/bureaux/ile-de-france/" "Île-de-France" "FR region chip label"
  assert_http_200 "$BASE/fr/a-louer/bureaux/paris-75/paris-9-75009/" "FR dept + arrondissement"
fi

echo ""
echo "--- 10. Legacy / query fallback still resolves ---"
assert_http_200 "$BASE/find-property?operation_type=LOC&asset_type=BUR&locations=region%3Aile-de-france" \
  "flexible base with ?locations=region token"

echo ""
echo "=== Summary: $PASS passed, $FAIL failed, $SKIP skipped ==="
if [[ "$FAIL" -gt 0 ]]; then
  exit 1
fi
exit 0
