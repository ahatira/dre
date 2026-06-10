#!/usr/bin/env bash
# B2B sync audit — global count, zone count, list cards, markers API, HTMX header.
set -euo pipefail

BASE="${BASE_URL:-http://localhost:8080}"
PASS=0
FAIL=0

pass() { echo "  PASS: $1"; PASS=$((PASS + 1)); }
fail() { echo "  FAIL: $1"; FAIL=$((FAIL + 1)); }

python3 - <<PY
import json
import re
import sys
import urllib.parse
import urllib.request

BASE = "${BASE}"
PASS = 0
FAIL = 0

def ok(msg):
    global PASS
    PASS += 1
    print(f"  PASS: {msg}")

def bad(msg):
    global FAIL
    FAIL += 1
    print(f"  FAIL: {msg}")

def get_json(path):
    with urllib.request.urlopen(BASE + path, timeout=120) as resp:
        return json.load(resp)

def get_html(path):
    with urllib.request.urlopen(BASE + path, timeout=120) as resp:
        return resp.read().decode("utf-8", "replace")

def count_api(qs=""):
    return int(get_json(f"/ps-search/count?{qs}").get("count", -1))

def markers_api(qs=""):
    data = get_json(f"/ps-search/markers?{qs}")
    return {
        "global": int(data.get("global_count", -1)),
        "zone": int(data.get("zone_count", -1)),
        "markers": len(data.get("markers") or []),
        "clusters": len(data.get("clusters") or []),
    }

def page_metrics(path):
    html = get_html(path)
    cards = len(re.findall(r'data-offer-id="[0-9]+"', html))
    setz = re.search(r'"zoneCount"\s*:\s*(\d+)', html)
    setg = re.search(r'"globalCount"\s*:\s*(\d+)', html)
    hintg = re.search(r'data-global-count="(\d+)"', html)
    hintz = re.search(r'Showing\s+([\d\s]+)\s+in this area', html)
    return {
        "cards": cards,
        "zone_setting": int(setz.group(1)) if setz else None,
        "global_setting": int(setg.group(1)) if setg else None,
        "hint_global": int(hintg.group(1)) if hintg else None,
        "hint_zone": int(hintz.group(1).replace(" ", "")) if hintz else None,
    }

def htmx_header(qs):
    req = urllib.request.Request(
        BASE + f"/ps-search-filters/htmx/results-header?{qs}",
        headers={"HX-Request": "true"},
    )
    with urllib.request.urlopen(req, timeout=120) as resp:
        html = resp.read().decode("utf-8", "replace")
    hintg = re.search(r'data-global-count="(\d+)"', html)
    zone_m = re.search(r'Showing\s+([\d\s]+)\s+in this area', html)
    zone = int(zone_m.group(1).replace(" ", "")) if zone_m else None
    return {
        "global": int(hintg.group(1)) if hintg else None,
        "zone_text": zone,
    }

cases = [
    ("default", "/find-property", ""),
    ("surface_min_100", "/find-property?surface[min]=100", "surface_min=100"),
    ("paris_bounds", "/find-property?map_bounds=48.80,2.30,48.90,2.40", "map_bounds=48.80,2.30,48.90,2.40"),
    ("surface+bounds", "/find-property?surface[min]=100&map_bounds=48.80,2.30,48.90,2.40", "surface_min=100&map_bounds=48.80,2.30,48.90,2.40"),
    ("locality_paris", "/find-property?locality=Paris", "locality=Paris"),
    ("office_rent_bef", "/for-rent/office/", "operation_type%5BLOC%5D=LOC&asset_type%5BBUR%5D=BUR"),
]

print(f"=== PS Search map/list/count sync audit ({BASE}) ===")
for name, page, qs in cases:
    g = count_api(qs)
    m = markers_api(qs)
    p = page_metrics(page)
    try:
        h = htmx_header(qs)
    except urllib.error.HTTPError as exc:
        bad(f"{name}: HTMX header HTTP {exc.code} for qs={qs}")
        continue

    label = f"{name} (global={g}, zone={m['zone']}, cards={p['cards']})"
    if m["global"] != g:
        bad(f"{label}: markers.global_count={m['global']} != count API={g}")
        continue
    if m["zone"] > g:
        bad(f"{label}: zone > global")
        continue
    if p["zone_setting"] is not None and p["zone_setting"] != m["zone"]:
        bad(f"{label}: page zoneCount={p['zone_setting']} != markers zone={m['zone']}")
        continue
    if p["global_setting"] is not None and p["global_setting"] != g:
        bad(f"{label}: page globalCount={p['global_setting']} != count API={g}")
        continue
    if p["hint_global"] is not None and p["hint_global"] != g:
        bad(f"{label}: header hint global={p['hint_global']} != count API={g}")
        continue
    if h["global"] is not None and h["global"] != g:
        bad(f"{label}: HTMX header global={h['global']} != count API={g}")
        continue
    if m["zone"] != g and h.get("zone_text") is not None and h["zone_text"] != m["zone"]:
        bad(f"{label}: HTMX header zone={h['zone_text']} != markers zone={m['zone']}")
        continue
    if m["zone"] != g and p.get("hint_zone") is not None and p["hint_zone"] != m["zone"]:
        bad(f"{label}: page hint zone={p['hint_zone']} != markers zone={m['zone']}")
        continue
    if m["zone"] <= 100 and p["cards"] != m["zone"]:
        bad(f"{label}: list cards={p['cards']} != zone={m['zone']}")
        continue
    if m["markers"] > m["zone"] and m["clusters"] == 0:
        bad(f"{label}: markers={m['markers']} > zone={m['zone']}")
        continue
    ok(label)

print("")
print(f"=== Summary: {PASS} passed, {FAIL} failed ===")
sys.exit(1 if FAIL else 0)
PY
