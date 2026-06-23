#!/usr/bin/env bash
# B2B — More filters per feature widget type (count API smoke).
set -euo pipefail

# shellcheck source=/dev/null
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/../../../../.." && pwd)/scripts/e2e/common.sh"

PASS=0
FAIL=0
SKIP=0

pass() { echo "  PASS: $1"; PASS=$((PASS + 1)); }
fail() { echo "  FAIL: $1"; FAIL=$((FAIL + 1)); }
skip() { echo "  SKIP: $1"; SKIP=$((SKIP + 1)); }

count_api() {
  local qs="$1"
  local url="${BASE}/api/ps/count"
  if [[ -n "$qs" ]]; then
    url="${url}?${qs}"
  fi
  curl -s -m 60 "$url" 2>/dev/null | python3 -c "import json,sys; print(json.load(sys.stdin).get('count', -1))" 2>/dev/null || echo "-1"
}

assert_count_valid() {
  local qs="$1" label="$2"
  local count
  count=$(count_api "$qs")
  if [[ "$count" =~ ^[0-9]+$ && "$count" -ge 0 ]]; then
    pass "$label (count=$count)"
  else
    fail "$label (count=$count)"
  fi
}

echo "=== PS Search B2B — More filters feature types ($BASE) ==="

# Discover exposed feature filters via Drush (param + widget).
FILTER_LINES=$(ps_e2e_drush php:eval "
\$registry = \Drupal::service('ps_search.feature_filter_registry');
\$filters = \$registry->getExposedFilters('BUR', FALSE);
foreach (\$filters as \$id => \$filter) {
  \$widget = \$filter['widget'] ?? '';
  \$param = \$filter['param'] ?? '';
  if (\$param === '') { continue; }
  print \$widget . '|' . \$param . '|' . \$id . PHP_EOL;
}
" 2>/dev/null || echo "")

if [[ -z "$FILTER_LINES" ]]; then
  skip "No exposed feature filters found for BUR"
else
  echo "--- Per-widget count API smoke (asset BUR) ---"
  while IFS= read -r line; do
    [[ -z "$line" ]] && continue
    widget="${line%%|*}"
    rest="${line#*|}"
    param="${rest%%|*}"
    def_id="${rest#*|}"
    case "$widget" in
      checkbox)
        assert_count_valid "asset_type=BUR&${param}=1" "checkbox $def_id ($param)"
        ;;
      yes_no)
        assert_count_valid "asset_type=BUR&${param}=1" "yes_no $def_id ($param=1)"
        assert_count_valid "asset_type=BUR&${param}=0" "yes_no $def_id ($param=0)"
        ;;
      tags)
        skip "tags $def_id (needs known option value)"
        ;;
      range)
        assert_count_valid "asset_type=BUR&${param}_min=1" "range $def_id (${param}_min)"
        ;;
      text|select)
        skip "text/select $def_id (needs known value)"
        ;;
      date)
        skip "date $def_id (needs known date)"
        ;;
      *)
        skip "widget $widget for $def_id"
        ;;
    esac
  done <<< "$FILTER_LINES"
fi

echo ""
echo "--- Core criteria filters ---"
assert_count_valid "asset_type=BUR&nearby_transport=metro" "nearby_transport contains metro"
assert_count_valid "asset_type=BUR&has_immersive_tour=1" "has_immersive_tour"
assert_count_valid "asset_type=BUR&has_video=1" "has_video"

echo ""
echo "=== Summary: $PASS passed, $FAIL failed, $SKIP skipped ==="
if [[ "$FAIL" -gt 0 ]]; then
  exit 1
fi
exit 0
