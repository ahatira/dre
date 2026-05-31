<?php

declare(strict_types=1);

namespace Drupal\ps_migrate\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Extracts multilingual availability values from an offer XML node.
 *
 * @MigrateProcessPlugin(
 *   id = "multilingual_availability"
 * )
 */
final class MultilingualAvailability extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property): string {
    [$offerNode, $language] = $this->normalizeInputs($value);
    if (!$offerNode instanceof \SimpleXMLElement || $language === '') {
      return '';
    }

    $nodes = $offerNode->xpath("ML_AVAILABILITY/AVAILABILITY[@LANGUAGE='{$language}']");
    if (empty($nodes)) {
      return '';
    }

    return trim((string) $nodes[0]);
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

    return [$value, strtoupper((string) ($this->configuration['language'] ?? ''))];
  }

}