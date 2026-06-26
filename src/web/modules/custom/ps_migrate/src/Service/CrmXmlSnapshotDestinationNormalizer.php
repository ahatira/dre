<?php

declare(strict_types=1);

namespace Drupal\ps_migrate\Service;

/**
 * Normalizes migrate destination properties for entity field synchronization.
 */
final class CrmXmlSnapshotDestinationNormalizer {

  /**
   * Collapses slash-separated destination properties into entity field values.
   *
   * @param array<string, mixed> $destination
   *   Raw migrate destination properties.
   *
   * @return array<string, mixed>
   *   Values keyed by entity field machine name.
   */
  public function normalize(array $destination): array {
    $normalized = [];

    foreach ($destination as $property => $value) {
      if (!is_string($property) || $property === '') {
        continue;
      }
      if ($value === NULL) {
        continue;
      }

      if (!str_contains($property, '/')) {
        $normalized[$property] = $value;
        continue;
      }

      [$field, $column] = explode('/', $property, 2);
      if ($field === '' || $column === '') {
        continue;
      }

      if (!isset($normalized[$field]) || !is_array($normalized[$field])) {
        $normalized[$field] = [];
      }
      if (!isset($normalized[$field][0]) || !is_array($normalized[$field][0])) {
        $normalized[$field][0] = [];
      }
      $normalized[$field][0][$column] = $value;
    }

    foreach ($normalized as $field => $value) {
      if (!is_array($value) || !isset($value[0]) || !is_array($value[0])) {
        continue;
      }
      if (array_is_list($value) && count($value) === 1) {
        $normalized[$field] = $value[0];
      }
    }

    return $normalized;
  }

}
