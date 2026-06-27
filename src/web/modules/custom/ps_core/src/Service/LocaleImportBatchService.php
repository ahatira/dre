<?php

declare(strict_types=1);

namespace Drupal\ps_core\Service;

use Drupal\Component\Serialization\Yaml;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Extension\ThemeHandlerInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\language\ConfigurableLanguageManagerInterface;
use Drupal\locale\Gettext;
use Symfony\Component\Finder\Finder;

/**
 * Imports contrib/custom PO files and language config overrides in one pass.
 */
final class LocaleImportBatchService {

  public function __construct(
    private readonly ModuleHandlerInterface $moduleHandler,
    private readonly LanguageManagerInterface $languageManager,
    private readonly ThemeHandlerInterface $themeHandler,
  ) {}

  /**
   * Imports translations for all active site languages.
   *
   * @param callable(string, array<string, mixed>): void|null $progress
   *   Optional progress callback.
   *
   * @return array<string, mixed>
   *   Aggregated import statistics.
   */
  public function import(?callable $progress = NULL): array {
    $this->moduleHandler->loadInclude('locale', 'bulk.inc');

    $activeLangcodes = $this->activeTranslatableLangcodes();
    $enabledExtensions = $this->enabledExtensionNames();

    $contribFiles = $this->collectContribPoFiles($activeLangcodes, $enabledExtensions);
    $customFiles = $this->collectCustomPoFiles($activeLangcodes);

    $stats = [
      'contrib' => $this->importPoFiles($contribFiles, LOCALE_NOT_CUSTOMIZED, $progress, 'contrib'),
      'custom' => $this->importPoFiles($customFiles, LOCALE_CUSTOMIZED, $progress, 'custom'),
      'overrides' => $this->importLanguageConfigOverrides($activeLangcodes, $progress),
    ];

    return $stats;
  }

  /**
   * Returns active translatable language codes.
   *
   * @return string[]
   *   Language codes.
   */
  private function activeTranslatableLangcodes(): array {
    $langcodes = [];
    foreach ($this->languageManager->getLanguages() as $langcode => $language) {
      if (function_exists('locale_is_translatable') && !locale_is_translatable($langcode)) {
        continue;
      }
      $langcodes[] = $langcode;
    }
    return $langcodes;
  }

  /**
   * Returns enabled module and theme machine names.
   *
   * @return string[]
   *   Extension machine names including drupal.
   */
  private function enabledExtensionNames(): array {
    $extensions = array_keys($this->moduleHandler->getModuleList());
    foreach ($this->themeHandler->listInfo() as $name => $theme) {
      if ($theme->status) {
        $extensions[] = $name;
      }
    }
    $extensions[] = 'drupal';
    return array_values(array_unique($extensions));
  }

  /**
   * Collects contrib PO files for active languages and enabled extensions.
   *
   * @param string[] $activeLangcodes
   *   Active language codes.
   * @param string[] $enabledExtensions
   *   Enabled extension machine names.
   *
   * @return list<array{path: string, langcode: string, label: string}>
   *   PO file descriptors.
   */
  private function collectContribPoFiles(array $activeLangcodes, array $enabledExtensions): array {
    $directory = dirname(DRUPAL_ROOT) . '/translations/contrib';
    if (!is_dir($directory)) {
      return [];
    }

    $files = [];
    $finder = new Finder();
    $finder->files()->in($directory)->depth('== 0')->name('*.po')->sortByName();

    foreach ($finder as $fileInfo) {
      $filename = $fileInfo->getFilename();
      if ($this->isCustomPoBasename($filename)) {
        continue;
      }

      $langcode = $this->langcodeFromPoBasename($filename);
      if ($langcode === NULL || !in_array($langcode, $activeLangcodes, TRUE)) {
        continue;
      }

      $extension = $this->extensionFromContribPoBasename($filename);
      if ($extension !== 'drupal' && !in_array($extension, $enabledExtensions, TRUE)) {
        continue;
      }

      $files[] = [
        'path' => $fileInfo->getPathname(),
        'langcode' => $langcode,
        'label' => $filename,
      ];
    }

    return $files;
  }

  /**
   * Collects custom project PO files for active languages.
   *
   * @param string[] $activeLangcodes
   *   Active language codes.
   *
   * @return list<array{path: string, langcode: string, label: string}>
   *   PO file descriptors.
   */
  private function collectCustomPoFiles(array $activeLangcodes): array {
    $files = [];
    $roots = [
      DRUPAL_ROOT . '/modules/custom',
      DRUPAL_ROOT . '/themes/custom',
    ];

    foreach ($roots as $root) {
      if (!is_dir($root)) {
        continue;
      }

      $finder = new Finder();
      $finder->files()->in($root)->path('/translations/')->name('*.po')->sortByName();

      foreach ($finder as $fileInfo) {
        $filename = $fileInfo->getFilename();
        if (!preg_match('/^(ps_|bnp_|ps_theme\.)/', $filename)) {
          continue;
        }
        if (!preg_match('/^(ps_.+|bnp_.+|ps_theme\..+)\.[a-z]{2,3}\.po$/', $filename)) {
          continue;
        }

        $langcode = $this->langcodeFromPoBasename($filename);
        if ($langcode === NULL || !in_array($langcode, $activeLangcodes, TRUE)) {
          continue;
        }

        $files[] = [
          'path' => $fileInfo->getPathname(),
          'langcode' => $langcode,
          'label' => $filename,
        ];
      }
    }

    return $files;
  }

  /**
   * Imports a list of PO files via Gettext::fileToDatabase().
   *
   * @param list<array{path: string, langcode: string, label: string}> $files
   *   PO file descriptors.
   * @param int $customized
   *   LOCALE_CUSTOMIZED or LOCALE_NOT_CUSTOMIZED.
   * @param callable|null $progress
   *   Optional progress callback.
   * @param string $phase
   *   Progress phase label.
   *
   * @return array{imported: int, skipped: int, failed: int}
   *   Import statistics.
   */
  private function importPoFiles(array $files, int $customized, ?callable $progress, string $phase): array {
    $stats = ['imported' => 0, 'skipped' => 0, 'failed' => 0];
    $total = count($files);

    if ($total === 0) {
      return $stats;
    }

    $options = [
      'overwrite_options' => [
        'not_customized' => TRUE,
        'customized' => TRUE,
      ],
      'customized' => $customized,
      'items' => -1,
      'seek' => 0,
    ];

    foreach ($files as $index => $file) {
      $current = $index + 1;
      if ($progress !== NULL) {
        $progress($phase, [
          'langcode' => $file['langcode'],
          'file' => $file['label'],
          'current' => $current,
          'total' => $total,
        ]);
      }

      try {
        $poFile = (object) [
          'filename' => $file['label'],
          'uri' => $file['path'],
        ];
        $poFile = locale_translate_file_attach_properties($poFile, ['langcode' => $file['langcode']]);
        Gettext::fileToDatabase($poFile, $options);
        $stats['imported']++;
      }
      catch (\Throwable) {
        $stats['failed']++;
      }
    }

    return $stats;
  }

  /**
   * Imports language config overrides from extension config/install/language.
   *
   * @param string[] $activeLangcodes
   *   Active language codes.
   * @param callable|null $progress
   *   Optional progress callback.
   *
   * @return array{imported: int, skipped: int, failed: int}
   *   Import statistics.
   */
  private function importLanguageConfigOverrides(array $activeLangcodes, ?callable $progress): array {
    $stats = ['imported' => 0, 'skipped' => 0, 'failed' => 0];

    if (!$this->languageManager instanceof ConfigurableLanguageManagerInterface) {
      return $stats;
    }

    $tasks = [];
    $roots = [DRUPAL_ROOT . '/modules/custom', DRUPAL_ROOT . '/themes/custom'];
    foreach ($activeLangcodes as $langcode) {
      foreach ($roots as $root) {
        if (!is_dir($root)) {
          continue;
        }
        foreach (glob($root . '/*', GLOB_ONLYDIR) ?: [] as $extensionPath) {
          foreach (['install', 'optional'] as $type) {
            $languageDir = $extensionPath . '/config/' . $type . '/language/' . $langcode;
            if (!is_dir($languageDir)) {
              continue;
            }
            foreach (glob($languageDir . '/*.yml') ?: [] as $file) {
              $tasks[] = [
                'langcode' => $langcode,
                'file' => basename($file),
                'path' => $file,
                'config_name' => basename($file, '.yml'),
              ];
            }
          }
        }
      }
    }

    $total = count($tasks);
    foreach ($tasks as $index => $task) {
      if ($progress !== NULL) {
        $progress('overrides', [
          'langcode' => $task['langcode'],
          'file' => $task['file'],
          'current' => $index + 1,
          'total' => $total,
        ]);
      }

      try {
        $data = Yaml::decode((string) file_get_contents($task['path']));
        if (!is_array($data) || $data === []) {
          $stats['skipped']++;
          continue;
        }
        $override = $this->languageManager->getLanguageConfigOverride($task['langcode'], $task['config_name']);
        foreach ($data as $key => $value) {
          if ($value === NULL || in_array($key, ['handlers', 'variants'], TRUE)) {
            continue;
          }
          $override->set($key, $value);
        }
        $override->save();
        $stats['imported']++;
      }
      catch (\Throwable) {
        $stats['failed']++;
      }
    }

    return $stats;
  }

  /**
   * Checks whether a PO basename belongs to a custom PS/BNP project file.
   */
  private function isCustomPoBasename(string $filename): bool {
    return str_starts_with($filename, 'ps_')
      || str_starts_with($filename, 'bnp_')
      || str_starts_with($filename, 'ps_theme.');
  }

  /**
   * Extracts the language code from a PO filename suffix.
   */
  private function langcodeFromPoBasename(string $filename): ?string {
    if (preg_match('/\.([a-z]{2,3})\.po$/', $filename, $matches) === 1) {
      return $matches[1];
    }
    return NULL;
  }

  /**
   * Extracts the Drupal extension name from a contrib PO filename.
   */
  private function extensionFromContribPoBasename(string $filename): string {
    $stem = substr($filename, 0, -3);
    $prefix = substr($stem, 0, (int) strrpos($stem, '.'));

    if (str_starts_with($prefix, 'drupal')) {
      return 'drupal';
    }

    if (preg_match('/^(.+)-[0-9].*$/', $prefix, $matches) === 1) {
      return $matches[1];
    }

    return $prefix;
  }

}
