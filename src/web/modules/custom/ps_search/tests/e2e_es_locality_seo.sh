#!/usr/bin/env bash
# ES locality / geo zone parity smoke tests (regions: Madrid, Catalonia, Andalusia).
set -euo pipefail

export PS_E2E_COUNTRY=es
export BASE_URL="${BASE_URL:-http://es.localhost:8082}"
export BASE="${BASE_URL}"

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
  local region_slug="$1" label="$2"
  local base region
  base=$(curl -sS -m 60 "${BASE}/api/ps/count?operation_type=LOC&asset_type=BUR" 2>/dev/null \
    | python3 -c "import json,sys; print(json.load(sys.stdin).get('count', -1))" 2>/dev/null || echo "-1")
  region=$(curl -sS -m 60 "${BASE}/api/ps/count?operation_type=LOC&asset_type=BUR&locations=region%3A${region_slug}" 2>/dev/null \
    | python3 -c "import json,sys; print(json.load(sys.stdin).get('count', -1))" 2>/dev/null || echo "-1")
  if [[ "$base" =~ ^[0-9]+$ && "$region" =~ ^[0-9]+$ && "$base" -gt 0 && "$region" -gt 0 && "$region" -le "$base" ]]; then
    pass "$label (base=$base region=$region)"
  else
    fail "$label (base=$base region=$region)"
  fi
}

echo "=== PS Search ES — Locality / geo zones ($BASE) ==="

echo ""
echo "--- Warm-up ---"
curl -sL -m 120 -o /dev/null "${BASE}/en-alquiler/oficinas/" 2>/dev/null || true

echo ""
echo "--- 1. SEO paths — regions (ES slugs) ---"
assert_http_200 "${BASE}/en-alquiler/oficinas/madrid/" "LOC+BUR + region madrid"
assert_http_200 "${BASE}/en-alquiler/oficinas/catalonia/" "LOC+BUR + region catalonia"
assert_http_200 "${BASE}/en-alquiler/oficinas/andalusia/" "LOC+BUR + region andalusia"

echo ""
echo "--- 2. drupalSettings initialLocality ---"
assert_drupal_setting "${BASE}/en-alquiler/oficinas/madrid/" \
  "initialLocality" "region:madrid" "madrid region token"
assert_drupal_setting "${BASE}/en-alquiler/oficinas/catalonia/" \
  "initialLocality" "region:catalonia" "catalonia region token"
assert_drupal_setting "${BASE}/en-alquiler/oficinas/andalusia/" \
  "initialLocality" "region:andalusia" "andalusia region token"

echo ""
echo "--- 3. Location suggest API ---"
assert_json_api \
  "${BASE}/api/ps/location-suggest?q=mad" \
  "any(i.get('label') == 'Community of Madrid' for g in data.get('groups', []) for i in g.get('items', []))" \
  "suggest q=mad returns Community of Madrid"
assert_json_api \
  "${BASE}/api/ps/location-suggest?q=mad" \
  "any(i.get('region_token') == 'region:madrid' for g in data.get('groups', []) for i in g.get('items', []))" \
  "suggest q=mad returns region:madrid"
assert_json_api \
  "${BASE}/api/ps/location-suggest?q=anda" \
  "any(i.get('label') == 'Andalusia' for g in data.get('groups', []) for i in g.get('items', []))" \
  "suggest q=anda returns Andalusia"

echo ""
echo "--- 4. Location data API ---"
assert_json_api \
  "${BASE}/api/ps/location-data?localities%5B%5D=region%3Amadrid" \
  "data.get('data') and data['data'][0].get('label') == 'Community of Madrid'" \
  "location-data region:madrid label"
assert_json_api \
  "${BASE}/api/ps/location-data?localities%5B%5D=28001" \
  "data.get('data') and data['data'][0].get('type') in ('postal_code', 'city')" \
  "location-data postal 28001"

echo ""
echo "--- 5. Count API with region filter ---"
assert_count_with_region "madrid" "madrid region count"
assert_count_with_region "andalusia" "andalusia region count (Sevilla)"

echo ""
echo "--- 6. City locality paths ---"
assert_http_200 "${BASE}/en-alquiler/oficinas/madrid/madrid-28001/" "madrid city segment"
assert_drupal_setting "${BASE}/en-alquiler/oficinas/madrid/madrid-28001/" \
  "initialLocality" "28001" "madrid city postal token"

echo ""
echo "=== Summary: $PASS passed, $FAIL failed, $SKIP skipped ==="
if [[ "$FAIL" -gt 0 ]]; then
  exit 1
fi
exit 0
