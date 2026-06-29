#!/usr/bin/env bash
# B2B tests — each contact hub webform audited individually (halt on first failure).
set -euo pipefail

# shellcheck source=/dev/null
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/../../../../.." && pwd)/scripts/e2e/common.sh"

WEBFORMS=(
  find_property
  entrust_search
  get_advice
  entrust_property
  invest_sell
  other_request
)

# path|legal_variant|has_job_title|has_location|has_op_choices|has_asset_grid|has_criteria|project_marker
declare -A WEBFORM_META=(
  [find_property]='find-property|transaction|1|1|1|1|1|transaction_type'
  [entrust_search]='entrust-search|transaction|1|1|1|1|1|transaction_type'
  [get_advice]='get-advice|advisory|1|0|0|0|0|consulting_type'
  [entrust_property]='entrust-property|transaction|1|1|1|1|0|transaction_type'
  [invest_sell]='invest-sell|transaction|1|1|1|1|1|transaction_type'
  [other_request]='other-request|advisory|0|0|0|0|0|other_need'
)

PASS=0
CURRENT_WEBFORM=""

pass() { echo "    PASS: $1"; PASS=$((PASS + 1)); }
fail() {
  echo ""
  echo "  *** ARRET — échec sur webform « ${CURRENT_WEBFORM} » ***"
  echo "  FAIL: $1"
  echo ""
  echo "Passed before stop: $PASS"
  exit 1
}

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
  if [[ "$code" != "$expected" ]]; then
    fail "$label (HTTP $code, expected $expected) — $url"
  fi
  pass "$label (HTTP $expected)"
}

assert_file_contains() {
  local file="$1" needle="$2" label="$3"
  if ! grep -Fq "$needle" "$file"; then
    fail "$label (missing: $needle)"
  fi
  pass "$label"
}

assert_file_not_contains() {
  local file="$1" needle="$2" label="$3"
  if grep -Fq "$needle" "$file"; then
    fail "$label (unexpected: $needle)"
  fi
  pass "$label"
}

drush_webform_audit() {
  local webform_id="$1"
  local project_marker="$2"
  ps_e2e_drush php:eval "
\$webformId = '$webform_id';
\$projectMarker = '$project_marker';
\$webform = \\Drupal::entityTypeManager()->getStorage('webform')->load(\$webformId);
if (!\$webform) { print 'missing=1\\n'; return; }
\$submission = \\Drupal\\webform\\Entity\\WebformSubmission::create(['webform_id' => \$webformId]);
\$formObject = \\Drupal::entityTypeManager()->getFormObject('webform_submission', 'add');
\$formObject->setEntity(\$submission);
\$formState = new \\Drupal\\Core\\Form\\FormState();

// step_project — marker may live under project fieldset.
\$formState->set('current_page', 'step_project');
\$form = \\Drupal::formBuilder()->buildForm(\$formObject, \$formState);
\$projectStep = \$form['elements']['step_project'] ?? [];
\$hasMarker = static function (array \$elements, string \$needle) use (&\$hasMarker): bool {
  foreach (\$elements as \$key => \$value) {
    if (!is_string(\$key) || str_starts_with(\$key, '#')) {
      continue;
    }
    if (\$key === \$needle) {
      return TRUE;
    }
    if (is_array(\$value) && \$hasMarker(\$value, \$needle)) {
      return TRUE;
    }
  }
  return FALSE;
};
\$marker = \$projectMarker;
print 'project_marker=' . (\$hasMarker(\$projectStep, \$marker) ? '1' : '0') . \"\\n\";

// step_contact
\$formState->set('current_page', 'step_contact');
\$form = \\Drupal::formBuilder()->buildForm(\$formObject, \$formState);
\$step = \$form['elements']['step_contact'] ?? [];
\$classes = \$step['#attributes']['class'] ?? [];
\$details = in_array('ps-form-contact-details', \$classes, TRUE) ? '1' : '0';
\$intro = isset(\$step['contact_details_intro']) ? '1' : '0';
\$optout = isset(\$step['optout_email_transaction']['#wrapper_attributes']['class'])
  && in_array('ps-form-optout-item', \$step['optout_email_transaction']['#wrapper_attributes']['class'], TRUE) ? '1' : '0';
\$legalClass = isset(\$step['legal_notice']['#wrapper_attributes']['class'])
  && in_array('ps-form-legal-notice', \$step['legal_notice']['#wrapper_attributes']['class'], TRUE) ? '1' : '0';
\$legalMarkup = (string) (\$step['legal_notice']['#markup'] ?? '');
\$legalBloctel = str_contains(\$legalMarkup, 'bloctel') ? '1' : '0';
\$legalPrivacy = str_contains(\$legalMarkup, 'data-privacy.realestate.bnpparibas') ? '1' : '0';
\$legalTitle = str_contains(\$legalMarkup, 'ps-form-legal-notice__title') ? '1' : '0';
\$legalTransaction = str_contains(\$legalMarkup, 'Transaction France') ? '1' : '0';
\$legalAdvisory = str_contains(\$legalMarkup, 'Advisory France') ? '1' : '0';
\$jobOptions = \$step['job_title']['#options'] ?? NULL;
\$jobCount = is_array(\$jobOptions)
  ? count(array_filter(array_keys(\$jobOptions), fn(\$k) => (string) \$k !== ''))
  : -1;
\$jobEmpty = (string) (\$step['job_title']['#empty_option'] ?? '');
\$footerLegal = isset(\$step['legal_notice']['#wrapper_attributes']['class'])
  && in_array('ps-form-contact-footer__legal', \$step['legal_notice']['#wrapper_attributes']['class'], TRUE) ? '1' : '0';
\$requiredNote = isset(\$step['required_fields_note']) ? '1' : '0';
print 'details=' . \$details . ',intro=' . \$intro . ',optout=' . \$optout . \"\\n\";
print 'legal_class=' . \$legalClass . ',bloctel=' . \$legalBloctel . ',privacy=' . \$legalPrivacy . ',title=' . \$legalTitle . \"\\n\";
print 'legal_tx=' . \$legalTransaction . ',legal_adv=' . \$legalAdvisory . ',footer_legal=' . \$footerLegal . \"\\n\";
print 'job_count=' . \$jobCount . ',job_empty=' . json_encode(\$jobEmpty) . \"\\n\";
print 'required_note=' . \$requiredNote . \"\\n\";

// step_message
\$formState->set('current_page', 'step_message');
\$form = \\Drupal::formBuilder()->buildForm(\$formObject, \$formState);
\$messageStep = \$form['elements']['step_message'] ?? [];
\$messageClasses = \$messageStep['#attributes']['class'] ?? [];
\$message = in_array('ps-form-message-step', \$messageClasses, TRUE) ? '1' : '0';
\$messageIntro = isset(\$messageStep['message_intro']) ? '1' : '0';
print 'message=' . \$message . ',message_intro=' . \$messageIntro . \"\\n\";
" 2>/dev/null
}

audit_webform() {
  local webform="$1"
  CURRENT_WEBFORM="$webform"
  local meta="${WEBFORM_META[$webform]}"
  IFS='|' read -r path legal has_job has_loc has_op has_asset has_criteria project_marker <<< "$meta"

  local tmp="${TMPDIR:-/tmp}/ps-form-b2b-each-${webform}"
  local url_standalone="${BASE}/form/${path}"
  local url_from_hub="${url_standalone}?from_hub=1"

  echo ""
  echo "=== Webform: ${webform} (/form/${path}) ==="

  echo "  --- HTTP ---"
  assert_http 200 "$url_standalone" "Standalone page"
  assert_http 200 "$url_from_hub" "From-hub page"

  echo "  --- HTTP standalone DOM ---"
  fetch_to_file "$url_standalone" "${tmp}-standalone.html"
  assert_file_contains "${tmp}-standalone.html" 'data-webform-page="step_project"' "Progress: Project step"
  assert_file_contains "${tmp}-standalone.html" 'data-webform-page="step_contact"' "Progress: Details step"
  assert_file_contains "${tmp}-standalone.html" 'data-webform-page="step_message"' "Progress: Message step"
  assert_file_not_contains "${tmp}-standalone.html" 'ps-contact-wizard--from-hub' "No from-hub class (standalone)"
  assert_file_contains "${tmp}-standalone.html" 'ps-webform-urgency-help' "Urgency contact block"

  echo "  --- HTTP from_hub DOM ---"
  fetch_to_file "$url_from_hub" "${tmp}-fromhub.html"
  assert_file_contains "${tmp}-fromhub.html" 'ps-contact-wizard--from-hub' "From-hub wizard class"
  assert_file_contains "${tmp}-fromhub.html" 'ps_from_hub' "Hidden from_hub field"
  assert_file_contains "${tmp}-fromhub.html" 'data-webform-page="step_need"' "Progress: Need step (from_hub)"

  echo "  --- HTTP project step markers ---"
  assert_file_contains "${tmp}-standalone.html" "$project_marker" "Project step field ($project_marker)"
  if [[ "$has_op" == "1" ]]; then
    assert_file_contains "${tmp}-standalone.html" 'ps-webform-op-choices' "Operation segmented control"
    assert_file_contains "${tmp}-standalone.html" 'ps-webform-asset-grid' "Asset tile grid"
  fi
  if [[ "$has_criteria" == "1" ]]; then
    assert_file_contains "${tmp}-standalone.html" 'ps-form-search-criteria' "Search criteria grid"
  fi
  if [[ "$has_loc" == "1" ]]; then
    assert_file_contains "${tmp}-standalone.html" 'js-ps-contact-location-input' "Location autocomplete input"
    assert_file_contains "${tmp}-standalone.html" 'js-ps-location-chips' "Location chips container"
    assert_file_contains "${tmp}-standalone.html" 'js-ps-location-suggest' "Location suggest listbox"
  fi

  echo "  --- Drush form build audit ---"
  local audit
  audit=$(drush_webform_audit "$webform" "$project_marker")
  if [[ "$audit" == *missing=1* ]]; then
    fail "Webform entity not loadable via Drush"
  fi

  if [[ "$audit" != *project_marker=1* ]]; then
    fail "step_project missing expected field '$project_marker'"
  fi
  pass "step_project contains $project_marker"

  if [[ "$audit" != *details=1* ]]; then
    fail "step_contact missing ps-form-contact-details ($(printf '%s\n' "$audit" | grep '^details='))"
  fi
  pass "step_contact grid class"

  if [[ "$audit" != *intro=1* ]]; then
    fail "step_contact missing contact_details_intro"
  fi
  pass "step_contact intro markup"

  if [[ "$audit" != *optout=1* ]]; then
    fail "step_contact opt-out items not styled"
  fi
  pass "step_contact opt-out layout"

  if [[ "$audit" != *legal_class=1* || "$audit" != *bloctel=1* || "$audit" != *privacy=1* || "$audit" != *title=1* ]]; then
    fail "Legal notice incomplete ($(printf '%s\n' "$audit" | grep -E '^legal_'))"
  fi
  pass "Legal notice (class + Bloctel + privacy URL + title)"

  if [[ "$legal" == "transaction" && "$audit" != *legal_tx=1* ]]; then
    fail "Legal notice should mention Transaction France (variant=$legal)"
  fi
  if [[ "$legal" == "advisory" && "$audit" != *legal_adv=1* ]]; then
    fail "Legal notice should mention Advisory France (variant=$legal)"
  fi
  pass "Legal entity variant ($legal)"

  if [[ "$audit" != *footer_legal=1* ]]; then
    fail "Legal notice missing ps-form-contact-footer__legal wrapper"
  fi
  pass "Legal footer wrapper"

  if [[ "$audit" != *required_note=1* ]]; then
    fail "step_contact missing required_fields_note"
  fi
  pass "Required fields note"

  if [[ "$has_job" == "1" ]]; then
    if [[ "$audit" != *job_count=34* ]]; then
      fail "job_title should have 34 CRM options (got: $(printf '%s\n' "$audit" | grep job_count=))"
    fi
    pass "job_title: 34 options (bnppre.fr)"
    if ! printf '%s\n' "$audit" | grep -q 'job_empty='; then
      fail "job_title missing #empty_option"
    fi
    pass "job_title empty option present"
  else
    if [[ "$audit" == *job_count=34* || "$audit" == *job_count=3[0-9]* ]]; then
      fail "other_request must not expose job_title select"
    fi
    pass "No job_title field (expected)"
  fi

  if [[ "$audit" != *message=1* || "$audit" != *message_intro=1* ]]; then
    fail "step_message presentation hooks ($(printf '%s\n' "$audit" | grep '^message='))"
  fi
  pass "step_message intro + layout"

  echo "  => ${webform}: OK"
}

echo "=== PS Form — B2B audit per webform hub ($BASE) ==="
echo "Ordre: ${WEBFORMS[*]}"
echo "Politique: arrêt immédiat au premier échec."

for webform in "${WEBFORMS[@]}"; do
  audit_webform "$webform"
done

echo ""
echo "=== RÉSUMÉ ==="
echo "Webforms audités: ${#WEBFORMS[@]}"
echo "Checks passés: $PASS"
echo "Tous les webforms hub contact sont OK."
