<?php

declare(strict_types=1);

namespace Drupal\ps_migrate\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Builds offer field_surfaces rows from GLOBAL_SURFACES XML arrays.
 *
 * Keeps all qualifications found in XML (e.g. TOTAL, MINIM, DISPO, ETREF)
 * in source order.
 *
 * @MigrateProcessPlugin(
 *   id = "offer_surfaces_from_xml"
 * )
 */
final class OfferSurfacesFromXml extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property): array {
    [$qualifications, $values, $unitCode] = $this->normalizeInputs($value);

    $rows = [];
    $count = max(count($qualifications), count($values));
    for ($i = 0; $i < $count; $i++) {
      $qualification = strtoupper(trim((string) ($qualifications[$i] ?? '')));
      $rawValue = $values[$i] ?? NULL;

      if ($qualification === '' || $rawValue === NULL || $rawValue === '') {
        continue;
      }

      $rows[] = [
        'qualification' => $qualification,
        'value' => round((float) $rawValue, 2),
        'unit_code' => $unitCode,
      ];
    }

    return $rows;
  }

  /**
   * @return array{0:array<int,mixed>,1:array<int,mixed>,2:string}
   */
  private function normalizeInputs(mixed $value): array {
    if (!is_array($value)) {
      return [[], [], $this->defaultUnitCode()];
    }

    $quals = isset($value[0]) && is_array($value[0]) ? array_values($value[0]) : [];
    $values = isset($value[1]) && is_array($value[1]) ? array_values($value[1]) : [];

    $sourceUnitCode = strtoupper(trim((string) ($value[2] ?? '')));
    $unitCode = $sourceUnitCode !== '' ? $sourceUnitCode : $this->defaultUnitCode();

    return [$quals, $values, $unitCode];
  }

  private function defaultUnitCode(): string {
    return strtoupper((string) ($this->configuration['default_unit_code'] ?? 'M2'));
  }

}
