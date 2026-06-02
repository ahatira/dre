#!/usr/bin/env bash
# shellcheck disable=SC1091
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)/_core/_source.sh"

ACTION="${1:-status}"

ps_header "Drupal Config"
case "${ACTION}" in
  export)
    ps_drush config:export -y
    ;;
  import)
    ps_drush config:import -y
    ;;
  status)
    ps_drush config:status
    ;;
  *)
    ps_die "Unsupported action: ${ACTION}. Use: export|import|status"
    ;;
esac

ps_success "Config action completed: ${ACTION}"
