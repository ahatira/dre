<?php

/**
 * @file
 * Imports module config/install/language/{lang}/*.yml into active overrides.
 *
 * Usage: drush php:script scripts/import_language_config_overrides.php fr
 */

declare(strict_types=1);

use Drupal\Component\Serialization\Yaml;

/** @var string[] $extra */
$langcode = $extra[0] ?? 'fr';

if (\Drupal::languageManager()->getLanguage($langcode) === NULL) {
  throw new \RuntimeException("Language not enabled: {$langcode}");
}

$roots = [
  DRUPAL_ROOT . '/modules/custom',
  DRUPAL_ROOT . '/themes/custom',
];

$imported = 0;
$skipped = 0;

foreach ($roots as $root) {
  if (!is_dir($root)) {
    continue;
  }

  foreach (glob($root . '/*', GLOB_ONLYDIR) ?: [] as $extensionPath) {
    foreach (['install', 'optional'] as $type) {
      $languageDir = $extensionPath . "/config/{$type}/language/{$langcode}";
      if (!is_dir($languageDir)) {
        continue;
      }

      foreach (glob($languageDir . '/*.yml') ?: [] as $file) {
        $name = basename($file, '.yml');
        $data = Yaml::decode((string) file_get_contents($file));
        if (!is_array($data) || $data === []) {
          $skipped++;
          continue;
        }

        $override = \Drupal::languageManager()->getLanguageConfigOverride($langcode, $name);
        foreach ($data as $key => $value) {
          $override->set($key, $value);
        }
        $override->save();
        $imported++;
        \Drupal::logger('ps_i18n')->notice('Imported language override @name (@lang).', [
          '@name' => $name,
          '@lang' => $langcode,
        ]);
      }
    }
  }
}

echo "Language config overrides imported={$imported} skipped={$skipped} lang={$langcode}" . PHP_EOL;
