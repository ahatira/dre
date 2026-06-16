<?php

declare(strict_types=1);

/**
 * @file
 * Early environment bootstrap for multisite (sites.php + settings).
 */

if (!function_exists('ps_load_env')) {
  /**
   * Loads Composer autoload and src/.env via symfony/dotenv.
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
   */
  function ps_env(string $key, ?string $default = NULL): string {
    $value = $_ENV[$key] ?? getenv($key);
    if ($value === FALSE || $value === NULL || $value === '') {
      return $default ?? '';
    }
    return (string) $value;
  }
}

if (!function_exists('ps_country_codes')) {
  /**
   * Supported Property Search country site codes.
   *
   * @return string[]
   */
  function ps_country_codes(): array {
    return ['com', 'be', 'es', 'fr', 'ie', 'it', 'lu', 'nl', 'pl'];
  }
}

if (!function_exists('ps_env_country')) {
  /**
   * Reads a per-country env var with global base fallback.
   *
   * Resolution order:
   * 1. {PREFIX}_{COUNTRY} (e.g. APP_PUBLIC_PATH_FR)
   * 2. {PREFIX}/{country} when PREFIX is set (e.g. APP_PUBLIC_PATH=/data/files)
   * 3. Empty string (caller applies default)
   */
  function ps_env_country(string $countryCode, string $prefix): string {
    $countryCode = strtolower($countryCode);
    $upper = strtoupper($countryCode);

    $countryValue = ps_env($prefix . '_' . $upper);
    if ($countryValue !== '') {
      return $countryValue;
    }

    $globalValue = ps_env($prefix);
    if ($globalValue !== '') {
      return rtrim($globalValue, '/') . '/' . $countryCode;
    }

    return '';
  }
}

if (!function_exists('ps_resolve_public_files_path')) {
  /**
   * Relative public files path under the Drupal web root.
   */
  function ps_resolve_public_files_path(string $countryCode): string {
    $configured = ps_env_country($countryCode, 'APP_PUBLIC_PATH');
    if ($configured !== '') {
      // Absolute paths outside web root are not supported for public files.
      if ($configured[0] === '/') {
        throw new \RuntimeException(sprintf(
          'APP_PUBLIC_PATH for %s must be relative to the Drupal web root, got: %s',
          $countryCode,
          $configured
        ));
      }
      return $configured;
    }

    return 'sites/' . strtolower($countryCode) . '/files';
  }
}

if (!function_exists('ps_resolve_private_files_path')) {
  /**
   * Absolute private files path (outside web root).
   */
  function ps_resolve_private_files_path(string $countryCode, string $appRoot): string {
    $configured = ps_env_country($countryCode, 'APP_PRIVATE_PATH');
    if ($configured !== '') {
      if ($configured[0] === '/') {
        return $configured;
      }
      return dirname($appRoot) . '/' . ltrim($configured, '/');
    }

    return dirname($appRoot) . '/private/' . strtolower($countryCode);
  }
}

if (!function_exists('ps_resolve_temp_files_path')) {
  /**
   * Absolute temp path, per country when a global base is configured.
   */
  function ps_resolve_temp_files_path(string $countryCode, string $appRoot): string {
    $configured = ps_env_country($countryCode, 'APP_TEMP_PATH');
    if ($configured !== '') {
      if ($configured[0] === '/') {
        return $configured;
      }
      return dirname($appRoot) . '/' . ltrim($configured, '/');
    }

    $globalTemp = ps_env('APP_TEMP_PATH');
    if ($globalTemp !== '') {
      $base = $globalTemp[0] === '/' ? $globalTemp : dirname($appRoot) . '/' . ltrim($globalTemp, '/');
      return rtrim($base, '/') . '/' . strtolower($countryCode);
    }

    return '';
  }
}
