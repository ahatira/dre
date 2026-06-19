#!/usr/bin/env bash
# shellcheck disable=SC1091
# Downloads contrib/core UI translations into src/translations/contrib/ (dev only).
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)/_core/bootstrap.sh"

show_help() {
  cat <<'EOF'
Fetch contrib/core interface translations (dev only).

Downloads from localize.drupal.org via Drush, keeps .po files flat in
src/translations/contrib/ for version control. Custom project strings
(ps_*, bnp_*, ps_theme) are not fetched here.

Usage: scripts/main.sh drupal translations-fetch [country] [lang...]

  country   Reference site with all contrib modules enabled (default: com)
  lang      Optional language filter (default: union of countries.yml)

Prerequisites:
  make install [country]   — site bootstrapped with full module stack
  APP_ENV=dev

Examples:
  make translations-fetch
  make translations-fetch com
  make translations-fetch com fr nl

After fetch: review git diff and commit src/translations/contrib/
EOF
}

COUNTRY="com"
FILTER_LANGS=()

while [[ $# -gt 0 ]]; do
  case "$1" in
    -h|--help) show_help; exit 0 ;;
    com|be|es|fr|ie|it|lu|nl|pl)
      COUNTRY="$1"
      shift
      ;;
    en|fr|nl|es|it|pl|de|lb)
      FILTER_LANGS+=("$1")
      shift
      ;;
    *)
      ps_die "Unknown argument: $1 (see --help)"
      ;;
  esac
done

ps_load_config
if [[ "$(ps_env_get APP_ENV dev)" != "dev" ]]; then
  ps_die "translations-fetch is dev-only (APP_ENV=$(ps_env_get APP_ENV dev))"
fi

ps_countries_init
ps_is_country_code "${COUNTRY}" || ps_die "Unknown country: ${COUNTRY}"

ps_drush_for_country "${COUNTRY}"
ps_header "Fetch contrib translations (${COUNTRY} → ${PS_DRUSH_ALIAS})"

ps_drush_bootstrapped || ps_die "Site not installed. Run: make install ${COUNTRY}"

cache_dir="$(ps_contrib_translations_dir)"
mkdir -p "${cache_dir}"
ps_contrib_translations_flatten

ps_info "Ensuring locale module is enabled..."
ps_drush pm:enable locale -y 2>/dev/null || ps_drush en -y locale

mapfile -t ALL_LANGS < <(ps_all_site_language_codes)
TARGET_LANGS=("${ALL_LANGS[@]}")
if [[ ${#FILTER_LANGS[@]} -gt 0 ]]; then
  TARGET_LANGS=("${FILTER_LANGS[@]}")
fi

ps_info "Ensuring languages on reference site: ${TARGET_LANGS[*]}"
for lang in "${TARGET_LANGS[@]}"; do
  if ps_drush language:info 2>/dev/null | grep -q "(${lang})"; then
    continue
  fi
  ps_retry 2 2 ps_drush language:add "${lang}" --skip-translations -y \
    || ps_warn "Could not add language ${lang} on ${COUNTRY}"
done

ps_drush cr

langcodes_csv="$(IFS=,; echo "${TARGET_LANGS[*]}")"

export PS_LOCALE_FETCH=1

ps_info "Checking remote translation status (locale:check)..."
ps_drush locale:clear-status -y 2>/dev/null || true
ps_retry 2 5 ps_drush locale:check || ps_die "locale:check failed (network / localize.drupal.org)"

ps_info "Downloading translations for: ${langcodes_csv}"
ps_retry 2 10 ps_drush locale:update --langcodes="${langcodes_csv}" -y \
  || ps_die "locale:update failed"

unset PS_LOCALE_FETCH

ps_contrib_translations_flatten
ps_contrib_translations_update_manifest

po_count="$(find "${cache_dir}" -maxdepth 1 -name '*.po' 2>/dev/null | wc -l | tr -d ' ')"
ps_success "Fetch complete: ${po_count} PO file(s) in ${cache_dir}"
ps_info "Review: git status src/translations/contrib/ && git diff --stat src/translations/contrib/"
