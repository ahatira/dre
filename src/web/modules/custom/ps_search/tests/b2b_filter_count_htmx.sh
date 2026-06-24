#!/usr/bin/env bash
# Phase 5A POC — HTMX count label fragment for filter bar.
set -euo pipefail

# shellcheck source=/dev/null
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/../../../../.." && pwd)/scripts/e2e/common.sh"

PASS=0
FAIL=0

pass() { echo "  PASS: $1"; PASS=$((PASS + 1)); }
fail() { echo "  FAIL: $1"; FAIL=$((FAIL + 1)); }

echo "=== PS Search filters HTMX count POC ($BASE) ==="

JSON_COUNT=$(curl -sL -m 60 "$BASE/api/ps/count?asset_type=BUR&operation_type=LOC" | python3 -c "import sys,json; print(json.load(sys.stdin).get('count', -1))" 2>/dev/null || echo "-1")
HTMX_BODY=$(curl -sL -m 60 -H "HX-Request: true" "$BASE/api/ps/htmx/count-label?asset_type=BUR&operation_type=LOC" 2>/dev/null || echo "")
HTMX_COUNT=$(python3 - <<PY
import re
import sys
body = """${HTMX_BODY}"""
match = re.search(r"<body>(.*?)</body>", body, re.S | re.I)
if not match:
    print("-1")
    sys.exit(0)
text = re.sub(r"<[^>]+>", "", match.group(1)).strip()
print(text if text.isdigit() else "-1")
PY
)

if [[ "$JSON_COUNT" =~ ^[0-9]+$ && "$JSON_COUNT" -ge 0 ]]; then
  pass "JSON count endpoint returns $JSON_COUNT"
else
  fail "JSON count endpoint (got $JSON_COUNT)"
fi

if [[ "$HTMX_COUNT" =~ ^[0-9]+$ && "$HTMX_COUNT" -ge 0 ]]; then
  pass "HTMX count fragment returns numeric body ($HTMX_COUNT)"
  if [[ "$HTMX_COUNT" == "$JSON_COUNT" ]]; then
    pass "HTMX count matches JSON count ($JSON_COUNT)"
  else
    fail "HTMX vs JSON mismatch (htmx=$HTMX_COUNT json=$JSON_COUNT)"
  fi
else
  fail "HTMX count fragment not numeric (parsed=$HTMX_COUNT)"
fi

APPLY_HEADERS=$(curl -sI -m 60 -H "HX-Request: true" \
  "$BASE/api/ps/htmx/apply-type?asset_type=BUR&operation_type=LOC" 2>/dev/null || echo "")
if echo "$APPLY_HEADERS" | grep -qi 'trigger-after-settle.*ps-search-filter-htmx-apply'; then
  pass "HTMX apply-type returns ps-search-filter-htmx-apply trigger"
else
  fail "HTMX apply-type missing HX-Trigger-After-Settle (ps-search-filter-htmx-apply)"
fi

LOC_JSON_COUNT=$(curl -sL -m 60 "$BASE/api/ps/count?locality=Paris" | python3 -c "import sys,json; print(json.load(sys.stdin).get('count', -1))" 2>/dev/null || echo "-1")
LOC_HTMX_BODY=$(curl -sL -m 60 -H "HX-Request: true" "$BASE/api/ps/htmx/count-label?locality=Paris" 2>/dev/null || echo "")
LOC_HTMX_COUNT=$(python3 - <<PY
import re
import sys
body = """${LOC_HTMX_BODY}"""
match = re.search(r"<body>(.*?)</body>", body, re.S | re.I)
if not match:
    print("-1")
    sys.exit(0)
text = re.sub(r"<[^>]+>", "", match.group(1)).strip()
print(text if text.isdigit() else "-1")
PY
)
if [[ "$LOC_HTMX_COUNT" =~ ^[0-9]+$ && "$LOC_HTMX_COUNT" -ge 0 && "$LOC_HTMX_COUNT" == "$LOC_JSON_COUNT" ]]; then
  pass "HTMX location count matches JSON (locality=Paris, count=$LOC_HTMX_COUNT)"
else
  fail "HTMX location count mismatch (htmx=$LOC_HTMX_COUNT json=$LOC_JSON_COUNT)"
fi

LOC_APPLY_HEADERS=$(curl -sI -m 60 -H "HX-Request: true" \
  "$BASE/api/ps/htmx/apply-location?locality=Paris" 2>/dev/null || echo "")
if echo "$LOC_APPLY_HEADERS" | grep -qi 'trigger-after-settle.*ps-search-filter-htmx-apply'; then
  pass "HTMX apply-location returns ps-search-filter-htmx-apply trigger"
else
  fail "HTMX apply-location missing apply trigger header"
fi

SURF_JSON_COUNT=$(curl -sL -m 60 "$BASE/api/ps/count?surface_min=100" | python3 -c "import sys,json; print(json.load(sys.stdin).get('count', -1))" 2>/dev/null || echo "-1")
SURF_HTMX_BODY=$(curl -sL -m 60 -H "HX-Request: true" "$BASE/api/ps/htmx/count-label?surface_min=100" 2>/dev/null || echo "")
SURF_HTMX_COUNT=$(python3 - <<PY
import re
import sys
body = """${SURF_HTMX_BODY}"""
match = re.search(r"<body>(.*?)</body>", body, re.S | re.I)
if not match:
    print("-1")
    sys.exit(0)
text = re.sub(r"<[^>]+>", "", match.group(1)).strip()
print(text if text.isdigit() else "-1")
PY
)
if [[ "$SURF_HTMX_COUNT" =~ ^[0-9]+$ && "$SURF_HTMX_COUNT" -ge 0 && "$SURF_HTMX_COUNT" == "$SURF_JSON_COUNT" ]]; then
  pass "HTMX surface count matches JSON (surface_min=100, count=$SURF_HTMX_COUNT)"
else
  fail "HTMX surface count mismatch (htmx=$SURF_HTMX_COUNT json=$SURF_JSON_COUNT)"
fi

echo ""
echo "--- SEO page + flat surface_min (Views exposed filter regression) ---"
SEO_SURF_CODE=$(curl -sL -m 60 -o /dev/null -w '%{http_code}' \
  "$BASE/for-rent/office/paris-75/?surface_min=100" 2>/dev/null || echo "000")
if [[ "$SEO_SURF_CODE" == "200" ]]; then
  pass "SEO path + flat surface_min loads (HTTP 200)"
  SEO_SURF_HTML=$(curl -sL -m 60 "$BASE/for-rent/office/paris-75/?surface_min=100" 2>/dev/null || echo "")
  if [[ "$SEO_SURF_HTML" == *'"globalCount"'* ]]; then
    pass "SEO path + flat surface_min has globalCount"
  else
    fail "SEO path + flat surface_min missing globalCount"
  fi
else
  fail "SEO path + flat surface_min (HTTP $SEO_SURF_CODE)"
fi

SURF_APPLY_HEADERS=$(curl -sI -m 60 -H "HX-Request: true" \
  "$BASE/api/ps/htmx/apply-range/surface?surface_min=100" 2>/dev/null || echo "")
if echo "$SURF_APPLY_HEADERS" | grep -qi 'trigger-after-settle.*ps-search-filter-htmx-apply'; then
  pass "HTMX apply-range/surface returns ps-search-filter-htmx-apply trigger"
else
  fail "HTMX apply-range/surface missing apply trigger header"
fi

MOBILE_APPLY_HEADERS=$(curl -sI -m 60 -H "HX-Request: true" \
  "$BASE/api/ps/htmx/apply-mobile" 2>/dev/null || echo "")
if echo "$MOBILE_APPLY_HEADERS" | grep -qi 'trigger-after-settle.*ps-search-filter-htmx-apply'; then
  pass "HTMX apply-mobile returns ps-search-filter-htmx-apply trigger"
else
  fail "HTMX apply-mobile missing apply trigger header"
fi

HEADER_BODY=$(curl -sL -m 60 -H "HX-Request: true" \
  "$BASE/api/ps/htmx/results-header?surface_min=100" 2>/dev/null || echo "")
if echo "$HEADER_BODY" | grep -q 'js-ps-results-header-title'; then
  pass "HTMX results-header returns title markup"
else
  fail "HTMX results-header missing title markup"
fi
if echo "$HEADER_BODY" | grep -q 'js-ps-results-header-count'; then
  pass "HTMX results-header returns count markup"
else
  fail "HTMX results-header missing count markup"
fi
HEADER_COUNT=$(python3 - <<PY
import re
import sys
body = """${HEADER_BODY}"""
match = re.search(r'js-ps-results-header-count[^>]*>([^<]+)<', body)
raw = match.group(1).strip() if match else ""
digits = re.sub(r"\\D+", "", raw)
print(digits if digits else "")
PY
)
if [[ -n "$HEADER_COUNT" && "$HEADER_COUNT" == "$SURF_JSON_COUNT" ]]; then
  pass "HTMX results-header count matches JSON ($HEADER_COUNT)"
else
  fail "HTMX results-header count mismatch (header=$HEADER_COUNT json=$SURF_JSON_COUNT)"
fi

if curl -sL -m 60 "$BASE/find-property" -o /tmp/ps-find-property.html 2>/dev/null \
  && grep -q 'ps-search-results-header' /tmp/ps-find-property.html; then
  pass "Results header HTMX target present on find-property"
else
  fail "Results header HTMX target missing on find-property"
fi

echo ""
echo "=== Summary: $PASS passed, $FAIL failed ==="
[[ "$FAIL" -eq 0 ]]
