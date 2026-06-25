<?php

declare(strict_types=1);

namespace Drupal\ps_migrate\Service;

/**
 * Builds migrate source rows from XPath field selectors on an XML item node.
 */
final class CrmOfferXmlXpathRowBuilder {

  /**
   * Builds one source row from field selectors relative to an item node.
   *
   * @param array<int, array<string, mixed>> $fields
   *
   * @return array<string, mixed>
   */
  public function buildRow(\SimpleXMLElement $item, array $fields): array {
    $row = [];

    foreach ($fields as $fieldInfo) {
      if (!is_array($fieldInfo)) {
        continue;
      }

      $name = (string) ($fieldInfo['name'] ?? '');
      $selector = (string) ($fieldInfo['selector'] ?? '');
      if ($name === '' || $selector === '') {
        continue;
      }

      $values = [];
      foreach ($item->xpath($selector) ?: [] as $value) {
        if ($value instanceof \SimpleXMLElement && $value->children() && !trim((string) $value)) {
          $values[] = $value;
        }
        else {
          $values[] = (string) $value;
        }
      }

      if ($values === []) {
        continue;
      }

      $row[$name] = count($values) === 1 ? reset($values) : $values;
    }

    return $row;
  }

}
