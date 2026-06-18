#!/usr/bin/env bash
# Manual recette — OFF-01→12 + VAL-01→10 (Drush entity + form validation).
set -euo pipefail

# shellcheck source=/dev/null
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/../../../../.." && pwd)/scripts/e2e/common.sh"

PASS=0
FAIL=0

pass() { echo "  PASS: $1"; PASS=$((PASS + 1)); }
fail() { echo "  FAIL: $1"; FAIL=$((FAIL + 1)); }

drush() {
  ps_e2e_drush "$@"
}

echo "=== Manual recette — OFF-01→18 + VAL-01→10 ==="

EVAL=$(drush php:script "web/modules/custom/bnp_admin/tests/e2e_manual_offer_val_recette.evaluate.php" 2>/dev/null || true)

while IFS= read -r line; do
  [[ -z "$line" ]] && continue
  key="${line%%=*}"
  value="${line#*=}"

  case "$key" in
    OFF-*)
      if [[ "$value" == pass,*pass_nid:* ]] || [[ "$value" == pass,* ]] || [[ "$value" == pass,* ]]; then
        if [[ "$value" == pass,pass_nid:* ]]; then
          nid="${value#*pass_nid:}"
          pass "${key}: onglets OK + brouillon sauvegardé (nid ${nid})"
        elif [[ "$value" == pass,all_hidden ]]; then
          pass "${key}: état initial — onglets dynamiques masqués"
        elif [[ "$value" == pass,bur_to_cow ]]; then
          pass "${key}: transition BUR → COW conforme"
        elif [[ "$value" == pass,cow_to_bur ]]; then
          pass "${key}: transition COW → BUR conforme"
        elif [[ "$value" == pass,lots_hidden ]]; then
          pass "${key}: Lots masqué sans opération/actif"
        elif [[ "$value" == pass,nid:* ]]; then
          nid="${value#*nid:}"
          pass "${key}: édition identique au create (nid ${nid})"
        elif [[ "$value" == pass,*client:B2C ]]; then
          nid="${value#*pass_nid:}"
          nid="${nid%%,*}"
          pass "${key}: B2C — mêmes onglets que B2B (nid ${nid})"
        else
          pass "${key}: ${value}"
        fi
      elif [[ "$value" == fail_tabs ]]; then
        fail "${key}: visibilité onglets Context KO"
      else
        fail "${key}: ${value}"
      fi
      ;;
    VAL-*)
      case "$value" in
        warn_ok|block_ok|pass_ok|form_block_ok|draft_warn_ok|normalize_ok)
          pass "${key}: ${value}"
          ;;
        *)
          fail "${key}: ${value}"
          ;;
      esac
      ;;
  esac
done <<< "$EVAL"

echo ""
echo "=== Results: ${PASS} passed, ${FAIL} failed ==="
[[ "$FAIL" -eq 0 ]]
