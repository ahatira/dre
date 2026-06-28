#!/usr/bin/env bash
# B2B smoke tests — Contact hub webform routing, admin-enabled webforms, direct webforms.
set -euo pipefail

# shellcheck source=/dev/null
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/../../../../.." && pwd)/scripts/e2e/common.sh"

PASS=0
FAIL=0
HUB_FILE="${TMPDIR:-/tmp}/ps-form-b2b-contact-hub.html"

pass() { echo "  PASS: $1"; PASS=$((PASS + 1)); }
fail() { echo "  FAIL: $1"; FAIL=$((FAIL + 1)); }

http_code() {
  curl -sL -m 120 -o /dev/null -w '%{http_code}' "$1" 2>/dev/null || echo "000"
}

fetch_to_file() {
  curl -sL -m 120 "$1" -o "$2" 2>/dev/null || echo "" >"$2"
}

assert_http() {
  local expected="$1" url="$2" label="$3"
  local code
  code=$(http_code "$url")
  if [[ "$code" == "$expected" ]]; then
    pass "$label (HTTP $expected)"
  else
    fail "$label (HTTP $code, expected $expected) — $url"
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

drush_router_snapshot() {
  ps_e2e_drush php:eval "
\$router = \\Drupal::service('ps_form.contact_need_router');
\$registered = \$router->getRegisteredDefinitions();
\$enabled = \$router->getEnabledHubWebformIds();
\$lines = [];
foreach (\$registered as \$webformId => \$def) {
  \$webform = \\Drupal::entityTypeManager()->getStorage('webform')->load(\$webformId);
  \$title = \$webform ? (string) \$webform->label() : '';
  \$lines[] = \$webformId . '|' . \$def['path'] . '|' . \$title;
}
print implode(\"\\n\", \$lines);
print \"\\n---\\n\";
print 'registered:' . count(\$registered) . \"\\n\";
print 'enabled:' . implode(',', \$enabled) . \"\\n\";
\$pathMap = \$router->getWebformPathMap();
print 'pathmap:' . implode(',', array_keys(\$pathMap)) . \"\\n\";
" 2>/dev/null
}

drush_contact_presentation_snapshot() {
  ps_e2e_drush php:eval "
\$webforms = ['find_property', 'other_request'];
foreach (\$webforms as \$webformId) {
  \$webform = \\Drupal::entityTypeManager()->getStorage('webform')->load(\$webformId);
  if (!\$webform) {
    print \$webformId . ':missing\\n';
    continue;
  }
  \$submission = \\Drupal\\webform\\Entity\\WebformSubmission::create(['webform_id' => \$webformId]);
  \$formObject = \\Drupal::entityTypeManager()
    ->getFormObject('webform_submission', 'add');
  \$formObject->setEntity(\$submission);
  \$formState = new \\Drupal\\Core\\Form\\FormState();
  \$formState->set('current_page', 'step_contact');
  \$form = \\Drupal::formBuilder()->buildForm(\$formObject, \$formState);
  \$step = \$form['elements']['step_contact'] ?? [];
  \$classes = \$step['#attributes']['class'] ?? [];
  \$intro = isset(\$step['contact_details_intro']) ? '1' : '0';
  \$optout = isset(\$step['optout_email_transaction']['#wrapper_attributes']['class'])
    && in_array('ps-form-optout-item', \$step['optout_email_transaction']['#wrapper_attributes']['class'], TRUE) ? '1' : '0';
  \$legal = isset(\$step['legal_notice']['#wrapper_attributes']['class'])
    && in_array('ps-form-legal-notice', \$step['legal_notice']['#wrapper_attributes']['class'], TRUE) ? '1' : '0';
  print \$webformId . ':details=' . (in_array('ps-form-contact-details', \$classes, TRUE) ? '1' : '0')
    . ',intro=' . \$intro . ',optout=' . \$optout . ',legal=' . \$legal;
  \$messageStep = \$form['elements']['step_message'] ?? [];
  \$messageClasses = \$messageStep['#attributes']['class'] ?? [];
  \$messageIntro = isset(\$messageStep['message_intro']) ? '1' : '0';
  print ',message=' . (in_array('ps-form-message-step', \$messageClasses, TRUE) ? '1' : '0')
    . ',message_intro=' . \$messageIntro . \"\\n\";
}
" 2>/dev/null
}

echo "=== PS Form contact hub B2B ($BASE) ==="

echo ""
echo "--- Drush: dynamic webform router ---"
ROUTER_OUT=$(drush_router_snapshot || true)
REGISTERED_COUNT=$(printf '%s\n' "$ROUTER_OUT" | awk -F: '/^registered:/ {print $2}')
ENABLED_CSV=$(printf '%s\n' "$ROUTER_OUT" | awk -F: '/^enabled:/ {print $2}')

if [[ "${REGISTERED_COUNT:-0}" -ge 6 ]]; then
  pass "Registered direct webforms via router ($REGISTERED_COUNT)"
else
  fail "Registered direct webforms via router (got ${REGISTERED_COUNT:-0}, expected >= 6)"
fi

EXPECTED_WEBFORMS=(find_property entrust_search get_advice entrust_property invest_sell other_request)
if [[ "${ENABLED_CSV:-}" == "$(IFS=,; echo "${EXPECTED_WEBFORMS[*]}")" ]]; then
  pass "Enabled hub webforms in mockup order ($ENABLED_CSV)"
else
  fail "Enabled hub webform order (got ${ENABLED_CSV:-empty}, expected find_property,entrust_search,get_advice,entrust_property,invest_sell,other_request)"
fi

for webform in "${EXPECTED_WEBFORMS[@]}"; do
  if printf '%s\n' "$ROUTER_OUT" | grep -q "^${webform}|"; then
    pass "Registered webform: $webform"
  else
    fail "Registered webform missing: $webform"
  fi
done

PATHMAP_KEYS=$(printf '%s\n' "$ROUTER_OUT" | awk -F: '/^pathmap:/ {print $2}')
if [[ "$PATHMAP_KEYS" == "contact,${ENABLED_CSV}" ]]; then
  pass "Deeplink path map aligned with enabled webforms ($PATHMAP_KEYS)"
else
  fail "Deeplink path map mismatch (pathmap=$PATHMAP_KEYS enabled=$ENABLED_CSV)"
fi

IFS=',' read -r -a ENABLED_WEBFORMS <<< "${ENABLED_CSV}"

echo ""
echo "--- HTTP: contact hub ---"
assert_http 200 "$BASE/form/contact" "Hub contact page"
fetch_to_file "$BASE/form/contact" "$HUB_FILE"
assert_file_contains "$HUB_FILE" 'ps-contact-wizard--contact' "Hub wizard CSS hook"
assert_file_contains "$HUB_FILE" 'ps-webform-urgency-help' "Hub urgency contact block"
assert_file_contains "$HUB_FILE" 'In a hurry' "Hub urgency lead text"
assert_file_contains "$HUB_FILE" 'data-webform-page="step_need"' "Hub progress includes Need step"
assert_file_contains "$HUB_FILE" 'data-webform-page="step_project"' "Hub progress includes Project step"
assert_file_contains "$HUB_FILE" 'data-webform-page="step_contact"' "Hub progress includes Details step"
assert_file_contains "$HUB_FILE" 'data-webform-page="step_message"' "Hub progress includes Message step"
assert_file_not_contains "$HUB_FILE" 'Your details' "Hub progress no legacy Your details label"
assert_file_not_contains "$HUB_FILE" 'value="rent"' "Hub no legacy need code radios"

for webform in "${ENABLED_WEBFORMS[@]}"; do
  line=$(printf '%s\n' "$ROUTER_OUT" | grep "^${webform}|" || true)
  if [[ -z "$line" ]]; then
    fail "Enabled webform $webform missing from router snapshot"
    continue
  fi
  title="${line##*|}"
  assert_file_contains "$HUB_FILE" "$title" "Hub shows enabled webform title ($webform)"
  assert_file_contains "$HUB_FILE" "value=\"${webform}\"" "Hub radio value for $webform"
done

for webform in "${EXPECTED_WEBFORMS[@]}"; do
  if printf '%s\n' "${ENABLED_WEBFORMS[@]}" | grep -qx "$webform"; then
    continue
  fi
  assert_file_not_contains "$HUB_FILE" "value=\"${webform}\"" "Hub hides disabled webform ($webform)"
done

echo ""
echo "--- HTTP: direct webforms (from_hub=1) ---"
while IFS='|' read -r webform path title; do
  [[ -z "${webform:-}" || "$webform" == "---" ]] && continue
  [[ "$webform" == registered* || "$webform" == enabled* ]] && continue

  enabled=0
  for e in "${ENABLED_WEBFORMS[@]}"; do
    if [[ "$e" == "$webform" ]]; then
      enabled=1
      break
    fi
  done
  if [[ "$enabled" -ne 1 ]]; then
    continue
  fi

  direct_url="${BASE}${path}?from_hub=1"
  direct_file="${TMPDIR:-/tmp}/ps-form-b2b-${webform}.html"
  assert_http 200 "$direct_url" "Direct webform $webform"
  fetch_to_file "$direct_url" "$direct_file"
  assert_file_contains "$direct_file" 'ps-contact-wizard--from-hub' "Direct form from-hub class ($webform)"
  assert_file_contains "$direct_file" 'ps_from_hub' "Direct form hidden from_hub field ($webform)"
  assert_file_contains "$direct_file" 'data-webform-page="step_need"' "Direct from-hub progress includes Need ($webform)"
  assert_file_contains "$direct_file" 'data-webform-page="step_contact"' "Direct from-hub progress includes Details ($webform)"
  assert_file_not_contains "$direct_file" 'Your details' "Direct from-hub progress no Your details ($webform)"
  assert_file_contains "$direct_file" 'ps-webform-urgency-help' "Direct form urgency block ($webform)"
  case "$webform" in
    find_property|entrust_search|entrust_property|invest_sell)
      assert_file_contains "$direct_file" 'ps-webform-op-choices' "Direct form operation segmented control ($webform)"
      assert_file_contains "$direct_file" 'ps-webform-asset-grid' "Direct form asset tile grid ($webform)"
      assert_file_contains "$direct_file" 'js-ps-contact-location-input' "Direct form location autocomplete ($webform)"
      ;;
  esac
  case "$webform" in
    find_property|entrust_search|invest_sell)
      assert_file_contains "$direct_file" 'ps-form-search-criteria' "Direct form criteria grid ($webform)"
      ;;
  esac
done <<< "$ROUTER_OUT"

echo ""
echo "--- Drush: contact details presentation (step_contact build) ---"
PRESENTATION_OUT=$(drush_contact_presentation_snapshot || true)
for webform in find_property other_request; do
  line=$(printf '%s\n' "$PRESENTATION_OUT" | grep "^${webform}:" || true)
  if [[ "$line" == *details=1* && "$line" == *intro=1* && "$line" == *optout=1* && "$line" == *legal=1* && "$line" == *message=1* && "$line" == *message_intro=1* ]]; then
    pass "Contact wizard presentation hooks ($webform)"
  else
    fail "Contact wizard presentation hooks ($webform) (got ${line:-empty})"
  fi
done

echo ""
echo "--- HTTP: contact hub need step ---"
fetch_to_file "$BASE/form/contact" "$HUB_FILE"
assert_file_contains "$HUB_FILE" 'ps-form-radios--stacked' "Hub need step stacked radios class"
assert_file_contains "$HUB_FILE" 'ps-form-section-intro' "Hub need step section intro class"

echo ""
echo "--- HTTP: direct webforms (standalone) ---"
while IFS='|' read -r webform path title; do
  [[ -z "${webform:-}" || "$webform" == "---" ]] && continue
  [[ "$webform" == registered* || "$webform" == enabled* ]] && continue

  enabled=0
  for e in "${ENABLED_WEBFORMS[@]}"; do
    if [[ "$e" == "$webform" ]]; then
      enabled=1
      break
    fi
  done
  if [[ "$enabled" -ne 1 ]]; then
    continue
  fi

  direct_url="${BASE}${path}"
  direct_file="${TMPDIR:-/tmp}/ps-form-b2b-${webform}-standalone.html"
  assert_http 200 "$direct_url" "Standalone direct webform $webform"
  fetch_to_file "$direct_url" "$direct_file"
  assert_file_not_contains "$direct_file" 'ps-contact-wizard--from-hub' "Standalone without from-hub class ($webform)"
done <<< "$ROUTER_OUT"

echo ""
echo "=== PS Form contact hub B2B SUMMARY ==="
echo "Passed: $PASS | Failed: $FAIL"
if [[ "$FAIL" -eq 0 ]]; then
  echo "All contact hub B2B checks passed."
  exit 0
fi

echo "$FAIL check(s) failed."
exit 1
