<?php

declare(strict_types=1);

/**
 * @file
 * Property Search environment bootstrap (dotenv, DB, files, memcache, trusted hosts).
 *
 * Included from sites/default/settings.php after $ps_country_code is set.
 */

require_once __DIR__ . '/../load-env.php';
ps_load_env();

if (!isset($ps_country_code) || !is_string($ps_country_code) || $ps_country_code === '') {
  $ps_country_code = 'com';
}

$ps_country_code = strtolower($ps_country_code);
if (!in_array($ps_country_code, ps_country_codes(), TRUE)) {
  throw new \RuntimeException(sprintf('Invalid Property Search country code: %s', $ps_country_code));
}

$settings['ps_country_code'] = $ps_country_code;

// Per-country configuration sync (full CMI export per site).
$settings['config_sync_directory'] = '../config/sites/' . $ps_country_code;

// Database connection per country.
$dbKey = 'DB_NAME_' . strtoupper($ps_country_code);
$databaseName = ps_env($dbKey);
if ($databaseName === '') {
  throw new \RuntimeException(sprintf('Missing required environment variable: %s', $dbKey));
}

$databases['default']['default'] = [
  'database' => $databaseName,
  'username' => ps_env('DB_USER', 'drupal'),
  'password' => ps_env('DB_PASSWORD', 'drupal'),
  'prefix' => '',
  'host' => ps_env('DB_HOST', 'postgres'),
  'port' => (int) ps_env('DB_PORT', '5432'),
  'driver' => 'pgsql',
  'namespace' => 'Drupal\\pgsql\\Driver\\Database\\pgsql',
  'autoload' => 'core/modules/pgsql/src/Driver/Database/pgsql/',
];

// Hash salt: prefer env, keep install stability when unset.
$hashSalt = ps_env('HASH_SALT');
if ($hashSalt !== '') {
  $settings['hash_salt'] = $hashSalt;
}

// Public / private / temp / assets — per country where applicable (never shared).
$publicPath = ps_resolve_public_files_path($ps_country_code);
$privatePath = ps_resolve_private_files_path($ps_country_code, $app_root);
$tempPath = ps_resolve_temp_files_path($ps_country_code, $app_root);

$settings['file_public_path'] = $publicPath;
$settings['file_private_path'] = $privatePath;
$settings['ps_file_public_path'] = $publicPath;
$settings['ps_file_private_path'] = $privatePath;

if ($tempPath !== '') {
  $settings['file_temp_path'] = $tempPath;
}

$assetsPath = ps_resolve_assets_files_path($ps_country_code);
if ($assetsPath !== '') {
  $settings['file_assets_path'] = $assetsPath;
}

// Versioned contrib/core UI translations (flat layout; see translations/contrib/README.md).
$config['locale.settings']['translation']['path'] = '../translations/contrib';
// Local files only — no remote fetch on install/module enable (use: make translations-fetch).
if (getenv('PS_LOCALE_FETCH') === '1') {
  $config['locale.settings']['translation']['use_source'] = 'remote_and_local';
  $config['locale.settings']['translation']['import_enabled'] = TRUE;
}
else {
  $config['locale.settings']['translation']['use_source'] = 'local';
  $config['locale.settings']['translation']['import_enabled'] = FALSE;
}

// Trusted host patterns from all configured front/admin/infra domains.
$trustedPatterns = ps_build_trusted_host_patterns();
if ($trustedPatterns !== []) {
  $settings['trusted_host_patterns'] = $trustedPatterns;
}

// Outbound HTTP proxy.
$httpProxy = ps_env('HTTP_PROXY');
if ($httpProxy !== '') {
  $settings['http_client_config']['proxy']['http'] = $httpProxy;
  $httpsProxy = ps_env('HTTPS_PROXY');
  $settings['http_client_config']['proxy']['https'] = $httpsProxy !== '' ? $httpsProxy : $httpProxy;
}
$noProxy = ps_env('NO_PROXY');
if ($noProxy !== '') {
  $settings['http_client_config']['proxy']['no'] = array_values(array_filter(array_map('trim', explode(',', $noProxy))));
}

// Memcached — only when contrib module is present and the server is reachable.
if (ps_memcache_bootstrap_enabled($app_root, $databases['default']['default'])) {
  $cacheHost = ps_env('CACHE_HOST');
  if ($cacheHost === '') {
    $cacheHost = ps_env('CHACHE_HOST');
  }
  $settings['memcache']['servers'] = [$cacheHost . ':11211' => 'default'];
  $settings['memcache']['bins'] = ['default' => 'default'];
  $settings['memcache']['key_prefix'] = ps_memcache_key_prefix($ps_country_code);
  $settings['cache']['default'] = 'cache.backend.memcache';
}

// Solr connector — dev only (SOLR_* in src/.env; ps_php overrides SOLR_HOST=solr).
// Prod/int: config_ignore + drush config:set (see docs/MULTISITE_OPS.md).
ps_apply_search_api_solr_connector_overrides($config, $ps_country_code);

// Config Split: local dev overrides only (see config/env/local/).
if (ps_env('APP_ENV', 'dev') === 'dev') {
  $config['config_split.config_split.local']['status'] = TRUE;
}

// Languages enabled but hidden on the front language switcher (manifest-driven).
$hiddenFrontLanguages = ps_country_hidden_front_languages($ps_country_code);
if ($hiddenFrontLanguages !== []) {
  $settings['ps_hidden_front_languages'] = $hiddenFrontLanguages;
}
