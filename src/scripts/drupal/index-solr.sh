#!/usr/bin/env bash
# shellcheck disable=SC1091
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)/_core/_source.sh"

# Solr — reindex the offers Search API index.

show_help() {
  cat <<'EOF'
Index Solr Script - Reindex offers in Search API / Solr

Usage: scripts/main.sh drupal index-solr

Prerequisites:
  - Site installed with ps_search enabled
  - Solr container running
  - Offers present (run make import-sample-xml first)

Examples:
  scripts/main.sh drupal index-solr
  make index-solr
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

ps_header "Drupal: Solr index (offers)"

ps_drush_bootstrapped || ps_die "Drupal is not installed. Run: make install"

ps_info "Clearing offers index and rebuilding tracker..."
ps_drush search-api:clear offers -y 2>/dev/null || ps_warn "Could not clear offers index (is ps_search enabled?)"
ps_drush search-api:rebuild-tracker offers -y 2>/dev/null || ps_warn "Could not rebuild offers tracker"

ps_info "Indexing offers..."
ps_retry 2 2 ps_drush search-api:index offers -y || ps_die "Solr index failed (is Solr up? Are offers imported?)"

ps_success "Offers indexed in Solr"
