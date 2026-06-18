<?php

// phpcs:ignoreFile

/**
 * @file
 * Multisite aliases driven by environment variables.
 *
 * Dev: src/.env (APP_ENV=dev). INT/staging/prod: system environment only.
 * HTTP port is handled by the reverse proxy; Host header selects the site.
 *
 * @see \Drupal\Core\DrupalKernel::getSitePath()
 */

require __DIR__ . '/load-env.php';
ps_load_env();

$sites = [];

foreach (ps_country_codes() as $countryCode) {
  $upper = strtoupper($countryCode);
  $domain = ps_env('APP_DOMAIN_' . $upper);
  $adminDomain = ps_env('APP_DOMAIN_' . $upper . '_ADMIN');

  if ($domain === '') {
    continue;
  }

  $siteDir = ps_country_site_dir($countryCode);
  $sites[$domain] = $siteDir;
  if ($adminDomain !== '') {
    $sites[$adminDomain] = $siteDir;
  }
}
