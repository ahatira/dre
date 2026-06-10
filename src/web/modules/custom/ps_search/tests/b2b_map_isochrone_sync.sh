#!/usr/bin/env bash
# Phase 4.3 — marker ↔ isochrone center sync (API-level regression).
set -euo pipefail

BASE="${BASE_URL:-http://localhost:8080}"
PASS=0
FAIL=0

pass() { echo "  PASS: $1"; PASS=$((PASS + 1)); }
fail() { echo "  FAIL: $1"; FAIL=$((FAIL + 1)); }

echo "=== PS Search map/isochrone sync tests ($BASE) ==="

MARKERS_JSON=$(curl -sL -m 60 "$BASE/ps-search/markers" 2>/dev/null || echo "")
if [[ -z "$MARKERS_JSON" ]]; then
  fail "Markers API unreachable"
  echo ""
  echo "=== Results: $PASS passed, $FAIL failed ==="
  exit 1
fi

read -r LAT LNG NID <<< "$(MARKERS_JSON="$MARKERS_JSON" python3 - <<'PY'
import json, os, sys
raw = os.environ.get("MARKERS_JSON", "")
try:
    data = json.loads(raw)
except json.JSONDecodeError:
    print("0 0 0")
    sys.exit(0)
markers = data.get("markers") or []
if not markers:
    print("0 0 0")
    sys.exit(0)
m = markers[0]
print(f"{m.get('lat', 0)} {m.get('lng', 0)} {m.get('nid', 0)}")
PY
)"

if [[ "$LAT" == "0" && "$LNG" == "0" ]]; then
  fail "No markers available for sync test"
else
  pass "Markers API returned at least one point (nid=$NID)"
fi

ISO=$(curl -sL -m 60 "$BASE/ps-search/isochrone?lat=${LAT}&lng=${LNG}&transport=walking&minutes=5" 2>/dev/null || echo "")
if [[ -z "$ISO" ]]; then
  fail "Isochrone API unreachable"
else
  pass "Isochrone API responds for marker coordinates"
fi

CENTER_OK=$(LAT="$LAT" LNG="$LNG" ISO="$ISO" python3 - <<'PY'
import json, os
lat = float(os.environ["LAT"])
lng = float(os.environ["LNG"])
iso = json.loads(os.environ["ISO"])
center = iso.get("center") or {}
clat = float(center.get("lat", 0))
clng = float(center.get("lng", 0))
print("yes" if abs(clat - lat) < 0.0001 and abs(clng - lng) < 0.0001 else "no")
PY
)

if [[ "$CENTER_OK" == "yes" ]]; then
  pass "Isochrone center matches marker coordinates"
else
  fail "Isochrone center does not match marker coordinates"
fi

BOUNDS=$(echo "$ISO" | sed -n 's/.*"map_bounds":"\([^"]*\)".*/\1/p' | head -1)
if [[ -n "$BOUNDS" ]]; then
  IN_BOUNDS=$(LAT="$LAT" LNG="$LNG" BOUNDS="$BOUNDS" python3 - <<'PY'
import os
lat = float(os.environ["LAT"])
lng = float(os.environ["LNG"])
parts = [float(p) for p in os.environ["BOUNDS"].split(",")]
if len(parts) != 4:
    print("no")
    raise SystemExit
sw_lat, sw_lng, ne_lat, ne_lng = parts
print("yes" if sw_lat <= lat <= ne_lat and sw_lng <= lng <= ne_lng else "no")
PY
)
  if [[ "$IN_BOUNDS" == "yes" ]]; then
    pass "Marker center lies inside isochrone map_bounds"
  else
    fail "Marker center outside isochrone map_bounds"
  fi
else
  fail "Could not parse map_bounds from isochrone response"
fi

PARIS_ISO=$(curl -sL -m 60 "$BASE/ps-search/isochrone?lat=48.8566&lng=2.3522&transport=walking&minutes=5" 2>/dev/null || echo "")
PARIS_BOUNDS=$(echo "$PARIS_ISO" | sed -n 's/.*"map_bounds":"\([^"]*\)".*/\1/p' | head -1)
if [[ -n "$BOUNDS" && -n "$PARIS_BOUNDS" && "$BOUNDS" != "$PARIS_BOUNDS" ]]; then
  pass "Marker-based isochrone bounds differ from Paris reference"
elif [[ -n "$BOUNDS" && -n "$PARIS_BOUNDS" ]]; then
  fail "Marker and Paris isochrone bounds unexpectedly identical"
else
  fail "Could not compare marker vs Paris isochrone bounds"
fi

echo ""
echo "=== Results: $PASS passed, $FAIL failed ==="
[[ "$FAIL" -eq 0 ]]
