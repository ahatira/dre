#!/usr/bin/env bash
# shellcheck disable=SC1091
# Installs one Property Search multisite country (modules, theme, translations).
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)/_core/_source.sh"

ps_install_country_site() {
  local country="$1"
  local site_name="$2"

  PS_COUNTRY_CODE="${country}"
  export PS_COUNTRY_CODE
  PS_DRUSH_URI="$(ps_site_uri "${country}")"
  export PS_DRUSH_URI

  ps_header "Drupal: Installing country ${country} (${PS_DRUSH_URI})"

  if ps_drush_bootstrapped && [[ ${FORCE_INSTALL:-0} -eq 0 ]]; then
    ps_warn "Country ${country} is already installed. Use --force to reinstall."
    return 0
  fi

  local default_lang
  default_lang="$(ps_site_default_langcode "${country}")"

  if [[ ${FORCE_INSTALL:-0} -eq 1 ]]; then
    ps_info "Dropping database for ${country}..."
    local db_name upper var
    upper="$(ps_country_upper "${country}")"
    var="DB_NAME_${upper}"
    ps_load_dotenv
    db_name="${!var:-}"
    if [[ -n "${db_name}" ]]; then
      ps_docker_exec_db "psql -v ON_ERROR_STOP=1 -U \"${DB_USER:-drupal}\" -d postgres -c \"DROP DATABASE IF EXISTS ${db_name};\" -c \"CREATE DATABASE ${db_name};\""
      ps_success "Database dropped and recreated"
    else
      ps_warn "DB name not found for ${country}, trying drush sql:drop..."
      ps_retry 2 2 ps_drush sql:drop --yes || ps_die "Could not drop database for ${country}"
    fi
  fi

  ps_info "Installing Drupal minimal profile for ${country} (locale=${default_lang})..."
  ps_retry 2 3 ps_drush site:install minimal \
    --site-name="${site_name}" \
    --account-name="${ADMIN_USER}" \
    --account-pass="${ADMIN_PASS}" \
    --account-mail="${ADMIN_MAIL}" \
    --locale="${default_lang}" \
    --yes
  ps_success "Drupal core installed (${country})"

  ps_add_site_languages "${country}"

  ps_info "Disabling optional Update Status module..."
  if ps_drush pm:list --status=enabled --filter=update --format=list 2>/dev/null | grep -q '^update$'; then
    ps_drush pm:uninstall update -y || ps_warn "Update module could not be uninstalled"
    ps_drush_cr
  fi

  ps_info "Enabling base theme (ui_suite_bnp)..."
  ps_retry 2 2 ps_drush theme:enable -y ui_suite_bnp
  ps_success "Base theme enabled"

  ps_info "Enabling essential contrib modules..."
  ps_drush en -y honeypot seckit || ps_warn "Some essential modules not available"
  ps_success "Essential modules enabled"

  ps_info "Enabling BNP admin baseline..."
  ps_drush theme:enable -y gin || ps_warn "Gin theme not available"
  ps_retry 2 2 ps_drush en -y bnp_admin
  ps_success "BNP admin baseline enabled"

  ps_apply_site_language_negotiation "${country}"

  ps_info "Enabling BNP Editor..."
  ps_retry 2 2 ps_drush en -y bnp_editor
  ps_success "BNP Editor enabled"

  if [[ ${ENABLE_DEV:-0} -eq 1 ]]; then
    ps_info "Enabling development modules..."
    ps_drush en -y devel devel_generate stage_file_proxy || ps_warn "Some dev modules not available"
    ps_success "Development modules enabled"
  fi

  ps_info "Enabling PS modules..."
  ps_retry 2 2 ps_drush en -y ps_core ps_dictionary ps_agent ps_feature
  ps_retry 2 2 ps_drush en -y ps_surface
  ps_retry 2 2 ps_drush en -y entity_browser_generic_embed
  ps_retry 2 2 ps_drush en -y bnp_media ps_media
  ps_retry 2 2 ps_drush en -y inline_form_errors webform webform_ui
  ps_drush entity:delete webform contact -y || true
  ps_drush config:delete webform.webform.contact -y || true
  ps_retry 2 2 ps_drush en -y ps_form
  ps_info "Provisioning PS Form webforms..."
  ps_drush ev '$missing = \Drupal::service("ps_form.webform_provisioner")->provisionMissing(); if ($missing !== []) { echo "Created: " . implode(", ", $missing) . PHP_EOL; }'
  ps_drush ev '$missing = \Drupal::service("ps_form.webform_provisioner")->getMissingWebformIds(); if ($missing !== []) { throw new \RuntimeException("Missing PS Form webforms: " . implode(", ", $missing)); } echo "PS Form webforms OK" . PHP_EOL;' || ps_die "PS Form webforms were not provisioned"
  ps_retry 2 2 ps_drush en -y ps_offer
  ps_retry 2 2 ps_drush en -y symfony_mailer mailer_override || ps_warn "Mail transport modules not available"
  ps_retry 2 2 ps_drush en -y ps_context
  ps_success "PS modules enabled"

  ps_info "Enabling anti-spam modules..."
  ps_retry 2 2 ps_drush en -y captcha altcha || ps_warn "Anti-spam modules not available"
  ps_success "Anti-spam configured"

  ps_info "Assigning administrator role to ${ADMIN_USER}..."
  ps_drush user:role:add administrator "${ADMIN_USER}" -y || true
  ps_success "Role assigned"

  ps_info "Importing custom module translations..."
  local imported=0 skipped=0 failed=0
  local active_langs
  active_langs="$(ps_drush ev 'echo implode(PHP_EOL, array_keys(\Drupal::languageManager()->getLanguages()));')"

  import_po_file() {
    local po_file="$1"
    local langcode="$2"
    if [[ -z "${po_file}" || -z "${langcode}" ]]; then
      return 1
    fi
    if ! echo "${active_langs}" | grep -q "^${langcode}$"; then
      skipped=$((skipped + 1))
      return 0
    fi
    if ps_drush locale:import "${langcode}" "/var/www/html/${po_file}" --type=customized --override=all -y >/dev/null 2>&1; then
      imported=$((imported + 1))
    else
      failed=$((failed + 1))
    fi
  }

  while IFS= read -r po_file; do
    [[ -z "${po_file}" ]] && continue
    local filename langcode
    filename=$(basename "${po_file}")
    langcode="${filename%.po}"
    langcode="${langcode##*.}"
    import_po_file "${po_file}" "${langcode}"
  done < <(ps_docker_exec_php "find web/modules/custom -path '*/translations/*.po' \( -name 'ps_*.*.po' -o -name 'bnp_*.*.po' \) 2>/dev/null | sort || true")

  if [[ -f "${PS_SRC_DIR}/web/themes/custom/ps_theme/translations/fr.po" ]] \
    && echo "${active_langs}" | grep -q '^fr$'; then
    import_po_file "web/themes/custom/ps_theme/translations/fr.po" "fr"
  fi

  if [[ -f "${PS_SRC_DIR}/web/themes/custom/ps_theme/translations/es.po" ]] \
    && echo "${active_langs}" | grep -q '^es$'; then
    import_po_file "web/themes/custom/ps_theme/translations/es.po" "es"
  fi

  while IFS= read -r po_file; do
    [[ -z "${po_file}" ]] && continue
    local filename langcode
    filename=$(basename "${po_file}")
    langcode="${filename#ps_theme.}"
    langcode="${langcode%.po}"
    import_po_file "${po_file}" "${langcode}"
  done < <(ps_docker_exec_php "find web/themes/custom/ps_theme/translations -name 'ps_theme.*.po' 2>/dev/null | sort || true")

  ps_info "Translations: imported=${imported}, skipped=${skipped}, failed=${failed}"
  [[ ${failed} -gt 0 ]] && ps_warn "Some translations failed to import"

  ps_info "Importing dictionary data..."
  ps_retry 2 2 ps_drush ps:dictionary:import || ps_warn "Dictionary import warnings"
  ps_success "Dictionary imported"

  ps_info "Enabling search, compare and SEO modules..."
  ps_retry 2 2 ps_drush en -y ps_compare ps_search ps_seo
  ps_success "ps_compare, ps_search and ps_seo enabled"

  ps_info "Syncing BNP RBAC roles..."
  bash "${PS_SCRIPTS_DIR}/drupal/rbac-sync.sh"

  ps_info "Enabling theme shell dependencies..."
  ps_retry 2 2 ps_drush en -y ps_block ps_homepage
  ps_drush en -y advanced_mega_menu menu_link_attributes languageicons social_media_links content_translation layout_builder path_alias || ps_warn "Some theme contrib modules not available"

  ps_info "Enabling Property Search front theme..."
  ps_retry 2 2 ps_drush theme:enable -y ps_theme
  ps_retry 2 2 ps_drush config:import --partial --source=themes/custom/ps_theme/config/install -y
  ps_drush config:set -y system.theme default ps_theme
  ps_drush config:set -y system.site slogan "Real Estate for a Changing World"
  ps_success "Front theme configured"

  ps_retry 2 2 ps_drush_cr
  ps_success "Country ${country} installation complete"
  ps_drush status --fields=bootstrap,db-status,uri,db-name
}
