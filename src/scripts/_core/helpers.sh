#!/usr/bin/env bash
# Shared helpers — retry, validation, module install.

ps_retry() {
  local attempts="$1"
  local delay="$2"
  shift 2
  local n=1
  until "$@"; do
    if [[ ${n} -ge ${attempts} ]]; then
      ps_error "Command failed after ${attempts} attempts"
      return 1
    fi
    ps_warn "Attempt ${n}/${attempts} failed, retrying in ${delay}s..."
    sleep "${delay}"
    n=$((n + 1))
  done
}

ps_require_cmd() {
  command -v "$1" >/dev/null 2>&1 || ps_die "Required command not found: $1"
}

ps_require_file() {
  [[ -f "$1" ]] || ps_die "${2:-Required file not found: $1}"
}

ps_enable_module_robust() {
  local module="$1"
  local attempts="${2:-2}"
  local delay="${3:-2}"
  local n=1

  while [[ ${n} -le ${attempts} ]]; do
    if ps_drush en -y "${module}"; then
      return 0
    fi
    ps_warn "Enable ${module} failed (attempt ${n}/${attempts})"
    if ps_drush pm:list --status=enabled --filter="${module}" --format=list 2>/dev/null | grep -q "^${module}$"; then
      ps_drush pm:uninstall "${module}" -y 2>/dev/null || true
    fi
    if [[ ${n} -ge ${attempts} ]]; then
      return 1
    fi
    sleep "${delay}"
    n=$((n + 1))
  done
}

ps_verify_ps_offer_install() {
  ps_drush ev '
    if (!\Drupal::moduleHandler()->moduleExists("ps_offer")) {
      throw new \RuntimeException("ps_offer is not enabled");
    }
    $fields = \Drupal::service("entity_field.manager")->getFieldDefinitions("node", "offer");
    if (!isset($fields["field_surfaces"])) {
      throw new \RuntimeException("field_surfaces missing on offer bundle");
    }
    echo "ps_offer OK\n";
  ' || return 1
}

ps_drush_po_path() {
  printf '%s' "$1"
}

ps_import_module_translations() {
  ps_resolve_runtime
  local active_langs imported=0 skipped=0 failed=0
  active_langs="$(ps_drush ev 'echo implode(PHP_EOL, array_keys(\Drupal::languageManager()->getLanguages()));')"

  import_po() {
    local po_file="$1" langcode="$2" drush_path
    [[ -n "${po_file}" && -n "${langcode}" ]] || return 1
    if ! echo "${active_langs}" | grep -q "^${langcode}$"; then
      skipped=$((skipped + 1))
      return 0
    fi
    drush_path="$(ps_drush_po_path "${po_file}")"
    if ps_drush locale:import "${langcode}" "${drush_path}" --type=customized --override=all -y >/dev/null 2>&1; then
      imported=$((imported + 1))
    else
      failed=$((failed + 1))
    fi
  }

  local po_file filename langcode
  while IFS= read -r po_file; do
    [[ -z "${po_file}" ]] && continue
    filename=$(basename "${po_file}")
    langcode="${filename%.po}"
    langcode="${langcode##*.}"
    import_po "${po_file}" "${langcode}"
  done < <(find "${PS_SRC_DIR}/web/modules/custom" -path '*/translations/*.po' \( -name 'ps_*.*.po' -o -name 'bnp_*.*.po' \) 2>/dev/null | sort)

  while IFS= read -r po_file; do
    [[ -z "${po_file}" ]] && continue
    filename=$(basename "${po_file}")
    langcode="${filename#ps_theme.}"
    langcode="${langcode%.po}"
    import_po "${po_file}" "${langcode}"
  done < <(find "${PS_SRC_DIR}/web/themes/custom/ps_theme/translations" -name 'ps_theme.*.po' 2>/dev/null | sort)

  ps_info "Translations: imported=${imported}, skipped=${skipped}, failed=${failed}"
  [[ ${failed} -eq 0 ]] || ps_warn "Some translations failed to import"
}

ps_apply_google_maps_api_key() {
  local api_key
  api_key="$(ps_env_get GOOGLE_MAPS_API_KEY)"
  if [[ -z "${api_key}" ]]; then
    ps_warn "GOOGLE_MAPS_API_KEY not set — Google Maps/geocoder key skipped"
    return 0
  fi

  ps_info "Applying Google Maps API key from GOOGLE_MAPS_API_KEY..."
  ps_drush config:set -y geofield_map.settings gmap_api_key "${api_key}" \
    || ps_warn "Could not set geofield_map.settings gmap_api_key"
  ps_drush config:set -y geocoder.geocoder_provider.google_maps configuration.apiKey "${api_key}" \
    || ps_warn "Could not set geocoder.geocoder_provider.google_maps configuration.apiKey"
}

ps_index_offers_solr() {
  ps_drush search-api:clear offers -y 2>/dev/null || ps_warn "Could not clear offers index"
  ps_drush search-api:rebuild-tracker offers -y 2>/dev/null || ps_warn "Could not rebuild offers tracker"
  ps_retry 2 2 ps_drush search-api:index offers -y \
    || ps_die "Solr index failed (is Solr up? Are offers imported?)"
  ps_retry 2 2 ps_drush ps:search:features:sync-index --rebuild-tracker=1 -y \
    || ps_warn "Feature filter sync failed"
}
