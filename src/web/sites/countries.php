<?php

/**
 * @file
 * Property Search multisite country manifest loader.
 */

declare(strict_types=1);

use Symfony\Component\Yaml\Yaml;

/**
 * Absolute path to the countries manifest YAML file.
 */
function ps_countries_manifest_path(): string {
  return __DIR__ . '/countries.yml';
}

/**
 * Ensures Composer autoload is available (Symfony Yaml).
 */
function ps_countries_ensure_autoload(): void {
  static $loaded = FALSE;
  if ($loaded) {
    return;
  }
  $autoload = dirname(__DIR__, 2) . '/vendor/autoload.php';
  if (!is_readable($autoload)) {
    throw new \RuntimeException('Composer autoload is required to read countries manifest.');
  }
  require_once $autoload;
  $loaded = TRUE;
}

/**
 * Loads and validates the countries manifest.
 *
 * @return array{countries: array<string, array<string, mixed>>}
 *   Parsed manifest with a top-level countries map.
 */
function ps_load_countries_manifest(): array {
  static $manifest = NULL;
  if ($manifest !== NULL) {
    return $manifest;
  }

  $path = ps_countries_manifest_path();
  if (!is_readable($path)) {
    throw new \RuntimeException(sprintf('Missing countries manifest: %s', $path));
  }

  ps_countries_ensure_autoload();
  $parsed = Yaml::parseFile($path);
  if (!is_array($parsed) || !isset($parsed['countries']) || !is_array($parsed['countries'])) {
    throw new \RuntimeException('Invalid countries manifest: expected top-level "countries" map.');
  }

  if ($parsed['countries'] === []) {
    throw new \RuntimeException('Invalid countries manifest: "countries" must not be empty.');
  }

  $manifest = $parsed;
  return $manifest;
}

/**
 * Supported Property Search country site codes (manifest order).
 *
 * @return string[]
 *   Country codes in manifest order.
 */
function ps_country_codes(): array {
  return array_keys(ps_load_countries_manifest()['countries']);
}

/**
 * Returns manifest config for a single country code.
 *
 * @return array<string, mixed>
 *   Country manifest entry.
 */
function ps_country_config(string $countryCode): array {
  $countryCode = strtolower($countryCode);
  $countries = ps_load_countries_manifest()['countries'];
  if (!isset($countries[$countryCode]) || !is_array($countries[$countryCode])) {
    throw new \RuntimeException(sprintf('Unknown country code in manifest: %s', $countryCode));
  }
  return $countries[$countryCode];
}

/**
 * Checks whether a country code exists in the manifest.
 */
function ps_is_country_code(string $countryCode): bool {
  $countryCode = strtolower($countryCode);
  if ($countryCode === 'all') {
    return TRUE;
  }
  return in_array($countryCode, ps_country_codes(), TRUE);
}

/**
 * Default language for a country site.
 */
function ps_country_default_langcode(string $countryCode): string {
  $config = ps_country_config($countryCode);
  $lang = $config['default_lang'] ?? '';
  if (!is_string($lang) || $lang === '') {
    throw new \RuntimeException(sprintf('Missing default_lang for country: %s', $countryCode));
  }
  return $lang;
}

/**
 * Enabled language codes for a country site.
 *
 * @return string[]
 *   Enabled language codes.
 */
function ps_country_language_codes(string $countryCode): array {
  $config = ps_country_config($countryCode);
  $languages = $config['languages'] ?? NULL;
  if (!is_array($languages) || $languages === []) {
    throw new \RuntimeException(sprintf('Missing languages for country: %s', $countryCode));
  }
  return array_values(array_map('strval', $languages));
}

/**
 * Languages hidden on the front language switcher (optional per country).
 *
 * @return string[]
 *   Language codes hidden on the front switcher.
 */
function ps_country_hidden_front_languages(string $countryCode): array {
  $config = ps_country_config($countryCode);
  $hidden = $config['hidden_front_languages'] ?? [];
  if (!is_array($hidden)) {
    return [];
  }
  return array_values(array_map('strval', $hidden));
}

/**
 * Drupal site directory name under web/sites/ (may differ from country code).
 */
function ps_country_site_dir(string $countryCode): string {
  $config = ps_country_config($countryCode);
  $dir = $config['site_dir'] ?? '';
  if (!is_string($dir) || $dir === '') {
    throw new \RuntimeException(sprintf('Missing site_dir for country: %s', $countryCode));
  }
  return $dir;
}

/**
 * Local dev HTTP port for a country (Phase 1 will wire nginx bindings).
 */
function ps_country_dev_port(string $countryCode): int {
  $config = ps_country_config($countryCode);
  $port = $config['dev_port'] ?? NULL;
  if (!is_int($port) && !is_string($port)) {
    throw new \RuntimeException(sprintf('Missing dev_port for country: %s', $countryCode));
  }
  return (int) $port;
}
