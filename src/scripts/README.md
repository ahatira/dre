# PS Project Scripts

Automation scripts for PS Project development and deployment.

## Architecture

### Entry Point

**`main.sh`** - Central CLI entry point for all operations

```bash
scripts/main.sh <domain> <command> [args...]
```

### Core Modules (`_core/`)

Shared bash modules providing reusable functions:

- **`_source.sh`** - Bootstrap loader (load all modules)
- **`constants.sh`** - Global project variables (paths, containers, URLs)
- **`colors.sh`** - Terminal color codes
- **`logger.sh`** - Structured logging (`ps_info`, `ps_success`, `ps_warn`, `ps_error`, `ps_header`)
- **`errors.sh`** - Error handling (`ps_die`, error trap)
- **`docker.sh`** - Docker utilities (`ps_in_docker`, `ps_docker_exec_php`)
- **`drush.sh`** - Drush wrapper with Docker auto-detection (`ps_drush`, `ps_drush_cr`)

### Script Domains

**`drupal/`** - Drupal operations (requires Drush)
- `install.sh` - Fresh Drupal installation (site, modules, theme — no demo)
- `demo.sh` - Demo content (ps_demo, mega-menu CMI)
- `index-solr.sh` - Reindex offers in Solr
- `cache-clear.sh` - Cache rebuild
- `deploy.sh` - Production deployment workflow (checks dependencies first)

**`tools/`** - Build and utility scripts (no Drush dependency)
- `build.sh` - Build dependencies (composer + npm + libraries)
- `check.sh` - Verify dependencies are built

## Usage

### Quick Start

```bash
# Build project (required first)
scripts/main.sh tools build

# Check build status
scripts/main.sh tools check

# Install Drupal
scripts/main.sh drupal install

# Clear cache
scripts/main.sh drupal cache-clear

# Deploy (checks build first)
scripts/main.sh drupal deploy
```

### Build Options

```bash
# Full build with dev dependencies
scripts/main.sh tools build

# Production build (no dev deps, cleanup npm)
scripts/main.sh tools build --production

# No cache, keep node_modules
scripts/main.sh tools build --no-cache --keep-npm
```

### Install Options

```bash
# Site install (modules, dictionary, ps_theme — no demo/offers/Solr)
scripts/main.sh drupal install

# Force reinstall (drop DB)
scripts/main.sh drupal install --force

# Custom site name
SITE_NAME="My Site" scripts/main.sh drupal install
```

### Demo & data (after install)

```bash
make demo                 # Menus, homepage, mega-menu config
make import-sample-xml    # Sample CRM offers (migrate)
make index-solr           # Solr index for offers
```

### Help

Every command supports `--help`:

```bash
scripts/main.sh tools build --help
scripts/main.sh drupal install --help
```

## Script Structure

Each script follows this pattern:

```bash
#!/usr/bin/env bash
# shellcheck disable=SC1091
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)/_core/_source.sh"

# Script uses ps_* functions from _core modules:
ps_header "My Script: Description"
ps_info "Doing something..."
ps_success "Done!"
```

## Adding New Scripts

1. Create script in appropriate domain (`drupal/`, `tools/`)
2. Source `_core/_source.sh` at the top
3. Use `ps_*` functions for logging and utilities
4. Add `--help` option
5. Make executable: `chmod +x scripts/<domain>/<command>.sh`

Example:

```bash
#!/usr/bin/env bash
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)/_core/_source.sh"

show_help() {
  cat <<'EOF'
My Command - Does something

Usage: scripts/main.sh <domain> <command> [OPTIONS]

Options:
  -h, --help    Show this help
EOF
}

[[ "${1:-}" == "--help" ]] && { show_help; exit 0; }

ps_header "My Command: Running"
ps_info "Step 1..."
# Do work
ps_success "Complete!"
```

## Docker Detection

Scripts automatically detect if running inside Docker:

- `ps_in_docker` - Returns true if ps_php container is running
- `ps_drush` - Executes in Docker if available, otherwise locally
- `ps_docker_exec_php` - Execute command in PHP container

## Environment Variables

### Docker Configuration
- `PS_PHP_CONTAINER` - PHP container name (default: `ps_php`)
- `PS_DB_CONTAINER` - Database container name (default: `ps_postgres`)
- `PS_HTTP_PORT` - HTTP port (default: `8080`)

### Drupal Installation
- `SITE_NAME` - Site name (default: "PS Project")
- `ADMIN_USER` - Admin username (default: "admin")
- `ADMIN_PASS` - Admin password (default: "admin")
- `ADMIN_MAIL` - Admin email (default: "admin@example.com")
- `DB_NAME` - Database name (default: "drupal")
- `DB_USER` - Database user (default: "drupal")
- `DB_PASS` - Database password (default: "drupal")
- `DB_HOST` - Database host (default: "postgres")
- `DB_PORT` - Database port (default: "5432")

## CI/CD Integration

### GitLab CI Example

```yaml
deploy:
  stage: deploy
  script:
    - scripts/main.sh tools build --production
    - scripts/main.sh drupal deploy
```

### GitHub Actions Example

```yaml
- name: Build
  run: scripts/main.sh tools build --production
- name: Deploy
  run: scripts/main.sh drupal deploy
```

## Principles

1. **Simple and Clean** - No unnecessary abstractions
2. **Self-Contained** - Each script can be understood independently
3. **Docker-Aware** - Automatic detection and adaptation
4. **Consistent Interface** - All commands through `main.sh`
5. **Built-in Help** - Every command documents itself
6. **Error Handling** - Automatic error trap with line numbers
7. **Structured Logging** - Clear, colored output with levels
8. **Separation of Concerns** - Build scripts (tools/) don't use Drush, Drupal operations (drupal/) do

## Comparison with Old Architecture

### Old (Complex)
- Nested directories: `_core/`, `drupal/`, `tools/`, `composer/`, `generate/`, `test/`
- 16 core modules (382 lines total)
- Mixed PHP and Bash scripts
- Complex dependency chains

### New (Simple)
- Flat structure: `_core/`, `drupal/`, `tools/`
- 7 essential core modules (~150 lines total)
- Pure Bash only
- Minimal dependencies, maximum clarity

### What Was Preserved
- `main.sh` entry point pattern
- `_core/` shared modules concept
- `ps_*` function naming convention
- Docker auto-detection
- Domain-based organization

### What Was Simplified
- Removed unused modules (network, time, ui, validate, git, process, filesystem, database, env)
- Simplified logger (removed timestamps, simplified format)
- Combined related functions
- Removed PHP scripts (kept only Bash)
- Eliminated generate/ and test/ domains (not needed yet)
