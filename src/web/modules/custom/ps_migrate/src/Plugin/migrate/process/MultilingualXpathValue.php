<?php

declare(strict_types=1);

namespace Drupal\ps_migrate\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Extracts a multilingual value from an XML path and target language.
 *
 * Inputs:
 * - value[0]: offer XML node
 * - value[1]: target XML language (optional, uppercase recommended)
 *
 * Configuration:
 * - xpath: XPath to multilingual nodes (e.g. ML_AVAILABILITY/AVAILABILITY)
 * - language_attribute: XML attribute name that stores language (default: LANGUAGE)
 *
 * @MigrateProcessPlugin(
 *   id = "multilingual_xpath_value"
 * )
 */
final class MultilingualXpathValue extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property): string {
    [$offerNode, $language] = $this->normalizeInputs($value);
    if (!$offerNode instanceof \SimpleXMLElement) {
      return '';
    }

    $xpath = trim((string) ($this->configuration['xpath'] ?? ''));
    if ($xpath === '') {
      return '';
    }

    $nodes = $offerNode->xpath($xpath) ?: [];
    if ($nodes === []) {
      return '';
    }

    $languageAttribute = (string) ($this->configuration['language_attribute'] ?? 'LANGUAGE');
    foreach ($nodes as $node) {
      $nodeLanguage = strtoupper(trim((string) ($node[$languageAttribute] ?? '')));
      if ($language !== '' && $nodeLanguage !== $language) {
        continue;
      }

      $text = trim((string) $node);
      if ($text !== '') {
        return $text;
      }
    }

    foreach ($nodes as $fallbackNode) {
      $text = trim((string) $fallbackNode);
      if ($text !== '') {
        return $text;
      }
    }

    return '';
  }

  /**
   * Normalizes plugin input values.
   *
   * @return array{0:mixed,1:string}
   *   Offer node and target XML language.
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
