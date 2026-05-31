#!/usr/bin/env bash
# shellcheck disable=SC1091
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)/_core/_source.sh"

SEARCH_INDEX_ID="${SEARCH_INDEX_ID:-offers}"

ps_header "Solr Hard Reset + Reindex"
ps_require_cmd docker
ps_require_container_running "${PS_SOLR_CONTAINER}"

ps_timed_run "Purge Solr core documents" ps_retry 2 2 \
  docker exec -i "${PS_SOLR_CONTAINER}" sh -lc \
  "curl -sS 'http://localhost:8983/solr/${PS_SOLR_CORE}/update?commit=true' -H 'Content-Type: application/json' --data-binary '{\"delete\":{\"query\":\"*:*\"}}' >/dev/null"

ps_timed_run "Clear Search API index tracker" ps_retry 2 2 ps_drush search-api:clear "${SEARCH_INDEX_ID}" -y

if ! ps_timed_run "Sync dynamic feature filters and index" ps_retry 2 2 ps_drush ps:search:features:sync-index --rebuild-tracker=1; then
  ps_warn "Feature sync/index command failed, falling back to plain Search API indexing"
  ps_timed_run "Reindex Search API index (fallback)" ps_retry 2 2 ps_drush search-api:index "${SEARCH_INDEX_ID}"
fi

ps_timed_run "Show Search API status" ps_drush search-api:status || true
ps_success "Solr hard reset workflow completed"
