#!/usr/bin/env bash
# shellcheck disable=SC1091
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)/_core/bootstrap.sh"

show_help() {
  cat <<'EOF'
Drush wrapper — runs commands in @ps.{country} context.

Usage: scripts/main.sh drupal drush [country] [drush args...]

Default country: com

Examples:
  scripts/main.sh drupal drush fr status
  scripts/main.sh drupal drush fr uli
  scripts/main.sh drupal drush com cex -y
  scripts/main.sh drupal drush es ps:dictionary:import -y
EOF
}

COUNTRY="${PS_COUNTRY_CODE:-com}"
ARGS=()

while [[ $# -gt 0 ]]; do
  case "$1" in
    -h|--help) show_help; exit 0 ;;
    com|be|es|fr|ie|it|lu|nl|pl)
      COUNTRY="$1"
      shift
      ;;
    *)
      ARGS+=("$1")
      shift
      ;;
  esac
done

[[ ${#ARGS[@]} -gt 0 ]] || { show_help; ps_die "Missing drush command"; }

ps_load_config
ps_resolve_runtime
ps_drush_for_country "${COUNTRY}"
ps_drush_bootstrapped || ps_die "Site ${COUNTRY} not bootstrapped"
ps_drush "${ARGS[@]}"
