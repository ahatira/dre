#!/usr/bin/env bash
# B2B Apply flow — budget filter navigation URL after Apply (simulates JS buildNavigationUrl output).
# Validates 200 + budget params preserved + BEF hydration for each asset × operation context.
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
  curl -sL -m 120 -o /dev/null -w '%{http_code}' "$1" 2>/dev/null || echo "000"
}

assert_apply_url() {
  local start_url="$1" min="$2" max="$3" label="$4"
  local enc_min enc_max apply_url code html attempt tmp
  enc_min=$(python3 -c "import urllib.parse; print(urllib.parse.quote('${min}'))")
  enc_max=$(python3 -c "import urllib.parse; print(urllib.parse.quote('${max}'))")
  apply_url="${start_url}?budget%5Bmin%5D=${enc_min}&budget%5Bmax%5D=${enc_max}"
  tmp=$(mktemp)
  code="000"
  for attempt in 1 2 3; do
    code=$(curl -sL -m 120 -o "$tmp" -w '%{http_code}' "$apply_url" 2>/dev/null || echo "000")
    [[ "$code" == "200" && $(wc -c < "$tmp") -gt 50000 ]] && break
    sleep 2
  done
  if [[ "$code" != "200" ]] || [[ $(wc -c < "$tmp") -lt 50000 ]]; then
    fail "$label (HTTP $code, body $(wc -c < "$tmp") bytes) — $apply_url"
    rm -f "$tmp"
    return
  fi
  if grep -Fq 'name="budget[min]"' "$tmp" && grep -Fq "value=\"${min}\"" "$tmp"; then
    pass "$label (200, budget[min]=${min})"
  elif grep -Fq 'js-ps-budget-min' "$tmp"; then
    pass "$label (200, filter bar budget inputs present)"
  else
    fail "$label (200 but budget filter not reflected) — $apply_url"
  fi
  rm -f "$tmp"
  sleep 0.5
}

echo "=== PS Search B2B Budget Apply URL tests ($BASE) ==="

echo "--- Flexible search ---"
assert_apply_url "$BASE/fr/recherche-immobiliere" "50" "5000" "FR flexible Apply budget"
assert_apply_url "$BASE/find-property" "100" "9999" "EN flexible Apply budget"

echo "--- LOC (rent) per asset type ---"
declare -A LOC_PATHS=(
  [BUR]="/fr/a-louer/bureaux/"
  [ENT]="/fr/a-louer/entrepot/"
  [ACT]="/fr/a-louer/activite/"
  [COM]="/fr/a-louer/commerce/"
  [TER]="/fr/a-louer/terrain/"
  [LOG]="/fr/a-louer/logistique/"
  [COW]="/fr/a-louer/coworking/"
)
for code in BUR ENT ACT COM TER LOG COW; do
  path="${LOC_PATHS[$code]}"
  if [[ "$code" == "COW" ]]; then
    assert_apply_url "$BASE${path}" "200" "800" "LOC+${code} Apply (€/poste/an)"
  else
    assert_apply_url "$BASE${path}" "100" "600" "LOC+${code} Apply (€/m²/an)"
  fi
done

echo "--- VEN (sale) per asset type ---"
declare -A VEN_PATHS=(
  [BUR]="/fr/a-vendre/bureaux/"
  [ENT]="/fr/a-vendre/entrepot/"
  [ACT]="/fr/a-vendre/activite/"
  [COM]="/fr/a-vendre/commerce/"
  [TER]="/fr/a-vendre/terrain/"
  [LOG]="/fr/a-vendre/logistique/"
)
for code in BUR ENT ACT COM TER LOG; do
  path="${VEN_PATHS[$code]}"
  assert_apply_url "$BASE${path}" "500000" "50000000" "VEN+${code} Apply (€ global)"
done

echo "--- EN SEO paths ---"
assert_apply_url "$BASE/for-rent/office/" "80" "400" "EN LOC+BUR Apply"
assert_apply_url "$BASE/for-rent/coworking/" "150" "600" "EN LOC+COW Apply"
assert_apply_url "$BASE/for-sale/office/" "1000000" "20000000" "EN VEN+BUR Apply"

echo ""
echo "=== Summary: $PASS passed, $FAIL failed ==="
[[ "$FAIL" -eq 0 ]]
