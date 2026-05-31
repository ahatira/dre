<?php

declare(strict_types=1);

namespace Drupal\ps_migrate\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Extract multilingual descriptions from XML ML_DESCRIPTION_X nodes.
 *
 * This plugin can either return the first non-empty description by priority,
 * or concatenate all non-empty descriptions from the configured priority list.
 *
 * Supports multiple languages (FR, EN, etc.).
 *
 * Configuration:
 * - language: Language code to extract (e.g., 'FR', 'EN')
 * - source language may also be provided as the second pipeline input value
 * - priority: Array of ML_DESCRIPTION field numbers in priority order.
 *   (default: [2, 4, 1])
 * - mode: Extraction mode, one of:
 *   - first (default): return first non-empty description from priority list.
 *   - concat: concatenate all non-empty descriptions from priority list.
 * - separator: Separator used in concat mode (default: "\n\n").
 *
 * Example usage:
 * @code
 * body_fr:
 *   plugin: multilingual_description
 *   source: ml_descriptions_xml
 *   language: FR
 *   priority: [2, 4, 1]
 * @endcode
 *
 * @MigrateProcessPlugin(
 *   id = "multilingual_description"
 * )
 */
final class MultilingualDescription extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property): string {
    [$offerNode, $language] = $this->normalizeInputs($value);
    if (!$offerNode instanceof \SimpleXMLElement) {
      return '';
    }

    $language = $language !== '' ? $language : ($this->configuration['language'] ?? 'FR');
    $priority = $this->configuration['priority'] ?? [2, 4, 1];
    $mode = strtolower((string) ($this->configuration['mode'] ?? 'first'));
    $separator = (string) ($this->configuration['separator'] ?? "\n\n");

    $collected = [];

    // Try each ML_DESCRIPTION in priority order.
    foreach ($priority as $num) {
      $field_name = "ML_DESCRIPTION_{$num}";
      $descriptions = $offerNode->xpath($field_name);

      if (empty($descriptions)) {
        continue;
      }

      $ml_description = $descriptions[0];
      
      // Find DESCRIPTION node with matching LANGUAGE attribute.
      $desc_nodes = $ml_description->xpath("DESCRIPTION[@LANGUAGE='{$language}']");
      
      if (!empty($desc_nodes)) {
        $text = trim((string) $desc_nodes[0]);
        if ($text !== '') {
          if ($mode === 'concat') {
            $collected[] = $text;
            continue;
          }
          return $text;
        }
      }
    }

    if ($mode === 'concat' && $collected !== []) {
      return implode($separator, $collected);
    }

    return '';
  }

  /**
   * Normalizes plugin inputs.
   *
   * @return array{0:mixed,1:string}
   *   Offer XML node and XML language code.
   */
  private function normalizeInputs(mixed $value): array {
    if (is_array($value)) {
      return [
        $value[0] ?? NULL,
        strtoupper(trim((string) ($value[1] ?? ''))),
      ];
    }

    return [$value, ''];
  }

}
