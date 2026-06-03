#!/usr/bin/env bash
# shellcheck disable=SC1091
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)/_core/_source.sh"

# Drupal installation script

show_help() {
  cat <<'EOF'
Install Script - Install Drupal site

Usage: scripts/main.sh drupal install [OPTIONS]

Options:
  --force          Force reinstall (recreate database)
  --dev            Enable development modules (devel, stage_file_proxy)
  --no-content     Skip content generation (dictionary, offers)
  -h, --help       Show this help

Prerequisites:
  - Docker containers running (ps_php, ps_postgres)
  - settings.php configured with database connection
  - Dependencies built (run 'scripts/main.sh tools build' first)

Environment variables:
  SITE_NAME        Site name (default: "PS Project")
  ADMIN_USER       Admin username (default: "admin")
  ADMIN_PASS       Admin password (default: "admin")
  ADMIN_MAIL       Admin email (default: "admin@example.com")
  DB_NAME          Database name (default: "drupal")
  DB_USER          Database user (default: "drupal")

Examples:
  scripts/main.sh drupal install
  scripts/main.sh drupal install --force
  scripts/main.sh drupal install --dev --no-content
  SITE_NAME="My Site" scripts/main.sh drupal install
EOF
}

# Default values
SITE_NAME="${SITE_NAME:-PS Project}"
ADMIN_USER="${ADMIN_USER:-admin}"
ADMIN_PASS="${ADMIN_PASS:-admin}"
ADMIN_MAIL="${ADMIN_MAIL:-admin@example.com}"
DB_NAME="${DB_NAME:-drupal}"
DB_USER="${DB_USER:-drupal}"
FORCE_INSTALL=0
SKIP_CONTENT=0
ENABLE_DEV=0

# Parse arguments
while [[ $# -gt 0 ]]; do
  case "$1" in
    --force)
      FORCE_INSTALL=1
      shift
      ;;
    --dev)
      ENABLE_DEV=1
      shift
      ;;
    --no-content)
      SKIP_CONTENT=1
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

ps_header "Drupal: Installing site"

# Prerequisites
ps_info "Checking prerequisites..."
ps_require_cmd docker
ps_require_file "${PS_DOCKER_COMPOSE_FILE}"
ps_in_docker || ps_die "Docker containers not running. Start them first: docker compose up -d"
ps_success "Prerequisites OK"

# Check if already installed
if ps_drush_bootstrapped && [[ ${FORCE_INSTALL} -eq 0 ]]; then
  ps_warn "Drupal is already installed. Use --force to reinstall."
  exit 0
fi

# Drop database if force install
if [[ ${FORCE_INSTALL} -eq 1 ]]; then
  ps_info "Dropping existing database..."
  ps_retry 2 2 ps_drush sql:drop --yes
  ps_success "Database dropped"
fi

# Site install (minimal profile, settings.php already configured)
ps_info "Installing Drupal with minimal profile..."
ps_retry 2 3 ps_drush site:install minimal \
  --site-name="${SITE_NAME}" \
  --account-name="${ADMIN_USER}" \
  --account-pass="${ADMIN_PASS}" \
  --account-mail="${ADMIN_MAIL}" \
  --yes
ps_success "Drupal installed"

# Front theme
ps_info "Configuring front theme (ui_suite_bnp)..."
ps_drush theme:enable -y ui_suite_bnp || true
ps_drush config:set -y system.theme default ui_suite_bnp || true
ps_success "Front theme configured"

# Enable essential contrib modules (not in custom module dependencies)
ps_info "Enabling essential contrib modules..."
ps_drush en -y \
  honeypot seckit || ps_warn "Some essential modules not available"
ps_success "Essential modules enabled"

# Enable BNP admin baseline module (Gin theme + config on install)
ps_info "Enabling BNP admin baseline..."
ps_drush theme:enable -y gin || ps_warn "Gin theme not available"
ps_retry 2 2 ps_drush en -y bnp_admin
ps_success "BNP admin baseline enabled"

# Enable development modules if --dev
if [[ ${ENABLE_DEV} -eq 1 ]]; then
  ps_info "Enabling development modules..."
  ps_drush en -y devel devel_generate stage_file_proxy || ps_warn "Some dev modules not available"
  ps_success "Development modules enabled"
fi

# Enable custom PS modules (specific order)
ps_info "Enabling PS modules..."
ps_retry 2 2 ps_drush en -y ps_core ps_dictionary ps_agent ps_feature
ps_retry 2 2 ps_drush en -y ps_surface
ps_retry 2 2 ps_drush en -y entity_browser_generic_embed
ps_retry 2 2 ps_drush en -y bnp_media ps_media
ps_retry 2 2 ps_drush en -y ps_offer
ps_drush role:perm:add ps_admin "manage ps_favorite" -y || true
ps_retry 2 2 ps_drush en -y ps_context
ps_retry 2 2 ps_drush en -y ps_search
ps_retry 2 2 ps_drush en -y bnp_editor
ps_success "PS modules enabled"

# Anti-spam modules
ps_info "Enabling anti-spam modules..."
ps_retry 2 2 ps_drush en -y captcha altcha || ps_warn "Anti-spam modules not available"
ps_success "Anti-spam configured"

# Assign ps_admin role
ps_info "Assigning ps_admin role to ${ADMIN_USER}..."
ps_drush user:role:add ps_admin "${ADMIN_USER}" -y || true
ps_success "Role assigned"

# French language
ps_info "Ensuring French language..."
if ps_drush language:info | grep -q "French (fr)"; then
  ps_info "French already enabled"
else
  ps_drush language:add fr --skip-translations -y
  ps_success "French language added"
fi

# Import PS module translations
ps_info "Importing PS module translations..."
IMPORTED=0
SKIPPED=0
FAILED=0

# Get active languages
ACTIVE_LANGS=$(ps_drush ev 'echo implode(PHP_EOL, array_keys(\Drupal::languageManager()->getLanguages()));')

# Find and import .po files
while IFS= read -r po_file; do
  [[ -z "${po_file}" ]] && continue
  
  filename=$(basename "${po_file}")
  langcode="${filename%.po}"
  langcode="${langcode##*.}"
  
  # Check if language is active
  if ! echo "${ACTIVE_LANGS}" | grep -q "^${langcode}$"; then
    SKIPPED=$((SKIPPED + 1))
    continue
  fi
  
  if ps_drush locale:import "${langcode}" "/var/www/html/${po_file}" --type=customized --override=all -y >/dev/null 2>&1; then
    IMPORTED=$((IMPORTED + 1))
  else
    FAILED=$((FAILED + 1))
  fi
done < <(ps_docker_exec_php "find web/modules/custom -path '*/translations/*.po' -name 'ps_*.*.po' 2>/dev/null || true")

ps_info "Translations: imported=${IMPORTED}, skipped=${SKIPPED}, failed=${FAILED}"
[[ ${FAILED} -gt 0 ]] && ps_warn "Some translations failed to import"

# Import dictionary
if [[ ${SKIP_CONTENT} -eq 0 ]]; then
  ps_info "Importing dictionary data..."
  ps_retry 2 2 ps_drush ps:dictionary:import || ps_warn "Dictionary import warnings"
  ps_success "Dictionary imported"
else
  ps_warn "Skipping dictionary import (--no-content)"
fi

# Cache rebuild
ps_info "Rebuilding cache..."
ps_retry 2 2 ps_drush_cr
ps_success "Cache rebuilt"

# Final status
ps_success "Installation complete!"
echo ""
ps_drush status --fields=bootstrap,db-status,drupal-version,drush-version
echo ""
ps_info "Back-office: ${PS_HTTP_URL}/admin"
ps_info "Login: ${ADMIN_USER} / ${ADMIN_PASS}"
