<?php

declare(strict_types=1);

namespace Drupal\ps_dictionary\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\ps_dictionary\Entity\DictionaryEntryInterface;

final class DictionaryResolver {

  public function __construct(
    private readonly EntityTypeManagerInterface $entityTypeManager,
  ) {}

  public function resolveLabel(string $type, string $code): ?string {
    foreach ($this->loadByType($type) as $entry) {
      if (mb_strtoupper($entry->getCode()) === mb_strtoupper($code)) {
        return $entry->label();
      }
    }
    return NULL;
  }

  public function resolveCode(string $type, string $label): ?string {
    foreach ($this->loadByType($type) as $entry) {
      if (mb_strtolower($entry->label()) === mb_strtolower($label)) {
        return $entry->getCode();
      }
    }
    return NULL;
  }

  public function all(string $type): array {
    $result = [];
    foreach ($this->loadByType($type) as $entry) {
      $result[] = [
        'code' => $entry->getCode(),
        'label' => $entry->label(),
        'weight' => $entry->getWeight(),
      ];
    }
    usort($result, static fn(array $a, array $b): int => $a['weight'] <=> $b['weight']);
    return $result;
  }

  public function isValid(string $type, string $code): bool {
    return $this->resolveLabel($type, $code) !== NULL;
  }

  /**
   * @return array<int, \Drupal\ps_dictionary\Entity\DictionaryEntryInterface>
   */
  private function loadByType(string $type): array {
    $storage = $this->entityTypeManager->getStorage('ps_dictionary_entry');
    $entities = $storage->loadByProperties(['type' => $type]);
    return array_values(array_filter($entities, static fn($e): bool => $e instanceof DictionaryEntryInterface));
  }

}
