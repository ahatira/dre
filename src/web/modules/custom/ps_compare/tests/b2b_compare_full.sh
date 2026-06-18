#!/usr/bin/env bash
# B2B full suite — Compare feature (search, page, homepage, authenticated).
set -euo pipefail

# shellcheck source=/dev/null
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/../../../../.." && pwd)/scripts/e2e/common.sh"

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
TOTAL_PASS=0
TOTAL_FAIL=0

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

FAIL_SUITES=0

for entry in \
  "b2b_compare_search.sh:Search page" \
  "b2b_compare_homepage.sh:Homepage offer cards" \
  "b2b_compare_page.sh:Compare table page" \
  "b2b_compare_share.sh:Share offcanvas + Mailpit" \
  "b2b_compare_undo.sh:Undo removal toggle" \
  "b2b_compare_email.sh:Share email Mailpit" \
  "b2b_compare_authenticated.sh:Authenticated user"; do
  script="${entry%%:*}"
  name="${entry#*:}"
  if ! run_suite "$SCRIPT_DIR/$script" "$name"; then
    FAIL_SUITES=$((FAIL_SUITES + 1))
  fi
done

echo ""
echo "################################################################"
echo "# FULL SUITE SUMMARY"
echo "################################################################"
if [[ "$FAIL_SUITES" -eq 0 ]]; then
  echo "All compare B2B suites passed."
  exit 0
fi

echo "$FAIL_SUITES suite(s) failed."
exit 1
