<?php

/**
 * @file
 * Sync contact-family webforms from ps_form config/install.
 *
 * Usage: drush @ps.com php:script ../scripts/forms/sync-contact-webforms.php
 */

$forms = [
  'find_property',
  'entrust_search',
  'get_advice',
  'entrust_property',
  'invest_sell',
  'other_request',
];

$extensionList = \Drupal::service('extension.list.module');
$installPath = $extensionList->getPath('ps_form') . '/config/install';
$configStorage = \Drupal::service('config.storage');

foreach ($forms as $webform_id) {
  $webformFile = $installPath . '/webform.webform.' . $webform_id . '.yml';
  if (!is_readable($webformFile)) {
    print "Skip missing {$webform_id}\n";
    continue;
  }

  $definition = \Symfony\Component\Yaml\Yaml::parseFile($webformFile);
  if (!is_array($definition) || empty($definition['id'])) {
    print "Skip invalid {$webform_id}\n";
    continue;
  }

  $configName = 'webform.webform.' . $webform_id;
  $editable = \Drupal::configFactory()->getEditable($configName);
  foreach ($definition as $key => $value) {
    $editable->set($key, $value);
  }
  $editable->save();

  \Drupal::entityTypeManager()->getStorage('webform')->resetCache([$definition['id']]);

  foreach (glob($installPath . '/language/*/webform.webform.' . $webform_id . '.yml') ?: [] as $file) {
    $langcode = basename(dirname($file));
    $data = \Symfony\Component\Yaml\Yaml::parseFile($file);
    if (!is_array($data) || $data === []) {
      continue;
    }

    $configStorage->createCollection('language.' . $langcode)->write($configName, $data);
  }

  print "Synced {$webform_id}\n";
}
