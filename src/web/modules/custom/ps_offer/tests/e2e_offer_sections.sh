#!/usr/bin/env bash
set -euo pipefail

NID="${1:-7}"
DRUSH="docker exec ps_php /var/www/html/vendor/bin/drush"

assert_eq() {
  local expected="$1"
  local actual="$2"
  local message="$3"
  if [[ "$expected" != "$actual" ]]; then
    echo "FAIL: ${message} (expected='${expected}', actual='${actual}')"
    exit 1
  fi
  echo "PASS: ${message}"
}

assert_contains() {
  local needle="$1"
  local haystack="$2"
  local message="$3"
  if [[ "$haystack" != *"$needle"* ]]; then
    echo "FAIL: ${message} (needle='${needle}')"
    exit 1
  fi
  echo "PASS: ${message}"
}

echo "== PS Offer sections E2E (node ${NID}) =="

${DRUSH} cr -q

ROUTE_DATA="$(${DRUSH} php:eval '
$route = \Drupal::service("router.route_provider")->getRouteByName("ps_offer.section_settings");
print "form=".$route->getDefault("_form")."\n";
print "permission=".$route->getRequirement("_permission")."\n";
')"

ROUTE_FORM="$(echo "$ROUTE_DATA" | sed -n 's/^form=//p' | head -n1)"
ROUTE_PERMISSION="$(echo "$ROUTE_DATA" | sed -n 's/^permission=//p' | head -n1)"

assert_eq '\Drupal\ps_offer\Form\OfferSectionSettingsForm' "${ROUTE_FORM}" "section settings route uses OfferSectionSettingsForm"
assert_eq 'manage ps_offer' "${ROUTE_PERMISSION}" "section settings route requires manage ps_offer"

# Submit the admin form programmatically (same contract as /admin/ps/config/offer-sections).
${DRUSH} php:eval '
use Drupal\Core\Form\FormState;
use Drupal\ps_offer\Form\OfferSectionSettingsForm;

$form_object = OfferSectionSettingsForm::create(\Drupal::getContainer());
$form = [];
$form_state = new FormState();
$form_state->setValues([
  "sections" => [
    "surface_table" => [
      "label" => "E2E Surface title",
      "icon" => "bnp_custom:floors",
    ],
    "location" => [
      "label" => "E2E Location title",
      "icon" => ["target_id" => "bnp_custom:pin-map"],
    ],
    "description" => [
      "label" => "E2E Description title",
      "icon" => "",
    ],
  ],
]);
$form_object->validateForm($form, $form_state);
$form_object->submitForm($form, $form_state);
print "form_submitted\n";
'

REGISTRY_DATA="$(${DRUSH} php:eval '
$registry = \Drupal::service("ps_core.section_registry");
$builder = \Drupal::service("ps_core.section_heading_builder");
print "surface_label=".$registry->getLabel("surface_table")."\n";
print "surface_icon=".$registry->getIconId("surface_table")."\n";
print "location_icon=".$registry->getIconId("location")."\n";
print "cache_tags=".implode(",", $builder->getCacheTags())."\n";
')"

SURFACE_LABEL="$(echo "$REGISTRY_DATA" | sed -n 's/^surface_label=//p' | head -n1)"
SURFACE_ICON="$(echo "$REGISTRY_DATA" | sed -n 's/^surface_icon=//p' | head -n1)"
LOCATION_ICON="$(echo "$REGISTRY_DATA" | sed -n 's/^location_icon=//p' | head -n1)"
CACHE_TAGS="$(echo "$REGISTRY_DATA" | sed -n 's/^cache_tags=//p' | head -n1)"

assert_eq "E2E Surface title" "${SURFACE_LABEL}" "form submit persisted surface label"
assert_eq "bnp_custom:floors" "${SURFACE_ICON}" "form submit persisted surface icon"
assert_eq "bnp_custom:pin-map" "${LOCATION_ICON}" "validateForm normalized icon picker submission"
assert_eq "config:ps_core.offer_section_settings" "${CACHE_TAGS}" "heading builder exposes config cache tag"

${DRUSH} cr -q

HTTP_CODE="$(curl -s -o /tmp/ps_offer_sections_e2e.html -w '%{http_code}' "http://localhost:8080/node/${NID}")"
assert_eq "200" "${HTTP_CODE}" "offer detail page responds with HTTP 200"

HTML="$(cat /tmp/ps_offer_sections_e2e.html)"
assert_contains "E2E Surface title" "${HTML}" "offer page renders updated surface section title"
assert_contains "ps-offer-section__title" "${HTML}" "offer page renders section heading markup"
assert_contains "bnp-icon-floors" "${HTML}" "offer page renders surface section icon class"

echo "All offer section E2E checks passed."
