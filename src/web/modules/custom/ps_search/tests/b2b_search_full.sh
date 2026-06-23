#!/usr/bin/env bash
# B2B full suite — PS Search (locality SEO, filters, HTMX count, SEO URLs).
set -euo pipefail

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
  "b2b_locality_seo.sh:Locality / region SEO + APIs" \
  "b2b_more_filters.sh:More filters" \
  "b2b_filter_count_htmx.sh:HTMX filter count" \
  "e2e_seo_urls.sh:SEO URLs (EN/FR, redirects, canonical)"; do
  script="${entry%%:*}"
  name="${entry#*:}"
  if ! run_suite "$SCRIPT_DIR/$script" "$name"; then
    FAIL_SUITES=$((FAIL_SUITES + 1))
  fi
done

echo ""
echo "################################################################"
echo "# PS Search B2B FULL SUITE SUMMARY"
echo "################################################################"
if [[ "$FAIL_SUITES" -eq 0 ]]; then
  echo "All PS Search B2B suites passed."
  exit 0
fi

echo "$FAIL_SUITES suite(s) failed."
exit 1
