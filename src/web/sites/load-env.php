<?php

declare(strict_types=1);

/**
 * @file
 * Early environment bootstrap for multisite (sites.php + settings).
 */

require_once __DIR__ . '/countries.php';

if (!function_exists('ps_app_env')) {
  /**
   * Current application environment (dev, int, staging, prod).
   */
  function ps_app_env(): string {
    $value = $_ENV['APP_ENV'] ?? getenv('APP_ENV');
    if ($value === FALSE || $value === NULL || $value === '') {
      return 'dev';
    }
    return (string) $value;
  }
}

if (!function_exists('ps_load_env')) {
  /**
   * Loads Composer autoload and, in dev only, src/.env via symfony/dotenv.
   *
   * INT, staging and production rely on system environment variables only.
   */
  function ps_load_env(): void {
    static $loaded = FALSE;
    if ($loaded) {
      return;
    }
    $loaded = TRUE;

    $composerRoot = dirname(__DIR__, 2);
    $autoload = $composerRoot . '/vendor/autoload.php';
    if (is_readable($autoload)) {
      require_once $autoload;
    }

    if (ps_app_env() !== 'dev') {
      return;
    }

    if (!class_exists(\Symfony\Component\Dotenv\Dotenv::class)) {
      return;
    }

    $envFile = $composerRoot . '/.env';
    if (!is_readable($envFile)) {
      $envFile = $composerRoot . '/.env.dist';
    }
    if (!is_readable($envFile)) {
      return;
    }

    $dotenv = new \Symfony\Component\Dotenv\Dotenv();
    $dotenv->usePutenv(FALSE)->loadEnv($envFile, 'APP_ENV', 'dev');
  }
}

if (!function_exists('ps_env')) {
  /**
   * Reads an environment variable with optional default.
   *
   * Missing, null and empty/whitespace-only values are treated as unset.
   */
  function ps_env(string $key, ?string $default = NULL): string {
    $value = $_ENV[$key] ?? getenv($key);
    if ($value === FALSE || $value === NULL) {
      return $default ?? '';
    }
    $value = trim((string) $value);
    if ($value === '') {
      return $default ?? '';
    }
    return $value;
  }
}

if (!function_exists('ps_env_hosts')) {
  /**
   * Parses a comma-separated environment variable into unique hostnames.
   *
   * @return string[]
   *   Lowercase trimmed hostnames (no port).
   */
  function ps_env_hosts(string $key): array {
    $raw = ps_env($key);
    if ($raw === '') {
      return [];
    }
    $hosts = [];
    foreach (explode(',', $raw) as $part) {
      $part = strtolower(trim($part));
      if ($part !== '') {
        $hosts[] = $part;
      }
    }
    return array_values(array_unique($hosts));
  }
}

if (!function_exists('ps_country_http_port')) {
  /**
   * HTTP port for a country (APP_DOMAIN_{CODE}_PORT, else manifest dev_port in dev).
   */
  function ps_country_http_port(string $countryCode): int {
    $upper = strtoupper($countryCode);
    $port = ps_env('APP_DOMAIN_' . $upper . '_PORT');
    if ($port !== '' && ctype_digit($port)) {
      return (int) $port;
    }
    if (ps_app_env() === 'dev') {
      return ps_country_dev_port($countryCode);
    }
    return 80;
  }
}

if (!function_exists('ps_sites_map_key')) {
  /**
   * Drupal sites.php map key for a host and optional non-standard port.
   */
  function ps_sites_map_key(string $host, ?int $port = NULL): string {
    if ($port !== NULL && $port !== 80 && $port !== 443) {
      return $port . '.' . $host;
    }
    return $host;
  }
}

if (!function_exists('ps_sites_register_hosts')) {
  /**
   * Registers host → site_dir entries in the sites.php map.
   *
   * @param string[] $hosts
   *   Hostnames (no port).
   * @param bool $portQualifiedOnly
   *   When TRUE, only register {port}.{host} keys (infra/service routing).
   */
  function ps_sites_register_hosts(array &$sites, array $hosts, string $siteDir, ?int $port = NULL, bool $portQualifiedOnly = FALSE): void {
    foreach ($hosts as $host) {
      if ($host === '') {
        continue;
      }
      if (!$portQualifiedOnly) {
        $sites[$host] = $siteDir;
      }
      if ($port !== NULL && $port !== 80 && $port !== 443) {
        $sites[ps_sites_map_key($host, $port)] = $siteDir;
      }
    }
  }
}

if (!function_exists('ps_build_multisite_sites_map')) {
  /**
   * Builds the full $sites map for sites.php from env + countries manifest.
   *
   * Country front/admin hosts support comma-separated lists. Non-standard ports
   * add Drupal keys "{port}.{host}". SERVICE/PROBES hosts (Option C) map per
   * country via that country's port only ({port}.{infra_host} → site_dir).
   *
   * @return array<string, string>
   */
  function ps_build_multisite_sites_map(): array {
    $sites = [];

    foreach (ps_country_codes() as $countryCode) {
      $upper = strtoupper($countryCode);
      $frontHosts = ps_env_hosts('APP_DOMAIN_' . $upper);
      if ($frontHosts === []) {
        continue;
      }

      $siteDir = ps_country_site_dir($countryCode);
      $port = ps_country_http_port($countryCode);
      ps_sites_register_hosts($sites, $frontHosts, $siteDir, $port);
      ps_sites_register_hosts($sites, ps_env_hosts('APP_DOMAIN_' . $upper . '_ADMIN'), $siteDir, $port);
    }

    $infraHosts = array_values(array_unique(array_merge(
      ps_env_hosts('APP_DOMAIN_SERVICE'),
      ps_env_hosts('APP_DOMAIN_PROBES'),
    )));
    if ($infraHosts !== []) {
      foreach (ps_country_codes() as $countryCode) {
        $siteDir = ps_country_site_dir($countryCode);
        $port = ps_country_http_port($countryCode);
        ps_sites_register_hosts($sites, $infraHosts, $siteDir, $port, TRUE);
      }
    }

    return $sites;
  }
}

if (!function_exists('ps_build_trusted_host_patterns')) {
  /**
   * Trusted host regex patterns for all configured front, admin and infra hosts.
   *
   * @return string[]
   */
  function ps_build_trusted_host_patterns(): array {
    $hosts = [];
    foreach (ps_country_codes() as $code) {
      $upper = strtoupper($code);
      $hosts = array_merge($hosts, ps_env_hosts('APP_DOMAIN_' . $upper));
      $hosts = array_merge($hosts, ps_env_hosts('APP_DOMAIN_' . $upper . '_ADMIN'));
    }
    $hosts = array_merge($hosts, ps_env_hosts('APP_DOMAIN_SERVICE'));
    $hosts = array_merge($hosts, ps_env_hosts('APP_DOMAIN_PROBES'));
    $hosts = array_values(array_unique($hosts));

    $patterns = [];
    foreach ($hosts as $host) {
      $patterns[] = '^' . preg_quote($host, '/') . '$';
    }
    return $patterns;
  }
}

if (!function_exists('ps_apply_search_api_solr_connector_overrides')) {
  /**
   * Applies per-environment Solr connector overrides via $config (settings.php).
   *
   * Only connector_config keys are set so other search_api.server.ps_solr
   * backend_config values remain editable in the UI and exportable via CMI.
   * config_ignore excludes backend_config from config import/export in prod.
   *
   * @param array<string, mixed> $config
   *   Drupal $config overrides array from settings.php.
   */
  function ps_apply_search_api_solr_connector_overrides(array &$config, string $countryCode): void {
    $solrHost = ps_env('SOLR_HOST', 'solr');
    $solrPort = (int) ps_env('SOLR_PORT', '8983');
    $solrPath = ps_env('SOLR_PATH', '/');
    $solrCoreKey = 'SOLR_CORE_' . strtoupper($countryCode);
    $solrCore = ps_env($solrCoreKey, 'ps_project');

    $config['search_api.server.ps_solr']['backend_config']['connector_config']['scheme'] = 'http';
    $config['search_api.server.ps_solr']['backend_config']['connector_config']['host'] = $solrHost;
    $config['search_api.server.ps_solr']['backend_config']['connector_config']['port'] = $solrPort;
    $config['search_api.server.ps_solr']['backend_config']['connector_config']['path'] = $solrPath;
    $config['search_api.server.ps_solr']['backend_config']['connector_config']['core'] = $solrCore;
  }
}

if (!function_exists('ps_env_path_base')) {
  /**
   * Global path base from env (empty when unset or blank).
   */
  function ps_env_path_base(string $key): string {
    return ps_env($key);
  }
}

if (!function_exists('ps_composer_root')) {
  /**
   * Composer project root (parent of the Drupal web root).
   */
  function ps_composer_root(string $appRoot): string {
    return dirname($appRoot);
  }
}

if (!function_exists('ps_path_is_absolute')) {
  /**
   * Whether a path string is absolute.
   */
  function ps_path_is_absolute(string $path): bool {
    return $path !== '' && ($path[0] === '/' || (PHP_OS_FAMILY === 'Windows' && preg_match('/^[A-Za-z]:[\\\\\\/]/', $path)));
  }
}

if (!function_exists('ps_resolve_path_from_base')) {
  /**
   * Builds a path from a global env base + optional site_dir segment.
   *
   * Absolute base → {base}/{site_dir} or {base} when site_dir is NULL.
   * Relative base → {composerRoot}/{base}/{site_dir} or {composerRoot}/{base}.
   */
  function ps_resolve_path_from_base(string $base, string $appRoot, ?string $siteDir = NULL): string {
    $base = rtrim($base, '/');
    $suffix = $siteDir !== NULL ? '/' . $siteDir : '';
    if (ps_path_is_absolute($base)) {
      return $base . $suffix;
    }
    return ps_composer_root($appRoot) . '/' . ltrim($base . $suffix, '/');
  }
}

if (!function_exists('ps_resolve_public_files_path')) {
  /**
   * Relative public files path under the Drupal web root.
   *
   * | APP_PUBLIC_PATH | Result (site_dir=france)        |
   * |-----------------|-------------------------------|
   * | unset / empty   | sites/france/files              |
   * | sites           | sites/france/files              |
   */
  function ps_resolve_public_files_path(string $countryCode): string {
    $siteDir = ps_country_site_dir($countryCode);
    $base = ps_env_path_base('APP_PUBLIC_PATH');
    if ($base === '') {
      return 'sites/' . $siteDir . '/files';
    }
    if (ps_path_is_absolute($base)) {
      throw new \RuntimeException(sprintf(
        'APP_PUBLIC_PATH must be relative to the Drupal web root, got: %s',
        $base
      ));
    }
    return rtrim($base, '/') . '/' . $siteDir . '/files';
  }
}

if (!function_exists('ps_private_path_is_configured')) {
  /**
   * Whether private files path comes from APP_PRIVATE_PATH (not the dev default).
   */
  function ps_private_path_is_configured(): bool {
    return ps_env_path_base('APP_PRIVATE_PATH') !== '';
  }
}

if (!function_exists('ps_resolve_private_files_path')) {
  /**
   * Absolute private files path (outside web root).
   *
   * | APP_PRIVATE_PATH | Result (site_dir=france)           |
   * |------------------|------------------------------------|
   * | unset / empty    | {composerRoot}/private/france      |
   * | /mnt/private     | /mnt/private/france                |
   */
  function ps_resolve_private_files_path(string $countryCode, string $appRoot): string {
    $siteDir = ps_country_site_dir($countryCode);
    $base = ps_env_path_base('APP_PRIVATE_PATH');
    if ($base !== '') {
      return ps_resolve_path_from_base($base, $appRoot, $siteDir);
    }
    return ps_composer_root($appRoot) . '/private/' . $siteDir;
  }
}

if (!function_exists('ps_resolve_temp_files_path')) {
  /**
   * Absolute temp path — shared across all country sites (no site_dir suffix).
   *
   * | APP_TEMP_PATH | Result              |
   * |---------------|---------------------|
   * | unset / empty | '' (Drupal default) |
   * | /tmp          | /tmp                |
   */
  function ps_resolve_temp_files_path(string $countryCode, string $appRoot): string {
    unset($countryCode);
    $base = ps_env_path_base('APP_TEMP_PATH');
    if ($base === '') {
      return '';
    }
    return ps_resolve_path_from_base($base, $appRoot, NULL);
  }
}

if (!function_exists('ps_resolve_assets_files_path')) {
  /**
   * Path for Drupal aggregated CSS/JS ($settings['file_assets_path']).
   *
   * | APP_ASSETS_PATH | Result (site_dir=france)                    |
   * |-----------------|---------------------------------------------|
   * | unset / empty   | '' → Drupal uses public files (recommended) |
   * | assets          | assets/france (relative to web root)        |
   * | /mnt/assets     | /mnt/assets/france                          |
   */
  function ps_resolve_assets_files_path(string $countryCode): string {
    $base = ps_env_path_base('APP_ASSETS_PATH');
    if ($base === '') {
      return '';
    }
    $siteDir = ps_country_site_dir($countryCode);
    if (ps_path_is_absolute($base)) {
      return rtrim($base, '/') . '/' . $siteDir;
    }
    return rtrim($base, '/') . '/' . $siteDir;
  }
}
