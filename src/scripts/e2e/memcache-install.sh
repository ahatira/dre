#!/usr/bin/env bash
# E2E: memcache + memcache_admin install via install helper (Docker ps_php when needed).
set -euo pipefail

# shellcheck disable=SC1091
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)/_core/bootstrap.sh"

COUNTRY="${COUNTRY:-fr}"
ps_drush_for_country "${COUNTRY}"

ps_header "E2E memcache install (${PS_DRUSH_ALIAS})"

ps_require_file "${PS_WEB_DIR}/modules/contrib/memcache/memcache.info.yml" \
  "Run: cd src && composer require drupal/memcache:^2.8"

if ! ps_php_container_drush_available; then
  ps_die "ps_php container required for this test (make up)"
fi

ps_info "Resetting memcache stack for clean install test..."
ps_drush_in_php_container pm:uninstall -y memcache_admin memcache 2>/dev/null || true
ps_drush_cr

ps_enable_memcache_if_available

enabled="$(ps_drush_in_php_container pm:list --status=enabled --filter=memcache --format=list)"
grep -q '^memcache$' <<< "${enabled}" || ps_die "memcache not enabled after install"
grep -q '^memcache_admin$' <<< "${enabled}" || ps_die "memcache_admin not enabled after install"

bootstrap="$(ps_drush_in_php_container ev '$db = \Drupal\Core\Database\Database::getConnectionInfo()["default"]; echo ps_memcache_bootstrap_enabled(DRUPAL_ROOT, $db) ? "yes" : "no";' 2>/dev/null | tr -d '[:space:]')"
[[ "${bootstrap}" == "yes" ]] || ps_die "Memcache bootstrap not active in ps_php (expected yes, got ${bootstrap:-empty})"

cache_backend="$(ps_drush_in_php_container ev 'echo \Drupal\Core\Site\Settings::get("cache")["default"] ?? "unset";' 2>/dev/null | tr -d '[:space:]')"
[[ "${cache_backend}" == "cache.backend.memcache" ]] \
  || ps_die "Expected cache.backend.memcache in ps_php settings, got: ${cache_backend:-empty}"

cache_class="$(ps_drush_in_php_container ev 'echo get_class(\Drupal::service("cache.default"));' 2>/dev/null | tr -d '[:space:]')"
[[ "${cache_class}" == *"Memcache"* ]] \
  || ps_die "Expected Memcache cache backend in ps_php, got: ${cache_class:-empty}"

expected_prefix="$(ps_drush_in_php_container ev 'echo ps_memcache_key_prefix(\Drupal\Core\Site\Settings::get("ps_country_code"));' 2>/dev/null | tr -d '[:space:]')"
actual_prefix="$(ps_drush_in_php_container ev 'echo \Drupal\Core\Site\Settings::get("memcache")["key_prefix"] ?? "";' 2>/dev/null | tr -d '[:space:]')"
[[ "${actual_prefix}" == "${expected_prefix}" ]] \
  || ps_die "Expected memcache key_prefix=${expected_prefix}, got: ${actual_prefix:-empty}"

ps_success "memcache + memcache_admin installed; MemcacheBackend active in ps_php"
ps_info "Admin UI: http://fr.localhost:8083/admin/reports/memcache (requires login + permission)"
