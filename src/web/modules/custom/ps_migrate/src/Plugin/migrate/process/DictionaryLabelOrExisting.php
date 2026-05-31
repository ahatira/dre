<?php

declare(strict_types=1);

namespace Drupal\ps_migrate\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Keep existing dictionary labels, fallback to source value/code for new ones.
 *
 * @MigrateProcessPlugin(
 *   id = "dictionary_label_or_existing"
 * )
 */
final class DictionaryLabelOrExisting extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property): string {
    $id = (string) ($row->getSourceProperty('id') ?? '');
    if ($id !== '') {
      $existing = \Drupal::entityTypeManager()->getStorage('ps_dictionary_entry')->load($id);
      if ($existing) {
        return (string) $existing->label();
      }
    }

    $label = trim((string) $value);
    if ($label !== '') {
      return $label;
    }

    return trim((string) ($row->getSourceProperty('code') ?? ''));
  }

}
