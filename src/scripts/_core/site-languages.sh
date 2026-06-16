#!/usr/bin/env bash
# Property Search per-country language matrix (validated multisite design).

# Drupal langcode for site default (local language rule).
ps_site_default_langcode() {
  case "$1" in
    com|ie) printf 'en' ;;
    fr|be|lu) printf 'fr' ;;
    es) printf 'es' ;;
    it) printf 'it' ;;
    nl) printf 'nl' ;;
    pl) printf 'pl' ;;
    *) ps_die "Unknown country for default lang: $1" ;;
  esac
}

# Active Drupal langcodes (space-separated). English from minimal install — listed when required.
ps_site_language_codes() {
  case "$1" in
    com) printf 'en fr' ;;
    fr) printf 'fr en' ;;
    be) printf 'en fr nl' ;;
    es) printf 'en es' ;;
    ie) printf 'en' ;;
    it) printf 'en it' ;;
    lu) printf 'en fr lb' ;;
    pl) printf 'en pl' ;;
    nl) printf 'en nl' ;;
    *) ps_die "Unknown country for languages: $1" ;;
  esac
}

# Langcodes hidden from the front language switcher (still enabled for admin/contrib).
ps_site_hidden_front_languages() {
  case "$1" in
    fr) printf 'en' ;;
    *) printf '' ;;
  esac
}

# Whether URL path prefixes are used (multi-language sites).
ps_site_uses_language_prefixes() {
  case "$1" in
    fr|ie) return 1 ;;
    *) return 0 ;;
  esac
}

# Config split id for per-country language.negotiation.
ps_site_language_split_id() {
  printf 'language_%s' "$1"
}

# Import config language overrides for each active language of a country.
ps_import_active_language_config_overrides() {
  local country="$1"
  local lang
  for lang in $(ps_site_language_codes "${country}"); do
    ps_info "Importing config overrides for lang=${lang} (${country})..."
    ps_drush php:script scripts/import_language_config_overrides.php "${lang}" \
      || ps_warn "Config override import failed for ${lang} on ${country}"
  done
}

# Write config_split.language_{country} from sync storage into active config.
ps_import_language_split_entity() {
  local country="$1"
  local split_id name

  split_id="$(ps_site_language_split_id "${country}")"
  name="config_split.config_split.${split_id}"

  ps_drush ev "
    \$name = '${name}';
    \$sync = \Drupal::service('config.storage.sync');
    if (!\$sync->exists(\$name)) {
      throw new \RuntimeException('Missing sync config: ' . \$name);
    }
    \Drupal::service('config.storage')->write(\$name, \$sync->read(\$name));
    echo 'Imported split entity: ' . \$name . PHP_EOL;
  "
}

# Add enabled languages and default langcode (after site:install).
ps_add_site_languages() {
  local country="$1"
  local default_lang lang_line lang

  default_lang="$(ps_site_default_langcode "${country}")"
  ps_info "Adding languages for ${country} (default=${default_lang})..."

  lang_line="$(ps_site_language_codes "${country}")"
  for lang in ${lang_line}; do
    if ps_drush language:info 2>/dev/null | grep -q "(${lang})"; then
      continue
    fi
    ps_retry 2 2 ps_drush language:add "${lang}" --skip-translations -y \
      || ps_warn "Could not add language ${lang} on ${country}"
  done

  ps_drush config:set -y system.site default_langcode "${default_lang}"
}

# Drush --source path (relative to Drupal web root after bootstrap).
ps_site_language_negotiation_source() {
  printf '../config/env/languages/%s' "$1"
}

# Import language.negotiation YAML (partial config:import; csim alias unavailable in Drush 13).
ps_apply_site_language_negotiation() {
  local country="$1"
  local split_id source

  split_id="$(ps_site_language_split_id "${country}")"
  source="$(ps_site_language_negotiation_source "${country}")"

  ps_require_file "${PS_SRC_DIR}/config/env/languages/${country}/language.negotiation.yml" \
    "Missing language.negotiation for ${country}"

  ps_import_language_split_entity "${country}"

  ps_info "Applying language negotiation for ${country} (config:import ${source})..."
  if ps_retry 2 2 ps_drush config:import --partial --source="${source}" -y; then
    ps_success "Language negotiation applied: ${split_id}"
  else
    ps_warn "language.negotiation import failed for ${country}"
  fi
  ps_drush cr
}

# Legacy wrapper (used if called in one shot after config_split is available).
ps_configure_site_languages() {
  local country="$1"
  ps_add_site_languages "${country}"
  ps_apply_site_language_negotiation "${country}"
}
