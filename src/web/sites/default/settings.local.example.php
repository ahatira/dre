<?php

/**
 * @file
 * Template for local dev overrides — copy to settings.local.php (gitignored).
 *
 * Usage (once per machine):
 *   cp web/sites/default/settings.local.example.php web/sites/default/settings.local.php
 *
 * Loaded after settings.bootstrap.php for every country site:
 * - sites/{site_dir}/settings.local.php if present (per-country override)
 * - else sites/default/settings.local.php (this file, shared across multisite)
 *
 * Values come from src/.env (make env). Safe to keep across make reinstall.
 *
 * @see settings.php
 * @see settings.bootstrap.php
 * @see docker/README.md — Mailpit UI http://localhost:8025
 */

// Dev-only overrides (APP_ENV=dev in src/.env).
if (ps_env('APP_ENV', 'dev') === 'dev') {

  // --- Mailpit (Symfony Mailer → SMTP) --------------------------------------
  // Env: MAILPIT_HOST (default mailpit), MAILPIT_PORT (default 1025).
  // Also listed in config_ignore — not exported via CMI.
  $mailpitHost = ps_env('MAILPIT_HOST', 'mailpit');
  $mailpitPort = (int) ps_env('MAILPIT_PORT', '1025');
  $config['mailer_transport.settings']['default_transport'] = 'sendmail';
  $config['mailer_transport.mailer_transport.sendmail']['plugin'] = 'smtp';
  $config['mailer_transport.mailer_transport.sendmail']['configuration']['user'] = '';
  $config['mailer_transport.mailer_transport.sendmail']['configuration']['pass'] = '';
  $config['mailer_transport.mailer_transport.sendmail']['configuration']['host'] = $mailpitHost;
  $config['mailer_transport.mailer_transport.sendmail']['configuration']['port'] = $mailpitPort;
  $config['mailer_transport.mailer_transport.sendmail']['configuration']['query']['verify_peer'] = FALSE;

  // --- Search API Solr connector (per country) --------------------------------
  // Env: SOLR_HOST, SOLR_PORT, SOLR_PATH, SOLR_CORE_{CODE} (see .env.dist).
  // backend_config is config_ignore'd — connector stays env-specific in dev;
  // other search_api.server.ps_solr keys remain editable in BO and exportable.
  ps_apply_search_api_solr_connector_overrides($config, $ps_country_code);

  // --- Optional: project-specific dev overrides -------------------------------
  // $settings['cache']['bins']['render']['class'] = 'Drupal\Core\Cache\NullBackendFactory';
  // $settings['cache']['bins']['dynamic_page_cache']['class'] = 'Drupal\Core\Cache\NullBackendFactory';
  // $settings['cache']['bins']['page']['class'] = 'Drupal\Core\Cache\NullBackendFactory';
  // $config['system.logging']['error_level'] = 'verbose';
}
