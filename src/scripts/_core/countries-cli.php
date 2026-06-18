#!/usr/bin/env php
<?php

/**
 * @file
 * CLI helper for bash scripts — reads web/sites/countries.yml.
 */

declare(strict_types=1);

require dirname(__DIR__, 2) . '/web/sites/countries.php';

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

    default:
      fwrite(STDERR, "Usage: countries-cli.php codes|default_lang|languages|dev_port|site_dir|is_valid|drush-site-yml [code]\n");
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
