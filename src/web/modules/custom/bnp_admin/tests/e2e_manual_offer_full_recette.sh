#!/usr/bin/env bash
# Manual recette §5.3 — OFF-01→12 données minimales + publication.
set -euo pipefail

PASS=0
FAIL=0

pass() { echo "  PASS: $1"; PASS=$((PASS + 1)); }
fail() { echo "  FAIL: $1"; FAIL=$((FAIL + 1)); }

drush() {
  docker exec -i ps_php sh -lc "cd /var/www/html && vendor/bin/drush $*"
}

echo "=== Manual recette §5.3 — OFF-01→12 full minimal + publication ==="

EVAL=$(drush php:script "web/modules/custom/bnp_admin/tests/e2e_manual_offer_full_recette.evaluate.php" 2>/dev/null || true)

while IFS= read -r line; do
  [[ -z "$line" ]] && continue
  key="${line%%=*}"
  value="${line#*=}"

  case "$key" in
    OFF-*_FULL)
      case_id="${key%_FULL}"
      if [[ "$value" == pass_pub,nid:* ]]; then
        nid="${value#*nid:}"
        pass "${case_id}: données minimales + publication OK (nid ${nid})"
      else
        fail "${case_id}: ${value}"
      fi
      ;;
    FULL=*)
      fail "Prérequis: ${value}"
      ;;
  esac
done <<< "$EVAL"

echo ""
echo "=== Results: ${PASS} passed, ${FAIL} failed ==="
[[ "$FAIL" -eq 0 ]]
