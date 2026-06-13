#!/usr/bin/env bash
# shellcheck disable=SC1091
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)/_core/_source.sh"

# Import or export BNP RBAC role config (post PS modules install).

readonly RBAC_DRUSH_SOURCE="modules/custom/bnp_admin/config/rbac"
readonly RBAC_HOST_DIR="${PS_WEB_DIR}/modules/custom/bnp_admin/config/rbac"
readonly RBAC_ROLES=(
  administrator
  site_admin
  content_admin
  content_editor
  seo_admin
  translate_admin
  translate_editor
)

show_help() {
  cat <<'EOF'
RBAC Sync - Import or export BNP role permissions

Usage: scripts/main.sh drupal rbac-sync [OPTIONS]

Options:
  --export    Export current role config to bnp_admin/config/rbac/
  --dry-run   Show what would be imported (no changes)
  -h, --help  Show this help

Import (default):
  Applies user.role.*.yml from bnp_admin/config/rbac/ after all PS modules
  are enabled. Safe to re-run (partial config import).

Export:
  Dumps the 7 baseline roles from the active site into config/rbac/.

Prerequisites:
  - Docker running, Drupal bootstrapped
  - PS modules enabled (ps_core, ps_offer, ps_context, ps_seo, ps_search, …)

Examples:
  scripts/main.sh drupal rbac-sync
  scripts/main.sh drupal rbac-sync --export
  make rbac-sync
EOF
}

MODE="import"
DRY_RUN=0

while [[ $# -gt 0 ]]; do
  case "$1" in
    --export)
      MODE="export"
      shift
      ;;
    --dry-run)
      DRY_RUN=1
      shift
      ;;
    -h|--help)
      show_help
      exit 0
      ;;
    *)
      ps_die "Unknown option: $1"
      ;;
  esac
done

ps_header "Drupal: RBAC sync (${MODE})"

ps_require_cmd docker
ps_in_docker || ps_die "Docker containers not running. Start them first: make up"
ps_drush_bootstrapped || ps_die "Drupal is not installed."

if [[ "${MODE}" == "export" ]]; then
  ps_info "Exporting role config to ${RBAC_HOST_DIR}..."
  EXPORT_DIR="${RBAC_HOST_DIR}"
  mkdir -p "${EXPORT_DIR}"

  for role in "${RBAC_ROLES[@]}"; do
    config_name="user.role.${role}"
    target="${EXPORT_DIR}/${config_name}.yml"
    if ! ps_drush config:get "${config_name}" --format=yaml > "${target}"; then
      ps_die "Failed to export ${config_name}"
    fi
    ps_success "Exported ${config_name}"
  done

  ps_success "RBAC export complete (${#RBAC_ROLES[@]} roles)"
  exit 0
fi

ps_info "Checking RBAC source..."
ps_require_file "${RBAC_HOST_DIR}/user.role.content_editor.yml"

if [[ ${DRY_RUN} -eq 1 ]]; then
  ps_info "Dry-run: listing configs in ${RBAC_DRUSH_SOURCE}"
  for role in "${RBAC_ROLES[@]}"; do
    echo "  - user.role.${role}"
  done
  exit 0
fi

ps_info "Importing RBAC roles (partial)..."
ps_retry 2 2 ps_drush config:import --partial --source="${RBAC_DRUSH_SOURCE}" -y
ps_drush_cr
ps_success "RBAC roles imported"

ps_info "Verifying key permissions..."
MISSING=0
check_perm() {
  local role="$1"
  local perm="$2"
  if ! ps_drush ev "echo \\Drupal\\user\\Entity\\Role::load('${role}')?->hasPermission('${perm}') ? 'yes' : 'no';" 2>/dev/null | grep -q '^yes$'; then
    ps_warn "Role ${role} missing permission: ${perm}"
    MISSING=$((MISSING + 1))
  fi
}

check_perm content_editor "access ps_core hub"
check_perm content_editor "create offer content"
check_perm content_admin "administer ps_core"
check_perm site_admin "administer ps_context matrix"
check_perm seo_admin "administer ps_seo"

if [[ ${MISSING} -gt 0 ]]; then
  ps_warn "${MISSING} permission check(s) failed — ensure all PS modules are enabled"
  exit 1
fi

ps_success "RBAC sync complete"
