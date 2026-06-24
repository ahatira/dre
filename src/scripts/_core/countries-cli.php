#!/usr/bin/env php
<?php

/**
 * @file
 * CLI helper for bash scripts — reads web/sites/countries.yml.
 */

declare(strict_types=1);

require dirname(__DIR__, 2) . '/vendor/autoload.php';
require dirname(__DIR__, 2) . '/web/sites/countries.php';
require __DIR__ . '/config-merge.php';

$action = $argv[1] ?? '';

try {
  switch ($action) {
    case 'codes':
      foreach (ps_country_codes() as $code) {
        echo $code, PHP_EOL;
      }
      break;

    case 'default_lang':
      echo ps_country_default_langcode(ps_cli_country_arg($argv)), PHP_EOL;
      break;

    case 'languages':
      echo implode(' ', ps_country_language_codes(ps_cli_country_arg($argv))), PHP_EOL;
      break;

    case 'dev_port':
      echo ps_country_dev_port(ps_cli_country_arg($argv)), PHP_EOL;
      break;

    case 'site_dir':
      echo ps_country_site_dir(ps_cli_country_arg($argv)), PHP_EOL;
      break;

    case 'is_valid':
      echo ps_is_country_code(ps_cli_country_arg($argv)) ? '1' : '0', PHP_EOL;
      break;

    case 'drush-site-yml':
      echo "# Property Search multisite — Drush site aliases.\n";
      echo "# Generated from scripts/multisite/countries.yml — run: make generate-multisite (repo root)\n";
      echo "# uri = site directory under web/sites/ (not a full HTTP URL).\n\n";
      foreach (ps_country_codes() as $code) {
        echo $code, ":\n  root: ../web\n  uri: ", ps_country_site_dir($code), "\n";
      }
      break;

    case 'generate-site-splits':
      ps_cli_generate_site_splits($argv);
      break;

    case 'generate-address-field-overrides':
      ps_cli_generate_address_field_overrides($argv);
      break;

    case 'merge-partial-config':
      ps_cli_merge_partial_config($argv);
      break;

    default:
      fwrite(STDERR, "Usage: countries-cli.php codes|default_lang|languages|dev_port|site_dir|is_valid|drush-site-yml|generate-site-splits|generate-address-field-overrides|merge-partial-config [args]\n");
      exit(1);
  }
}
catch (\Throwable $e) {
  fwrite(STDERR, $e->getMessage() . PHP_EOL);
  exit(1);
}

/**
 * Returns the country code argument from CLI argv.
 *
 * @param array<int, string> $argv
 *   CLI arguments.
 *
 * @return string
 *   Country code from argv[2].
 */
function ps_cli_country_arg(array $argv): string {
  $code = $argv[2] ?? '';
  if ($code === '') {
    fwrite(STDERR, "Missing country code argument.\n");
    exit(1);
  }
  return strtolower($code);
}

/**
 * Generates Config Split entity YAML for each country site.
 *
 * @param array<int, string> $argv
 *   CLI arguments: generate-site-splits splitsDir sitesDir.
 */
function ps_cli_generate_site_splits(array $argv): void {
  $splitsDir = $argv[2] ?? '';
  $sitesDir = $argv[3] ?? '';
  if ($splitsDir === '' || $sitesDir === '') {
    fwrite(STDERR, "Usage: countries-cli.php generate-site-splits SPLITS_DIR SITES_DIR\n");
    exit(1);
  }

  $partialList = [
    'language.negotiation',
    'field.field.node.offer.field_address',
    'system.site',
    'ps_homepage.homepage',
    'ps_homepage.settings',
  ];

  foreach (ps_country_codes() as $code) {
    $config = ps_country_config($code);
    $label = (string) ($config['label'] ?? strtoupper($code));
    $languages = ps_country_language_codes($code);

    $siteDir = $sitesDir . '/' . $code;
    if (!is_dir($siteDir)) {
      mkdir($siteDir, 0775, TRUE);
    }

    $completeList = [];
    foreach ($languages as $langcode) {
      $completeList[] = 'language.entity.' . $langcode;
    }

    $yaml = ps_cli_render_site_split_yaml($code, $label, $completeList, $partialList);
    $splitPath = $splitsDir . '/config_split.config_split.site_' . $code . '.yml';
    file_put_contents($splitPath, $yaml);

    $legacySplit = $splitsDir . '/config_split.config_split.language_' . $code . '.yml';
    if (is_file($legacySplit)) {
      unlink($legacySplit);
    }
  }
}

/**
 * Renders a Config Split entity YAML for a country site.
 *
 * @param string[] $completeList
 *   Config names owned entirely by this split.
 * @param string[] $partialList
 *   Config names partially overridden by this split.
 */
function ps_cli_render_site_split_yaml(
  string $code,
  string $label,
  array $completeList,
  array $partialList,
): string {
  $upper = strtoupper($code);
  $lines = [
    'langcode: en',
    'status: false',
    'dependencies: {  }',
    'id: site_' . $code,
    'label: \'Site ' . ps_cli_yaml_escape($upper) . ' (' . ps_cli_yaml_escape($label) . ')\'',
    'description: \'Per-country CMI overrides. Enabled via settings.bootstrap.php.\'',
    'weight: 10',
    'stackable: true',
    'no_patching: false',
    'storage: folder',
    'folder: ../config/env/sites/' . $code,
    'module: {  }',
  ];

  $lines[] = ps_cli_yaml_list('complete_list', $completeList);
  $lines[] = ps_cli_yaml_list('partial_list', $partialList);

  return implode("\n", $lines) . "\n";
}

/**
 * Renders a YAML list key for Config Split entity fields.
 *
 * @param string $key
 *   YAML mapping key (e.g. complete_list).
 * @param string[] $items
 *   List item values.
 *
 * @return string
 *   YAML fragment.
 */
function ps_cli_yaml_list(string $key, array $items): string {
  if ($items === []) {
    return $key . ': {  }';
  }
  $lines = [$key . ':'];
  foreach ($items as $item) {
    $lines[] = '  - ' . $item;
  }
  return implode("\n", $lines);
}

/**
 * Escapes single quotes for YAML single-quoted strings.
 *
 * @param string $value
 *   Raw string value.
 *
 * @return string
 *   YAML-safe string for single-quoted scalars.
 */
function ps_cli_yaml_escape(string $value): string {
  return str_replace("'", "''", $value);
}

/**
 * Generates partial offer address field overrides for each country site.
 *
 * @param array<int, string> $argv
 *   CLI arguments: generate-address-field-overrides envSitesDir.
 */
function ps_cli_generate_address_field_overrides(array $argv): void {
  $envSitesDir = $argv[2] ?? '';
  if ($envSitesDir === '') {
    fwrite(STDERR, "Usage: countries-cli.php generate-address-field-overrides ENV_SITES_DIR\n");
    exit(1);
  }

  foreach (ps_country_codes() as $code) {
    $siteDir = $envSitesDir . '/' . $code;
    if (!is_dir($siteDir) && !mkdir($siteDir, 0775, TRUE) && !is_dir($siteDir)) {
      throw new \RuntimeException(sprintf('Could not create directory: %s', $siteDir));
    }

    $path = $siteDir . '/field.field.node.offer.field_address.yml';
    file_put_contents($path, ps_config_render_offer_address_override_yaml($code));
  }
}

/**
 * Merges a partial override YAML file into a target CMI export file.
 *
 * @param array<int, string> $argv
 *   CLI arguments: merge-partial-config targetPath overridePath srcRoot.
 */
function ps_cli_merge_partial_config(array $argv): void {
  $targetPath = $argv[2] ?? '';
  $overridePath = $argv[3] ?? '';
  $srcRoot = $argv[4] ?? '';
  if ($targetPath === '' || $overridePath === '' || $srcRoot === '') {
    fwrite(STDERR, "Usage: countries-cli.php merge-partial-config TARGET_PATH OVERRIDE_PATH SRC_ROOT\n");
    exit(1);
  }

  ps_config_merge_yaml_files($targetPath, $overridePath, $srcRoot);
}
