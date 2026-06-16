<?php

// phpcs:ignoreFile

/**
 * @file
 * Multisite aliases driven by environment variables (src/.env).
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
  $port = ps_env('APP_DOMAIN_' . $upper . '_PORT', '80');

  if ($domain === '') {
    continue;
  }

  $siteDir = $countryCode;

  if ($port !== '' && $port !== '80' && $port !== '443') {
    $sites[$port . '.' . $domain] = $siteDir;
    if ($adminDomain !== '') {
      $sites[$port . '.' . $adminDomain] = $siteDir;
    }
  }
  else {
    $sites[$domain] = $siteDir;
    if ($adminDomain !== '') {
      $sites[$adminDomain] = $siteDir;
    }
  }
}
