#!/usr/bin/env bash
# Export Stellar assets from Figma API into ps_theme/assets/images/
#
# Usage:
#   export FIGMA_TOKEN='figd_...'
#   bash scripts/figma-export.sh
#
# Requires: curl, python3

set -euo pipefail

FILE_KEY="rrA1dlYnJMzcXlwOZ5iuuw"
ROOT="$(cd "$(dirname "$0")/.." && pwd)"
OUT="$ROOT/assets/images"

if [[ -z "${FIGMA_TOKEN:-}" ]]; then
  echo "ERROR: set FIGMA_TOKEN environment variable." >&2
  exit 1
fi

export_file() {
  local dest="$1" node_id="$2" format="$3" scale="${4:-}"
  local params="ids=${node_id}&format=${format}"
  [[ -n "$scale" ]] && params="${params}&scale=${scale}"
  local url="https://api.figma.com/v1/images/${FILE_KEY}?${params}"
  local img_url
  img_url=$(curl -sf -H "X-Figma-Token: $FIGMA_TOKEN" "$url" | python3 -c "import json,sys; d=json.load(sys.stdin); print(d['images'].get('${node_id}',''))")
  if [[ -z "$img_url" ]]; then
    echo "FAILED: $dest ($node_id)" >&2
    return 1
  fi
  mkdir -p "$(dirname "$dest")"
  curl -sf "$img_url" -o "$dest"
  echo "OK $dest ($(wc -c < "$dest") bytes)"
}

# Node IDs from BNP PRE Stellar — Livrable client
export_file "$OUT/logo/header-logo.svg" "I48:8052;17:429" svg
export_file "$OUT/logo/footer-logo.svg" "I48:8170;18:783" svg
export_file "$OUT/logo/header-logo.png" "I48:8052;17:429" png 3
export_file "$OUT/logo/footer-logo.png" "I48:8170;18:783" png 3
export_file "$OUT/hero/hero-homepage.png" "918:13529" png 2
export_file "$OUT/hero/hero-profile.png" "48:7863" png 2

cp "$OUT/logo/header-logo.svg" "$ROOT/logo.svg"
cp "$OUT/hero/hero-homepage.png" "$OUT/hero/hero-default.png"
echo "Theme logo.svg and hero-default.png updated."
