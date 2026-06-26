#!/usr/bin/env bash
# shellcheck disable=SC1091
# Installs one Property Search country from config/sites/{code} (no demo, no CRM import).
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)/_core/bootstrap.sh"
# shellcheck source=/dev/null
source "${PS_CORE_DIR}/config-sync.sh"

ps_install_country_from_conf() {
  local country="$1"
  local site_name="$2"

  export PS_INSTALL_SKIP_CONTAINER_CR=1

  ps_drush_for_country "${country}"
  ps_header "Install from config ${country} (${PS_DRUSH_ALIAS} → $(ps_site_uri "${country}"))"

  if ps_drush_bootstrapped && [[ ${FORCE_INSTALL:-0} -eq 0 ]]; then
    ps_warn "Country ${country} already installed. Use --force to reinstall."
    unset PS_INSTALL_SKIP_CONTAINER_CR
    return 0
  fi

  local default_lang
  default_lang="$(ps_site_default_langcode "${country}")"

  if [[ ${FORCE_INSTALL:-0} -eq 1 ]]; then
    ps_info "Recreating database (drush sql:create)..."
    ps_retry 2 2 ps_drush_sql_create || ps_die "Could not recreate database for ${country}"
  fi

  ps_require_config_sync
  ps_site_install_from_config "${site_name}" "${default_lang}"

  ps_uninstall_update_module_if_present
  ps_enable_memcache_if_available
  ps_apply_config_ignore_settings_from_env
  ps_enable_seo_modules_post_import
  ps_add_site_languages "${country}"
  ps_apply_site_language_negotiation "${country}"

  ps_info "Running database updates..."
  ps_drush updatedb -y

  ps_info "Importing dictionary..."
  ps_retry 2 2 ps_drush ps:dictionary:import -y || ps_warn "Dictionary import warnings"

  ps_apply_google_maps_api_key

  ps_drush user:role:add administrator "${ADMIN_USER}" -y 2>/dev/null || true

  ps_install_homepage_from_configuration

  ps_info "Importing translations (contrib, custom, config overrides)..."
  ps_enable_locale_and_import_contrib_translations
  ps_import_module_translations
  ps_import_active_language_config_overrides "${country}"
  ps_drush_cr

  ps_drush search-api:clear offers -y 2>/dev/null || true
  ps_drush search-api:rebuild-tracker offers -y 2>/dev/null || true
  ps_drush search-api:index offers -y 2>/dev/null || ps_warn "Solr index empty until make import"

  ps_info "Rebuilding content permissions..."
  ps_drush_rebuild_permissions

  ps_drush_sync_container_cr
  unset PS_INSTALL_SKIP_CONTAINER_CR

  ps_success "Install from config complete: ${country}"
  ps_info "Next: make rbac-sync ${country} | make import ${country} | make demo ${country}"
}
