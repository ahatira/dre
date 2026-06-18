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
