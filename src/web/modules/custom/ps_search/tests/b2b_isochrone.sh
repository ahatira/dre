#!/usr/bin/env bash
# Phase 3 — isochrone API and bbox filtering for distance zone.
set -euo pipefail

BASE="${BASE_URL:-http://localhost:8080}"
PASS=0
FAIL=0

pass() { echo "  PASS: $1"; PASS=$((PASS + 1)); }
fail() { echo "  FAIL: $1"; FAIL=$((FAIL + 1)); }

echo "=== PS Search isochrone tests ($BASE) ==="

ISO=$(curl -sL -m 60 "$BASE/ps-search/isochrone?lat=48.8566&lng=2.3522&transport=walking&minutes=5" 2>/dev/null || echo "")
if [[ -n "$ISO" ]] && grep -q '"map_bounds"' <<< "$ISO" && grep -q '"polygon"' <<< "$ISO"; then
  pass "Isochrone API returns map_bounds and polygon"
else
  fail "Isochrone API payload incomplete"
fi

if grep -q '"provider":"approximation"' <<< "$(echo "$ISO" | tr -d '[:space:]')" \
  || grep -q '"provider": "approximation"' <<< "$ISO" \
  || grep -q '"fallback":true' <<< "$(echo "$ISO" | tr -d '[:space:]')" \
  || grep -q '"fallback": true' <<< "$ISO"; then
  pass "Isochrone provider is approximation or fallback"
else
  fail "Isochrone provider missing (expected approximation or fallback)"
fi

BOUNDS=$(echo "$ISO" | sed -n 's/.*"map_bounds":"\([^"]*\)".*/\1/p' | head -1)
if [[ -n "$BOUNDS" ]]; then
  ZONE=$(curl -sL -m 60 "$BASE/ps-search/markers?map_bounds=${BOUNDS}" 2>/dev/null | sed -n 's/.*"zone_count":\([0-9]*\).*/\1/p' | head -1)
  GLOBAL=$(curl -sL -m 60 "$BASE/ps-search/count" 2>/dev/null | sed -n 's/.*"count":\([0-9]*\).*/\1/p' | head -1)
  if [[ -n "$ZONE" && -n "$GLOBAL" && "$ZONE" -le "$GLOBAL" ]]; then
    pass "Isochrone map_bounds filters markers (zone=$ZONE global=$GLOBAL)"
  else
    fail "Isochrone map_bounds filtering (zone=$ZONE global=$GLOBAL)"
  fi
else
  fail "Could not parse map_bounds from isochrone response"
fi

INVALID=$(curl -sL -m 60 -o /dev/null -w '%{http_code}' "$BASE/ps-search/isochrone?lat=999&lng=2.3522" 2>/dev/null || echo "000")
if [[ "$INVALID" == "400" ]]; then
  pass "Invalid isochrone center rejected (HTTP 400)"
else
  fail "Invalid isochrone center (HTTP $INVALID)"
fi

echo ""
echo "=== Results: $PASS passed, $FAIL failed ==="
[[ "$FAIL" -eq 0 ]]
