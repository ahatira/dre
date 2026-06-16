#!/usr/bin/env bash
# shellcheck disable=SC1091
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)/_core/bootstrap.sh"

readonly RBAC_DIR="${PS_WEB_DIR}/modules/custom/bnp_admin/config/rbac"
readonly RBAC_ROLES=(
  administrator site_admin content_admin content_editor
  seo_admin translate_admin translate_editor
)

show_help() {
  cat <<'EOF'
RBAC sync — import or export BNP role permissions.

Usage: scripts/main.sh drupal rbac-sync [OPTIONS] [country]

Default country: com

Options:
  --export   Export roles to bnp_admin/config/rbac/
  --dry-run  List configs that would be imported
  -h, --help Show this help

Not run automatically by install — call explicitly: make rbac-sync
EOF
}

MODE="import"
DRY_RUN=0
COUNTRY="${PS_COUNTRY_CODE:-com}"

while [[ $# -gt 0 ]]; do
  case "$1" in
    --export) MODE="export"; shift ;;
    --dry-run) DRY_RUN=1; shift ;;
    -h|--help) show_help; exit 0 ;;
    com|be|es|fr|ie|it|lu|nl|pl) COUNTRY="$1"; shift ;;
    *) ps_die "Unknown option: $1" ;;
  esac
done

ps_load_config
ps_drush_for_country "${COUNTRY}"
ps_header "RBAC ${MODE} (${COUNTRY})"
ps_drush_bootstrapped || ps_die "Site not installed"

if [[ "${MODE}" == "export" ]]; then
  mkdir -p "${RBAC_DIR}"
  for role in "${RBAC_ROLES[@]}"; do
    name="user.role.${role}"
    ps_drush config:get "${name}" --format=yaml > "${RBAC_DIR}/${name}.yml" \
      || ps_die "Export failed: ${name}"
    ps_success "Exported ${name}"
  done
  ps_success "RBAC export complete"
  exit 0
fi

ps_require_file "${RBAC_DIR}/user.role.content_editor.yml"
if [[ ${DRY_RUN} -eq 1 ]]; then
  for role in "${RBAC_ROLES[@]}"; do echo "  - user.role.${role}"; done
  exit 0
fi

ps_drush ev '
  $count = \Drupal::service("bnp_admin.rbac_role_importer")->import();
  echo "Imported {$count} RBAC role configs\n";
'
ps_drush_cr
ps_success "RBAC sync complete"
