#!/usr/bin/env bash
# Configure Symfony Mailer SMTP transport (mailer_transport) from SMTP_* env vars.
# shellcheck disable=SC1091
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)/_core/bootstrap.sh"

show_help() {
  cat <<'EOF'
Configure mailer_transport SMTP via Drush (Symfony Mailer 2).

Usage:
  make smtp-mail [country...]
  scripts/main.sh drupal smtp-mail [country...]

Countries:
  (none)     all countries from web/sites/countries.yml
  fr         single country
  fr,be,com  comma-separated list

Environment variables (export before running, or set in src/.env for APP_ENV=dev):
  SMTP_HOST          SMTP hostname (required)
  SMTP_PORT          Port (default: 587; Mailpit dev: 1025)
  SMTP_USER          Username (optional)
  SMTP_PASSWORD      Password (optional; alias: SMTP_PASS)
  SMTP_VERIFY_PEER   TLS peer verification: 1/0 (default: 0 for mailpit/localhost, else 1)

Examples:
  export SMTP_HOST=smtp.internal SMTP_PORT=587 SMTP_USER=noreply@example.com SMTP_PASSWORD='***'
  make smtp-mail fr

  # Dev Mailpit (values in src/.env)
  make smtp-mail com

See docs/MULTISITE_OPS.md and docs/EMAIL_ARCHITECTURE.md.
EOF
}

ps_smtp_resolve_password() {
  local value
  value="$(ps_env_get SMTP_PASSWORD)"
  if [[ -n "${value}" ]]; then
    printf '%s' "${value}"
    return 0
  fi
  ps_env_get SMTP_PASS
}

ps_smtp_verify_peer_for_host() {
  local raw="$1"
  local host="$2"
  if [[ -n "${raw}" ]]; then
    case "${raw}" in
      0|false|FALSE|no|NO|off|OFF) printf 'false'; return 0 ;;
      1|true|TRUE|yes|YES|on|ON) printf 'true'; return 0 ;;
      *) ps_die "Invalid SMTP_VERIFY_PEER: ${raw} (use 1/0 or true/false)" ;;
    esac
  fi
  case "${host}" in
    mailpit|127.0.0.1|localhost|::1) printf 'false' ;;
    *) printf 'true' ;;
  esac
}

ps_smtp_configure_country() {
  local country="$1"
  local host port user pass verify_peer

  host="$(ps_require_env SMTP_HOST)"
  port="$(ps_env_get SMTP_PORT 587)"
  user="$(ps_env_get SMTP_USER)"
  pass="$(ps_smtp_resolve_password)"
  verify_peer="$(ps_smtp_verify_peer_for_host "$(ps_env_get SMTP_VERIFY_PEER)" "${host}")"

  ps_info "SMTP @ps.${country} → ${host}:${port} (verify_peer=${verify_peer}, user=${user:-<empty>})"

  ps_drush config:set -y mailer_transport.settings default_transport sendmail
  ps_drush config:set -y mailer_transport.mailer_transport.sendmail plugin smtp
  ps_drush config:set -y mailer_transport.mailer_transport.sendmail configuration.host "${host}"
  ps_drush config:set -y mailer_transport.mailer_transport.sendmail configuration.port "${port}"
  ps_drush config:set -y mailer_transport.mailer_transport.sendmail configuration.user "${user}"
  ps_drush config:set -y mailer_transport.mailer_transport.sendmail configuration.pass "${pass}"
  ps_drush config:set -y mailer_transport.mailer_transport.sendmail configuration.query.verify_peer "${verify_peer}"

  ps_drush cache:rebuild
  ps_success "SMTP transport configured: ${country}"
}

[[ "${1:-}" == "--help" || "${1:-}" == "-h" ]] && { show_help; exit 0; }

ps_header "Configure SMTP (mailer_transport)"
ps_load_config
ps_resolve_runtime

ps_require_env SMTP_HOST

ps_countries_init
target_countries=()
if [[ $# -gt 0 ]]; then
  countries_raw="$(IFS=,; printf '%s' "$*")"
  mapfile -t target_countries < <(ps_parse_countries_arg "${countries_raw}")
else
  target_countries=("${_PS_COUNTRIES_CACHE[@]}")
fi

for country in "${target_countries[@]}"; do
  ps_drush_for_country "${country}"
  if ps_drush_bootstrapped; then
    ps_smtp_configure_country "${country}"
  else
    ps_warn "Skip ${country} (not bootstrapped)"
  fi
done
