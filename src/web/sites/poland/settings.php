<?php

// phpcs:ignoreFile

/**
 * @file
 * Property Search multisite — pl (poland/) country bootstrap.
 */

$ps_country_code = 'pl';
require dirname(__DIR__) . '/default/settings.php';
$databases['default']['default'] = array (
  'database' => 'local_psv2_pl_db',
  'username' => 'drupal',
  'password' => 'drupal',
  'prefix' => '',
  'host' => '127.0.0.1',
  'port' => 5432,
  'driver' => 'pgsql',
  'namespace' => 'Drupal\\pgsql\\Driver\\Database\\pgsql',
  'autoload' => 'core/modules/pgsql/src/Driver/Database/pgsql/',
);
$settings['hash_salt'] = 'YrJkY32zVCRAGcJZrVRL2XKnzArET8lSmRUXjuleDZFJL5f1wI3rVIboA87ZTxfh3S02039msw';
