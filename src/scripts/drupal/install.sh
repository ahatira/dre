#!/usr/bin/env bash
# shellcheck disable=SC1091
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)/_core/_source.sh"

SITE_NAME="${SITE_NAME:-PS Project}"
ADMIN_USER="${ADMIN_USER:-admin}"
ADMIN_PASS="${ADMIN_PASS:-admin}"
ADMIN_MAIL="${ADMIN_MAIL:-admin@example.com}"
DB_NAME="${DB_NAME:-drupal}"
DB_USER="${DB_USER:-drupal}"
DB_PASS="${DB_PASS:-drupal}"
DB_HOST="${DB_HOST:-postgres}"
DB_PORT="${DB_PORT:-5432}"
FORCE_INSTALL="0"
SKIP_DICTIONARY_IMPORT="0"

import_ps_module_translations_for_active_languages() {
  local active_langcodes_raw
  local -A active_langcodes=()
  local -a po_files=()
  local imported_count=0
  local skipped_count=0
  local failed_count=0

  active_langcodes_raw="$(ps_drush ev 'echo implode(PHP_EOL, array_keys(\Drupal::languageManager()->getLanguages()));')"
  while IFS= read -r langcode; do
    [[ -n "${langcode}" ]] && active_langcodes["${langcode}"]=1
  done <<< "${active_langcodes_raw}"

  if [[ ${#active_langcodes[@]} -eq 0 ]]; then
    ps_warn "No active languages detected, skipping PS module translations import"
    return 0
  fi

  mapfile -t po_files < <(ps_docker_exec_php "find web/modules/custom -path '*/translations/*.po' -name 'ps_*.*.po' | sort")
  if [[ ${#po_files[@]} -eq 0 ]]; then
    ps_warn "No PS module translation files found under web/modules/custom/*/translations"
    return 0
  fi

  for po_file in "${po_files[@]}"; do
    local filename
    local langcode
    filename="$(basename "${po_file}")"
    langcode="${filename%.po}"
    langcode="${langcode##*.}"

    if [[ -z "${active_langcodes[${langcode}]:-}" ]]; then
      skipped_count=$((skipped_count + 1))
      continue
    fi

    if ps_drush locale:import "${langcode}" "/var/www/html/${po_file}" --type=customized --override=all -y >/dev/null; then
      imported_count=$((imported_count + 1))
    else
      ps_warn "Failed to import ${po_file} for active language ${langcode}"
      failed_count=$((failed_count + 1))
    fi
  done

  ps_info "PS translations import summary: imported=${imported_count}, skipped_inactive_language=${skipped_count}, failed=${failed_count}"

  if [[ ${failed_count} -gt 0 ]]; then
    return 1
  fi

  return 0
}

usage() {
  cat <<'EOF'
Usage: src/scripts/drupal/install.sh [--force] [--skip-dictionary-import]

Options:
  --force                   Force reinstall (drop/create DB + site:install)
  --skip-dictionary-import  Skip dictionary CSV import during installation
EOF
}

while [[ $# -gt 0 ]]; do
  case "$1" in
    --force)
      FORCE_INSTALL="1"
      ;;
    --skip-dictionary-import)
      SKIP_DICTIONARY_IMPORT="1"
      ;;
    -h|--help)
      usage
      exit 0
      ;;
    *)
      ps_die "Unknown option: $1"
      ;;
  esac
  shift
done

ps_header "Drupal Install"
ps_require_cmd docker
ps_require_file "${PS_DOCKER_COMPOSE_FILE}"

ps_timed_run "Start Docker services" ps_retry 3 2 ps_docker_up

ps_timed_run "Composer install in PHP container" ps_retry 3 3 ps_docker_exec_php "COMPOSER_PROCESS_TIMEOUT=2000 composer install --no-interaction --prefer-dist"

if ! ps_docker_exec_php "test -x vendor/bin/drush"; then
  ps_timed_run "Install Drush" ps_retry 3 3 ps_docker_exec_php "COMPOSER_PROCESS_TIMEOUT=2000 composer require --dev --no-interaction --prefer-dist drush/drush:^13"
fi

if ps_drush_bootstrapped && [[ "${FORCE_INSTALL}" != "1" ]]; then
  ps_warn "Drupal is already installed. Use --force to reinstall."
else
  ps_timed_run "Recreate PostgreSQL database" ps_retry 2 2 ps_db_recreate_postgres_database "${DB_USER}" "${DB_NAME}"

  ps_timed_run "Prepare settings.php" ps_docker_exec_php "cp web/sites/default/default.settings.php web/sites/default/settings.php && mkdir -p web/sites/default/files && chmod 664 web/sites/default/settings.php && chmod 775 web/sites/default/files"

  ps_timed_run "Run Drupal site:install" ps_retry 2 3 ps_docker_exec_php "vendor/bin/drush site:install minimal --db-url=pgsql://${DB_USER}:${DB_PASS}@${DB_HOST}:${DB_PORT}/${DB_NAME} --account-name=${ADMIN_USER} --account-pass=${ADMIN_PASS} --account-mail=${ADMIN_MAIL} --site-name=\"${SITE_NAME}\" -y"
fi

ps_timed_run "Enable admin theme" ps_drush theme:enable -y gin || true
ps_timed_run "Set admin theme" ps_drush cset -y system.theme admin gin || true

ps_timed_run "Enable base contrib modules" ps_retry 2 2 ps_drush en -y token pathauto scheduler language locale content_translation config_translation \
  metatag schema_metatag metatag_open_graph metatag_twitter_cards redirect simple_sitemap \
  inline_entity_form field_group \
  media media_library embed entity_embed editor \
  entity_browser entity_browser_enhanced entity_browser_entity_form \
  dropzonejs dropzonejs_eb_widget \
  link address geofield entity_reference_revisions geocoder geocoder_field geocoder_address geocoder_geofield \
  geofield_map search_api search_api_solr facets better_exposed_filters facets_exposed_filters \
  datetime path field options telephone image crop focal_point \
  admin_toolbar admin_toolbar_tools module_filter \
  devel devel_generate stage_file_proxy \
  config_split config_ignore \
  altcha honeypot seckit captcha

ps_timed_run "Enable custom PS modules" ps_retry 2 2 ps_drush en -y ps_core ps_dictionary ps_agent ps_feature
ps_timed_run "Enable ps_surface" ps_retry 2 2 ps_drush en -y ps_surface
ps_timed_run "Enable entity_browser_generic_embed" ps_retry 2 2 ps_drush en -y entity_browser_generic_embed
ps_timed_run "Enable bnp_media" ps_retry 2 2 ps_drush en -y bnp_media
ps_timed_run "Enable ps_media" ps_retry 2 2 ps_drush en -y ps_media
ps_timed_run "Enable ps_offer" ps_retry 2 2 ps_drush en -y ps_offer
ps_timed_run "Enable ps_context" ps_retry 2 2 ps_drush en -y ps_context
ps_timed_run "Enable ps_search" ps_retry 2 2 ps_drush en -y ps_search

ps_info "Assigning ps_admin role"
ps_drush user:role:add ps_admin "${ADMIN_USER}" -y || true

ps_info "Ensuring French language exists"
if ps_drush language:info | grep -q "French (fr)"; then
  ps_info "French language already enabled"
else
  ps_drush language:add fr -y
fi

ps_timed_run "Import PS module translations (active languages only)" ps_retry 2 2 import_ps_module_translations_for_active_languages

if [[ "${SKIP_DICTIONARY_IMPORT}" == "1" ]]; then
  ps_warn "Skipping dictionary import (--skip-dictionary-import)"
else
  ps_timed_run "Import dictionary data" ps_retry 2 2 ps_drush ps:dictionary:import
fi

ps_timed_run "Run cache rebuild" ps_retry 2 2 ps_drush cr

ps_success "Installation completed"
ps_drush status --fields=bootstrap,db-status,drupal-version,drush-version

echo
echo "Back-office: http://localhost:8080/admin"
echo "Login: ${ADMIN_USER} / ${ADMIN_PASS}"
