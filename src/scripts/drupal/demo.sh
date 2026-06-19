#!/usr/bin/env bash
# shellcheck disable=SC1091
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)/_core/bootstrap.sh"

show_help() {
  cat <<'EOF'
Demo — ps_demo content (menus, homepage, mega-menu).

Usage: scripts/main.sh drupal demo [country]

Default country: com

Prerequisites: make install [country]
Does not import CRM XML — use make import separately.
EOF
}

COUNTRY="${PS_COUNTRY_CODE:-com}"

while [[ $# -gt 0 ]]; do
  case "$1" in
    -h|--help) show_help; exit 0 ;;
    com|be|es|fr|ie|it|lu|nl|pl) COUNTRY="$1"; shift ;;
    *) ps_die "Unknown option or country: $1" ;;
  esac
done

ps_is_country_code "${COUNTRY}" || ps_die "Unknown country: ${COUNTRY}"
ps_load_config
ps_drush_for_country "${COUNTRY}"

ps_header "Demo content (${COUNTRY})"
ps_drush_bootstrapped || ps_die "Site not installed. Run: make install ${COUNTRY}"

ps_drush en -y default_content ps_block ps_homepage advanced_mega_menu menu_link_attributes \
  languageicons social_media_links content_translation layout_builder path_alias 2>/dev/null || true

if ! ps_drush pm:list --status=enabled --filter=ps_demo --format=list 2>/dev/null | grep -q '^ps_demo$'; then
  ps_retry 2 2 ps_drush en -y ps_demo
fi

ps_drush ev '
use Drupal\ps_demo\Service\DemoInstaller;
DemoInstaller::create(\Drupal::getContainer())->finalizeInstall();
echo "DemoInstaller complete\n";
' || ps_warn "Demo finalize had warnings"

ps_apply_site_language_negotiation "${COUNTRY}"

ps_import_contrib_translations
ps_import_module_translations
ps_import_active_language_config_overrides "${COUNTRY}"

ps_drush_cr
ps_success "Demo ready: ${PS_DRUSH_ALIAS} ($(ps_site_uri "${COUNTRY}"))"
