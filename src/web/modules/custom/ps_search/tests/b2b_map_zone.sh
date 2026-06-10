#!/usr/bin/env bash
# B2B map zone tests — bounds API, dual counter, list/markers sync.
set -euo pipefail

BASE="${BASE_URL:-http://localhost:8080}"
PASS=0
FAIL=0
SKIP=0

pass() { echo "  PASS: $1"; PASS=$((PASS + 1)); }
fail() { echo "  FAIL: $1"; FAIL=$((FAIL + 1)); }
skip() { echo "  SKIP: $1"; SKIP=$((SKIP + 1)); }

status_code() {
  curl -sI -m 120 -o /dev/null -w '%{http_code}' "$1" 2>/dev/null || echo "000"
}

assert_status() {
  local url="$1" expected="$2" label="$3"
  local got
  got=$(status_code "$url")
  if [[ "$got" == "$expected" ]]; then
    pass "$label ($got)"
  else
    fail "$label (expected $expected, got $got) — $url"
  fi
}

fetch_json() {
  curl -s -m 120 "$1" 2>/dev/null || echo ""
}

echo "=== PS Search map zone tests ($BASE) ==="

echo "--- Admin config ---"
assert_status "$BASE/admin/ps/config/search/map-zone" "403" "Map zone settings form requires auth"

echo "--- JSON endpoints ---"
assert_status "$BASE/ps-search/markers" "200" "Markers API"
assert_status "$BASE/ps-search/count" "200" "Global count API"

METRICS=$(python3 - <<PY
import json
import sys
import urllib.request

base = "${BASE}"

def fetch(path):
    with urllib.request.urlopen(base + path, timeout=120) as resp:
        return json.load(resp)

try:
    api_global = int(fetch("/ps-search/count").get("count", -1))
    markers = fetch("/ps-search/markers")
    zone_count = int(markers.get("zone_count", -1))
    global_count = int(markers.get("global_count", -1))
    marker_list = markers.get("markers") or []
    marker_len = len(marker_list)
    print(f"api_global={api_global}")
    print(f"global={global_count}")
    print(f"zone={zone_count}")
    print(f"markers={marker_len}")
    if global_count < 0 or zone_count < 0 or api_global < 0:
        print("error=invalid_counts", file=sys.stderr)
        sys.exit(1)
    if global_count != api_global:
        print("error=global_mismatch", file=sys.stderr)
        sys.exit(1)
    if zone_count > global_count:
        print("error=zone_gt_global", file=sys.stderr)
        sys.exit(1)
    if marker_len > zone_count:
        print("error=markers_gt_zone", file=sys.stderr)
        sys.exit(1)
    for item in marker_list:
        if not all(k in item for k in ("nid", "lat", "lng", "label")):
            print("error=marker_shape", file=sys.stderr)
            sys.exit(1)
except Exception as exc:
    print(f"error={exc}", file=sys.stderr)
    sys.exit(1)
PY
)

if [[ $? -eq 0 ]]; then
  GLOBAL_COUNT=$(echo "$METRICS" | awk -F= '/^global=/{print $2}')
  ZONE_COUNT=$(echo "$METRICS" | awk -F= '/^zone=/{print $2}')
  MARKER_LEN=$(echo "$METRICS" | awk -F= '/^markers=/{print $2}')
  pass "zone_count ($ZONE_COUNT) <= global_count ($GLOBAL_COUNT)"
  pass "markers API global_count matches count API ($GLOBAL_COUNT)"
  pass "markers payload ($MARKER_LEN) <= zone_count"
  pass "marker objects expose nid/lat/lng/label"
else
  fail "markers/count JSON validation"
fi

echo "--- map_bounds filtering ---"
TIGHT_BOUNDS="48.80,2.30,48.90,2.40"
TIGHT_ZONE=$(python3 - <<PY
import json
import urllib.parse
import urllib.request

base = "${BASE}"
bounds = "${TIGHT_BOUNDS}"
url = base + "/ps-search/markers?" + urllib.parse.urlencode({"map_bounds": bounds})
with urllib.request.urlopen(url, timeout=120) as resp:
    data = json.load(resp)
print(int(data.get("zone_count", -1)))
PY
)

if [[ -n "$TIGHT_ZONE" && "$TIGHT_ZONE" -ge 0 ]]; then
  pass "Explicit Paris bounds return zone_count=$TIGHT_ZONE"
  if [[ -n "$ZONE_COUNT" && "$TIGHT_ZONE" != "$ZONE_COUNT" ]]; then
    pass "Explicit bounds change zone_count ($ZONE_COUNT -> $TIGHT_ZONE)"
  elif [[ -n "$ZONE_COUNT" ]]; then
    pass "Explicit bounds accepted (zone_count=$TIGHT_ZONE)"
  fi
else
  fail "Explicit map_bounds markers request"
fi

INVALID_ZONE=$(python3 - <<PY
import json
import urllib.parse
import urllib.request

base = "${BASE}"
url = base + "/ps-search/markers?" + urllib.parse.urlencode({"map_bounds": "1,2,3"})
with urllib.request.urlopen(url, timeout=120) as resp:
    data = json.load(resp)
print(int(data.get("zone_count", -1)))
PY
)

if [[ -n "$INVALID_ZONE" && -n "$ZONE_COUNT" && "$INVALID_ZONE" == "$ZONE_COUNT" ]]; then
  pass "Invalid map_bounds falls back to default zone ($INVALID_ZONE)"
else
  fail "Invalid map_bounds fallback (invalid=$INVALID_ZONE default=$ZONE_COUNT)"
fi

echo "--- Search page zone sync ---"
PAGE_BODY=$(curl -s -m 120 "$BASE/find-property" 2>/dev/null || true)
if [[ -n "$PAGE_BODY" ]]; then
  if grep -Fq 'js-ps-zone-hint' <<< "$PAGE_BODY"; then
    pass "Search page exposes zone hint element"
  else
    fail "Search page missing js-ps-zone-hint"
  fi
  if grep -Fq '"zoneCount"' <<< "$PAGE_BODY"; then
    pass "Search page exposes zoneCount in drupalSettings"
  else
    fail "Search page missing zoneCount setting"
  fi

  LIST_LOAD_ALL=$(grep -oE '"listLoadAll"\s*:\s*(true|false)' <<< "$PAGE_BODY" 2>/dev/null | head -1 | sed -E 's/.*:\s*(true|false)/\1/') || LIST_LOAD_ALL=""
  if [[ -n "$ZONE_COUNT" && "$ZONE_COUNT" -le 100 ]]; then
    if [[ "$LIST_LOAD_ALL" == "true" ]]; then
      pass "Search page enables listLoadAll for small zone ($ZONE_COUNT)"
    else
      fail "Small zone ($ZONE_COUNT) should expose listLoadAll=true"
    fi
    if grep -Fq 'pager--load-more' <<< "$PAGE_BODY"; then
      fail "Small zone page should not expose load more pager"
    else
      pass "Small zone page has no load more pager"
    fi
  elif [[ -n "$ZONE_COUNT" && "$ZONE_COUNT" -gt 100 ]]; then
    if [[ "$LIST_LOAD_ALL" == "false" ]]; then
      pass "Large zone ($ZONE_COUNT) disables listLoadAll (pager mode)"
    else
      fail "Large zone ($ZONE_COUNT) should expose listLoadAll=false"
    fi
    if grep -Fq 'pager--load-more' <<< "$PAGE_BODY"; then
      pass "Large zone page exposes load more pager"
    else
      fail "Large zone page should expose load more pager"
    fi
  fi

  CARD_COUNT=$(grep -o 'data-offer-id="[0-9]\+"' <<< "$PAGE_BODY" 2>/dev/null | wc -l | tr -d ' ') || CARD_COUNT=0
  if [[ -n "$ZONE_COUNT" && "$ZONE_COUNT" -le 100 && "$CARD_COUNT" == "$ZONE_COUNT" ]]; then
    pass "List cards ($CARD_COUNT) match zone_count ($ZONE_COUNT)"
  elif [[ -n "$ZONE_COUNT" && "$ZONE_COUNT" -gt 100 && "$CARD_COUNT" -gt 0 && "$CARD_COUNT" -le 40 ]]; then
    pass "Large zone list is paginated ($CARD_COUNT cards of $ZONE_COUNT, first page ≤40)"
  else
    fail "List cards ($CARD_COUNT) inconsistent with zone_count ($ZONE_COUNT)"
  fi
else
  fail "Search page body empty"
fi

BOUNDS_BODY=$(curl -s -m 120 "$BASE/find-property?map_bounds=${TIGHT_BOUNDS}" 2>/dev/null || true)
if [[ -n "$BOUNDS_BODY" ]]; then
  BOUNDS_CARDS=$(grep -o 'data-offer-id="[0-9]\+"' <<< "$BOUNDS_BODY" 2>/dev/null | wc -l | tr -d ' ') || BOUNDS_CARDS=0
  BOUNDS_ZONE_SETTING=$(grep -o '"zoneCount":[0-9]*' <<< "$BOUNDS_BODY" 2>/dev/null | head -1 | sed 's/"zoneCount"://') || BOUNDS_ZONE_SETTING=""
  if [[ -n "$TIGHT_ZONE" && -n "$BOUNDS_ZONE_SETTING" && "$BOUNDS_ZONE_SETTING" == "$TIGHT_ZONE" ]]; then
    pass "Bounded page zoneCount ($BOUNDS_ZONE_SETTING) matches API ($TIGHT_ZONE)"
  elif [[ -n "$TIGHT_ZONE" && "$TIGHT_ZONE" -le 100 && "$BOUNDS_CARDS" == "$TIGHT_ZONE" ]]; then
    pass "Bounded list cards ($BOUNDS_CARDS) match bounded zone_count ($TIGHT_ZONE)"
  else
    fail "Bounded zone sync (cards=$BOUNDS_CARDS settings=$BOUNDS_ZONE_SETTING api=$TIGHT_ZONE)"
  fi
else
  fail "Bounded search page body empty"
fi

echo ""
echo "=== Summary: $PASS passed, $FAIL failed, $SKIP skipped ==="
[[ "$FAIL" -eq 0 ]]
