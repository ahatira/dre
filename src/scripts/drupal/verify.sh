#!/usr/bin/env bash
# shellcheck disable=SC1091
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)/_core/_source.sh"

HTTP_PORT="${PS_HTTP_PORT:-8080}"
BASE_URL="http://localhost:${HTTP_PORT}"

ps_header "Stack Verification"
ps_require_cmd docker
ps_require_cmd curl
ps_require_file "${PS_DOCKER_COMPOSE_FILE}"

ps_info "Checking compose stack"
ps_docker_compose ps

if ! curl -s --max-time 5 -o /dev/null "${BASE_URL}"; then
  if curl -s --max-time 5 -o /dev/null "http://localhost:8080"; then
    HTTP_PORT="8080"
    BASE_URL="http://localhost:${HTTP_PORT}"
    ps_warn "Configured port unavailable, falling back to ${HTTP_PORT}"
  fi
fi

ps_info "Checking Drupal bootstrap"
ps_drush status --fields=bootstrap,db-status,drupal-version,drush-version

ps_info "Checking ps_dictionary resolver service"
ps_drush php:eval '$r=\Drupal::service("ps_dictionary.resolver"); var_export(["label" => $r->resolveLabel("asset_type","BUR"), "code" => $r->resolveCode("operation_type","Vente"), "all" => $r->all("currency"), "valid" => $r->isValid("currency","EUR")]);'

ps_info "Running critical unit tests"
ps_docker_exec_php "vendor/bin/phpunit -c web/core/phpunit.xml.dist web/modules/custom/ps_dictionary/tests/src/Unit/DictionaryResolverTest.php"
ps_docker_exec_php "vendor/bin/phpunit -c web/core/phpunit.xml.dist web/modules/custom/ps_core/tests/src/Unit/AuditLoggerTest.php"

ps_info "Running HTTP smoke checks"
curl -I --max-time 20 "${BASE_URL}/admin/ps" | head -n 1
curl -I --max-time 20 "${BASE_URL}/admin/ps/health" | head -n 1

ps_info "Generating admin login link"
ps_drush uli

ps_success "Verification completed"
