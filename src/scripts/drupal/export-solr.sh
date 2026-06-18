#!/usr/bin/env bash
# shellcheck disable=SC1091
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)/_core/bootstrap.sh"

readonly PS_SOLR_SERVER_ID="ps_solr"
readonly PS_SOLR_SEARCH_INDEX="offers"
readonly PS_SOLR_VERSION="9.0"

show_help() {
  cat <<'EOF'
Export Solr — write one complete core directory per SOLR_CORE_* under conf/solr/.

Usage: scripts/main.sh drupal export-solr [OPTIONS] [country]

Options:
  --skip-finalize   Skip search-api-solr:finalize-index before export
  --finalize-all    Finalize index on every country site (default: export country only)
  -h, --help        Show this help

Default country: com

Output (project root):
  conf/solr/{core_name}/conf/       Full Solr config (schema, solrconfig, …)
  conf/solr/{core_name}/core.properties
  conf/solr/cores.yml               Country → core name manifest

Prerequisites: Drupal installed, Search API Solr module, unzip on host.
EOF
}

ps_require_unzip() {
  command -v unzip >/dev/null 2>&1 \
    || ps_die "unzip is required on the host to extract the Solr configset"
}

ps_solr_export_finalize_country() {
  local country="$1"
  ps_info "Finalizing Solr index schema (${country})..."
  ps_drush_for_country "${country}"
  ps_drush_bootstrapped || ps_die "Site not installed: ${country}. Run: make install ${country}"
  ps_drush search-api-solr:finalize-index "${PS_SOLR_SEARCH_INDEX}" -y \
    || ps_die "search-api-solr:finalize-index failed for ${country}"
}

ps_solr_export_write_cores_manifest() {
  local manifest="$1"
  local country core_name
  {
    printf '# Property Search Solr cores — one deployable directory per core (from src/.env).\n\n'
    printf 'cores:\n'
    for country in $(ps_multisite_countries); do
      core_name="$(ps_solr_core_name "${country}")"
      [[ -n "${core_name}" ]] || ps_die "Missing SOLR_CORE_$(ps_country_upper "${country}") in configuration"
      printf '  %s: %s\n' "${country}" "${core_name}"
    done
  } > "${manifest}"
}

ps_solr_export_fetch_config_zip() {
  local country="$1"
  local zip_host="${PS_REPO_ROOT}/conf/solr/.ps_solr_export.zip"

  ps_drush_for_country "${country}"
  ps_resolve_runtime
  ps_info "Generating Solr config from Search API server ${PS_SOLR_SERVER_ID}..."

  rm -f "${zip_host}"
  ps_drush search-api-solr:get-server-config "${PS_SOLR_SERVER_ID}" "${zip_host}" "${PS_SOLR_VERSION}" \
    || ps_die "search-api-solr:get-server-config failed"
  [[ -f "${zip_host}" ]] || ps_die "Solr config zip not found: ${zip_host}"
  PS_SOLR_EXPORT_ZIP="${zip_host}"
}

ps_solr_export_unzip_to_staging() {
  local zip_host="$1"
  local staging="${PS_REPO_ROOT}/conf/solr/.ps_solr_export_staging"

  ps_require_unzip
  rm -rf "${staging}"
  mkdir -p "${staging}"
  unzip -qo "${zip_host}" -d "${staging}"
  rm -f "${zip_host}"

  if [[ -d "${staging}/conf" ]]; then
    printf '%s/conf' "${staging}"
  else
    printf '%s' "${staging}"
  fi
}

ps_solr_export_write_core_directory() {
  local country="$1"
  local core_name="$2"
  local config_src="$3"
  local core_root="${PS_REPO_ROOT}/conf/solr/${core_name}"

  rm -rf "${core_root}"
  mkdir -p "${core_root}/conf"
  cp -a "${config_src}/." "${core_root}/conf/"
  cat > "${core_root}/core.properties" <<EOF
# Solr core ${core_name} (${country}) — deploy this directory as a Solr core instance.
name=${core_name}
EOF
  ps_info "Exported core: ${country} → conf/solr/${core_name}/"
}

ps_solr_export_remove_legacy_layout() {
  local solr_root="$1"
  rm -rf "${solr_root}/ps_project" "${solr_root}/cores"
}

COUNTRY="${PS_COUNTRY_CODE:-com}"
SKIP_FINALIZE=0
FINALIZE_ALL=0

while [[ $# -gt 0 ]]; do
  case "$1" in
    -h|--help) show_help; exit 0 ;;
    --skip-finalize) SKIP_FINALIZE=1; shift ;;
    --finalize-all) FINALIZE_ALL=1; shift ;;
    com|be|es|fr|ie|it|lu|nl|pl) COUNTRY="$1"; shift ;;
    *) ps_die "Unknown option or country: $1" ;;
  esac
done

ps_is_country_code "${COUNTRY}" || ps_die "Unknown country: ${COUNTRY}"
ps_load_config

solr_root="${PS_REPO_ROOT}/conf/solr"
manifest="${solr_root}/cores.yml"

ps_header "Solr config export (${COUNTRY})"

if [[ "${SKIP_FINALIZE}" -eq 0 ]]; then
  if [[ "${FINALIZE_ALL}" -eq 1 ]]; then
    for code in $(ps_multisite_countries); do
      ps_solr_export_finalize_country "${code}"
    done
  else
    ps_solr_export_finalize_country "${COUNTRY}"
  fi
fi

mkdir -p "${solr_root}"
ps_solr_export_remove_legacy_layout "${solr_root}"

PS_SOLR_EXPORT_ZIP=""
ps_solr_export_fetch_config_zip "${COUNTRY}"
config_src="$(ps_solr_export_unzip_to_staging "${PS_SOLR_EXPORT_ZIP}")"

core_count=0
for country in $(ps_multisite_countries); do
  core_name="$(ps_solr_core_name "${country}")"
  [[ -n "${core_name}" ]] || continue
  ps_solr_export_write_core_directory "${country}" "${core_name}" "${config_src}"
  core_count=$((core_count + 1))
done

rm -rf "${PS_REPO_ROOT}/conf/solr/.ps_solr_export_staging"
ps_solr_export_write_cores_manifest "${manifest}"

ps_success "Solr config exported to conf/solr/ (${core_count} cores)"
ps_info "Each core is in conf/solr/{core_name}/ — local dev: make init-solr-cores (repo root)"
