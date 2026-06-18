#!/usr/bin/env bash
# shellcheck disable=SC1091
# Installs one Property Search multisite country (shell only — no demo, no CRM XML).
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)/_core/bootstrap.sh"

ps_install_country_site() {
  local country="$1"
  local site_name="$2"

  ps_drush_for_country "${country}"
  ps_header "Install country ${country} (${PS_DRUSH_ALIAS} → $(ps_site_uri "${country}"))"

  if ps_drush_bootstrapped && [[ ${FORCE_INSTALL:-0} -eq 0 ]]; then
    ps_warn "Country ${country} already installed. Use --force to reinstall."
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

  if ps_drush pm:list --status=enabled --filter=update --format=list 2>/dev/null | grep -q '^update$'; then
    ps_drush pm:uninstall update -y 2>/dev/null || true
    ps_drush_cr
  fi

  ps_retry 2 2 ps_drush theme:enable -y ui_suite_bnp
  ps_drush en -y honeypot seckit 2>/dev/null || ps_warn "Some essential modules not available"

  ps_drush theme:enable -y gin 2>/dev/null || ps_warn "Gin theme not available"
  ps_enable_module_robust bnp_admin 2 2 || ps_die "bnp_admin could not be enabled"
  ps_add_site_languages "${country}"
  ps_retry 2 2 ps_drush en -y bnp_editor

  if [[ ${ENABLE_DEV:-0} -eq 1 ]]; then
    ps_drush en -y devel devel_generate stage_file_proxy 2>/dev/null || ps_warn "Dev modules not available"
  fi

  ps_info "Enabling PS modules..."
  ps_retry 2 2 ps_drush en -y ps_core ps_dictionary ps_agent ps_feature
  ps_apply_site_language_negotiation "${country}"
  ps_retry 2 2 ps_drush en -y ps_surface entity_browser_generic_embed bnp_media ps_media
  ps_retry 2 2 ps_drush en -y inline_form_errors webform webform_ui
  ps_drush entity:delete webform contact -y 2>/dev/null || true
  ps_drush config:delete webform.webform.contact -y 2>/dev/null || true
  ps_retry 2 2 ps_drush en -y ps_form
  ps_drush_cr
  ps_enable_module_robust ps_offer 2 2 || ps_die "ps_offer could not be enabled"
  ps_verify_ps_offer_install || ps_die "ps_offer verification failed"
  ps_apply_google_maps_api_key
  ps_retry 2 2 ps_drush en -y symfony_mailer mailer_override ps_context 2>/dev/null || true
  ps_retry 2 2 ps_drush en -y captcha altcha 2>/dev/null || true
  ps_drush user:role:add administrator "${ADMIN_USER}" -y 2>/dev/null || true

  ps_info "Importing dictionary..."
  ps_retry 2 2 ps_drush ps:dictionary:import -y || ps_warn "Dictionary import warnings"

  ps_retry 2 2 ps_drush en -y ps_compare ps_search ps_seo
  ps_retry 2 2 ps_drush en -y ps_favorite migrate migrate_plus migrate_tools ps_migrate

  ps_retry 2 2 ps_drush en -y ps_block ps_homepage
  ps_drush en -y advanced_mega_menu menu_link_attributes languageicons social_media_links content_translation layout_builder path_alias 2>/dev/null || true

  ps_retry 2 2 ps_drush theme:enable -y ps_theme
  ps_drush config:set -y system.theme default ps_theme
  ps_drush config:set -y system.site name "${site_name}"
  ps_drush config:set -y system.site slogan "Real Estate for a Changing World"
  ps_drush_cr

  ps_drush ev '\Drupal::service("ps_core.ps_theme_shell_installer")->applyShellInstallConfig(); echo "ps_theme shell OK\n";' \
    || ps_die "ps_theme shell install failed"
  ps_retry 2 2 ps_drush en -y prevent_homepage_deletion
  ps_drush ev '\Drupal::service("ps_homepage.shell_installer")->install(); echo "ps_homepage shell OK\n";' \
    || ps_die "ps_homepage shell install failed"

  ps_import_module_translations

  ps_drush search-api:clear offers -y 2>/dev/null || true
  ps_drush search-api:rebuild-tracker offers -y 2>/dev/null || true
  ps_drush search-api:index offers -y 2>/dev/null || ps_warn "Solr index empty until make import"

  ps_import_active_language_config_overrides "${country}"
  ps_retry 2 2 ps_drush_cr

  ps_success "Shell install complete: ${country}"
  ps_info "Next: make import ${country}  |  make demo ${country}"
}
