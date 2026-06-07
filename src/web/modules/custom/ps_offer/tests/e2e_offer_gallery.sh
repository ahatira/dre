#!/usr/bin/env bash
# Functional tests for offer hero gallery + unified lightbox.
# Usage: bash web/modules/custom/ps_offer/tests/e2e_offer_gallery.sh [base_url] [node_id]
set -euo pipefail

BASE_URL="${1:-http://localhost:8080}"
NODE_ID="${2:-2}"
URL="${BASE_URL}/node/${NODE_ID}"
FAIL=0

pass() { echo "  ✓ $1"; }
fail() { echo "  ✗ $1"; FAIL=1; }

echo "== Offer gallery E2E =="
echo "URL: ${URL}"

# HTTP + drupalSettings integrity.
TMP_HTML="$(mktemp)"
TMP_JS=""
trap 'rm -f "${TMP_HTML}" "${TMP_JS}"' EXIT
curl -fsS "${URL}" -o "${TMP_HTML}"
python3 - <<'PY' "${TMP_HTML}"
import json, re, sys
with open(sys.argv[1], encoding='utf-8', errors='replace') as fh:
    html = fh.read()
blocks = re.findall(r'<script type="application/json" data-drupal-selector="drupal-settings-json">(.*?)</script>', html, re.S)
if not blocks:
    print("FAIL:drupal_settings_missing")
    sys.exit(0)
data = json.loads(blocks[0])
g = data.get("psOfferGallery", {})
slides = g.get("slides", [])
entries = g.get("entry_indexes", {})
print(f"INFO:slides={len(slides)}")
for key in ("photos", "video", "visit_3d", "plan"):
    idx = entries.get(key)
    if idx is None:
        print(f"FAIL:entry_{key}_null")
    elif idx >= len(slides):
        print(f"FAIL:entry_{key}_out_of_range")
    else:
        print(f"PASS:entry_{key}={idx}:{slides[idx].get('type')}")
required = {"image", "video_oembed", "video_url", "visit_3d", "plan_pdf"}
found = {s.get("type") for s in slides}
for t in required:
    if t in found:
        print(f"PASS:type_{t}")
    else:
        print(f"FAIL:type_{t}_missing")
PY

# Markup presence.
grep -q 'data-ps-gallery-hero' "${TMP_HTML}" && pass 'hero_markup' || fail 'hero_markup'
grep -q 'data-ps-gallery-stage' "${TMP_HTML}" && pass 'hero_stage' || fail 'hero_stage'
grep -q 'data-ps-gallery-swiper' "${TMP_HTML}" && pass 'hero_swiper' || fail 'hero_swiper'
grep -q 'data-ps-gallery-slides' "${TMP_HTML}" && pass 'hero_all_slides_template' || fail 'hero_all_slides_template'
grep -q 'data-ps-gallery-lightbox' "${TMP_HTML}" && pass 'lightbox_markup' || fail 'lightbox_markup'
grep -q 'ps-gallery-lightbox__thumb-divider' "${TMP_HTML}" && pass 'lightbox_thumb_dividers' || fail 'lightbox_thumb_dividers'
grep -q 'data-ps-gallery-entry="video"' "${TMP_HTML}" && pass 'video_badge' || fail 'video_badge'
grep -q 'data-ps-gallery-entry="visit_3d"' "${TMP_HTML}" && pass 'visit_badge' || fail 'visit_badge'
grep -q 'data-ps-gallery-entry="plan"' "${TMP_HTML}" && pass 'plan_badge' || fail 'plan_badge'
THUMB_COUNT="$(grep -cE 'data-ps-lightbox-thumb([^s]|$)' "${TMP_HTML}" || true)"
if [[ "${THUMB_COUNT}" -eq 41 ]]; then
  pass "lightbox_thumbs=${THUMB_COUNT}"
else
  fail "lightbox_thumbs=${THUMB_COUNT} (expected 41)"
fi

# JS asset served.
TMP_JS="$(mktemp)"
JS_URL="${BASE_URL}/themes/custom/ps_theme/assets/js/offer-gallery.js"
curl -fsS "${JS_URL}" -o "${TMP_JS}"
grep -q "setAttribute('data-ps-lightbox-counter'" "${TMP_JS}" && pass 'lightbox_counter' || fail 'lightbox_counter'
grep -q 'initHeroSwiper' "${TMP_JS}" && pass 'js_hero_swiper' || fail 'js_hero_swiper'
grep -q 'openOfferGallery' "${TMP_JS}" && pass 'js_photoswipe_lightbox' || fail 'js_photoswipe_lightbox'
grep -q 'initOfferGalleryPhotoSwipe' "${TMP_JS}" && pass 'js_photoswipe_init' || fail 'js_photoswipe_init'
grep -q 'syncHeroIndex' "${TMP_JS}" && pass 'js_hero_lightbox_sync' || fail 'js_hero_lightbox_sync'
grep -q 'buildPhotoSwipeDataSource' "${TMP_JS}" && pass 'js_mixed_media_slides' || fail 'js_mixed_media_slides'

if [[ "${FAIL}" -eq 0 ]]; then
  echo "RESULT: PASS"
  exit 0
fi
echo "RESULT: FAIL"
exit 1
