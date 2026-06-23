#!/usr/bin/env bash
# E2E — SEO search URLs (EN/FR, redirects, canonical, content language).
set -euo pipefail

# shellcheck source=/dev/null
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/../../../../.." && pwd)/scripts/e2e/common.sh"

PASS=0
FAIL=0
SKIP=0

pass() { PASS=$((PASS + 1)); echo "  PASS: $1"; }
fail() { FAIL=$((FAIL + 1)); echo "  FAIL: $1"; }
skip() { SKIP=$((SKIP + 1)); echo "  SKIP: $1"; }

# assert_http CODE URL [DESC]
assert_http() {
  local expected="$1" url="$2" desc="${3:-$url}"
  local code
  code=$(curl -s -o /dev/null -w '%{http_code}' -m 30 "$url")
  if [[ "$code" == "$expected" ]]; then
    pass "$desc → HTTP $code"
  else
    fail "$desc → expected HTTP $expected, got $code"
  fi
}

# assert_redirect LOCATION_PREFIX URL [DESC]
assert_redirect() {
  local expected_prefix="$1" url="$2" desc="${3:-$url}"
  local location code
  location=$(curl -sI -m 30 "$url" | tr -d '\r' | awk -F': ' 'tolower($1)=="location"{print $2; exit}')
  code=$(curl -s -o /dev/null -w '%{http_code}' -m 30 "$url")
  if [[ "$code" == "301" || "$code" == "302" ]]; then
    if [[ "$location" == *"$expected_prefix"* ]]; then
      pass "$desc → redirect $code to $location"
    else
      fail "$desc → redirect $code but Location=$location (expected *$expected_prefix*)"
    fi
  else
    fail "$desc → expected redirect, got HTTP $code"
  fi
}

# assert_final_url EXPECTED_PATH URL [DESC]
assert_final_url() {
  local expected_path="$1" url="$2" desc="${3:-$url}"
  local final
  final=$(curl -sL -o /dev/null -w '%{url_effective}' -m 60 "$url")
  local path="${final#"$BASE"}"
  local normalized="${path%/}/"
  local expected="${expected_path%/}/"
  if [[ "$normalized" == "$expected" ]]; then
    pass "$desc → final path $path"
  else
    fail "$desc → expected path $expected_path, got $path"
  fi
}

# assert_html_contains URL NEEDLE DESC
assert_html_contains() {
  local url="$1" needle="$2" desc="$3"
  local html
  html=$(curl -sL -m 60 "$url")
  if [[ "$html" == *"$needle"* ]]; then
    pass "$desc"
  else
    fail "$desc (missing: $needle)"
  fi
}

# assert_content_language URL LANG DESC
assert_content_language() {
  local url="$1" lang="$2" desc="$3"
  local header
  header=$(curl -sI -m 30 "$url" | tr -d '\r' | awk -F': ' 'tolower($1)=="content-language"{print $2; exit}')
  if [[ "$header" == "$lang" ]]; then
    pass "$desc → Content-language: $header"
  else
    fail "$desc → expected Content-language: $lang, got: ${header:-<none>}"
  fi
}

# assert_canonical URL CANONICAL_PATH DESC
assert_canonical() {
  local url="$1" canonical_path="$2" desc="$3"
  local html canonical
  html=$(curl -sL -m 60 "$url")
  canonical=$(echo "$html" | grep -oE '<link rel="canonical" href="[^"]+"' | head -1 | sed 's/.*href="//;s/"$//')
  if [[ "$canonical" == *"$canonical_path"* ]]; then
    pass "$desc → canonical contains $canonical_path"
  else
    fail "$desc → canonical=$canonical expected *$canonical_path*"
  fi
}

# assert_settings URL KEY VALUE DESC
assert_drupal_setting() {
  local url="$1" key="$2" value="$3" desc="$4"
  local html
  html=$(curl -sL -m 60 "$url")
  local needle="\"${key}\":\"${value}\""
  if [[ "$html" == *"$needle"* ]]; then
    pass "$desc"
  else
    fail "$desc (expected $needle in drupalSettings.psSearch)"
  fi
}

echo "=== SEO URL E2E — base: $BASE ==="
echo ""

echo "--- 1. Flexible search base paths ---"
assert_http 200 "$BASE/find-property" "EN flexible /find-property"
assert_http 200 "$BASE/fr/recherche-immobiliere" "FR flexible /fr/recherche-immobiliere"
assert_final_url "/fr/recherche-immobiliere/" "$BASE/recherche-immobiliere" "FR flexible slug without prefix redirects"
assert_redirect "/find-property" "$BASE/fr/find-property" "EN flexible slug under /fr/"
assert_content_language "$BASE/find-property" "en" "EN flexible page language"
assert_content_language "$BASE/fr/recherche-immobiliere" "fr" "FR flexible page language"

echo ""
echo "--- 2. Cross-language slug redirects ---"
assert_redirect "/fr/a-louer" "$BASE/a-louer/" "FR op slug without /fr/"
assert_redirect "/fr/a-louer/bureaux" "$BASE/a-louer/bureaux/" "FR op+asset without /fr/"
assert_redirect "/for-rent/office" "$BASE/fr/for-rent/office/" "EN op+asset under /fr/"
assert_redirect "/for-rent" "$BASE/fr/for-rent/" "EN op under /fr/"

echo ""
echo "--- 3. EN operation + asset SEO paths (200) ---"
declare -A EN_ASSETS=(
  [BUR]=office [ENT]=warehouse [ACT]=activity [COM]=retail
  [TER]=land [LOG]=logistics [COW]=coworking
)
for code in "${!EN_ASSETS[@]}"; do
  slug="${EN_ASSETS[$code]}"
  assert_http 200 "$BASE/for-rent/$slug/" "EN LOC+$code → /for-rent/$slug/"
  assert_drupal_setting "$BASE/for-rent/$slug/" "activeOp" "LOC" "EN LOC+$code activeOp"
  assert_drupal_setting "$BASE/for-rent/$slug/" "activeAsset" "$code" "EN LOC+$code activeAsset"
done
assert_http 200 "$BASE/for-sale/office/" "EN VEN+BUR"
assert_http 200 "$BASE/for-rent/" "EN op only LOC"

echo ""
echo "--- 4. FR operation + asset SEO paths (200) ---"
declare -A FR_ASSETS=(
  [BUR]=bureaux [ENT]=entrepot [ACT]=activite [COM]=commerce
  [TER]=terrain [LOG]=logistique
)
for code in "${!FR_ASSETS[@]}"; do
  slug="${FR_ASSETS[$code]}"
  assert_http 200 "$BASE/fr/a-louer/$slug/" "FR LOC+$code → /fr/a-louer/$slug/"
  assert_drupal_setting "$BASE/fr/a-louer/$slug/" "activeOp" "LOC" "FR LOC+$code activeOp"
  assert_drupal_setting "$BASE/fr/a-louer/$slug/" "activeAsset" "$code" "FR LOC+$code activeAsset"
  assert_content_language "$BASE/fr/a-louer/$slug/" "fr" "FR LOC+$code content language"
done
assert_http 200 "$BASE/fr/a-vendre/bureaux/" "FR VEN+BUR"
assert_http 200 "$BASE/fr/a-louer/" "FR op only LOC"

echo ""
echo "--- 5. Query param → canonical SEO redirect ---"
assert_redirect "/for-rent/" "$BASE/find-property?operation_type=LOC" "find-property?operation_type=LOC"
assert_redirect "/for-rent/office/" "$BASE/find-property?operation_type=LOC&asset_type=BUR" "find-property?op+asset"
assert_redirect "/fr/a-louer/bureaux/" "$BASE/fr/recherche-immobiliere?operation_type=LOC&asset_type=BUR" "FR find-property?op+asset"
assert_redirect "/fr/a-louer/coworking/" "$BASE/fr/recherche-immobiliere?operation_type=LOC&asset_type=COW" "FR op+asset from flexible base"
assert_redirect "/fr/coworking/" "$BASE/fr/recherche-immobiliere?asset_type%5BCOW%5D=COW" "FR asset-only (Indifférent) → /fr/coworking/"
assert_redirect "/coworking/" "$BASE/find-property?asset_type=COW" "EN asset-only (Indifférent) → /coworking/"
assert_redirect "/for-rent/retail/" "$BASE/find-property?operation_type=LOC&asset_type=COM&surface_min=&budget_max=" "Hero-style submit strips empty optional query params"

echo ""
echo "--- 5b. Asset-only SEO (Indifférent, no operation slug) ---"
assert_http 200 "$BASE/office/" "EN asset-only /office/"
assert_http 200 "$BASE/fr/bureaux/" "FR asset-only /fr/bureaux/"
assert_drupal_setting "$BASE/office/" "activeAsset" "BUR" "EN asset-only activeAsset"
html_office=$(curl -sL -m 60 "$BASE/office/")
if echo "$html_office" | grep -q '"activeFlexible":true'; then
  pass "EN asset-only activeFlexible"
else
  fail "EN asset-only activeFlexible missing"
fi
assert_canonical "$BASE/office/" "/office/" "EN asset-only canonical"
assert_canonical "$BASE/fr/bureaux/" "/fr/bureaux/" "FR asset-only canonical"

echo ""
echo "--- 6. Locality SEO segments ---"
assert_http 200 "$BASE/for-rent/office/paris/" "EN locality slug paris"
assert_http 200 "$BASE/for-rent/office/ile-de-france/" "EN LOC+BUR region slug ile-de-france"
assert_http 200 "$BASE/for-sale/office/paris-75/" "EN dept slug paris-75"
assert_http 200 "$BASE/for-sale/office/ile-de-france/" "EN region slug ile-de-france"
assert_http 200 "$BASE/for-rent/office/paris-75/paris-9-75009/" "EN dept+city arrondissement"
assert_http 200 "$BASE/office/ile-de-france/" "EN asset-only region slug"
assert_http 200 "$BASE/office/bouches-du-rhone-13/" "EN asset-only dept slug"
assert_http 200 "$BASE/office/bouches-du-rhone-13/marseille-1-13001/" "EN asset-only dept+city"
assert_http 200 "$BASE/fr/a-louer/bureaux/paris-75/paris-9-75009/" "FR dept+city arrondissement"
assert_http 200 "$BASE/fr/a-vendre/bureaux/ile-de-france/" "FR region-only slug"
assert_drupal_setting "$BASE/for-sale/office/paris-75/" "initialLocality" "75" "EN dept slug sets locality token 75"
assert_drupal_setting "$BASE/for-rent/office/ile-de-france/" "initialLocality" "region:ile-de-france" "EN region slug sets region token"
assert_html_contains "$BASE/for-sale/office/paris-75/" "Paris (75)" "EN dept chip label"
assert_html_contains "$BASE/for-sale/office/ile-de-france/" "Île-de-France" "EN region chip label (for-sale)"
assert_html_contains "$BASE/for-rent/office/ile-de-france/" "Île-de-France" "EN region chip label (for-rent)"
html_region=$(curl -sL -m 60 "$BASE/for-rent/office/ile-de-france/")
if echo "$html_region" | grep -q 'regionile-de-france'; then
  fail "EN region page must not show corrupted token regionile-de-france"
else
  pass "EN region page has no corrupted token regionile-de-france"
fi
assert_canonical "$BASE/for-rent/office/ile-de-france/" "/for-rent/office/ile-de-france/" "EN region canonical"
assert_final_url "/fr/a-louer/bureaux/paris/" "$BASE/a-louer/bureaux/paris/" "FR locality preserved after cross-lang redirect"

echo ""
echo "--- 6b. Locality APIs (suggest + data) ---"
SUGGEST_BODY=$(curl -sS -m 30 "$BASE/api/ps/location-suggest?q=ile")
if python3 -c "import json,sys; d=json.loads(sys.argv[1]); sys.exit(0 if any(i.get('label')=='Île-de-France' for g in d.get('groups',[]) for i in g.get('items',[])) else 1)" "$SUGGEST_BODY" 2>/dev/null; then
  pass "location-suggest q=ile returns Île-de-France"
else
  fail "location-suggest q=ile missing Île-de-France"
fi
if echo "$SUGGEST_BODY" | grep -q 'region:ile-de-france'; then
  pass "location-suggest q=ile returns region_token"
else
  fail "location-suggest q=ile missing region:ile-de-france token"
fi
DATA_BODY=$(curl -sS -m 30 "$BASE/api/ps/location-data?localities%5B%5D=region%3Aile-de-france")
if python3 -c "import json,sys; d=json.loads(sys.argv[1]); sys.exit(0 if d.get('data') and d['data'][0].get('label')=='Île-de-France' else 1)" "$DATA_BODY" 2>/dev/null; then
  pass "location-data preserves region token label"
else
  fail "location-data region label (got: ${DATA_BODY:0:120})"
fi
if echo "$DATA_BODY" | grep -q 'regionile-de-france'; then
  fail "location-data must not corrupt token to regionile-de-france"
else
  pass "location-data token not corrupted"
fi

echo ""
echo "--- 7. Canonical tags ---"
assert_canonical "$BASE/for-rent/office/" "/for-rent/office/" "EN canonical"
assert_canonical "$BASE/fr/a-louer/bureaux/" "/fr/a-louer/bureaux/" "FR canonical"

echo ""
echo "--- 8. FR drupalSettings slugs match translated config ---"
html_fr=$(curl -sL -m 60 "$BASE/fr/a-louer/bureaux/")
if echo "$html_fr" | grep -q 'searchPath.*recherche-immobiliere'; then
  pass "FR searchPath translated (recherche-immobiliere)"
else
  fail "FR searchPath not translated in drupalSettings"
fi
if echo "$html_fr" | grep -q '"opSlugs":{"LOC":"a-louer"'; then
  pass "FR opSlugs translated"
else
  fail "FR opSlugs not translated in drupalSettings"
fi
if echo "$html_fr" | grep -q '"assetSlugs":{"BUR":"bureaux"'; then
  pass "FR assetSlugs translated"
else
  fail "FR assetSlugs not translated in drupalSettings"
fi
html_en=$(curl -sL -m 60 "$BASE/for-rent/office/")
if echo "$html_en" | grep -q '"opSlugs":{"LOC":"for-rent"'; then
  pass "EN opSlugs in drupalSettings"
else
  fail "EN opSlugs missing in drupalSettings"
fi

echo ""
echo "--- 9. Strict content language (count/list alignment) ---"

assert_list_count_alignment() {
  local url="$1" desc="$2"
  local html gc has_rows
  html=$(curl -sL -m 60 "$url")
  gc=$(echo "$html" | grep -oE '"globalCount":[0-9]+' | head -1 | cut -d: -f2)
  if [[ "$html" == *"js-ps-search-results-rows"* ]]; then
    has_rows=1
  else
    has_rows=0
  fi
  if [[ -z "$gc" ]]; then
    fail "$desc — globalCount missing in drupalSettings"
    return
  fi
  if [[ "$gc" -gt 0 && "$has_rows" -eq 1 ]]; then
    pass "$desc — globalCount=$gc with list rows"
  elif [[ "$gc" -eq 0 && "$has_rows" -eq 0 ]]; then
    pass "$desc — globalCount=0, list empty (strict language)"
  else
    fail "$desc — globalCount=$gc but list rows=$has_rows (misaligned)"
  fi
}

html_fr=$(curl -sL -m 60 "$BASE/fr/a-louer/bureaux/")
gc_fr=$(echo "$html_fr" | grep -oE '"globalCount":[0-9]+' | head -1 | cut -d: -f2)
if [[ -n "$gc_fr" && "$gc_fr" -gt 0 ]]; then
  pass "FR /fr/a-louer/bureaux/ globalCount=$gc_fr (>0)"
else
  fail "FR /fr/a-louer/bureaux/ globalCount is 0"
fi
assert_list_count_alignment "$BASE/fr/a-louer/bureaux/" "FR filtered search"
assert_list_count_alignment "$BASE/find-property" "EN base search"
assert_list_count_alignment "$BASE/for-rent/office/" "EN filtered search"

echo ""
echo "--- 10. Legacy slug ---"
assert_redirect "/find-property" "$BASE/recherche" "legacy /recherche → current EN slug"

echo ""
echo "--- 11. FR translated asset types and aliases ---"
assert_http 200 "$BASE/fr/a-louer/coworking/" "FR COW coworking slug"
assert_redirect "/fr/a-louer/bureaux/" "$BASE/fr/a-louer/bureau/" "FR legacy asset alias bureau → bureaux"

echo ""
echo "=== Summary: $PASS passed, $FAIL failed, $SKIP skipped ==="
if [[ "$FAIL" -gt 0 ]]; then
  exit 1
fi
exit 0
