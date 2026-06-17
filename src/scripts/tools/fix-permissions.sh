#!/usr/bin/env bash
# shellcheck disable=SC1091
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)/_core/bootstrap.sh"

show_help() {
  cat <<'EOF'
Fix permissions — align custom code ownership with www-data in ps_php.

Usage: scripts/main.sh tools fix-permissions

Fixes EACCES on config/sync and custom modules/themes after Drush as root.
EOF
}

[[ "${1:-}" == "--help" || "${1:-}" == "-h" ]] && { show_help; exit 0; }

ps_header "Fix permissions (www-data in ${PS_PHP_CONTAINER})"
ps_in_docker || ps_die "Container ${PS_PHP_CONTAINER} not running — run: make up"

docker exec -i "${PS_PHP_CONTAINER}" chown -R www-data:www-data \
  /var/www/html/config/sync \
  /var/www/html/web/modules/custom \
  /var/www/html/web/themes/custom

ps_success "Permissions fixed"
