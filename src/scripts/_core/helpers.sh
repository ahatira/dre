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
    elif [[ "${module}" == "ps_offer" ]]; then
      ps_recover_ps_offer_if_partial
    fi
    ps_refresh_field_type_cache 2>/dev/null || true
    if [[ ${n} -ge ${attempts} ]]; then
      return 1
    fi
    sleep "${delay}"
    n=$((n + 1))
  done
}

ps_memcache_modules_enabled() {
  local enabled
  enabled="$(ps_drush pm:list --status=enabled --filter=memcache --format=list 2>/dev/null || true)"
  grep -q '^memcache$' <<< "${enabled}" && grep -q '^memcache_admin$' <<< "${enabled}"
}

ps_enable_memcache_if_available() {
  local module_info="${PS_WEB_DIR}/modules/contrib/memcache/memcache.info.yml"
  if [[ ! -f "${module_info}" ]]; then
    ps_warn "memcache module not found (run: make build or composer install) — using database cache"
    return 0
  fi
  if ps_memcache_modules_enabled; then
    ps_info "memcache + memcache_admin already enabled"
    return 0
  fi

  ps_info "Enabling memcache + memcache_admin..."
  local enabled=0
  if ps_memcache_php_extension_available; then
    ps_retry 2 2 ps_drush en -y memcache memcache_admin && enabled=1
  elif ps_php_container_drush_available; then
    local php_container="${PS_PHP_CONTAINER:-ps_php}"
    ps_info "Host PHP lacks memcache extension — enabling via ${php_container} container..."
    ps_drush_in_php_container en -y memcache memcache_admin && enabled=1
  else
    ps_warn "Host PHP lacks memcache extension and ps_php is unavailable — trying host Drush..."
    ps_retry 2 2 ps_drush en -y memcache memcache_admin && enabled=1
  fi

  if [[ ${enabled} -eq 1 ]] && ps_memcache_modules_enabled; then
    ps_drush_cr
    return 0
  fi

  ps_warn "memcache enable failed — using database cache (web container still uses Memcache when reachable)"
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

ps_ensure_bnp_media_foundation() {
  ps_info "Ensuring BNP Media foundation (image field storage)..."
  ps_drush ev '
    $moduleInstaller = \Drupal::service("module_installer");
    $moduleHandler = \Drupal::moduleHandler();
    foreach (["image", "file", "media", "media_library"] as $module) {
      if (!$moduleHandler->moduleExists($module)) {
        $moduleInstaller->install([$module], TRUE);
        echo "enabled {$module}\n";
      }
    }
    if (!$moduleHandler->moduleExists("bnp_media")) {
      throw new \RuntimeException("bnp_media must be enabled before ps_offer");
    }
    $storage = \Drupal::configFactory()->get("field.storage.media.field_media_gallery_image");
    if ($storage->isNew()) {
      $source = new \Drupal\Core\Config\FileStorage(DRUPAL_ROOT . "/modules/custom/bnp_media/config/install");
      if ($source->exists("field.storage.media.field_media_gallery_image")) {
        \Drupal::configFactory()->getEditable("field.storage.media.field_media_gallery_image")
          ->setData($source->read("field.storage.media.field_media_gallery_image"))
          ->save(TRUE);
        echo "imported field.storage.media.field_media_gallery_image\n";
      }
    }
    if (\Drupal::configFactory()->get("field.storage.media.field_media_gallery_image")->isNew()) {
      throw new \RuntimeException("field.storage.media.field_media_gallery_image is missing");
    }
    echo "bnp_media foundation OK\n";
  ' || return 1
}

ps_ensure_telephone_field_stack() {
  ps_info "Ensuring telephone field stack (ps_agent)..."
  ps_drush ev '
    $moduleInstaller = \Drupal::service("module_installer");
    $moduleHandler = \Drupal::moduleHandler();
    foreach (["telephone", "telephone_formatter"] as $module) {
      if (!$moduleHandler->moduleExists($module)) {
        $moduleInstaller->install([$module], TRUE);
        echo "enabled {$module}\n";
      }
    }
    \Drupal::service("plugin.manager.field.field_type")->clearCachedDefinitions();
    if (!\Drupal::service("plugin.manager.field.field_type")->hasDefinition("telephone")) {
      throw new \RuntimeException("telephone field type is missing");
    }
    echo "telephone field stack OK\n";
  ' || return 1
}

ps_ensure_entity_browser_stack() {
  ps_info "Ensuring Entity Browser stack (bnp_media)..."
  ps_drush ev '
    $moduleInstaller = \Drupal::service("module_installer");
    $moduleHandler = \Drupal::moduleHandler();
    foreach ([
      "embed",
      "entity_embed",
      "entity_browser",
      "entity_browser_enhanced",
      "entity_browser_entity_form",
    ] as $module) {
      if (!$moduleHandler->moduleExists($module)) {
        $moduleInstaller->install([$module], TRUE);
        echo "enabled {$module}\n";
      }
    }
    \Drupal::service("entity_type.manager")->clearCachedDefinitions();
    if (!\Drupal::entityTypeManager()->hasDefinition("entity_browser")) {
      throw new \RuntimeException("entity_browser entity type is missing");
    }
    echo "entity_browser stack OK\n";
  ' || return 1
}

ps_refresh_field_type_cache() {
  ps_drush ev '
    $moduleInstaller = \Drupal::service("module_installer");
    $moduleHandler = \Drupal::moduleHandler();
    foreach (["image", "file", "address", "geofield", "telephone"] as $module) {
      if (!$moduleHandler->moduleExists($module)) {
        $moduleInstaller->install([$module], TRUE);
      }
    }
    \Drupal::service("plugin.manager.field.field_type")->clearCachedDefinitions();
    echo "field type cache refreshed\n";
  ' >/dev/null 2>&1 || return 1
}

ps_recover_ps_offer_if_partial() {
  if ps_drush pm:list --status=enabled --filter=ps_offer --format=list 2>/dev/null | grep -q '^ps_offer$'; then
    if ! ps_verify_ps_offer_install 2>/dev/null; then
      ps_warn "ps_offer partially installed — uninstalling before retry"
      ps_drush pm:uninstall ps_offer -y 2>/dev/null || true
      ps_drush_cr
    fi
    return 0
  fi

  local orphan
  orphan="$(ps_drush ev 'echo \Drupal::config("node.type.offer")->isNew() ? "no" : "yes";' 2>/dev/null || echo no)"
  if [[ "${orphan}" == "yes" ]]; then
    ps_warn "ps_offer orphan config detected — removing node.type.offer before retry"
    ps_drush config:delete node.type.offer -y 2>/dev/null || true
    ps_drush_cr
  fi
}

ps_ensure_ps_search_stack() {
  ps_info "Ensuring Search API Solr stack (index offers)..."
  ps_drush ev '
    $moduleHandler = \Drupal::moduleHandler();
    $moduleInstaller = \Drupal::service("module_installer");
    foreach (["search_api", "search_api_solr"] as $module) {
      if (!$moduleHandler->moduleExists($module)) {
        try {
          $moduleInstaller->install([$module], TRUE);
          echo "enabled {$module}\n";
        }
        catch (\Throwable $e) {
          echo "warn enable {$module}: " . $e->getMessage() . "\n";
        }
      }
    }
    \Drupal::service("entity_type.manager")->clearCachedDefinitions();
    if (!\Drupal::entityTypeManager()->hasDefinition("search_api_index")) {
      echo "warn search_api_index entity type is missing — enable search_api and retry\n";
      return;
    }
    $source = new \Drupal\Core\Config\FileStorage(DRUPAL_ROOT . "/modules/custom/ps_search/config/install");
    $serverStorage = \Drupal::entityTypeManager()->getStorage("search_api_server");
    if ($serverStorage->load("ps_solr") === NULL && $source->exists("search_api.server.ps_solr")) {
      try {
        $serverStorage->createFromStorageRecord($source->read("search_api.server.ps_solr"))->save();
        echo "created search_api.server.ps_solr\n";
      }
      catch (\Throwable $e) {
        echo "warn search_api.server.ps_solr: " . $e->getMessage() . "\n";
      }
    }
    $indexStorage = \Drupal::entityTypeManager()->getStorage("search_api_index");
    if ($indexStorage->load("offers") === NULL && $source->exists("search_api.index.offers")) {
      try {
        $indexStorage->createFromStorageRecord($source->read("search_api.index.offers"))->save();
        echo "created search_api.index.offers\n";
      }
      catch (\Throwable $e) {
        echo "warn search_api.index.offers: " . $e->getMessage() . "\n";
      }
    }
    if ($indexStorage->load("offers") === NULL) {
      echo "warn search_api.index.offers missing — search degraded until Solr is configured\n";
      return;
    }
    echo "search_api offers index OK\n";
  ' || ps_warn "Search API / Solr stack setup had warnings (install continues; search degraded without Solr)"
  return 0
}

ps_drush_po_path() {
  printf '%s' "$1"
}

ps_contrib_translations_dir() {
  printf '%s/translations/contrib' "${PS_SRC_DIR}"
}

# Returns 0 when the PO basename is a custom project file (not contrib/core cache).
ps_contrib_translation_is_custom_po() {
  local name="$1"
  [[ "${name}" == ps_* || "${name}" == bnp_* || "${name}" == ps_theme.* ]]
}

# Extracts the Drupal extension name from a contrib PO filename (e.g. views-3.0.fr.po → views).
ps_contrib_po_extension_from_filename() {
  local filename="$1"
  local stem="${filename%.po}"
  local prefix="${stem%.*}"

  if [[ "${prefix}" == drupal* ]]; then
    printf 'drupal'
    return 0
  fi

  if [[ "${prefix}" =~ ^(.+)-[0-9].*$ ]]; then
    printf '%s' "${BASH_REMATCH[1]}"
    return 0
  fi

  printf '%s' "${prefix}"
}

# Returns 0 when the PO targets an enabled module, theme, or Drupal core.
ps_contrib_translation_extension_enabled() {
  local extension="$1"
  local enabled_list="$2"

  if [[ "${extension}" == "drupal" ]]; then
    return 0
  fi

  echo "${enabled_list}" | grep -qx "${extension}"
}

ps_import_contrib_translations() {
  ps_resolve_runtime
  local cache_dir active_langs enabled_extensions imported=0 skipped=0 failed=0 missing=0
  cache_dir="$(ps_contrib_translations_dir)"

  if [[ ! -d "${cache_dir}" ]]; then
    ps_warn "Contrib translation cache missing: ${cache_dir} (run: make translations-fetch)"
    return 0
  fi

  active_langs="$(ps_drush ev 'echo implode(PHP_EOL, array_keys(\Drupal::languageManager()->getLanguages()));')"
  enabled_extensions="$(ps_drush pm:list --status=enabled --format=list 2>/dev/null)"
  enabled_extensions="$(printf '%s\n%s' "${enabled_extensions}" "$(ps_drush theme:list --status=enabled --format=list 2>/dev/null)")"

  import_po() {
    local po_file="$1" langcode="$2" drush_path
    [[ -n "${po_file}" && -n "${langcode}" ]] || return 1
    if ! echo "${active_langs}" | grep -q "^${langcode}$"; then
      skipped=$((skipped + 1))
      return 0
    fi
    drush_path="$(ps_drush_po_path "${po_file}")"
    if ps_drush locale:import "${langcode}" "${drush_path}" --type=not-customized --override=all -y >/dev/null 2>&1; then
      imported=$((imported + 1))
    else
      failed=$((failed + 1))
    fi
  }

  local po_file filename langcode extension found=0
  ps_contrib_translations_flatten

  while IFS= read -r po_file; do
    [[ -z "${po_file}" ]] && continue
    found=1
    filename=$(basename "${po_file}")
    if ps_contrib_translation_is_custom_po "${filename}"; then
      skipped=$((skipped + 1))
      continue
    fi
    langcode="${filename%.po}"
    langcode="${langcode##*.}"
    extension="$(ps_contrib_po_extension_from_filename "${filename}")"
    if ! ps_contrib_translation_extension_enabled "${extension}" "${enabled_extensions}"; then
      skipped=$((skipped + 1))
      continue
    fi
    import_po "${po_file}" "${langcode}"
  done < <(find "${cache_dir}" -maxdepth 1 -name '*.po' 2>/dev/null | sort)

  if [[ ${found} -eq 0 ]]; then
    missing=1
  fi

  ps_info "Contrib translations: imported=${imported}, skipped=${skipped}, failed=${failed}, empty_cache=${missing}"
  [[ ${failed} -eq 0 ]] || ps_warn "Some contrib translations failed to import"
  if [[ ${imported} -eq 0 && ${missing} -gt 0 ]]; then
    ps_warn "Contrib cache has no .po files yet — run: make translations-fetch (dev)"
  fi
}

ps_contrib_translations_flatten() {
  local cache_dir moved=0 po_file filename dest
  cache_dir="$(ps_contrib_translations_dir)"

  while IFS= read -r po_file; do
    [[ -z "${po_file}" ]] && continue
    filename=$(basename "${po_file}")
    dest="${cache_dir}/${filename}"
    if [[ "${po_file}" == "${dest}" ]]; then
      continue
    fi
    if [[ -f "${dest}" ]]; then
      rm -f "${po_file}"
      continue
    fi
    mv -f "${po_file}" "${dest}"
    moved=$((moved + 1))
  done < <(find "${cache_dir}" -mindepth 2 -name '*.po' 2>/dev/null | sort)

  if [[ ${moved} -gt 0 ]]; then
    ps_info "Flattened ${moved} contrib PO file(s) to ${cache_dir}/"
  fi
}

# @deprecated Use ps_contrib_translations_flatten — Drupal expects a flat translation.path.
ps_contrib_translations_organize() {
  ps_contrib_translations_flatten
}

ps_enable_locale_and_import_contrib_translations() {
  ps_info "Importing contrib/core translations from local cache..."
  ps_drush pm:enable locale -y 2>/dev/null || ps_drush en -y locale
  ps_import_contrib_translations
}

# Single-bootstrap translation import (contrib PO + custom PO + config overrides).
ps_import_all_translations_batch() {
  local country="${1:-${PS_COUNTRY_CODE:-com}}"
  ps_info "Importing translations (contrib, custom, config overrides)..."
  ps_info "Ensuring locale module is enabled..."
  ps_drush pm:enable locale -y 2>/dev/null || ps_drush en -y locale
  ps_drush ps:locale:import-batch --country="${country}" -y \
    || ps_warn "Translation batch import had warnings"
}

ps_contrib_translations_update_manifest() {
  ps_resolve_runtime
  PS_CACHE_DIR="$(ps_contrib_translations_dir)" \
  PS_SRC_DIR="${PS_SRC_DIR}" \
  PS_WEB_DIR="${PS_WEB_DIR}" \
  php -r '
    require getenv("PS_SRC_DIR") . "/vendor/autoload.php";
    $cache = getenv("PS_CACHE_DIR");
    $web = getenv("PS_WEB_DIR");
    $langs = [];
    $count = 0;
    foreach (glob($cache . "/*.po") ?: [] as $po) {
      $count++;
      $base = basename($po);
      if (preg_match("/\.([a-z]{2,3})\.po$/", $base, $m)) {
        $langs[$m[1]] = true;
      }
    }
    ksort($langs);
    $core = "unknown";
    $drupalPhp = $web . "/core/lib/Drupal.php";
    if (is_readable($drupalPhp) && preg_match("/const VERSION = '\''([^'\'']+)'\''/", file_get_contents($drupalPhp), $m)) {
      $core = $m[1];
    }
    $lockFile = getenv("PS_SRC_DIR") . "/composer.lock";
    $lockHash = is_readable($lockFile) ? substr(hash_file("sha256", $lockFile), 0, 16) : null;
    $data = [
      "generated_at" => gmdate("c"),
      "drupal_core" => $core,
      "composer_lock_hash" => $lockHash,
      "languages" => array_keys($langs),
      "po_files_count" => $count,
    ];
    file_put_contents(
      $cache . "/manifest.yml",
      \Symfony\Component\Yaml\Yaml::dump($data, 4, 2, \Symfony\Component\Yaml\Yaml::DUMP_EMPTY_ARRAY_AS_SEQUENCE)
    );
  '
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

# ps_theme source-only: compiled CSS must not be tracked (CI builds artefacts).
ps_check_ps_theme_source_only() {
  command -v git >/dev/null 2>&1 || return 0

  local git_root theme_prefix tracked
  git_root="$(git -C "${PS_REPO_ROOT}" rev-parse --show-toplevel 2>/dev/null)" || return 0
  theme_prefix="$(git -C "${git_root}" ls-files '**/ps_theme/ps_theme.info.yml' 2>/dev/null | head -1)"
  [[ -n "${theme_prefix}" ]] || return 0
  theme_prefix="${theme_prefix%/ps_theme.info.yml}"

  local tracked_compiled=()
  while IFS= read -r rel; do
    [[ -z "${rel}" ]] && continue
    if [[ "${rel}" == "${theme_prefix}/assets/css/"* ]]; then
      tracked_compiled+=("${rel}")
      continue
    fi
    if [[ "${rel}" == *".css.map" ]]; then
      tracked_compiled+=("${rel}")
      continue
    fi
    if [[ "${rel}" =~ ${theme_prefix}/components/.+/styles/.+\.css$ ]]; then
      local abs="${git_root}/${rel}"
      if [[ -f "${abs%.css}.scss" ]]; then
        tracked_compiled+=("${rel}")
      fi
    fi
  done < <(git -C "${git_root}" ls-files "${theme_prefix}/assets/css" "${theme_prefix}/components" 2>/dev/null \
    | grep -E '\.(css|css\.map)$' || true)

  if [[ ${#tracked_compiled[@]} -gt 0 ]]; then
    ps_error "ps_theme compiled CSS tracked in Git (source-only mode):"
    printf '  - %s\n' "${tracked_compiled[@]}" >&2
    ps_error "Run: git rm --cached <paths> — see ps_theme/.gitignore"
    return 1
  fi

  ps_success "ps_theme source-only Git OK"
  return 0
}
