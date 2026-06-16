#!/usr/bin/env bash
# shellcheck disable=SC1091
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)/_core/bootstrap.sh"

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
Create test users — one account per BNP baseline role.

Usage: scripts/main.sh drupal create-test-users [OPTIONS] [country]

Default country: com

Options:
  --password=PASS   Default: test or TEST_USER_PASS env
  --force           Reset passwords for existing users
  -h, --help        Show this help

Prerequisite: make rbac-sync
EOF
}

FORCE=0
PASS="${TEST_USER_PASS:-test}"
COUNTRY="${PS_COUNTRY_CODE:-com}"

while [[ $# -gt 0 ]]; do
  case "$1" in
    --password=*) PASS="${1#*=}"; shift ;;
    --force) FORCE=1; shift ;;
    -h|--help) show_help; exit 0 ;;
    com|be|es|fr|ie|it|lu|nl|pl) COUNTRY="$1"; shift ;;
    *) ps_die "Unknown option: $1" ;;
  esac
done

ps_load_config
ps_drush_for_country "${COUNTRY}"
ps_header "Test users (${COUNTRY})"
ps_drush_bootstrapped || ps_die "Site not installed"

CREATED=0
UPDATED=0
SKIPPED=0

for role in "${!TEST_USERS[@]}"; do
  user="${TEST_USERS[$role]}"
  mail="${user}@test.ps.local"

  if ps_drush user:information "${user}" --fields=uid 2>/dev/null | grep -qE '^[0-9]+$'; then
    if [[ ${FORCE} -eq 0 ]]; then
      SKIPPED=$((SKIPPED + 1))
      continue
    fi
    ps_drush user:password "${user}" "${PASS}"
    for r in "${!TEST_USERS[@]}"; do
      [[ "${r}" == "${role}" ]] && continue
      ps_drush user:role:remove "${r}" "${user}" -y 2>/dev/null || true
    done
    ps_drush user:role:add "${role}" "${user}" -y
    UPDATED=$((UPDATED + 1))
    continue
  fi

  ps_drush user:create "${user}" --mail="${mail}" --password="${PASS}" -y
  ps_drush user:role:add "${role}" "${user}" -y
  CREATED=$((CREATED + 1))
done

ps_success "Users: created=${CREATED} updated=${UPDATED} skipped=${SKIPPED} password=${PASS}"
