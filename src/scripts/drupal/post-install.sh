#!/usr/bin/env bash
# shellcheck disable=SC1091
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)/_core/_source.sh"

# Post-install — demo content, sample offers, search/SEO modules, Solr index.

readonly XML_SAMPLE="${PS_PROJECT_ROOT}/data/xml/bnppre_sample_50_per_type.xml"
readonly XML_TARGET="${PS_SRC_DIR}/web/sites/default/files/crm/offers.xml"

show_help() {
  cat <<'EOF'
Post-Install Script - Complete local site after Drupal install

Usage: scripts/main.sh drupal post-install [OPTIONS]

Runs (in order):
  1. Demo content (menus, homepage, mega-menu CMI)
  2. Sample CRM XML import (offers + dependencies)
  3. PS modules: ps_compare, ps_search, ps_seo
  4. Solr index (offers)
  5. Health checks

Options:
  --skip-demo       Skip demo content import
  --skip-offers     Skip sample XML migrate import
  --force-offers    Re-import sample XML even if offers already exist
  --skip-solr       Skip Solr indexing
  -h, --help        Show this help

Examples:
  scripts/main.sh drupal post-install
  make post-install
EOF
}

SKIP_DEMO=0
SKIP_OFFERS=0
SKIP_SOLR=0
FORCE_OFFERS=0

while [[ $# -gt 0 ]]; do
  case "$1" in
    --skip-demo)
      SKIP_DEMO=1
      shift
      ;;
    --skip-offers)
      SKIP_OFFERS=1
      shift
      ;;
    --force-offers)
      FORCE_OFFERS=1
      shift
      ;;
    --skip-solr)
      SKIP_SOLR=1
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

ps_header "Drupal: Post-install (complete site)"

ps_info "Checking prerequisites..."
ps_require_cmd docker
ps_require_file "${PS_DOCKER_COMPOSE_FILE}"
ps_in_docker || ps_die "Docker containers not running. Start them first: make up"
ps_drush_bootstrapped || ps_die "Drupal is not installed. Run: make install"
ps_success "Prerequisites OK"

if [[ ${SKIP_DEMO} -eq 0 ]]; then
  ps_info "Step 1/5: Demo content..."
  bash "${PS_SCRIPTS_DIR}/drupal/demo.sh"
  ps_success "Demo content ready"
else
  ps_info "Step 1/5: Demo content skipped"
fi

if [[ ${SKIP_OFFERS} -eq 0 ]]; then
  EXISTING_OFFERS=$(ps_drush sql:query "SELECT COUNT(*) FROM node_field_data WHERE type='offer' AND status=1" 2>/dev/null | tr -d '[:space:]')
  if [[ -n "${EXISTING_OFFERS}" && "${EXISTING_OFFERS}" -gt 0 && ${FORCE_OFFERS} -eq 0 ]]; then
    ps_info "Step 2/5: Sample offers already present (${EXISTING_OFFERS}) — skipping migrate (use --force-offers to re-import)"
  else
    ps_info "Step 2/5: Sample offers import..."
    ps_require_file "${XML_SAMPLE}" "Sample XML not found: ${XML_SAMPLE}"
    mkdir -p "$(dirname "${XML_TARGET}")"
    cp "${XML_SAMPLE}" "${XML_TARGET}"
    ps_success "XML staged: ${XML_TARGET}"

    ps_info "Importing dictionary (ensure referentials are up to date)..."
    ps_retry 2 2 ps_drush ps:dictionary:import -y || ps_warn "Dictionary import warnings"

    ps_info "Running CRM migrate pipeline..."
    ps_retry 2 2 ps_drush pm:install migrate migrate_plus migrate_tools ps_migrate -y
    if ! ps_retry 2 2 ps_drush migrate:import ps_offer_from_xml --update --execute-dependencies -y; then
      ps_die "Offer migrate failed (check ${XML_TARGET})"
    fi

    OFFER_COUNT=$(ps_drush sql:query "SELECT COUNT(*) FROM node_field_data WHERE type='offer' AND status=1" 2>/dev/null | tr -d '[:space:]')
    [[ -n "${OFFER_COUNT}" && "${OFFER_COUNT}" -gt 0 ]] \
      || ps_die "No offers found after migrate import"
    ps_success "Offers imported: ${OFFER_COUNT}"
  fi
else
  ps_info "Step 2/5: Sample offers import skipped"
fi

ps_info "Step 3/5: Enabling search, compare and SEO modules..."
ps_retry 2 2 ps_drush en -y ps_compare
ps_retry 2 2 ps_drush en -y ps_search
ps_retry 2 2 ps_drush en -y ps_seo
ps_success "ps_compare, ps_search and ps_seo enabled"

if [[ ${SKIP_SOLR} -eq 0 ]]; then
  ps_info "Step 4/5: Solr index..."
  bash "${PS_SCRIPTS_DIR}/drupal/index-solr.sh"
  ps_info "Syncing feature filters (More filters) into Solr..."
  ps_retry 2 2 ps_drush ps:search:features:sync-index --rebuild-tracker=1 -y \
    || ps_warn "Feature filter sync failed — More filters may return 0 until: drush ps:search:features:sync-index"
else
  ps_info "Step 4/5: Solr index skipped"
fi

ps_info "Step 5/5: Health checks..."
REQUIRED_MODULES=(ps_demo ps_favorite ps_compare ps_search ps_seo ps_migrate)
for module in "${REQUIRED_MODULES[@]}"; do
  if ! ps_drush pm:list --status=enabled --filter="${module}" --format=list 2>/dev/null | grep -q "^${module}$"; then
    ps_die "Required module not enabled: ${module}"
  fi
done

OFFER_COUNT=$(ps_drush sql:query "SELECT COUNT(*) FROM node_field_data WHERE type='offer' AND status=1" 2>/dev/null | tr -d '[:space:]')
[[ -n "${OFFER_COUNT}" && "${OFFER_COUNT}" -gt 0 ]] \
  || ps_warn "No published offers — search and favorites will be empty"

INDEXED=$(ps_drush search-api:status offers --format=csv 2>/dev/null | tail -n 1 | cut -d',' -f4 | tr -d '[:space:]' || echo "0")
if [[ ${SKIP_SOLR} -eq 0 && -n "${INDEXED}" && "${INDEXED}" != "0" ]]; then
  ps_success "Solr offers index: ${INDEXED} items"
elif [[ ${SKIP_SOLR} -eq 0 ]]; then
  ps_warn "Solr offers index appears empty — run: make index-solr"
fi

ps_retry 2 2 ps_drush_cr

ps_success "Post-install complete!"
echo ""
ps_info "Front (FR): ${PS_HTTP_URL}/fr/"
ps_info "Search (FR): ${PS_HTTP_URL}/fr/recherche-immobiliere"
ps_info "Admin: ${PS_HTTP_URL}/admin"
ps_info "Note: sample offers are FR-only; use /fr/ URLs or import EN translations for English paths."
