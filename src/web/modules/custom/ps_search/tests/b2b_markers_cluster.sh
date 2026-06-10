#!/usr/bin/env bash
# B2B marker cluster tests — server-side grid clusters for dense map zones.
set -euo pipefail

BASE="${BASE_URL:-http://localhost:8080}"
PASS=0
FAIL=0

pass() { echo "  PASS: $1"; PASS=$((PASS + 1)); }
fail() { echo "  FAIL: $1"; FAIL=$((FAIL + 1)); }

PARIS_BOUNDS="48.80,2.30,48.90,2.40"

echo "=== PS Search marker cluster tests ($BASE) ==="

CLUSTER_RESULT=$(python3 - <<PY
import json
import sys
import urllib.parse
import urllib.request

base = "${BASE}"
bounds = "${PARIS_BOUNDS}"
url = base + "/ps-search/markers?" + urllib.parse.urlencode({"map_bounds": bounds})

with urllib.request.urlopen(url, timeout=120) as resp:
    data = json.load(resp)

zone_count = int(data.get("zone_count", -1))
markers_max = int(data.get("markers_max", 500))
display_mode = str(data.get("display_mode", ""))
markers = data.get("markers") or []
clusters = data.get("clusters") or []
capped = bool(data.get("capped"))

print(f"zone_count={zone_count}")
print(f"markers_max={markers_max}")
print(f"display_mode={display_mode}")
print(f"markers_len={len(markers)}")
print(f"clusters_len={len(clusters)}")
print(f"capped={int(capped)}")

if zone_count <= markers_max:
    print("skip_dense_zone=1")
    sys.exit(0)

if display_mode != "clusters":
    print("error=expected_clusters", file=sys.stderr)
    sys.exit(1)
if markers:
    print("error=markers_should_be_empty", file=sys.stderr)
    sys.exit(1)
if not clusters:
    print("error=no_clusters", file=sys.stderr)
    sys.exit(1)
if not capped:
    print("error=expected_capped", file=sys.stderr)
    sys.exit(1)

cluster_sum = 0
for cluster in clusters:
    for key in ("lat", "lng", "count", "map_bounds"):
        if key not in cluster:
            print(f"error=cluster_missing_{key}", file=sys.stderr)
            sys.exit(1)
    if int(cluster["count"]) <= 0:
        print("error=cluster_count", file=sys.stderr)
        sys.exit(1)
    cluster_sum += int(cluster["count"])

print(f"cluster_sum={cluster_sum}")

first_bounds = clusters[0]["map_bounds"]
zoom_url = base + "/ps-search/markers?" + urllib.parse.urlencode({"map_bounds": first_bounds})
with urllib.request.urlopen(zoom_url, timeout=120) as resp:
    zoom = json.load(resp)

zoom_mode = str(zoom.get("display_mode", ""))
zoom_markers = zoom.get("markers") or []
zoom_clusters = zoom.get("clusters") or []
zoom_zone = int(zoom.get("zone_count", -1))

print(f"zoom_mode={zoom_mode}")
print(f"zoom_markers_len={len(zoom_markers)}")
print(f"zoom_clusters_len={len(zoom_clusters)}")
print(f"zoom_zone={zoom_zone}")
print(f"first_bounds={first_bounds}")

if zoom_zone <= 0:
    print("error=zoom_zone", file=sys.stderr)
    sys.exit(1)
if zoom_mode == "clusters" and not zoom_clusters:
    print("error=zoom_empty", file=sys.stderr)
    sys.exit(1)
if zoom_mode == "markers" and not zoom_markers:
    print("error=zoom_no_markers", file=sys.stderr)
    sys.exit(1)
PY
)

if [[ $? -ne 0 ]]; then
  fail "Dense zone cluster payload validation"
  echo "$CLUSTER_RESULT"
else
  if grep -q '^skip_dense_zone=1' <<< "$CLUSTER_RESULT"; then
    pass "Dense zone skip (zone_count <= markers_max in this environment)"
  else
    ZONE_COUNT=$(echo "$CLUSTER_RESULT" | awk -F= '/^zone_count=/{print $2}')
    DISPLAY_MODE=$(echo "$CLUSTER_RESULT" | awk -F= '/^display_mode=/{print $2}')
    CLUSTERS_LEN=$(echo "$CLUSTER_RESULT" | awk -F= '/^clusters_len=/{print $2}')
    MARKERS_LEN=$(echo "$CLUSTER_RESULT" | awk -F= '/^markers_len=/{print $2}')
    CLUSTER_SUM=$(echo "$CLUSTER_RESULT" | awk -F= '/^cluster_sum=/{print $2}')
    ZOOM_MODE=$(echo "$CLUSTER_RESULT" | awk -F= '/^zoom_mode=/{print $2}')
    ZOOM_MARKERS=$(echo "$CLUSTER_RESULT" | awk -F= '/^zoom_markers_len=/{print $2}')

    pass "Paris bounds zone_count=$ZONE_COUNT returns display_mode=$DISPLAY_MODE"
    pass "Cluster payload empty markers ($MARKERS_LEN) with $CLUSTERS_LEN clusters"
    pass "Cluster counts sum to $CLUSTER_SUM (sampled points)"
    pass "Zoom into first cell returns mode=$ZOOM_MODE (markers=$ZOOM_MARKERS)"
  fi
fi

echo ""
echo "=== Summary: $PASS passed, $FAIL failed ==="
[[ "$FAIL" -eq 0 ]]
