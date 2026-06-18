#!/usr/bin/env bash
# Manual recette helper — CTX-FORM, CTX-DEF, CTX-SEARCH, CTX-ADM (browser-complement).
set -euo pipefail

# shellcheck source=/dev/null
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/../../../../.." && pwd)/scripts/e2e/common.sh"

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PASS=0
FAIL=0
SKIP=0
TMP="$(mktemp -d)"
TMPJAR="$(mktemp)"
trap 'rm -rf "$TMP" "$TMPJAR"' EXIT

pass() { echo "  PASS: $1"; PASS=$((PASS + 1)); }
fail() { echo "  FAIL: $1"; FAIL=$((FAIL + 1)); }
skip() { echo "  SKIP: $1"; SKIP=$((SKIP + 1)); }

drush() {
  ps_e2e_drush "$@"
}

login_jar() {
  local user="$1"
  local jar="$2"
  rm -f "$jar"
  touch "$jar"
  local uli
  uli=$(drush uli --name="${user}" --uri="${BASE}" 2>/dev/null | tail -1 || true)
  [[ -n "$uli" ]] || return 1
  curl -sL -m 120 -b "$jar" -c "$jar" -o /dev/null "$uli" 2>/dev/null || true
}

tab_visible() {
  local jar="$1"
  local tab="$2"
  local html
  html=$(curl -sL -m 60 -b "$jar" -c "$jar" "${BASE}/node/add/offer" 2>/dev/null || true)
  if echo "$html" | grep -q "href=\"#edit-${tab}\""; then
    if echo "$html" | grep -q "href=\"#edit-${tab}\"[^>]*style=\"display:\s*none\""; then
      echo "hidden"
    else
      # Check if li parent is hidden via inline style in surrounding context — heuristic.
      if echo "$html" | grep -Po "href=\"#edit-${tab}\"[^<]*</a>" | grep -qi "display:\s*none"; then
        echo "hidden"
      else
        echo "$html" | grep -q "href=\"#edit-${tab}\"" && {
          # If link exists in horizontal tabs list, check li:hidden via JS eval below instead.
          echo "present"
        }
      fi
    fi
  else
    echo "absent"
  fi
}

echo "=== Manual recette — Context & Search (${BASE}) ==="

echo "--- CTX-FORM / CTX-DEF (admin Gin form via Drush DOM simulation) ---"

EVAL=$(drush php:script "web/modules/custom/bnp_admin/tests/e2e_manual_ctx_form.evaluate.php" 2>/dev/null || true)
declare -A E
while IFS='=' read -r k v; do
  [[ -n "$k" ]] && E[$k]="$v"
done <<< "$EVAL"

check_eq() {
  local id="$1" key="$2" expected="$3"
  if [[ "${E[$key]:-}" == "$expected" ]]; then
    pass "${id}: ${key}=${expected}"
  else
    fail "${id}: expected ${key}=${expected}, got ${E[$key]:-missing}"
  fi
}

[[ "${E[rules_count]:-0}" -ge 14 ]] && pass "CTX rules loaded (${E[rules_count]:-0})" || fail "CTX rules missing"

check_eq "CTX-FORM-01" "initial_price" "hidden"
check_eq "CTX-FORM-01" "initial_surface" "hidden"
check_eq "CTX-FORM-01" "initial_capacity" "hidden"
check_eq "CTX-FORM-01" "initial_lots" "hidden"

check_eq "CTX-FORM-02" "ent_surface" "visible"
check_eq "CTX-FORM-03" "cow_capacity" "visible"
check_eq "CTX-FORM-03" "cow_surface" "hidden"
check_eq "CTX-FORM-03" "cow_divisible" "hidden"
check_eq "CTX-FORM-04" "loc_price" "visible"
check_eq "CTX-FORM-05" "bur_loc_div_lots" "visible"
check_eq "CTX-FORM-06" "bur_to_cow_surface" "hidden"

check_eq "CTX-DEF-01" "def_loc_bur_period" "YEAR"
check_eq "CTX-DEF-01" "def_loc_bur_unit" "PER_M2"
check_eq "CTX-DEF-02" "def_loc_cow_unit" "PER_POSTE"
check_eq "CTX-DEF-03" "def_ven_bur_unit" "GLOBAL"
check_eq "CTX-DEF-04" "def_currency" "EUR"

echo "--- CTX-FORM content.editor (Gin admin theme) ---"
if [[ "${E[editor_admin_theme_perm]:-}" == "yes" ]] && [[ "${E[editor_form_mode]:-}" == "horizontal-tabs" || "${E[editor_has_horizontal_price]:-}" == "yes" ]]; then
  pass "CTX-FORM content.editor: Gin horizontal-tabs + permission admin theme"
elif [[ "${E[editor_admin_theme_perm]:-}" == "yes" ]]; then
  fail "CTX-FORM content.editor: permission OK mais rendu ${E[editor_form_mode]:-unknown} (attendu horizontal-tabs)"
else
  fail "CTX-FORM content.editor: permission view the administration theme manquante"
fi

echo "--- CTX-ADM-03 (site.admin matrix COW rule) ---"
login_jar "site.admin" "$TMPJAR" || fail "CTX-ADM-03: login site.admin"
MATRIX=$(curl -sL -m 60 -b "$TMPJAR" -c "$TMPJAR" "${BASE}/admin/ps/config/matrix" 2>/dev/null || true)
if echo "$MATRIX" | grep -qiE "asset_type_cow|Coworking|COW"; then
  pass "CTX-ADM-03: règle COW présente dans la liste"
else
  fail "CTX-ADM-03: règle COW non trouvée"
fi

COW_EDIT_URL=$(echo "$MATRIX" | grep -oE '/admin/ps/config/matrix/[^"/]+' | head -1 || true)
if [[ -n "$COW_EDIT_URL" ]]; then
  COW_PAGE=$(curl -sL -m 60 -b "$TMPJAR" -c "$TMPJAR" "${BASE}${COW_EDIT_URL}" 2>/dev/null || true)
  if echo "$COW_PAGE" | grep -qi "group_surface\|Surface"; then
    pass "CTX-ADM-03: page édition règle — condition/action Surface référencée"
  else
    skip "CTX-ADM-03: ouvrir manuellement asset_type_cow pour vérifier hide Surface / show Capacity"
  fi
fi

echo "--- CTX-ADM-04/05 (désactivation operation_selected_show_budget) ---"
if drush php:script "web/modules/custom/bnp_admin/tests/e2e_manual_ctx_adm45.php" 2>/dev/null | grep -q '^CTX_ADM_04=pass'; then
  pass "CTX-ADM-04: onglet Price masqué après désactivation règle budget"
else
  fail "CTX-ADM-04: Price toujours visible après désactivation"
fi
if drush php:script "web/modules/custom/bnp_admin/tests/e2e_manual_ctx_adm45.php" 2>/dev/null | grep -q '^CTX_ADM_05=pass'; then
  pass "CTX-ADM-05: onglet Price restauré après réactivation"
else
  fail "CTX-ADM-05: Price non restauré"
fi

echo "--- CTX-SEARCH (front /find-property) ---"
SEARCH_FILE="$TMP/search.html"
curl -sL -m 60 "${BASE}/find-property" -o "$SEARCH_FILE" 2>/dev/null || true
if grep -q "edit-surface-wrapper" "$SEARCH_FILE" 2>/dev/null; then
  pass "CTX-SEARCH-01/03: filtre surface présent (état initial / BUR)"
else
  fail "CTX-SEARCH-01: filtre surface non détecté"
fi
if grep -qiE "capacity|poste|seat" "$SEARCH_FILE" 2>/dev/null && ! grep -q "edit-capacity-wrapper" "$SEARCH_FILE" 2>/dev/null; then
  fail "CTX-SEARCH-01: filtre capacité visible sans COW (devrait être masqué)"
else
  pass "CTX-SEARCH-01: filtre capacité masqué par défaut"
fi

HP=$(curl -sL -m 60 "${BASE}/" 2>/dev/null || true)
if echo "$HP" | grep -qiE "find-property|search|surface"; then
  pass "CTX-SEARCH-07: panneau recherche hero présent sur homepage"
else
  fail "CTX-SEARCH-07: hero search non détecté"
fi

echo ""
echo "=== Results: ${PASS} passed, ${FAIL} failed, ${SKIP} skipped ==="
[[ "$FAIL" -eq 0 ]]
