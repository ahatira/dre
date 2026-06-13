#!/usr/bin/env bash
# shellcheck disable=SC1091
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)/_core/_source.sh"

# Create one test user per BNP baseline role (for QA / recette).

readonly DEFAULT_PASS="${TEST_USER_PASS:-test}"

declare -A TEST_USERS=(
  [administrator]="admin.test"
  [site_admin]="site.admin"
  [content_admin]="content.admin"
  [content_editor]="content.editor"
  [seo_admin]="seo.admin"
  [translate_admin]="translate.admin"
  [translate_editor]="translate.editor"
)

show_help() {
  cat <<'EOF'
Create Test Users - One account per BNP baseline role

Usage: scripts/main.sh drupal create-test-users [OPTIONS]

Creates (or updates) test users with predictable credentials for QA personas.
Does not modify the main admin account created at install.

Options:
  --password=PASS   Password for all test users (default: "test" or TEST_USER_PASS)
  --force           Reset password even if user already exists
  --skip-existing   Skip users that already exist (default)
  -h, --help        Show this help

Users created (mail: {username}@test.ps.local):
  admin.test          → administrator
  site.admin          → site_admin
  content.admin       → content_admin
  content.editor      → content_editor
  seo.admin           → seo_admin
  translate.admin     → translate_admin
  translate.editor    → translate_editor

Prerequisites:
  - Drupal installed
  - RBAC roles imported: make rbac-sync

Examples:
  scripts/main.sh drupal create-test-users
  TEST_USER_PASS=Recette2026! scripts/main.sh drupal create-test-users
  make create-test-users
EOF
}

FORCE=0
SKIP_EXISTING=1
PASS="${DEFAULT_PASS}"

while [[ $# -gt 0 ]]; do
  case "$1" in
    --password=*)
      PASS="${1#*=}"
      shift
      ;;
    --force)
      FORCE=1
      SKIP_EXISTING=0
      shift
      ;;
    --skip-existing)
      SKIP_EXISTING=1
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

ps_header "Drupal: Create test users (BNP roles)"

ps_in_docker || ps_die "Docker containers not running."
ps_drush_bootstrapped || ps_die "Drupal is not installed."

CREATED=0
UPDATED=0
SKIPPED=0

for role in "${!TEST_USERS[@]}"; do
  username="${TEST_USERS[$role]}"
  mail="${username}@test.ps.local"

  if ps_drush user:information "${username}" --fields=uid 2>/dev/null | grep -qE '^[0-9]+$'; then
    if [[ ${SKIP_EXISTING} -eq 1 && ${FORCE} -eq 0 ]]; then
      ps_info "Skip existing user: ${username} (${role})"
      SKIPPED=$((SKIPPED + 1))
      continue
    fi
    ps_info "Updating user: ${username}"
    ps_drush user:password "${username}" "${PASS}"
    for other_role in "${!TEST_USERS[@]}"; do
      [[ "${other_role}" == "${role}" ]] && continue
      ps_drush user:role:remove "${other_role}" "${username}" -y 2>/dev/null || true
    done
    ps_drush user:role:add "${role}" "${username}" -y
    UPDATED=$((UPDATED + 1))
    continue
  fi

  ps_info "Creating user: ${username} → ${role}"
  ps_drush user:create "${username}" \
    --mail="${mail}" \
    --password="${PASS}" \
    -y
  ps_drush user:role:add "${role}" "${username}" -y
  CREATED=$((CREATED + 1))
done

ps_success "Test users: created=${CREATED}, updated=${UPDATED}, skipped=${SKIPPED}"
ps_info "Password for all test users: ${PASS}"
ps_info "Example login: content.editor / ${PASS}"
