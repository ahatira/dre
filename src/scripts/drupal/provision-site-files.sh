#!/usr/bin/env bash
# shellcheck disable=SC1091
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)/_core/_source.sh"

show_help() {
  cat <<'EOF'
Provision per-country public and private file directories.

Usage: scripts/main.sh drupal provision-site-files [OPTIONS]

Options:
  --countries=LIST  Country codes or "all" (default: all)
  -h, --help        Show this help

Creates (per country):
  - web/sites/{code}/files/ (+ crm, translations, bnp-media/placeholders)
  - private/{code}/ (outside web root, src/private/{code})
EOF
}

COUNTRIES_RAW="all"

while [[ $# -gt 0 ]]; do
  case "$1" in
    --countries=*)
      COUNTRIES_RAW="${1#*=}"
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

ps_header "Drupal: Provision multisite file directories"

ps_require_cmd docker
ps_in_docker || ps_die "Docker containers not running. Start them first: make up"

mapfile -t COUNTRIES < <(ps_parse_countries_arg "${COUNTRIES_RAW}")

for country in "${COUNTRIES[@]}"; do
  public_dir="$(ps_public_files_dir "${country}")"
  private_dir="$(ps_private_files_dir "${country}")"

  ps_info "Provisioning files for ${country}..."
  ps_docker_exec_php "mkdir -p \
    web/sites/${country}/files/crm \
    web/sites/${country}/files/translations \
    web/sites/${country}/files/bnp-media/placeholders \
    private/${country}"

  ps_docker_exec_php "chown -R www-data:www-data \
    web/sites/${country}/files \
    private/${country}" || ps_warn "chown failed for ${country} (run make fix-permissions)"

  ps_success "${country}: public=${public_dir} private=${private_dir}"
done

ps_success "File directories provisioned"
