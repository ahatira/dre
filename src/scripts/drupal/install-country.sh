#!/usr/bin/env bash
# shellcheck disable=SC1091
# Installs one Property Search multisite country (shell only — no demo, no CRM XML).
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)/_core/bootstrap.sh"

ps_install_country_site() {
  local country="$1"
  local site_name="$2"

  export PS_INSTALL_SKIP_CONTAINER_CR=1

  ps_drush_for_country "${country}"
  ps_header "Install country ${country} (${PS_DRUSH_ALIAS} → $(ps_site_uri "${country}"))"

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

  ps_info "Drupal site:install (locale=${default_lang})..."
  ps_retry 2 3 ps_drush site:install minimal \
    --site-name="${site_name}" \
    --account-name="${ADMIN_USER}" \
    --account-pass="${ADMIN_PASS}" \
    --account-mail="${ADMIN_MAIL}" \
    --locale="${default_lang}" \
    --yes

  ps_enable_memcache_if_available

  # Orphan update.settings from a partial install blocks bnp_admin (update is a dependency).
  if ! ps_drush pm:list --status=enabled --filter=update --format=list 2>/dev/null | grep -q '^update$'; then
    ps_drush config:delete update.settings -y 2>/dev/null || true
  else
    ps_drush pm:uninstall update -y 2>/dev/null || true
    ps_drush config:delete update.settings -y 2>/dev/null || true
    ps_drush_cr
  fi

  ps_retry 2 2 ps_drush theme:enable -y ui_suite_bnp
  ps_drush en -y seckit 2>/dev/null || ps_warn "Some essential modules not available"

  ps_drush theme:enable -y gin 2>/dev/null || ps_warn "Gin theme not available"
  ps_enable_module_robust bnp_admin 2 2 || ps_die "bnp_admin could not be enabled"
  ps_drush_cr
  ps_add_site_languages "${country}"
  ps_apply_site_language_negotiation "${country}"
  ps_retry 2 2 ps_drush en -y bnp_editor

  if [[ ${ENABLE_DEV:-0} -eq 1 ]]; then
    ps_drush en -y devel devel_generate stage_file_proxy 2>/dev/null || ps_warn "Dev modules not available"
  fi

  ps_info "Enabling PS modules..."
  ps_ensure_telephone_field_stack || ps_die "Telephone field stack not ready"
  ps_retry 2 2 ps_drush en -y ps_core ps_dictionary ps_agent ps_feature
  ps_retry 2 2 ps_drush en -y ps_surface entity_browser_generic_embed bnp_media ps_media
  ps_ensure_entity_browser_stack || ps_die "Entity Browser stack not ready"
  ps_ensure_bnp_media_foundation || ps_die "BNP Media foundation not ready"
  ps_retry 2 2 ps_drush en -y inline_form_errors webform webform_ui
  ps_drush entity:delete webform contact -y 2>/dev/null || true
  ps_drush config:delete webform.webform.contact -y 2>/dev/null || true
  ps_ensure_entity_browser_stack || ps_die "Entity Browser stack not ready before ps_form"
  ps_retry 2 2 ps_drush en -y ps_form
  # shellcheck source=/dev/null
  source "${PS_CORE_DIR}/config-sync.sh"
  ps_import_form_cmi_from_site_config
  ps_info "Preparing ps_offer dependencies..."
  ps_retry 2 2 ps_drush en -y ps_favorite ps_diagnostic layout_builder layout_discovery
  ps_recover_ps_offer_if_partial
  ps_enable_module_robust ps_offer 2 2 || ps_die "ps_offer could not be enabled"
  ps_drush ev 'require_once DRUPAL_ROOT . "/modules/custom/ps_offer/ps_offer.install"; ps_offer_apply_full_layout_display(); echo "ps_offer layout OK\n";' \
    || ps_die "ps_offer layout apply failed"
  ps_drush_cr
  ps_verify_ps_offer_install || ps_die "ps_offer verification failed"
  ps_apply_google_maps_api_key
  ps_retry 2 2 ps_drush en -y symfony_mailer mailer_override ps_context 2>/dev/null || true
  ps_drush user:role:add administrator "${ADMIN_USER}" -y 2>/dev/null || true

  ps_info "Importing dictionary..."
  ps_retry 2 2 ps_drush ps:dictionary:import -y || ps_warn "Dictionary import warnings"

  ps_retry 2 2 ps_drush en -y ps_compare
  ps_retry 2 2 ps_drush en -y search_api search_api_solr || ps_warn "search_api / search_api_solr enable had warnings"
  ps_refresh_field_type_cache
  ps_retry 2 2 ps_drush en -y ps_search || ps_warn "ps_search enable had warnings — search degraded until Solr is configured"
  ps_ensure_ps_search_stack
  ps_drush_cr
  ps_retry 2 2 ps_drush en -y ps_seo
  ps_ensure_ps_search_stack

  ps_info "Enabling CRM import pipeline (ps_migrate)..."
  ps_retry 2 2 ps_drush en -y migrate migrate_plus migrate_tools file ps_migrate

  ps_retry 2 2 ps_drush en -y ps_block ps_homepage
  ps_drush en -y advanced_mega_menu menu_link_attributes languageicons social_media_links content_translation layout_builder path_alias 2>/dev/null || true

  ps_ensure_ps_search_stack || true
  ps_refresh_field_type_cache
  ps_retry 2 2 ps_drush theme:enable -y ps_theme
  ps_drush config:set -y system.theme default ps_theme
  ps_drush config:set -y system.site name "${site_name}"
  ps_drush config:set -y system.site slogan "Real Estate for a Changing World"

  ps_drush ev '\Drupal::service("ps_core.ps_theme_shell_installer")->applyShellInstallConfig(); echo "ps_theme shell OK\n";' \
    || ps_die "ps_theme shell install failed"
  ps_retry 2 2 ps_drush en -y prevent_homepage_deletion
  ps_drush ev '\Drupal::service("ps_homepage.shell_installer")->install(); echo "ps_homepage shell OK\n";' \
    || ps_die "ps_homepage shell install failed"
  ps_drush_cr

  ps_import_all_translations_batch "${country}"
  ps_drush_cr

  # Fresh shell install has an empty index — skip Solr clear (needs a live connector).
  ps_drush search-api:rebuild-tracker offers -y 2>/dev/null || true
  ps_info "Solr index skipped on shell install (run: make import ${country})"

  ps_info "Rebuilding content permissions..."
  ps_drush_rebuild_permissions

  ps_drush_sync_container_cr
  unset PS_INSTALL_SKIP_CONTAINER_CR

  ps_success "Shell install complete: ${country}"
  ps_info "Next: make import ${country}  |  make demo ${country}"
}
