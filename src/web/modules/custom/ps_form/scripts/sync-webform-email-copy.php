#!/usr/bin/env php
<?php

/**
 * @file
 * Syncs webform email handler copy from WebformEmailCopyCatalog to install YAML.
 *
 * Usage (from repo root):
 *   php src/web/modules/custom/ps_form/scripts/sync-webform-email-copy.php
 */

declare(strict_types=1);

use Drupal\ps_form\Service\WebformEmailCopyCatalog;
use Symfony\Component\Yaml\Yaml;

$autoload = dirname(__DIR__, 5) . '/vendor/autoload.php';
if (!is_readable($autoload)) {
  fwrite(STDERR, "Composer autoload not found at {$autoload}\n");
  exit(1);
}
require_once $autoload;
require_once dirname(__DIR__) . '/src/Service/WebformEmailCopyCatalog.php';

/**
 * Ensures a nested handler override structure exists.
 */
function ensure_handler(array &$data, string $handlerId): void {
  if (!isset($data['handlers'])) {
    $data['handlers'] = [];
  }
  if (!isset($data['handlers'][$handlerId])) {
    $data['handlers'][$handlerId] = [];
  }
  if (!isset($data['handlers'][$handlerId]['settings'])) {
    $data['handlers'][$handlerId]['settings'] = [];
  }
}

/**
 * Removes legacy handler overrides mistakenly nested under settings.
 */
function cleanup_legacy_handler_overrides(array &$data): void {
  foreach (['email_confirmation', 'email_agent', 'email_notification'] as $handlerId) {
    if (isset($data['settings'][$handlerId])) {
      unset($data['settings'][$handlerId]);
    }
  }
}

$installPath = dirname(__DIR__) . '/config/install';
$updated = 0;
$yamlFlags = Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK;

foreach (WebformEmailCopyCatalog::HUB_WEBFORM_IDS as $webformId) {
  $file = "{$installPath}/webform.webform.{$webformId}.yml";
  if (!is_readable($file)) {
    fwrite(STDERR, "Missing base file: {$file}\n");
    continue;
  }

  $data = Yaml::parseFile($file);
  if (!is_array($data)) {
    continue;
  }

  $data['handlers']['email_notification']['settings']['subject']
    = WebformEmailCopyCatalog::getHubNotificationSubject($webformId, 'en');
  $data['handlers']['email_confirmation']['settings']['subject']
    = WebformEmailCopyCatalog::getHubConfirmationSubject('en');
  $data['handlers']['email_confirmation']['settings']['body']
    = WebformEmailCopyCatalog::buildHubConfirmationBody($webformId, 'en');

  file_put_contents($file, Yaml::dump($data, 10, 2, $yamlFlags));
  $updated++;
  echo "Updated base: {$webformId}\n";

  foreach (array_filter(WebformEmailCopyCatalog::LANGUAGE_CODES, static fn(string $l): bool => $l !== 'en') as $langcode) {
    $langFile = "{$installPath}/language/{$langcode}/webform.webform.{$webformId}.yml";
    if (!is_readable($langFile)) {
      continue;
    }

    $langData = Yaml::parseFile($langFile);
    if (!is_array($langData)) {
      continue;
    }

    cleanup_legacy_handler_overrides($langData);
    ensure_handler($langData, 'email_notification');
    ensure_handler($langData, 'email_confirmation');

    $langData['handlers']['email_notification']['label']
      = WebformEmailCopyCatalog::getHandlerLabel('hub', 'email_notification', $langcode);
    $langData['handlers']['email_notification']['settings']['subject']
      = WebformEmailCopyCatalog::getHubNotificationSubject($webformId, $langcode);
    $langData['handlers']['email_notification']['settings']['body'] = '_default';

    $langData['handlers']['email_confirmation']['label']
      = WebformEmailCopyCatalog::getConfirmationHandlerLabel('hub', $langcode);
    $langData['handlers']['email_confirmation']['settings']['subject']
      = WebformEmailCopyCatalog::getHubConfirmationSubject($langcode);
    $langData['handlers']['email_confirmation']['settings']['body']
      = WebformEmailCopyCatalog::buildHubConfirmationBody($webformId, $langcode);

    file_put_contents($langFile, Yaml::dump($langData, 10, 2, $yamlFlags));
    $updated++;
    echo "Updated {$langcode}: {$webformId}\n";
  }
}

foreach (WebformEmailCopyCatalog::OFFER_WEBFORM_IDS as $webformId) {
  $file = "{$installPath}/webform.webform.{$webformId}.yml";
  if (!is_readable($file)) {
    fwrite(STDERR, "Missing base file: {$file}\n");
    continue;
  }

  $data = Yaml::parseFile($file);
  if (!is_array($data)) {
    continue;
  }

  $data['handlers']['email_agent']['settings']['subject']
    = WebformEmailCopyCatalog::getOfferAgentSubject($webformId, 'en');
  $data['handlers']['email_confirmation']['settings']['subject']
    = WebformEmailCopyCatalog::getOfferConfirmationSubject($webformId, 'en');
  $data['handlers']['email_confirmation']['settings']['body']
    = WebformEmailCopyCatalog::buildOfferConfirmationBody($webformId, 'en');

  file_put_contents($file, Yaml::dump($data, 10, 2, $yamlFlags));
  $updated++;
  echo "Updated base: {$webformId}\n";

  foreach (array_filter(WebformEmailCopyCatalog::LANGUAGE_CODES, static fn(string $l): bool => $l !== 'en') as $langcode) {
    $langFile = "{$installPath}/language/{$langcode}/webform.webform.{$webformId}.yml";
    if (!is_readable($langFile)) {
      continue;
    }

    $langData = Yaml::parseFile($langFile);
    if (!is_array($langData)) {
      continue;
    }

    cleanup_legacy_handler_overrides($langData);
    ensure_handler($langData, 'email_agent');
    ensure_handler($langData, 'email_confirmation');

    $langData['handlers']['email_agent']['label']
      = WebformEmailCopyCatalog::getHandlerLabel('offer', 'email_agent', $langcode);
    $langData['handlers']['email_agent']['settings']['subject']
      = WebformEmailCopyCatalog::getOfferAgentSubject($webformId, $langcode);
    $langData['handlers']['email_agent']['settings']['body'] = '_default';

    $langData['handlers']['email_confirmation']['label']
      = WebformEmailCopyCatalog::getConfirmationHandlerLabel('offer', $langcode);
    $langData['handlers']['email_confirmation']['settings']['subject']
      = WebformEmailCopyCatalog::getOfferConfirmationSubject($webformId, $langcode);
    $langData['handlers']['email_confirmation']['settings']['body']
      = WebformEmailCopyCatalog::buildOfferConfirmationBody($webformId, $langcode);

    file_put_contents($langFile, Yaml::dump($langData, 10, 2, $yamlFlags));
    $updated++;
    echo "Updated {$langcode}: {$webformId}\n";
  }
}

echo "Done. {$updated} file(s) updated.\n";
