<?php

// phpcs:ignoreFile

/**
 * @file
 * Property Search multisite — com country bootstrap.
 */

$ps_country_code = 'com';
require dirname(__DIR__) . '/default/settings.php';
$databases['default']['default'] = array (
  'database' => 'local_psv2_com_db',
  'username' => 'drupal',
  'password' => 'drupal',
  'prefix' => '',
  'host' => 'postgres',
  'port' => 5432,
  'driver' => 'pgsql',
  'namespace' => 'Drupal\\pgsql\\Driver\\Database\\pgsql',
  'autoload' => 'core/modules/pgsql/src/Driver/Database/pgsql/',
);
$settings['hash_salt'] = 'TvxQf5AIt4A-VI7Pzx27zRJINczclwYNF4FM0B9wBWwWPBZF7zB56R0-MNKEyRWhWa7PFitwuA';
