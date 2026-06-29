#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Updates English install webform YAML with bnppre-aligned legal notices.
 *
 * Usage: php scripts/forms/update-contact-legal-install.php
 */

$root = dirname(__DIR__, 2);
require $root . '/src/vendor/autoload.php';
require __DIR__ . '/contact-legal-notice-strings.php';

use Symfony\Component\Yaml\Yaml;

$installDir = $root . '/src/web/modules/custom/ps_form/config/install';
$legalStrings = ps_form_contact_legal_notice_strings()['en'];
$notices = [
  'transaction' => ps_form_build_contact_legal_notice($legalStrings, 'transaction'),
  'advisory' => ps_form_build_contact_legal_notice($legalStrings, 'advisory'),
];

$formVariants = [
  'find_property' => 'transaction',
  'entrust_search' => 'transaction',
  'invest_sell' => 'transaction',
  'entrust_property' => 'transaction',
  'get_advice' => 'advisory',
  'other_request' => 'advisory',
];

foreach ($formVariants as $formId => $variant) {
  $path = "{$installDir}/webform.webform.{$formId}.yml";
  if (!is_readable($path)) {
    fwrite(STDERR, "Skip missing: {$path}\n");
    continue;
  }

  $config = Yaml::parseFile($path);
  if (!is_string($config['elements'] ?? NULL)) {
    fwrite(STDERR, "No elements string in {$formId}\n");
    continue;
  }

  /** @var array<string, mixed> $elements */
  $elements = Yaml::parse($config['elements']);
  if (!isset($elements['step_contact']['legal_notice'])) {
    fwrite(STDERR, "No legal_notice in {$formId}\n");
    continue;
  }

  $elements['step_contact']['legal_notice']['#markup'] = $notices[$variant];
  $config['elements'] = Yaml::dump($elements, 10, 2, Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK);

  file_put_contents($path, Yaml::dump($config, 10, 2, Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK));
  echo "Updated {$formId} ({$variant})\n";
}
