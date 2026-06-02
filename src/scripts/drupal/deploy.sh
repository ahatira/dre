#!/usr/bin/env bash
# shellcheck disable=SC1091
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)/_core/_source.sh"

# Deployment script - Full production deployment workflow

show_help() {
  cat <<'EOF'
Deploy Script - Production deployment workflow

Usage: scripts/main.sh drupal deploy

Performs a complete deployment:
  1. Check dependencies are built (run 'tools build' first)
  2. Import configuration
  3. Run database updates
  4. Rebuild cache

Prerequisites:
  - Run 'scripts/main.sh tools build --production' before deploying

This script is designed for production environments.

Examples:
  scripts/main.sh tools build --production
  scripts/main.sh drupal deploy
EOF
}

if [[ "${1:-}" == "--help" ]] || [[ "${1:-}" == "-h" ]]; then
  show_help
  exit 0
fi

ps_header "Deployment: Starting production workflow"

# Step 1: Check dependencies
ps_info "Step 1/4: Checking dependencies..."
bash "${PS_SCRIPTS_DIR}/tools/check.sh" || ps_die "Build verification failed. Run 'scripts/main.sh tools build' first."
ps_success "Dependencies verified"

# Step 2: Config import
ps_info "Step 2/4: Importing configuration..."
ps_drush config:import --yes
ps_success "Configuration imported"

# Step 3: Database updates
ps_info "Step 3/4: Running database updates..."
ps_drush updatedb --yes
ps_success "Database updated"

# Step 4: Cache rebuild
ps_info "Step 4/4: Rebuilding cache..."
ps_drush_cr
ps_success "Cache rebuilt"

ps_success "Deployment complete!"
ps_info "Site URL: ${PS_HTTP_URL}"
