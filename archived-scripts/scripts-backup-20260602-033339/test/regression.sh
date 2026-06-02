#!/usr/bin/env bash
# shellcheck disable=SC1091
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)/_core/_source.sh"

ps_header "Module Regression Orchestration"
cd "${PS_SRC_DIR}"

run_step() {
  local label="$1"
  shift
  ps_info "${label}"
  "$@"
}

run_step "Behat suite: ps_dictionary" ./vendor/bin/behat --config behat.yml.dist --suite=ps_dictionary --strict --colors --no-interaction --format=progress
run_step "Behat suite: ps_feature" ./vendor/bin/behat --config behat.yml.dist --suite=ps_feature --strict --colors --no-interaction --format=progress
run_step "Behat suite: ps_offer_reference" ./vendor/bin/behat --config behat.yml.dist --suite=ps_offer_reference --strict --colors --no-interaction --format=progress

run_step "Engine: ps_dictionary" bash web/modules/custom/ps_dictionary/tests/e2e_dictionary.sh ensure_type regression_suite_type '' 'Regression Suite Type' 0
run_step "Engine: ps_feature" bash web/modules/custom/ps_feature/tests/e2e_feature.sh feature_type_exists '' '' '' flag
run_step "Engine: ps_offer reference" bash web/modules/custom/ps_offer/tests/e2e_offer_reference.sh 1 LOC BUR
run_step "Engine: ps_offer validation" bash web/modules/custom/ps_offer/tests/e2e_offer_validation.sh 1 0 draft offer

ps_success "Module regression completed"
