<?php

declare(strict_types=1);

/**
 * @file
 * Property Search environment bootstrap (dotenv, DB, Solr, files, trusted hosts).
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

// Locale translation downloads: per-site public files directory.
$config['locale.settings']['translation']['path'] = $publicPath . '/translations';

// Trusted host patterns from all configured front/admin domains.
$trustedPatterns = [];
foreach (ps_country_codes() as $code) {
  $upper = strtoupper($code);
  foreach (['APP_DOMAIN_' . $upper, 'APP_DOMAIN_' . $upper . '_ADMIN'] as $envKey) {
    $host = ps_env($envKey);
    if ($host === '') {
      continue;
    }
    $escaped = preg_quote($host, '/');
    $trustedPatterns[] = '^' . $escaped . '$';
  }
}
$serviceDomain = ps_env('APP_DOMAIN_SERVICE');
if ($serviceDomain !== '') {
  $trustedPatterns[] = '^' . preg_quote($serviceDomain, '/') . '$';
}
$probesDomain = ps_env('APP_DOMAIN_PROBES');
if ($probesDomain !== '') {
  $trustedPatterns[] = '^' . preg_quote($probesDomain, '/') . '$';
}
if ($trustedPatterns !== []) {
  $settings['trusted_host_patterns'] = array_values(array_unique($trustedPatterns));
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

// Memcached (when drupal/memcache is enabled).
$cacheHost = ps_env('CACHE_HOST');
if ($cacheHost === '') {
  $cacheHost = ps_env('CHACHE_HOST');
}
if ($cacheHost !== '') {
  $settings['memcache']['servers'] = [$cacheHost . ':11211' => 'default'];
  $settings['memcache']['bins'] = ['default' => 'default'];
  $settings['cache_default_class'] = 'MemcacheBackend';
}

// Infrastructure overrides (Solr connector per country).
$solrHost = ps_env('SOLR_HOST', 'solr');
$solrPort = (int) ps_env('SOLR_PORT', '8983');
$solrPath = ps_env('SOLR_PATH', '/');
$solrCoreKey = 'SOLR_CORE_' . strtoupper($ps_country_code);
$solrCore = ps_env($solrCoreKey, 'ps_project');

$config['search_api.server.ps_solr']['backend_config']['connector_config']['scheme'] = 'http';
$config['search_api.server.ps_solr']['backend_config']['connector_config']['host'] = $solrHost;
$config['search_api.server.ps_solr']['backend_config']['connector_config']['port'] = $solrPort;
$config['search_api.server.ps_solr']['backend_config']['connector_config']['path'] = $solrPath;
$config['search_api.server.ps_solr']['backend_config']['connector_config']['core'] = $solrCore;

// Config Split: local dev overrides only (see config/env/local/).
if (ps_env('APP_ENV', 'dev') === 'dev') {
  $config['config_split.config_split.local']['status'] = TRUE;
}

// Languages enabled but hidden on the front language switcher (manifest-driven).
$hiddenFrontLanguages = ps_country_hidden_front_languages($ps_country_code);
if ($hiddenFrontLanguages !== []) {
  $settings['ps_hidden_front_languages'] = $hiddenFrontLanguages;
}

// Mailpit defaults for local Docker (overridable in settings.local.php).
if (ps_env('APP_ENV', 'dev') === 'dev') {
  $mailpitHost = ps_env('MAILPIT_HOST', 'mailpit');
  $mailpitPort = (int) ps_env('MAILPIT_PORT', '1025');
  $config['mailer_transport.settings']['default_transport'] = 'sendmail';
  $config['mailer_transport.mailer_transport.sendmail']['plugin'] = 'smtp';
  $config['mailer_transport.mailer_transport.sendmail']['configuration']['user'] = '';
  $config['mailer_transport.mailer_transport.sendmail']['configuration']['pass'] = '';
  $config['mailer_transport.mailer_transport.sendmail']['configuration']['host'] = $mailpitHost;
  $config['mailer_transport.mailer_transport.sendmail']['configuration']['port'] = $mailpitPort;
  $config['mailer_transport.mailer_transport.sendmail']['configuration']['query']['verify_peer'] = FALSE;
}
