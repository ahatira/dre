#!/usr/bin/env bash
# shellcheck disable=SC1091
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)/_core/_source.sh"

show_help() {
  cat <<'EOF'
Post-Install Script (deprecated)

Install is now split into three independent steps:

  1. make install [country]     Greenfield shell (modules, theme, search, SEO)
  2. make import [country]      Sample CRM XML (offers + Solr index)
  3. make demo [country]        Demo menus, homepage, mega-menu CMI

Usage: scripts/main.sh drupal post-install

This command runs make import then make demo for the current PS_COUNTRY_CODE
(or com). Prefer the separate targets above.

Options:
  -h, --help        Show this help
EOF
}

while [[ $# -gt 0 ]]; do
  case "$1" in
    -h|--help)
      show_help
      exit 0
      ;;
    *)
      ps_die "Unknown option: $1 (post-install is deprecated — use import + demo)"
      ;;
  esac
done

country="${PS_COUNTRY_CODE:-com}"
ps_is_country_code "${country}" || ps_die "Unknown country: ${country}"

ps_warn "post-install is deprecated — running import + demo for ${country}"
ps_warn "Prefer: make import ${country} && make demo ${country}"

PS_COUNTRY_CODE="${country}"
export PS_COUNTRY_CODE
PS_DRUSH_URI="$(ps_site_uri "${country}")"
export PS_DRUSH_URI

bash "${PS_SCRIPTS_DIR}/drupal/import.sh" "${country}"
bash "${PS_SCRIPTS_DIR}/drupal/demo.sh" "${country}"
