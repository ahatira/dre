<?php

declare(strict_types=1);

namespace Drupal\ps_diagnostic\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\ps_diagnostic\Entity\DiagnosticTypeInterface;

final class DiagnosticTypeOptionsProvider {

  public function __construct(private readonly EntityTypeManagerInterface $entityTypeManager) {
  }

  public function getOptions(bool $enabledOnly = TRUE): array {
    $options = [];
    $types = $this->entityTypeManager->getStorage('ps_diagnostic_type')->loadMultiple();
    uasort($types, static function (DiagnosticTypeInterface $a, DiagnosticTypeInterface $b): int {
      $weight_compare = ((int) $a->get('weight')) <=> ((int) $b->get('weight'));
      if ($weight_compare !== 0) {
        return $weight_compare;
      }
      return strnatcasecmp($a->label(), $b->label());
    });

    foreach ($types as $type) {
      if ($enabledOnly && !$type->get('enabled')) {
        continue;
      }
      $options[$type->id()] = $type->label();
    }
    return $options;
  }

  public function getType(string $type_id, bool $enabledOnly = FALSE): ?DiagnosticTypeInterface {
    $type = $this->entityTypeManager->getStorage('ps_diagnostic_type')->load($type_id);
    if (!$type instanceof DiagnosticTypeInterface) {
      return NULL;
    }
    if ($enabledOnly && !$type->isEnabled()) {
      return NULL;
    }
    return $type;
  }

  public function getClassOptions(string $type_id): array {
    $type = $this->getType($type_id);
    if (!$type) {
      return [];
    }

    $options = [];
    foreach ($type->getClasses() as $class) {
      $label = trim((string) ($class['label'] ?? ''));
      if ($label === '') {
        continue;
      }
      $options[$label] = $label;
    }
    return $options;
  }

  /**
   * @param string[] $type_ids
   */
  public function getClassOptionsForTypes(array $type_ids): array {
    $options = [];
    foreach ($type_ids as $type_id) {
      foreach ($this->getClassOptions((string) $type_id) as $value => $label) {
        $options[$value] = $label;
      }
    }
    natcasesort($options);
    return $options;
  }

}
