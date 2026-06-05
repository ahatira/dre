<?php

/**
 * @file
 * Imports structural menu links (Login, Contact) from export/content-structural/.
 *
 * Usage: drush php:script modules/custom/ps_demo/scripts/import-structural-content.php
 */

declare(strict_types=1);

use Drupal\Core\DefaultContent\Existing;
use Drupal\Core\DefaultContent\Finder;

$path = DRUPAL_ROOT . '/modules/custom/ps_demo/export/content-structural';
if (!is_dir($path)) {
  throw new \RuntimeException("Structural content directory not found: {$path}");
}

$finder = new Finder($path);
if ($finder->data === []) {
  throw new \RuntimeException('No structural content YAML found.');
}

/** @var \Drupal\Core\DefaultContent\Importer $importer */
$importer = \Drupal::service(\Drupal\Core\DefaultContent\Importer::class);
$importer->importContent($finder, Existing::Skip);
\Drupal::service('plugin.manager.menu.link')->rebuild();

print "Structural header menu links imported.\n";
