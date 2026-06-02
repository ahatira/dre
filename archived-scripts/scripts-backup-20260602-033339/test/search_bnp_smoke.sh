#!/usr/bin/env bash
# shellcheck disable=SC1091
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)/_core/_source.sh"

ps_header "BNP Search Smoke (list/map)"

BASE_URL="${PS_HTTP_URL}"

assert_equals() {
  local label="$1"
  local expected="$2"
  local actual="$3"
  if [[ "${actual}" == "${expected}" ]]; then
    ps_success "${label}: ${actual}"
  else
    ps_die "${label}: expected '${expected}', got '${actual}'"
  fi
}

assert_contains() {
  local label="$1"
  local haystack="$2"
  local needle="$3"
  if [[ "${haystack}" == *"${needle}"* ]]; then
    ps_success "${label}: found '${needle}'"
  else
    ps_die "${label}: missing '${needle}'"
  fi
}

assert_not_contains() {
  local label="$1"
  local haystack="$2"
  local needle="$3"
  if [[ "${haystack}" == *"${needle}"* ]]; then
    ps_die "${label}: unexpected '${needle}'"
  else
    ps_success "${label}: '${needle}' not present"
  fi
}

ps_info "Check HTTP availability"
ps_wait_for_http "${BASE_URL}/recherche" 20 1

ps_info "Case 1: search endpoint canonical behavior is stable"
loc_effective="$(curl -sS -L -o /tmp/ps_bnp_loc.html -w '%{url_effective}' "${BASE_URL}/recherche?operation_type=LOC&asset_type=BUR")"
loc_html="$(cat /tmp/ps_bnp_loc.html)"
if [[ "${loc_effective}" == *"/for-rent/"* || "${loc_effective}" == *"/recherche"* ]]; then
  ps_success "Canonical URL is stable: ${loc_effective}"
else
  ps_die "Canonical URL is unexpected: ${loc_effective}"
fi

ps_info "Case 2: primary filter labels are present"
if [[ "${loc_html}" == *"Transaction type"* || "${loc_html}" == *"Type de transaction"* ]]; then
  ps_success "Transaction type label detected"
else
  ps_die "Transaction type label missing"
fi

if [[ "${loc_html}" == *"Property type"* || "${loc_html}" == *"Type de bien"* ]]; then
  ps_success "Property type label detected"
else
  ps_die "Property type label missing"
fi

assert_contains "More filters toggle" "${loc_html}" "More filters"

ps_info "Case 3: feature filters are rendered in page"
assert_contains "Feature filter namespace" "${loc_html}" "feature_"

ps_info "Case 4: no legacy facet query arrays"
assert_not_contains "No f[] links" "${loc_html}" "f%5B0%5D"

ps_success "BNP search smoke passed"
