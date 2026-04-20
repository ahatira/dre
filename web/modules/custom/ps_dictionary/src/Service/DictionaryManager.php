<?php

declare(strict_types=1);

namespace Drupal\ps_dictionary\Service;

use Symfony\Component\Yaml\Yaml;
use Drupal\ps_dictionary\Entity\DictionaryTypeInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Logger\LoggerChannelInterface;
use Drupal\ps_dictionary\Entity\DictionaryEntryInterface;

/**
 * Dictionary manager service implementation.
 *
 * Provides centralized management of business dictionaries with caching.
 * Performance: O(1) after first load per dictionary type (cached).
 */
final class DictionaryManager implements DictionaryManagerInterface {

  /**
   * Runtime cache of loaded entries by type.
   *
   * @var array<string, \Drupal\ps_dictionary\Entity\DictionaryEntryInterface[]>
   */
  private array $entriesCache = [];

  /**
   * The logger.
   */
  private readonly LoggerChannelInterface $logger;

  /**
   * Constructs a DictionaryManager.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache
   *   The cache backend.
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $loggerFactory
   *   The logger factory.
   */
  public function __construct(
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly CacheBackendInterface $cache,
    LoggerChannelFactoryInterface $loggerFactory,
  ) {
    $this->logger = $loggerFactory->get('ps_dictionary');
  }

  /**
   * {@inheritdoc}
   */
  public function isValid(string $type, string $code): bool {
    $entry = $this->getEntry($type, $code);
    return $entry !== NULL && $entry->isActive();
  }

  /**
   * {@inheritdoc}
   */
  public function getLabel(string $type, string $code, ?string $langcode = NULL): ?string {
    $entry = $this->getEntry($type, $code);

    // Only return label for active entries.
    if ($entry && !$entry->isActive()) {
      return NULL;
    }

    return $entry?->getLabel();
  }

  /**
   * {@inheritdoc}
   */
  public function getOptions(string $type, bool $activeOnly = TRUE): array {
    $entries = $this->getEntries($type, $activeOnly);
    $options = [];

    foreach ($entries as $entry) {
      $options[$entry->getCode()] = $entry->label();
    }

    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function getEntry(string $type, string $code): ?DictionaryEntryInterface {
    $entries = $this->loadEntries($type);
    // Entity ID uses lowercase code, but the code field stores uppercase.
    $id = $type . '_' . strtolower($code);

    return $entries[$id] ?? NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getEntries(string $type, bool $activeOnly = TRUE): array {
    $entries = $this->loadEntries($type);

    if ($activeOnly) {
      $entries = array_filter($entries, fn($entry) => $entry->isActive());
    }

    // Sort by weight, then label.
    uasort($entries, function (DictionaryEntryInterface $a, DictionaryEntryInterface $b) {
      $weightCompare = $a->getWeight() <=> $b->getWeight();
      return $weightCompare !== 0 ? $weightCompare : strcmp($a->label(), $b->label());
    });

    return $entries;
  }

  /**
   * {@inheritdoc}
   */
  public function isDeprecated(string $type, string $code): bool {
    $entry = $this->getEntry($type, $code);
    return $entry?->isDeprecated() ?? FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function getMetadata(string $type, string $code): array {
    $entry = $this->getEntry($type, $code);
    $metadata = $entry?->getMetadata() ?? [];

    // Wrap translatable metadata fields in TranslatableMarkup based on schema.
    $dictionaryType = $this->getDictionaryType($type);
    if ($dictionaryType) {
      $schema = $this->parseMetadataSchema($dictionaryType);
      foreach ($metadata as $key => $value) {
        if (is_string($value) && isset($schema[$key]['translate']) && $schema[$key]['translate'] === TRUE) {
          $metadata[$key] = new TranslatableMarkup(
            $value,
            [],
            ['context' => "ps_dictionary:$type:$code:$key"]
          );
        }
      }
    }

    return $metadata;
  }

  /**
   * Load a dictionary type entity.
   *
   * @param string $type
   *   The dictionary type ID.
   *
   * @return \Drupal\ps_dictionary\Entity\DictionaryTypeInterface|null
   *   The dictionary type entity or NULL.
   */
  private function getDictionaryType(string $type): ?DictionaryTypeInterface {
    return $this->entityTypeManager->getStorage('ps_dictionary_type')->load($type);
  }

  /**
   * Parse metadata schema YAML and return as array.
   *
   * @param \Drupal\ps_dictionary\Entity\DictionaryTypeInterface $dictionaryType
   *   The dictionary type entity.
   *
   * @return array<string, mixed>
   *   Parsed metadata schema.
   */
  private function parseMetadataSchema(DictionaryTypeInterface $dictionaryType): array {
    $schemaYaml = $dictionaryType->get('metadata_schema');
    if (empty($schemaYaml)) {
      return [];
    }

    try {
      $schema = Yaml::parse($schemaYaml);
      return is_array($schema) ? $schema : [];
    }
    catch (\Exception $e) {
      $this->safeLog('warning', 'Failed to parse metadata schema for @type: @error', [
        '@type' => $dictionaryType->id(),
        '@error' => $e->getMessage(),
      ]);
      return [];
    }
  }

  /**
   * {@inheritdoc}
   */
  public function clearCache(?string $type = NULL): void {
    if ($type === NULL) {
      $this->entriesCache = [];
      $this->cache->deleteAll();
      $this->safeLog('info', 'Cleared all dictionary caches');
    }
    else {
      unset($this->entriesCache[$type]);
      $this->cache->delete('ps_dictionary:entries:' . $type);
      $this->safeLog('info', 'Cleared cache for dictionary type: @type', ['@type' => $type]);
    }
  }

  /**
   * Logs without breaking runtime when logging storage is not ready.
   *
   * @param string $level
   *   Log level.
   * @param string $message
   *   Log message.
   * @param array<string, mixed> $context
   *   Log context.
   */
  private function safeLog(string $level, string $message, array $context = []): void {
    try {
      $this->logger->log($level, $message, $context);
    }
    catch (\Throwable) {
      // Ignore logger storage issues during early install/bootstrap phases.
    }
  }

  /**
   * Loads all entries for a dictionary type.
   *
   * Uses 2-level cache (runtime + persistent).
   *
   * @param string $type
   *   Dictionary type ID.
   *
   * @return array<string, \Drupal\ps_dictionary\Entity\DictionaryEntryInterface>
   *   Entries keyed by ID.
   */
  private function loadEntries(string $type): array {
    // Check runtime cache first.
    if (isset($this->entriesCache[$type])) {
      return $this->entriesCache[$type];
    }

    // Check persistent cache.
    $cacheKey = 'ps_dictionary:entries:' . $type;
    $cached = $this->cache->get($cacheKey);

    if ($cached) {
      $this->entriesCache[$type] = $cached->data;
      return $this->entriesCache[$type];
    }

    // Load from storage.
    $storage = $this->entityTypeManager->getStorage('ps_dictionary_entry');
    $entries = $storage->loadByProperties(['dictionary_type' => $type]);

    // Cache with tags for automatic invalidation.
    $this->cache->set(
      $cacheKey,
      $entries,
      CacheBackendInterface::CACHE_PERMANENT,
      ["ps_dictionary:{$type}"]
    );

    /** @var array<string, \Drupal\ps_dictionary\Entity\DictionaryEntryInterface> $typedEntries */
    $typedEntries = $entries;
    $this->entriesCache[$type] = $typedEntries;
    return $typedEntries;
  }

  /**
   * Gets metadata value for entry.
   *
   * @param string $type
   *   The dictionary type.
   * @param string $code
   *   The entry code.
   * @param string $key
   *   The metadata key.
   * @param mixed $default
   *   The default value.
   *
   * @return mixed
   *   The metadata value or default.
   */
  public function getMetadataValue(string $type, string $code, string $key, mixed $default = NULL): mixed {
    $entry = $this->getEntry($type, $code);
    return $entry !== NULL ? $entry->getMetadataValue($key, $default) : $default;
  }

  /**
   * Gets metadata value with type coercion.
   *
   * @param string $type
   *   The dictionary type.
   * @param string $code
   *   The entry code.
   * @param string $key
   *   The metadata key.
   * @param string $targetType
   *   The target type: string, int, float, bool, array.
   * @param mixed $default
   *   The default value.
   *
   * @return mixed
   *   The typed value or default.
   */
  public function getMetadataTyped(string $type, string $code, string $key, string $targetType = 'string', mixed $default = NULL): mixed {
    $entry = $this->getEntry($type, $code);
    return $entry !== NULL ? $entry->getMetadataTyped($key, $targetType, $default) : $default;
  }

  /**
   * {@inheritdoc}
   */
  public function getAvailableTypes(): array {
    $cacheKey = 'ps_dictionary:available_types';
    $cached = $this->cache->get($cacheKey);

    if ($cached) {
      return $cached->data;
    }

    $storage = $this->entityTypeManager->getStorage('ps_dictionary_type');
    $types = $storage->loadMultiple();

    $options = [];
    foreach ($types as $type) {
      $options[$type->id()] = $type->label();
    }

    // Cache with general tag for invalidation on any dictionary type change.
    $this->cache->set(
      $cacheKey,
      $options,
      CacheBackendInterface::CACHE_PERMANENT,
      ['ps_dictionary:types']
    );

    return $options;
  }

}
