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
   * Finds entries whose label starts with the given query (case-insensitive).
   *
   * @return list<array{code: string, label: string}>
   *   Matching entries sorted by weight then label.
   */
  public function searchByLabelPrefix(string $type, string $query, int $limit = 5): array {
    $query = trim($query);
    if ($query === '' || $limit < 1) {
      return [];
    }

    $needle = mb_strtolower($query);
    $matches = [];

    foreach ($this->loadByType($type) as $entry) {
      if (!str_starts_with(mb_strtolower($entry->label()), $needle)) {
        continue;
      }
      $matches[] = [
        'code' => $entry->getCode(),
        'label' => $entry->label(),
        'weight' => $entry->getWeight(),
      ];
    }

    usort($matches, static function (array $a, array $b): int {
      return ($a['weight'] ?? 0) <=> ($b['weight'] ?? 0)
        ?: strcasecmp($a['label'], $b['label']);
    });

    $results = [];
    foreach (array_slice($matches, 0, $limit) as $match) {
      $results[] = [
        'code' => $match['code'],
        'label' => $match['label'],
      ];
    }

    return $results;
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
