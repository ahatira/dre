#!/usr/bin/env bash
# shellcheck disable=SC1091
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)/_core/_source.sh"

show_help() {
  cat <<'EOF'
Create PostgreSQL databases for Property Search multisite countries.

Usage: scripts/main.sh drupal provision-databases

Reads DB_NAME_{COUNTRY} from src/.env and creates missing databases.
EOF
}

while [[ $# -gt 0 ]]; do
  case "$1" in
    -h|--help)
      show_help
      exit 0
      ;;
    *)
      ps_die "Unknown option: $1"
      ;;
  esac
done

ps_header "Drupal: Provision multisite databases"

ps_load_dotenv
ps_in_docker || ps_die "Docker containers not running. Start them first: make up"

create_db() {
  local name="$1"
  if [[ -z "${name}" ]]; then
    return 0
  fi
  if ps_docker_exec_db "psql -v ON_ERROR_STOP=1 -U \"${DB_USER:-drupal}\" -d postgres -tAc \"SELECT 1 FROM pg_database WHERE datname='${name}'\"" | grep -q 1; then
    ps_info "Database exists: ${name}"
    return 0
  fi
  ps_info "Creating database: ${name}"
  ps_docker_exec_db "psql -v ON_ERROR_STOP=1 -U \"${DB_USER:-drupal}\" -d postgres -c \"CREATE DATABASE ${name};\""
}

mapfile -t COUNTRIES < <(ps_multisite_countries)
for country in "${COUNTRIES[@]}"; do
  upper="$(ps_country_upper "${country}")"
  var="DB_NAME_${upper}"
  create_db "${!var:-}"
done

ps_success "Database provisioning complete"
