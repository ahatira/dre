#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Updates English install webform YAML with bnppre-aligned job title options.
 *
 * Usage: php scripts/forms/update-contact-job-titles-install.php
 */

$root = dirname(__DIR__, 2);
require $root . '/src/vendor/autoload.php';
require __DIR__ . '/contact-job-title-strings.php';

use Symfony\Component\Yaml\Yaml;

$installDir = $root . '/src/web/modules/custom/ps_form/config/install';
$options = [];
foreach (ps_form_contact_job_title_values() as $value) {
  $options[$value] = ps_form_contact_job_title_strings()['en'][$value] ?? $value;
}

$formIds = [
  'find_property',
  'entrust_search',
  'get_advice',
  'entrust_property',
  'invest_sell',
];

foreach ($formIds as $formId) {
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
  if (!isset($elements['step_contact']['job_title'])) {
    fwrite(STDERR, "No job_title in {$formId}\n");
    continue;
  }

  $elements['step_contact']['job_title']['#options'] = $options;
  $config['elements'] = Yaml::dump($elements, 10, 2, Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK);
  file_put_contents($path, Yaml::dump($config, 10, 2, Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK));
  echo "Updated {$formId} (" . count($options) . " job titles)\n";
}
