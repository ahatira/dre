#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Generates config translation YAML for PS contact-family webforms.
 *
 * Usage: php scripts/forms/generate-contact-webform-translations.php
 */

$root = dirname(__DIR__, 2);
$outDir = $root . '/src/web/modules/custom/ps_form/config/install/language';
$autoload = $root . '/src/vendor/autoload.php';
if (!is_readable($autoload)) {
  fwrite(STDERR, "Composer autoload not found at {$autoload}\n");
  exit(1);
}
require $autoload;

use Symfony\Component\Yaml\Yaml;

$langs = ['fr', 'de', 'es', 'it', 'nl', 'pl', 'lb'];
$webforms = [
  'contact',
  'find_property',
  'entrust_search',
  'get_advice',
  'entrust_property',
  'invest_sell',
  'other_request',
];

$dict = require __DIR__ . '/contact-webform-translations.php';

/**
 * Webform element properties that are safe in config translation overrides.
 */
const PS_FORM_TRANSLATABLE_ELEMENT_PROPS = [
  '#title',
  '#markup',
  '#text',
  '#placeholder',
  '#options',
  '#empty_option',
  '#description',
  '#help',
  '#more',
  '#field_prefix',
  '#field_suffix',
  '#other__option_label',
  '#other__title',
  '#other__placeholder',
];

/**
 * Reduces a full elements tree to flattened Webform config-translation format.
 *
 * @param mixed $elements
 */
function ps_form_partial_elements(mixed $elements): array {
  if (!is_array($elements)) {
    return [];
  }

  $partial = [];
  ps_form_collect_partial_elements($elements, $partial);
  return $partial;
}

/**
 * @param array<string, mixed> $elements
 * @param array<string, mixed> $partial
 */
function ps_form_collect_partial_elements(array $elements, array &$partial): void {
  foreach ($elements as $key => $value) {
    if (!is_string($key) || !is_array($value) || str_starts_with($key, '#')) {
      continue;
    }

    $props = [];
    foreach ($value as $prop => $propValue) {
      if (is_string($prop) && str_starts_with($prop, '#') && in_array($prop, PS_FORM_TRANSLATABLE_ELEMENT_PROPS, TRUE)) {
        $props[$prop] = $propValue;
      }
    }
    if ($props !== []) {
      $partial[$key] = $props;
    }

    ps_form_collect_partial_elements($value, $partial);
  }
}

foreach ($langs as $lang) {
  foreach ($webforms as $webform) {
    if (!isset($dict[$lang][$webform])) {
      fwrite(STDERR, "Missing translation: {$lang}/{$webform}\n");
      continue;
    }
    $data = $dict[$lang][$webform];
    if (isset($data['elements']) && is_string($data['elements'])) {
      $decoded = Yaml::parse($data['elements']);
      if (is_array($decoded)) {
        $data['elements'] = Yaml::dump(
          ps_form_partial_elements($decoded),
          10,
          2,
          Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK,
        );
      }
    }
    $dir = "{$outDir}/{$lang}";
    if (!is_dir($dir) && !mkdir($dir, 0755, TRUE) && !is_dir($dir)) {
      throw new RuntimeException("Cannot create {$dir}");
    }
    $file = "{$dir}/webform.webform.{$webform}.yml";
    file_put_contents($file, Yaml::dump($data, 10, 2, Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK));
    echo "Wrote {$file}\n";
  }
}

echo "Done.\n";
