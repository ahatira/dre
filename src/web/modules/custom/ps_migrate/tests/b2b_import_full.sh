#!/usr/bin/env bash
# B2B full suite — CRM import pipeline (BO, Drush, failure alerts).
set -euo pipefail

export PS_E2E_COUNTRY="${PS_E2E_COUNTRY:-${COUNTRY:-fr}}"

# shellcheck source=/dev/null
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/../../../../.." && pwd)/scripts/e2e/common.sh"

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
FAIL_SUITES=0

run_suite() {
  local script="$1"
  local name="$2"
  echo ""
  echo "################################################################"
  echo "# $name"
  echo "################################################################"
  if bash "$script"; then
    return 0
  fi
  return 1
}

for entry in \
  "b2b_import_drush.sh:Drush pipeline + unified source" \
  "b2b_import_bo.sh:Import BO pages" \
  "e2e_import_alert.sh:Failure email alerts (Mailpit)"; do
  script="${entry%%:*}"
  name="${entry#*:}"
  if ! run_suite "$SCRIPT_DIR/$script" "$name"; then
    FAIL_SUITES=$((FAIL_SUITES + 1))
  fi
done

echo ""
echo "################################################################"
echo "# PS Migrate B2B FULL SUITE SUMMARY (@ps.${PS_E2E_COUNTRY})"
echo "################################################################"
if [[ "$FAIL_SUITES" -eq 0 ]]; then
  echo "All CRM import B2B suites passed."
  exit 0
fi

echo "$FAIL_SUITES suite(s) failed."
exit 1
