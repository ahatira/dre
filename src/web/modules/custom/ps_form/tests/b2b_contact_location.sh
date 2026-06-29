#!/usr/bin/env bash
# B2B smoke tests — Contact webform location field (DOM, Drush form build, API wiring).
# Shared editor: ps_search/js/search-location-editor.js (Tagify-like chips + suggest).
set -euo pipefail

# shellcheck source=/dev/null
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/../../../../.." && pwd)/scripts/e2e/common.sh"

PASS=0
FAIL=0

pass() { echo "  PASS: $1"; PASS=$((PASS + 1)); }
fail() { echo "  FAIL: $1"; FAIL=$((FAIL + 1)); }

fetch() {
  curl -sL -m 120 "$1" 2>/dev/null || echo ""
}

http_code() {
  curl -sL -m 120 -o /dev/null -w '%{http_code}' "$1" 2>/dev/null || echo "000"
}

assert_http_200() {
  local url="$1" label="$2"
  local code
  code=$(http_code "$url")
  if [[ "$code" == "200" ]]; then
    pass "$label (HTTP 200)"
  else
    fail "$label (HTTP $code) — $url"
  fi
}

assert_file_contains() {
  local file="$1" needle="$2" label="$3"
  if grep -Fq "$needle" "$file"; then
    pass "$label"
  else
    fail "$label (missing: $needle)"
  fi
}

assert_file_not_contains() {
  local file="$1" needle="$2" label="$3"
  if grep -Fq "$needle" "$file"; then
    fail "$label (unexpected: $needle)"
  else
    pass "$label"
  fi
}

assert_json_api() {
  local url="$1" py_check="$2" label="$3"
  local body
  body=$(curl -sS -m 60 "$url" 2>/dev/null || echo "")
  if [[ -z "$body" ]]; then
    fail "$label (empty API response) — $url"
    return
  fi
  if python3 -c "import json,sys; data=json.loads(sys.argv[1]); sys.exit(0 if ($py_check) else 1)" "$body" 2>/dev/null; then
    pass "$label"
  else
    fail "$label — $url"
    echo "       body: ${body:0:200}"
  fi
}

assert_location_field_block() {
  local file="$1" label="$2"
  if python3 - "$file" <<'PY'
import re
import sys

html = open(sys.argv[1], encoding="utf-8", errors="replace").read()
start = html.find("form-item-search-territory")
if start == -1:
    sys.exit(1)
chunk = html[start : start + 4000]
required = [
    "ps-form-location",
    "js-ps-location-editor",
    "js-ps-location-chips",
    "js-ps-location-suggest",
    "js-ps-contact-location-input",
    'role="listbox"',
    "City, district, postcode, building name",
]
if not all(token in chunk for token in required):
    sys.exit(1)
# Suggest must live inside the location form-item (not orphaned after closing wrapper).
suggest_pos = chunk.find("js-ps-location-suggest")
form_item_end = chunk.rfind("</div>")
if suggest_pos == -1 or suggest_pos > form_item_end:
    sys.exit(1)
# Optional field: no HTML5 required on the territory input.
input_match = re.search(r'id="edit-search-territory"[^>]*>', chunk)
if input_match and re.search(r'\brequired\b', input_match.group(0)):
    sys.exit(1)
sys.exit(0)
PY
  then
    pass "$label"
  else
    fail "$label (invalid location DOM block)"
  fi
}

drush_location_form_snapshot() {
  ps_e2e_drush php:eval "
\$webforms = [
  'find_property' => 'nested',
  'entrust_search' => 'nested',
  'entrust_property' => 'top',
  'invest_sell' => 'top',
];
foreach (\$webforms as \$webformId => \$layout) {
  \$webform = \\Drupal::entityTypeManager()->getStorage('webform')->load(\$webformId);
  if (!\$webform) {
    print \$webformId . ':missing\\n';
    continue;
  }
  \$submission = \\Drupal\\webform\\Entity\\WebformSubmission::create(['webform_id' => \$webformId]);
  \$formObject = \\Drupal::entityTypeManager()->getFormObject('webform_submission', 'add');
  \$formObject->setEntity(\$submission);
  \$formState = new \\Drupal\\Core\\Form\\FormState();
  \$formState->set('current_page', 'step_project');
  \$form = \\Drupal::formBuilder()->buildForm(\$formObject, \$formState);
  \$project = \$form['elements']['step_project']['project'] ?? [];
  \$territory = \$layout === 'nested'
    ? (\$project['search_criteria']['search_territory'] ?? NULL)
    : (\$project['search_territory'] ?? NULL);
  if (!is_array(\$territory)) {
    print \$webformId . ':no_territory\\n';
    continue;
  }
  \$classes = \$territory['#attributes']['class'] ?? [];
  \$wrapper = \$territory['#wrapper_attributes']['class'] ?? [];
  \$required = !empty(\$territory['#required']) ? '1' : '0';
  \$inputGroup = !empty(\$territory['#input_group']) ? '1' : '0';
  \$contactClass = in_array('js-ps-contact-location-input', \$classes, TRUE) ? '1' : '0';
  \$wrapperLocation = in_array('ps-form-location', \$wrapper, TRUE) ? '1' : '0';
  \$chips = isset(\$territory['#input_group_before'][0]['#markup'])
    && str_contains((string) \$territory['#input_group_before'][0]['#markup'], 'js-ps-location-chips') ? '1' : '0';
  \$suggest = isset(\$territory['#input_group_after'][0]['#markup'])
    && str_contains((string) \$territory['#input_group_after'][0]['#markup'], 'js-ps-location-suggest') ? '1' : '0';
  print \$webformId . ':layout=' . \$layout
    . ',required=' . \$required
    . ',input_group=' . \$inputGroup
    . ',contact_class=' . \$contactClass
    . ',wrapper=' . \$wrapperLocation
    . ',chips=' . \$chips
    . ',suggest=' . \$suggest . \"\\n\";
}
" 2>/dev/null
}

echo "=== PS Form contact location B2B ($BASE) ==="

echo ""
echo "--- Drush: location field form build (step_project) ---"
SNAPSHOT=$(drush_location_form_snapshot || true)
for webform in find_property entrust_search entrust_property invest_sell; do
  line=$(printf '%s\n' "$SNAPSHOT" | grep "^${webform}:" || true)
  if [[ "$line" == *required=0* && "$line" == *input_group=1* && "$line" == *contact_class=1* \
    && "$line" == *wrapper=1* && "$line" == *chips=1* && "$line" == *suggest=1* ]]; then
    pass "Location form build ($webform) — $line"
  else
    fail "Location form build ($webform) (got ${line:-empty})"
  fi
done

echo ""
echo "--- HTTP: contact webforms with location field ---"
LOCATION_FORMS=(
  "find_property:/form/find-property"
  "entrust_search:/form/entrust-search"
  "entrust_property:/form/entrust-property"
  "invest_sell:/form/invest-sell"
)
TMP="${TMPDIR:-/tmp}"
for entry in "${LOCATION_FORMS[@]}"; do
  webform="${entry%%:*}"
  path="${entry#*:}"
  url="${BASE}${path}"
  file="${TMP}/ps-form-b2b-location-${webform}.html"
  assert_http_200 "$url" "Location webform page ($webform)"
  fetch "$url" >"$file"
  assert_location_field_block "$file" "Location DOM structure ($webform)"
  assert_file_contains "$file" '"contentLangcode"' "drupalSettings psForm contentLangcode ($webform)"
  assert_file_contains "$file" 'locationSuggestUrl' "drupalSettings psForm suggest URL ($webform)"
  assert_file_contains "$file" 'locationDataUrl' "drupalSettings psForm data URL ($webform)"
  assert_file_not_contains "$file" 'Location field is required' "No stale location required message ($webform)"
  case "$webform" in
    find_property|entrust_search|invest_sell)
      assert_file_contains "$file" 'ps-form-search-criteria' "Location nested in criteria ($webform)"
      ;;
  esac
done

echo ""
echo "--- HTTP: webforms without location field ---"
NO_LOCATION_FORMS=(
  "get_advice:/form/get-advice"
  "other_request:/form/other-request"
)
for entry in "${NO_LOCATION_FORMS[@]}"; do
  webform="${entry%%:*}"
  path="${entry#*:}"
  file="${TMP}/ps-form-b2b-no-location-${webform}.html"
  fetch "${BASE}${path}" >"$file"
  assert_file_not_contains "$file" 'js-ps-contact-location-input' "No location input ($webform)"
  assert_file_not_contains "$file" '"contentLangcode"' "No location drupalSettings ($webform)"
done

echo ""
echo "--- API: location suggest (contact wizard dependency) ---"
assert_json_api \
  "${BASE}/api/ps/location-suggest?q=paris&limit=3" \
  "any(g.get('items') for g in data.get('groups', []))" \
  "suggest q=paris returns grouped items"
assert_json_api \
  "${BASE}/api/ps/location-suggest?q=par&limit=5&langcode=en" \
  "isinstance(data.get('groups'), list)" \
  "suggest accepts langcode param"

echo ""
echo "--- Regression: search filter location editor still present ---"
SEARCH_FILE="${TMP}/ps-form-b2b-search-location-regression.html"
fetch "${BASE}/for-rent/office/" >"$SEARCH_FILE"
assert_file_contains "$SEARCH_FILE" 'js-ps-locality-input' "Search filter locality input"
assert_file_contains "$SEARCH_FILE" 'js-ps-location-editor' "Search filter location editor shell"
assert_file_contains "$SEARCH_FILE" 'js-ps-location-chips' "Search filter location chips container"
assert_file_contains "$SEARCH_FILE" 'js-ps-location-suggest' "Search filter location suggest listbox"

echo ""
echo "=== PS Form contact location B2B SUMMARY ==="
echo "Passed: $PASS | Failed: $FAIL"
if [[ "$FAIL" -eq 0 ]]; then
  echo "All contact location B2B checks passed."
  exit 0
fi

echo "$FAIL check(s) failed."
exit 1
