#!/usr/bin/env bash
# shellcheck disable=SC1091
# Audits demo/homepage translation assets (static) and optional live site state.
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)/_core/_source.sh"

show_help() {
  cat <<'EOF'
Audit demo and homepage translations (all countries / languages).

Usage: scripts/main.sh drupal audit-translations [--live]

Static checks (default):
  - language.negotiation per country
  - homepage_block_defaults overlays (es, it, nl, pl, lb)
  - ps_demo.settings front_paths (removed — aliases from ps_demo.homepage + export)
  - DemoTranslationSync (dynamic per enabled languages)

Live checks (--live, requires bootstrapped sites):
  - drush audit per http://{country}.localhost:8080

Examples:
  scripts/main.sh drupal audit-translations
  scripts/main.sh drupal audit-translations --live
EOF
}

LIVE=0
while [[ $# -gt 0 ]]; do
  case "$1" in
    --live) LIVE=1; shift ;;
    -h|--help) show_help; exit 0 ;;
    *) ps_die "Unknown option: $1" ;;
  esac
done

ps_header "Translation audit (static)"

HOMEPAGE_DATA="${PS_SRC_DIR}/web/modules/custom/ps_homepage/data"
DEMO_SETTINGS="${PS_SRC_DIR}/web/modules/custom/ps_demo/config/install/ps_demo.settings.yml"
LANG_ENV="${PS_SRC_DIR}/config/env/languages"
DEMO_HOMEPAGE="${PS_SRC_DIR}/web/modules/custom/ps_demo/config/install/ps_demo.homepage.yml"

printf '%-6s %-12s %-8s %-8s %-8s %-8s %-8s\n' "COUNTRY" "LANGS" "nego" "hp_es" "hp_it" "hp_nl" "hp_pl"
for country in com fr be es ie it lu nl pl; do
  langs="$(ps_site_language_codes "${country}" | tr ' ' ',')"
  nego="no"
  [[ -f "${LANG_ENV}/${country}/language.negotiation.yml" ]] && nego="yes"
  hp_es="no"; hp_it="no"; hp_nl="no"; hp_pl="no"
  [[ -f "${HOMEPAGE_DATA}/homepage_block_defaults.es.yml" ]] && hp_es="yes"
  [[ -f "${HOMEPAGE_DATA}/homepage_block_defaults.it.yml" ]] && hp_it="yes"
  [[ -f "${HOMEPAGE_DATA}/homepage_block_defaults.nl.yml" ]] && hp_nl="yes"
  [[ -f "${HOMEPAGE_DATA}/homepage_block_defaults.pl.yml" ]] && hp_pl="yes"
  printf '%-6s %-12s %-8s %-8s %-8s %-8s %-8s\n' "$country" "$langs" "$nego" "$hp_es" "$hp_it" "$hp_nl" "$hp_pl"
done

echo ""
ps_info "ps_demo.homepage paths (install config):"
grep -A20 '^  path:' "${DEMO_HOMEPAGE}" || true

echo ""
ps_info "DemoTranslationSync: copies missing menu/homepage translations for each enabled site language."

if [[ ${LIVE} -eq 1 ]]; then
  ps_header "Translation audit (live sites)"
  ps_require_cmd docker
  ps_in_docker || ps_die "Docker not running"
  for country in com fr be es ie it lu nl pl; do
    uri="http://${country}.localhost:8080"
    echo "----- ${country} (${uri}) -----"
    export PS_DRUSH_URI="${uri}"
    if ! ps_drush ev 'echo \Drupal::languageManager()->getDefaultLanguage()->getId();' 2>/dev/null; then
      ps_warn "Site not bootstrapped — run: make install ${country}"
      continue
    fi
    ps_drush php:script /var/www/html/scripts/tools/audit_demo_translations.php 2>&1 || true
    echo ""
  done
fi

ps_success "Translation audit complete"
