<?php

declare(strict_types=1);

namespace Drupal\ps_migrate\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\MigrateSkipRowException;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Skips translation rows when target and source languages are identical.
 *
 * @MigrateProcessPlugin(
 *   id = "skip_translation_if_same_language"
 * )
 */
final class SkipTranslationIfSameLanguage extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property): mixed {
    $targetLanguage = strtolower(trim((string) ($this->configuration['target_language'] ?? '')));
    $sourceLanguage = strtolower(trim((string) $value));

    if ($targetLanguage !== '' && $sourceLanguage === $targetLanguage) {
      throw new MigrateSkipRowException(sprintf('Skipping %s translation because it matches the canonical source language.', $targetLanguage));
    }

    return $value;
  }

}