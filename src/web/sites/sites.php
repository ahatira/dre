<?php

// phpcs:ignoreFile

/**
 * @file
 * Multisite aliases driven by environment variables.
 *
 * Dev: src/.env (APP_ENV=dev). INT/staging/prod: system environment only.
 *
 * - APP_DOMAIN_{CODE} / APP_DOMAIN_{CODE}_ADMIN: comma-separated hostnames.
 * - APP_DOMAIN_{CODE}_PORT: non-standard port adds Drupal key "{port}.{host}".
 * - APP_DOMAIN_SERVICE / APP_DOMAIN_PROBES: infra hosts routed per country
 *   via that country's port (e.g. localhost:8083 → france).
 *
 * @see \Drupal\Core\DrupalKernel::getSitePath()
 * @see ps_build_multisite_sites_map()
 */

require __DIR__ . '/load-env.php';
ps_load_env();

$sites = ps_build_multisite_sites_map();
