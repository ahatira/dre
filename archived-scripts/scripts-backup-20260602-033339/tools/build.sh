#!/usr/bin/env bash
# shellcheck disable=SC1091
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)/_core/_source.sh"

################################################################################
# Build Script - Install Dependencies and Prepare Libraries
#
# This script:
# 1. Installs Composer dependencies
# 2. Installs npm dependencies
# 3. Copies JavaScript libraries to web/libraries/
# 4. Cleans up node_modules to save disk space
# 5. Fixes file permissions
# 6. Optionally clears Drupal cache
#
# Usage:
#   bash src/scripts/main.sh tools build [--production] [--no-cache-clear] [--keep-node-modules]
#
# Options:
#   --production         Use production optimizations (no dev dependencies)
#   --no-cache-clear     Skip Drupal cache clearing
#   --keep-node-modules  Keep node_modules/ directory after build
################################################################################

# Parse arguments
PRODUCTION=false
CLEAR_CACHE=true
KEEP_NODE_MODULES=false

while [[ "$#" -gt 0 ]]; do
    case $1 in
        --production) PRODUCTION=true ;;
        --no-cache-clear) CLEAR_CACHE=false ;;
        --keep-node-modules) KEEP_NODE_MODULES=true ;;
        *) ps_error "Unknown parameter: $1"; exit 1 ;;
    esac
    shift
done

ps_header "Build Project"

################################################################################
# Step 1: Install Composer Dependencies
################################################################################
ps_info "Step 1/6: Installing Composer dependencies..."
cd "${PS_SRC_DIR}"

if [ "$PRODUCTION" = true ]; then
    ps_info "  → Production mode: no-dev, optimized autoloader"
    COMPOSER_PROCESS_TIMEOUT=2000 composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist
else
    ps_info "  → Development mode: with dev dependencies"
    COMPOSER_PROCESS_TIMEOUT=2000 composer install --no-interaction --prefer-dist
fi
ps_success "Composer dependencies installed"
echo ""

################################################################################
# Step 2: Install npm Dependencies
################################################################################
ps_info "Step 2/6: Installing npm dependencies..."

if [ "$PRODUCTION" = true ]; then
    ps_info "  → Using 'npm ci' for reproducible builds"
    npm ci --production=false --ignore-scripts
else
    ps_info "  → Using 'npm install'"
    npm install --ignore-scripts
fi
ps_success "npm dependencies installed"
echo ""

################################################################################
# Step 3: Copy JavaScript Libraries
################################################################################
ps_info "Step 3/6: Copying JavaScript libraries to web/libraries/..."
npm run libs
ps_success "Libraries copied successfully"
echo ""

################################################################################
# Step 4: Fix Permissions
################################################################################
ps_info "Step 4/6: Fixing file permissions..."

if [ -d "web/libraries" ]; then
    # Check if running in Docker (check for .dockerenv or cgroup)
    if [ -f "/.dockerenv" ] || grep -q docker /proc/1/cgroup 2>/dev/null; then
        ps_info "  → Setting www-data:www-data ownership"
        chown -R www-data:www-data web/libraries/ 2>/dev/null || ps_warn "Could not change ownership (may need root)"
    else
        ps_info "  → Skipping ownership change (not in Docker)"
    fi
    
    chmod -R 755 web/libraries/ 2>/dev/null || ps_warn "Could not change permissions"
    ps_success "Permissions fixed"
else
    ps_warn "web/libraries/ not found, skipping permissions"
fi
echo ""

################################################################################
# Step 5: Clean npm Artifacts
################################################################################
ps_info "Step 5/6: Cleaning npm artifacts..."

if [ "$KEEP_NODE_MODULES" = true ]; then
    ps_info "  → Keeping node_modules/ (--keep-node-modules)"
else
    if [ -d "node_modules" ]; then
        NODE_SIZE=$(du -sh node_modules 2>/dev/null | cut -f1 || echo "unknown")
        ps_info "  → Removing node_modules/ ($NODE_SIZE)"
        rm -rf node_modules/
    fi
fi

if [ -f "package-lock.json" ]; then
    PACKAGE_SIZE=$(du -sh package-lock.json 2>/dev/null | cut -f1 || echo "unknown")
    ps_info "  → Keeping package-lock.json for reproducibility ($PACKAGE_SIZE)"
fi

# Clean npm cache
ps_info "  → Cleaning npm cache..."
npm cache clean --force 2>/dev/null || ps_warn "Could not clean npm cache"

ps_success "npm artifacts cleaned"
echo ""

################################################################################
# Step 6: Clear Drupal Cache (Optional)
################################################################################
if [ "$CLEAR_CACHE" = true ]; then
    ps_info "Step 6/6: Clearing Drupal cache..."
    
    if [ -f "web/index.php" ] && [ -f "vendor/bin/drush" ]; then
        ps_drush cr
        ps_success "Drupal cache cleared"
    else
        ps_warn "Drupal not found or not installed, skipping cache clear"
    fi
else
    ps_info "Step 6/6: Skipping Drupal cache clear (--no-cache-clear)"
fi
echo ""

################################################################################
# Summary
################################################################################
ps_success "Build completed successfully"

if [ "$PRODUCTION" = true ]; then
    ps_info "Production build:"
    ps_info "  → Composer: no-dev, optimized autoloader"
    ps_info "  → npm: reproducible install (npm ci)"
fi

if [ "$KEEP_NODE_MODULES" = false ]; then
    ps_info "Disk space saved by removing node_modules/"
fi

ps_info ""
ps_info "Libraries installed at: web/libraries/"
ps_info "  - Ace Editor"
ps_info "  - Clipboard.js"
ps_info "  - Dropzone"
ps_info "  - noUiSlider"
ps_info "  - Slick Carousel"
ps_info "  - CKEditor 5 Media Embed"
ps_info ""
ps_info "Next steps:"
ps_info "  → Deploy: bash src/scripts/main.sh drupal deploy"
ps_info "  → Verify: bash src/scripts/main.sh drupal verify"
