#!/usr/bin/env bash
# shellcheck disable=SC1091
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)/_core/_source.sh"

# Demo content — menus, homepage, mega-menu config (requires site install first).

show_help() {
  cat <<'EOF'
Demo Script - Import Property Search demo content

Usage: scripts/main.sh drupal demo

Imports ps_demo default content (menus, homepage LB) and partial demo CMI
(mega-menu panels, multilingual settings).

Prerequisites:
  - Site installed: scripts/main.sh drupal install (or make install)
  - Docker containers running

Examples:
  scripts/main.sh drupal demo
  make demo
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

ps_header "Drupal: Demo content"

ps_info "Checking prerequisites..."
ps_require_cmd docker
ps_require_file "${PS_DOCKER_COMPOSE_FILE}"
ps_in_docker || ps_die "Docker containers not running. Start them first: docker compose up -d"
ps_drush_bootstrapped || ps_die "Drupal is not installed. Run: make install"
ps_success "Prerequisites OK"

if ! ps_drush theme:status ps_theme 2>/dev/null | grep -q Enabled; then
  ps_retry 2 2 ps_drush theme:enable -y ps_theme
  ps_drush config:set -y system.theme default ps_theme
fi

ps_info "Ensuring demo dependencies..."
ps_drush en -y default_content ps_block ps_homepage advanced_mega_menu menu_link_attributes languageicons social_media_links content_translation layout_builder path_alias || ps_warn "Some demo dependencies not available"

ps_info "Enabling ps_demo and importing default content..."
if ps_drush pm:list --status=enabled --filter=ps_demo --format=list 2>/dev/null | grep -q '^ps_demo$'; then
  ps_info "ps_demo already enabled"
else
  ps_retry 2 2 ps_drush en -y ps_demo
fi

ps_info "Ensuring demo content, translations and homepage layout..."
ps_drush ev '
use Drupal\ps_demo\Service\DemoInstaller;
DemoInstaller::create(\Drupal::getContainer())->finalizeInstall();
echo "DemoInstaller::finalizeInstall() completed\n";
' || ps_warn "Demo homepage finalize step had warnings"

ps_info "Applying search SEO URL language overrides..."
for lang in $(ps_drush ev 'echo implode(" ", array_keys(\Drupal::languageManager()->getLanguages()));'); do
  ps_drush php:script scripts/import_language_config_overrides.php "${lang}" \
    || ps_warn "Search SEO override import failed for ${lang}"
done

ps_info "Applying demo configuration (mega-menu, multilingual)..."
ps_retry 2 2 ps_drush config:import --partial --source=../config/demo -y

ps_info "Rebuilding cache..."
ps_retry 2 2 ps_drush_cr
ps_success "Demo content ready"
ps_info "Front page: ${PS_HTTP_URL}/"
