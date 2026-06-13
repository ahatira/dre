#!/usr/bin/env bash
# E2E — Homepage hero GET submit → canonical SEO search URLs.
# Covers Buy / Rent / Indifférent × all asset types × EN + FR.
set -euo pipefail

BASE="${BASE_URL:-http://localhost:8080}"
PASS=0
FAIL=0

pass() { echo "  PASS: $1"; PASS=$((PASS + 1)); }
fail() { echo "  FAIL: $1"; FAIL=$((FAIL + 1)); }

# assert_hero_redirect DESC FORM_ACTION EXPECTED_FINAL_PATH_FRAGMENT
assert_hero_redirect() {
  local desc="$1"
  local action="$2"
  local expect_fragment="$3"
  local final
  final=$(curl -sL -m 60 -o /dev/null -w '%{url_effective}' "$action")
  if [[ "$final" == *"$expect_fragment"* ]]; then
    pass "$desc → $final"
  else
    fail "$desc (expected *${expect_fragment}*, got $final)"
  fi
}

# assert_hero_http DESC FORM_ACTION
assert_hero_http() {
  local desc="$1"
  local action="$2"
  local code
  code=$(curl -sL -m 60 -o /dev/null -w '%{http_code}' "$action")
  if [[ "$code" == "200" ]]; then
    pass "$desc → HTTP $code"
  else
    fail "$desc → HTTP $code"
  fi
}

echo "=== Homepage hero → search E2E — base: $BASE ==="
echo ""

LOCALITY="Paris"

declare -A EN_SLUGS=( [BUR]=office [ENT]=warehouse [ACT]=activity [COM]=retail [TER]=land [LOG]=logistics [COW]=coworking )
declare -A FR_SLUGS=( [BUR]=bureaux [ENT]=entrepot [ACT]=activite [COM]=commerce [TER]=terrain [LOG]=logistique [COW]=coworking )

echo "--- EN — Rent (LOC) ---"
for code in BUR ENT ACT COM TER LOG COW; do
  slug="${EN_SLUGS[$code]}"
  assert_hero_redirect "EN Rent+$code" \
    "$BASE/find-property?operation_type=LOC&asset_type=${code}&locality[]=${LOCALITY}" \
    "/for-rent/${slug}/"
done

echo ""
echo "--- EN — Buy (VEN) ---"
for code in BUR ENT ACT COM TER LOG COW; do
  slug="${EN_SLUGS[$code]}"
  assert_hero_redirect "EN Buy+$code" \
    "$BASE/find-property?operation_type=VEN&asset_type=${code}&locality[]=${LOCALITY}" \
    "/for-sale/${slug}/"
done

echo ""
echo "--- EN — Indifférent (no operation slug) ---"
for code in BUR ENT ACT COM TER LOG COW; do
  slug="${EN_SLUGS[$code]}"
  assert_hero_redirect "EN Indiff+$code" \
    "$BASE/find-property?asset_type=${code}&locality[]=${LOCALITY}" \
    "/${slug}/"
  final=$(curl -sL -m 60 -o /dev/null -w '%{url_effective}' \
    "$BASE/find-property?asset_type=${code}&locality[]=${LOCALITY}")
  if [[ "$final" == *"/for-rent/"* ]] || [[ "$final" == *"/for-sale/"* ]]; then
    fail "EN Indiff+$code must not contain for-rent/for-sale (got $final)"
  else
    pass "EN Indiff+$code has no op slug"
  fi
done

echo ""
echo "--- FR — Louer (LOC) ---"
for code in BUR ENT ACT COM TER LOG COW; do
  slug="${FR_SLUGS[$code]}"
  assert_hero_redirect "FR Rent+$code" \
    "$BASE/fr/recherche-immobiliere?operation_type=LOC&asset_type=${code}&locality[]=${LOCALITY}" \
    "/fr/a-louer/${slug}/"
done

echo ""
echo "--- FR — Acheter (VEN) ---"
for code in BUR ENT ACT COM TER LOG COW; do
  slug="${FR_SLUGS[$code]}"
  assert_hero_redirect "FR Buy+$code" \
    "$BASE/fr/recherche-immobiliere?operation_type=VEN&asset_type=${code}&locality[]=${LOCALITY}" \
    "/fr/a-vendre/${slug}/"
done

echo ""
echo "--- FR — Indifférent (no operation slug) ---"
for code in BUR ENT ACT COM TER LOG COW; do
  slug="${FR_SLUGS[$code]}"
  assert_hero_redirect "FR Indiff+$code" \
    "$BASE/fr/recherche-immobiliere?asset_type=${code}&locality[]=${LOCALITY}" \
    "/fr/${slug}/"
  final=$(curl -sL -m 60 -o /dev/null -w '%{url_effective}' \
    "$BASE/fr/recherche-immobiliere?asset_type=${code}&locality[]=${LOCALITY}")
  if [[ "$final" == *"/a-louer/"* ]] || [[ "$final" == *"/a-vendre/"* ]]; then
    fail "FR Indiff+$code must not contain a-louer/a-vendre (got $final)"
  else
    pass "FR Indiff+$code has no op slug"
  fi
done

echo ""
echo "--- Optional fields stripped (empty surface/budget) ---"
assert_hero_redirect "EN Rent+COM empty optional" \
  "$BASE/find-property?operation_type=LOC&asset_type=COM&locality[]=${LOCALITY}&surface_min=&budget_max=" \
  "/for-rent/retail/"
assert_hero_redirect "EN Indiff+BUR empty optional" \
  "$BASE/find-property?asset_type=BUR&locality[]=${LOCALITY}&surface_min=&budget_max=" \
  "/office/"

echo ""
echo "--- Search page renders results (sample) ---"
for url in \
  "$BASE/for-rent/office/" \
  "$BASE/for-sale/office/" \
  "$BASE/office/" \
  "$BASE/fr/a-louer/bureaux/" \
  "$BASE/fr/a-vendre/bureaux/" \
  "$BASE/fr/bureaux/"; do
  html=$(curl -sL -m 60 "$url")
  code=$(curl -sL -m 60 -o /dev/null -w '%{http_code}' "$url")
  if [[ "$code" != "200" ]]; then
    fail "$url → HTTP $code"
    continue
  fi
  if echo "$html" | grep -q '"globalCount":'; then
    pass "$url → HTTP 200 + globalCount"
  else
    fail "$url → HTTP 200 but no globalCount"
  fi
done

echo ""
echo "=== Summary: $PASS passed, $FAIL failed ==="
[[ "$FAIL" -eq 0 ]]
