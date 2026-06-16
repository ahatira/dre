#!/usr/bin/env bash
# shellcheck disable=SC1091
# Verifies one country site is clean (shell) or complete (demo / full).
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)/_core/_source.sh"

show_help() {
  cat <<'EOF'
Verify Country Site - Property Search multisite QA

Usage: scripts/main.sh drupal verify-country <country> <mode>

Modes:
  shell   Coquille sans demo (make install --minimal <country>)
  demo    Coquille + contenu demo (make demo apres shell)
  full    Shell + demo + offres sample + Solr (make post-install)

Examples:
  scripts/main.sh drupal verify-country fr shell
  scripts/main.sh drupal verify-country es demo
  make verify-country fr shell

Workflow pays par pays:
  1. make reinstall --minimal fr
  2. make verify-country fr shell
  3. make demo   (avec PS_DRUSH_URI du pays)
  4. make verify-country fr demo
  5. make post-install  (optionnel)
  6. make verify-country fr full
EOF
}

MODE=""
COUNTRY=""

while [[ $# -gt 0 ]]; do
  case "$1" in
    -h|--help)
      show_help
      exit 0
      ;;
    shell|demo|full)
      MODE="$1"
      shift
      ;;
    com|fr|be|es|ie|it|lu|nl|pl)
      COUNTRY="$1"
      shift
      ;;
    *)
      ps_die "Unknown argument: $1 (expected: <country> <shell|demo|full>)"
      ;;
  esac
done

if [[ -z "${COUNTRY}" || -z "${MODE}" ]]; then
  show_help
  ps_die "Usage: verify-country <country> <shell|demo|full>"
fi

ps_header "Verify country: ${COUNTRY} (${MODE})"

ps_require_cmd docker
ps_require_file "${PS_DOCKER_COMPOSE_FILE}"
ps_in_docker || ps_die "Docker not running. Start: make up"

PS_COUNTRY_CODE="${COUNTRY}"
export PS_COUNTRY_CODE
PS_DRUSH_URI="$(ps_site_uri "${COUNTRY}")"
export PS_DRUSH_URI

ps_info "URI: ${PS_DRUSH_URI}"

if ! ps_drush_bootstrapped; then
  ps_die "Site not bootstrapped. Run: make install --minimal ${COUNTRY}"
fi

output="$(ps_drush php:script /var/www/html/scripts/tools/verify_country_site.php "${MODE}" "${COUNTRY}" 2>&1)" || true
printf '%s\n' "${output}"

if printf '%s\n' "${output}" | grep -qE 'SUMMARY mode=.* fail=0 '; then
  ps_success "Verification passed (${COUNTRY} / ${MODE})"
else
  ps_error "Verification failed (${COUNTRY} / ${MODE})"
  exit 1
fi
